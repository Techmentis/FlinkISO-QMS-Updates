<?php
App::uses('TranslationCatalog', 'Lib');
App::uses('AiCatalogTranslator', 'Lib');
/** Persist each validated batch to the selected language catalog. */
class CatalogTranslationJob {
    private $catalog; private $directory;
    public function __construct($catalog = null, $directory = null) {
        $this->catalog = $catalog === null ? new TranslationCatalog() : $catalog;
        $this->directory = $directory === null ? TMP . 'language-translations' : $directory;
    }
    private function folder($id) {
        if (!is_string($id) || !preg_match('/^[a-f0-9]{32}$/D', $id)) throw new RuntimeException(__('Invalid translation job.'));
        return $this->directory . '/' . $id;
    }
    private function write($folder, $job) {
        $temporary = tempnam($folder, '.job-');
        if ($temporary === false) throw new RuntimeException(__('Cannot save AI translation progress.'));
        try {
            $json = json_encode($job, JSON_UNESCAPED_UNICODE);
            if ($json === false || file_put_contents($temporary, $json) === false || !chmod($temporary, 0600) || !rename($temporary, $folder . '/job.json')) throw new RuntimeException(__('Cannot save AI translation progress.'));
        } finally { if (is_file($temporary)) unlink($temporary); }
    }
    public function read($id, $owner) {
        $path = $this->folder($id) . '/job.json';
        $job = is_readable($path) ? json_decode(file_get_contents($path), true) : null;
        if (!is_array($job) || $job['owner'] !== $owner) throw new RuntimeException(__('Translation job not found.'));
        return $job;
    }
    public function start($upload, $language, $owner) {
        $source = array_filter($this->catalog->parse($upload), function ($entry) { return $entry['msgid'] !== ''; });
        if (!$source) throw new RuntimeException(__('The uploaded catalog contains no messages.'));
        $id = bin2hex(openssl_random_pseudo_bytes(16)); $folder = $this->folder($id);
        if (!mkdir($folder, 0700, true)) throw new RuntimeException(__('Cannot create AI translation workspace.'));
        $job = array('id' => $id, 'owner' => $owner, 'language' => $language, 'entries' => $source, 'keys' => array_keys($source),
            'next' => 0, 'total' => count($source), 'status' => 'working', 'version' => $this->catalog->version($language['code']), 'batch_size' => 12);
        $this->write($folder, $job);
        return $this->progress($job);
    }
    public function progress($job) {
        return array('job' => $job['id'], 'completed' => $job['next'], 'total' => $job['total'], 'done' => $job['status'] === 'complete',
            'percent' => $job['status'] === 'complete' ? 100 : min(99, (int)floor($job['next'] * 100 / max(1, $job['total']))),
            'location' => 'app/Locale/' . $job['language']['code'] . '/LC_MESSAGES/default.po');
    }
    public function step($id, $owner, $translator = null) {
        $folder = $this->folder($id);
        // Check ownership before opening any job files for writing.
        $this->read($id, $owner);
        $lock = fopen($folder . '/lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) throw new RuntimeException(__('This translation batch is already running.'));
        try {
            $job = $this->read($id, $owner);
            if ($job['status'] === 'complete') return $this->progress($job);
            // Finish a pending checkpoint before sending another AI request.
            if ($job['next'] > 0) $this->publishCheckpoint($folder, $job);
            $language = $job['language']; $batch = array(); $bytes = 0;
            foreach (array_slice($job['keys'], $job['next']) as $key) {
                $entry = $job['entries'][$key]; $length = strlen($entry['msgid']) + (isset($entry['msgid_plural']) ? strlen($entry['msgid_plural']) : 0);
                if ($batch && (count($batch) >= $job['batch_size'] || $bytes + $length > 4000)) break;
                $batch[$key] = $entry; $bytes += $length;
            }
            if ($batch) {
                if ($translator === null) $translator = new AiCatalogTranslator();
                try {
                    $text = $this->catalog->render($language['code'], $batch, $language['plural_forms']);
                    $result = $translator->translateFile($text, $language, 8000);
                    $translated = $this->catalog->validateReturnedFile($batch, $result, $language['plural_forms']);
                } catch (Exception $e) {
                    // Retry uses a smaller batch if the model truncated or omitted messages.
                    $job['batch_size'] = max(1, (int)floor($job['batch_size'] / 2));
                    $this->write($folder, $job);
                    throw $e;
                }
                foreach ($translated as $key => $entry) $job['entries'][$key] = $entry;
                $job['next'] += count($batch);
                $this->write($folder, $job);
            }
            $this->publishCheckpoint($folder, $job);
            if ($job['next'] === $job['total']) {
                $job['status'] = 'complete'; $this->write($folder, $job);
            }
            return $this->progress($job);
        } finally { flock($lock, LOCK_UN); fclose($lock); }
    }
    private function publishCheckpoint($folder, &$job) {
        $language = $job['language'];
        $entries = $this->catalog->entries($language['code']);
        // Only translated keys are copied; later batches remain in the source template.
        foreach (array_slice($job['keys'], 0, $job['next']) as $key) $entries[$key] = $job['entries'][$key];
        $result = new TranslationCatalog($folder);
        $result->save($language['code'], $entries, $language['plural_forms']);
        $current = $this->catalog->version($language['code']);
        if ($current !== $job['version'] && $current !== $result->version($language['code'])) throw new RuntimeException(__('The PO file was edited during translation. Download the result to review it before replacing the newer file.'));
        if ($current === $job['version']) {
            $path = $this->catalog->path($language['code']);
            if (is_file($path) && !is_file($folder . '/previous.po') && !copy($path, $folder . '/previous.po')) throw new RuntimeException(__('Cannot retain the previous PO file.'));
            $this->catalog->save($language['code'], $entries, $language['plural_forms']);
        }
        $job['version'] = $this->catalog->version($language['code']);
        $this->write($folder, $job);
    }
    public function resultPath($id, $owner) {
        $job = $this->read($id, $owner);
        $path = (new TranslationCatalog($this->folder($id)))->path($job['language']['code']);
        if (!is_readable($path)) throw new RuntimeException(__('The translated file is not ready.'));
        return $path;
    }
}
