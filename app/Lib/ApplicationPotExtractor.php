<?php
App::uses('ExtractTask', 'Console/Command/Task');
/** Web adapter for CakePHP's own i18n extractor; requires no CLI or shell process. */
class ApplicationPotExtractor extends ExtractTask {
    public $warnings = array();
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        // Console streams are unnecessary in an HTTP request.
        $this->params = array('overwrite' => true, 'exclude-plugins' => true);
    }
    public function out($message = null, $newlines = 1, $level = Shell::NORMAL) { return 0; }
    public function err($message = null, $newlines = 1) {
        if ($message !== null) $this->warnings[] = strip_tags(is_array($message) ? implode("\n", $message) : $message);
        return 0;
    }
    public function hr($newlines = 0, $width = 63) { return 0; }
    public function generate($application = null, $destination = null, $validation = true) {
        // Some legacy model source files emit whitespace when reflected.
        // Keep those bytes out of the controller's JSON response.
        ob_start();
        try { return $this->_generate($application, $destination, $validation); }
        finally { ob_end_clean(); }
    }
    protected function _generate($application, $destination, $validation) {
        $application = $application === null ? APP : rtrim($application, DS) . DS;
        $destination = $destination === null ? APP . 'Locale' . DS . 'default.pot' : $destination;
        $this->_paths = array(); $this->_files = array(); $this->_storage = array(); $this->_translations = array(); $this->warnings = array();
        foreach (array('Controller', 'Model', 'View', 'Lib') as $part) if (is_dir($application . $part)) $this->_paths[] = $application . $part;
        if (!$this->_paths) throw new RuntimeException(__('Cannot find application sources for translation extraction.'));
        $this->_merge = false;
        $this->_exclude = array('Vendor', 'Plugin', 'Test');
        $this->_searchFiles();
        $this->_extractTokens();
        // CakePHP reads model validation defaults using reflection, without constructing models.
        if ($validation) $this->_extractValidationMessages();
        $this->_buildFiles();
        if (empty($this->_storage['LC_MESSAGES']['default'])) throw new RuntimeException(__('No translatable messages were found.'));
        $text = $this->_writeHeader();
        foreach ($this->_storage['LC_MESSAGES']['default'] as $sentence => $references) $text .= $references . $sentence;
        $directory = dirname($destination);
        if (!is_dir($directory) && !mkdir($directory, 0755, true)) throw new RuntimeException(__('Cannot create the Locale folder.'));
        $temporary = tempnam($directory, '.pot-');
        if ($temporary === false) throw new RuntimeException(__('Cannot write the generated POT file.'));
        try {
            if (file_put_contents($temporary, $text) === false || !chmod($temporary, 0644) || !rename($temporary, $destination)) throw new RuntimeException(__('Cannot save the generated POT file.'));
        } finally { if (is_file($temporary)) unlink($temporary); }
        return $destination;
    }
}
