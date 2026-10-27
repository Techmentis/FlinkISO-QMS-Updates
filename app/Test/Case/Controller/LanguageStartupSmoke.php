<?php
// Standalone login startup checks with absent, directly loaded and autoloaded locale support.
class App {
    public static function uses($name, $package) {}
    public static function import($name) {}
}
class Controller {
    public $viewVars = array();
    public function set($key, $value) { $this->viewVars[$key] = $value; }
}
class Configure {
    public static $values = array();
    public static function write($key, $value) { self::$values[$key] = $value; }
}
class CakeLog {
    public static $messages = array();
    public static function write($level, $message) { self::$messages[] = $message; }
}
class LanguageStartupSession {
    public $values = array();
    public function read($key) { return isset($this->values[$key]) ? $this->values[$key] : null; }
    public function write($key, $value) { $this->values[$key] = $value; }
}
$mode = isset($argv[1]) ? $argv[1] : 'missing';
if (!in_array($mode, array('missing', 'file', 'autoload'), true)) throw new RuntimeException('Unknown test mode');
$root = sys_get_temp_dir() . '/flinkiso-language-startup-' . bin2hex(random_bytes(8));
mkdir($root . '/Lib', 0700, true);
define('APP', $root . '/');
define('DS', DIRECTORY_SEPARATOR);
$stub = '<?php class ApplicationL10n { public static $applied = null; public static function apply($code) { self::$applied = $code; } }';
if ($mode === 'file') file_put_contents(APP . 'Lib/ApplicationL10n.php', $stub);
if ($mode === 'autoload') spl_autoload_register(function($name) use ($stub) {
    if ($name === 'ApplicationL10n') eval(substr($stub, 5));
});
$project = isset($argv[2]) ? $argv[2] : dirname(__DIR__, 4);
require $project . '/app/Controller/AppController.php';
class LanguageStartupFixture extends AppController {
    public function applyLanguage() { $this->_applyUserLanguage(); }
}
try {
    $controller = new LanguageStartupFixture;
    $controller->Session = new LanguageStartupSession;
    $controller->applyLanguage();
    if (Configure::$values['Config.language'] !== 'eng') throw new RuntimeException('Login must select English');
    if ($controller->Session->values['Config.language'] !== 'eng') throw new RuntimeException('Session language missing');
    if ($mode === 'missing') {
        if (!CakeLog::$messages || $controller->viewVars['languagePreferenceAvailable'] !== false) throw new RuntimeException('Missing locale support must be logged and unavailable');
    } elseif (ApplicationL10n::$applied !== 'eng' || CakeLog::$messages) {
        throw new RuntimeException('Available locale support must be applied without warning');
    }
    echo 'PASS language startup: ' . $mode . "\n";
} finally {
    if (is_file(APP . 'Lib/ApplicationL10n.php')) unlink(APP . 'Lib/ApplicationL10n.php');
    rmdir(APP . 'Lib');
    rmdir($root);
}
