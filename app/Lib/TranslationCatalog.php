<?php
/** PO/POT catalogs are data. Never execute their contents. */
class TranslationCatalog {
    private $base;
    public function __construct($base = null) { $this->base = $base === null ? APP . 'Locale' : $base; }
    public function path($code) {
        if (!preg_match('/^[a-z]{3}$/D', $code)) throw new RuntimeException('Invalid language code.');
        return $this->base . '/' . $code . '/LC_MESSAGES/default.po';
    }
    public function parse($path) {
        if (!is_readable($path)) throw new RuntimeException('Cannot read translation catalog: ' . $path);
        $entries = array(); $entry = array(); $field = null;
        foreach (preg_split('/\R/', file_get_contents($path)) as $line) {
            $line = trim($line);
            if ($line === '') {
                if (isset($entry['msgid'])) $entries[$this->key($entry)] = $entry;
                $entry = array(); $field = null; continue;
            }
            if ($line[0] === '#') continue;
            if (preg_match('/^(msgctxt|msgid_plural|msgid|msgstr(?:\[\d+\])?)\s+(".*")$/', $line, $m)) {
                $field = $m[1]; $value = json_decode($m[2], true);
                if (!is_string($value)) throw new RuntimeException('Invalid quoted catalog text.');
                $entry[$field] = $value;
            } elseif ($line[0] === '"' && $field !== null) {
                $value = json_decode($line, true);
                if (!is_string($value)) throw new RuntimeException('Invalid catalog continuation.');
                $entry[$field] .= $value;
            } else throw new RuntimeException('Unsupported catalog syntax.');
        }
        if (isset($entry['msgid'])) $entries[$this->key($entry)] = $entry;
        return $entries;
    }
    private function key($entry) { return hash('sha256', (isset($entry['msgctxt']) ? $entry['msgctxt'] : '') . "\004" . $entry['msgid']); }
    public function entries($code) {
        $entries = $this->parse($this->base . '/default.pot');
        $path = $this->path($code);
        if (is_file($path)) foreach ($this->parse($path) as $key => $entry) $entries[$key] = $entry;
        foreach ($entries as $key => $entry) if ($entry['msgid'] === '') unset($entries[$key]);
        return $entries;
    }
    public function version($code) { $p = $this->path($code); return is_file($p) ? hash_file('sha256', $p) : 'new'; }
    private function placeholders($value) {
        preg_match_all('/(?<!%)%(?!%)(?:\d+\$)?[-+0 #]*(?:\d+)?(?:\.\d+)?[bcdeEfFgGosuxX]|\{:[A-Za-z0-9_]+\}/', $value, $matches);
        $items = $matches[0]; sort($items); return $items;
    }
    public function update($entries, $changes, $pluralCount) {
        if (!is_array($changes)) throw new RuntimeException('Invalid translation values.');
        foreach ($changes as $key => $values) {
            if (!isset($entries[$key]) || !is_array($values)) throw new RuntimeException('Unknown translation entry.');
            $entry = &$entries[$key];
            foreach ($values as $index => $value) {
                if (!ctype_digit((string)$index) || (int)$index >= $pluralCount || !is_string($value)) throw new RuntimeException('Invalid translation value.');
                $source = (int)$index > 0 && isset($entry['msgid_plural']) ? $entry['msgid_plural'] : $entry['msgid'];
                if ($value !== '' && $this->placeholders($source) !== $this->placeholders($value)) throw new RuntimeException('Keep all placeholders unchanged: ' . $source);
                $field = isset($entry['msgid_plural']) ? 'msgstr[' . (int)$index . ']' : 'msgstr';
                $entry[$field] = $value;
            }
            unset($entry);
        }
        return $entries;
    }
    public function save($code, $entries, $pluralForms) {
        if (!preg_match('/^nplurals=([1-6]);\s*plural=[n0-9\s()!<>=&|%?:+*\/\-]+;$/D', $pluralForms)) throw new RuntimeException('Invalid plural forms.');
        $count = (int)$pluralForms[9];
        $header = "Project-Id-Version: FlinkISO\nLanguage: " . $code . "\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nPlural-Forms: " . $pluralForms . "\n";
        $quote = function ($value) { return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); };
        $text = 'msgid ""' . "\nmsgstr " . $quote($header) . "\n\n";
        foreach ($entries as $entry) {
            if ($entry['msgid'] === '') continue;
            if (isset($entry['msgctxt'])) $text .= 'msgctxt ' . $quote($entry['msgctxt']) . "\n";
            $text .= 'msgid ' . $quote($entry['msgid']) . "\n";
            if (isset($entry['msgid_plural'])) {
                $text .= 'msgid_plural ' . $quote($entry['msgid_plural']) . "\n";
                for ($i = 0; $i < $count; $i++) $text .= 'msgstr[' . $i . '] ' . $quote(isset($entry['msgstr[' . $i . ']']) ? $entry['msgstr[' . $i . ']'] : '') . "\n";
            } else $text .= 'msgstr ' . $quote(isset($entry['msgstr']) ? $entry['msgstr'] : '') . "\n";
            $text .= "\n";
        }
        $path = $this->path($code); $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) throw new RuntimeException('Cannot create language folder.');
        $temporary = tempnam($directory, '.catalog-');
        if ($temporary === false) throw new RuntimeException('Cannot write language folder.');
        try {
            if (file_put_contents($temporary, $text) === false || !chmod($temporary, 0644)) throw new RuntimeException('Cannot write translation catalog.');
            // CakePHP prefers MO over PO. Remove a stale compiled file before replacing the PO.
            $mo = substr($path, 0, -3) . '.mo';
            if (is_file($mo) && !unlink($mo)) throw new RuntimeException('Cannot remove stale compiled catalog.');
            if (!rename($temporary, $path)) throw new RuntimeException('Cannot publish translation catalog.');
        } finally { if (is_file($temporary)) unlink($temporary); }
        if (class_exists('Cache')) Cache::delete('default_' . $code, '_cake_core_');
        if (class_exists('I18n')) I18n::clear();
    }
}
