<?php
// Standalone integration tests. Uses disposable directories and a fake SQL executor; never touches a live DB.
require dirname(__DIR__, 3) . '/Lib/FlinkisoUpdater.php';
class FixtureUpdater extends FlinkisoUpdater {
    public $archive;
    protected function download($repo, $path) { if ($path) copy($this->archive, $path); }
}
function ensure($condition, $message) { if (!$condition) throw new RuntimeException($message); }
function putFixture($root, $rel, $content) { if (!is_dir(dirname($root . '/' . $rel))) mkdir(dirname($root . '/' . $rel), 0777, true); file_put_contents($root . '/' . $rel, $content); }
function removeFixture($path) { if (is_dir($path) && !is_link($path)) { foreach (scandir($path) as $name) if ($name !== '.' && $name !== '..') removeFixture($path . '/' . $name); rmdir($path); } else unlink($path); }
$base = sys_get_temp_dir() . '/flinkiso-test-' . bin2hex(random_bytes(8)); mkdir($base);
try {
    $parsed = FlinkisoUpdater::splitSql("-- comment;\nINSERT INTO t VALUES ('a;b', 'it''s'); /* x; */\nALTER TABLE t ADD x INT; # trailing");
    ensure(count($parsed) === 2, 'SQL splitting failed');
    foreach (array('../evil', '/absolute', 'a/../../evil', 'a\\evil', 'a/C:evil') as $path) ensure(!FlinkisoUpdater::safeArchivePath($path), 'Accepted traversal');
    ensure(FlinkisoUpdater::safeArchivePath('repo/app/Controller/Test.php'), 'Rejected safe path');
    ensure((new FlinkisoUpdater($base))->repository()['url'] === FlinkisoUpdater::DEFAULT_URL, 'Default fallback failed');
    try { (new FlinkisoUpdater($base, array('url' => 'bad')))->repository(); throw new LogicException('Accepted incomplete config'); } catch (RuntimeException $expected) {}
    $private = (new FlinkisoUpdater($base, array('url'=>'https://github.com/example/private/archive/refs/heads/main.zip', 'folder'=>'private-main', 'pat'=>'secret')))->repository();
    ensure(strpos($private['url'], 'https://api.github.com/repos/example/private/zipball/main') === 0, 'Private API normalization failed');
    foreach (array('success', 'sql-failure', 'bad-zip', 'repeat-denied', 'repeat-allowed', 'publish-failure', 'permission-failure', 'locked') as $scenario) {
        $root = $base . '/' . $scenario; mkdir($root);
        putFixture($root, 'app/Controller/Example.php', 'old');
        putFixture($root, 'app/Config/core.php', 'private config');
        putFixture($root, 'app/webroot/updates/old.sql', 'old SQL');
        if (strpos($scenario, 'repeat-') === 0) { mkdir($root . '/backup/' . date('Y-m-d'), 0777, true); }
        if ($scenario === 'repeat-allowed') mkdir($root . '/backup/' . date('Y-m-d') . '/' . date('H:i'));
        if ($scenario === 'locked') {
            mkdir($root . '/backup/.updater', 0777, true);
            $heldLock = fopen($root . '/backup/.updater/lock', 'c'); flock($heldLock, LOCK_EX);
        }
        if ($scenario === 'permission-failure') chmod($root . '/app/Controller/Example.php', 0444);
        $zipPath = $base . '/' . $scenario . '.zip';
        $zip = new ZipArchive(); $zip->open($zipPath, ZipArchive::CREATE);
        $prefix = 'FlinkISO-QMS-Updates-main/app/';
        $zip->addFromString($prefix . 'Controller/Example.php', 'new');
        $zip->addFromString($prefix . 'Controller/Z.php', 'new file');
        $zip->addFromString($prefix . 'Config/core.php', 'must not overwrite');
        $zip->addFromString($prefix . 'webroot/updates/updates.sql', "INSERT INTO t VALUES ('a;b');\nALTER TABLE t ADD x INT;");
        if ($scenario === 'bad-zip') $zip->addFromString('../evil.php', 'bad');
        $zip->close();
        $events = array(); $queries = array();
        $service = new FixtureUpdater($root, array(), function ($e) use (&$events) { $events[] = $e; }); $service->archive = $zipPath;
        $service->run($scenario === 'repeat-allowed', date('Y-m-d'), function ($sql) use (&$queries, $scenario, $root) {
            $queries[] = $sql;
            if ($scenario === 'sql-failure') throw new RuntimeException('test database failure');
            if ($scenario === 'publish-failure') putFixture($root, 'app/Controller/Z.php.flinkiso-new', 'conflict');
            return true;
        });
        if ($scenario === 'locked') { flock($heldLock, LOCK_UN); fclose($heldLock); }
        if ($scenario === 'permission-failure') chmod($root . '/app/Controller/Example.php', 0644);
        $last = end($events); $success = in_array($scenario, array('success', 'repeat-allowed'));
        ensure(($last['step'] === 'complete') === $success, $scenario . ': wrong outcome ' . json_encode($last));
        ensure(file_get_contents($root . '/app/Controller/Example.php') === ($success ? 'new' : 'old'), $scenario . ': live files changed incorrectly');
        ensure(file_get_contents($root . '/app/Config/core.php') === 'private config', 'Lost installation config');
        if ($scenario === 'sql-failure') ensure(count($queries) === 1 && file_exists($root . '/backup/.updater/needs-review'), 'SQL failure did not stop / persist recovery marker');
        if (in_array($scenario, array('bad-zip', 'repeat-denied', 'permission-failure', 'locked'))) ensure(count($queries) === 0, 'Ran SQL before validation');
        if ($success) ensure(!file_exists($root . '/app/webroot/updates/old.sql') && !file_exists($root . '/backup/.updater/needs-review'), 'Cleanup or recovery-marker failure');
        if ($scenario === 'publish-failure') ensure(file_get_contents($root . '/app/Controller/Z.php.flinkiso-new') === 'conflict', 'Removed an unowned temporary file');
        if ($scenario === 'permission-failure') ensure(strpos($last['message'], 'No write permission:') !== false, 'Missing explicit permission message');
        if ($scenario === 'repeat-allowed') ensure(count(glob($root . '/backup/' . date('Y-m-d') . '/*/.complete')) === 1, 'Repeat backup missing hh:mm subdirectory');
        echo "PASS $scenario\n";
    }
    echo "PASS SQL lexer, archive paths, repository configuration\n";
} finally { removeFixture($base); }
