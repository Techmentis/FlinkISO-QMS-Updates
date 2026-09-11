<?php
/** Fresh updater engine. No shell commands or request-supplied repository credentials. */
class FlinkisoUpdater {
    const DEFAULT_URL = 'https://github.com/Techmentis/FlinkISO-QMS-Updates/archive/refs/heads/main.zip';
    private $root;
    private $config;
    private $emit;
    private $log;
    private $work;

    public function __construct($root, $config = array(), $emit = null) {
        $this->root = rtrim($root, DIRECTORY_SEPARATOR);
        $this->config = $config ? $config : array();
        $this->emit = $emit;
    }

    public function repository() {
        $c = $this->config;
        if (!$c) {
            return array('url' => self::DEFAULT_URL, 'folder' => 'FlinkISO-QMS-Updates-main', 'pat' => '');
        }
        $missing = array();
        foreach (array('url', 'folder', 'pat') as $key) {
            if (!isset($c[$key]) || !is_string($c[$key]) || trim($c[$key]) === '') $missing[] = $key;
        }
        if ($missing) {
            throw new RuntimeException('Incomplete paid repository configuration. Missing or empty: ' . implode(', ', $missing) . '.');
        }
        $c['url'] = trim($c['url']);
        $c['folder'] = trim($c['folder']);
        $c['pat'] = trim($c['pat']);
        $this->validateUrl($c['url']);
        if (!preg_match('/^[A-Za-z0-9_-][A-Za-z0-9_.-]*$/D', $c['folder']) || strpos($c['folder'], '..') !== false || preg_match('/[\r\n]/', $c['pat'])) {
            throw new RuntimeException('Invalid repository folder or PAT configuration.');
        }
        // GitHub browser archive URLs do not reliably accept PATs; use its API.
        if (preg_match('~^https://github.com/([^/]+)/([^/]+)/archive/(?:refs/heads/)?(.+)\.zip$~', $c['url'], $m)) {
            $c['url'] = 'https://api.github.com/repos/' . $m[1] . '/' . $m[2] . '/zipball/' . rawurlencode($m[3]);
            $c['apiArchive'] = true;
        }
        return $c;
    }

    private function validateUrl($url) {
        $u = parse_url($url);
        if (!$u || empty($u['scheme']) || $u['scheme'] !== 'https' || empty($u['host']) ||
            !in_array(strtolower($u['host']), array('github.com', 'api.github.com', 'codeload.github.com'), true) ||
            isset($u['user']) || isset($u['pass']) || (isset($u['port']) && $u['port'] != 443)) {
            throw new RuntimeException('Update downloads and redirects must use HTTPS on GitHub.');
        }
    }

    public function check() {
        $this->repository();
        return array('date' => date('Y-m-d'), 'exists' => is_dir($this->root . '/backup/' . date('Y-m-d')));
    }

    private function event($step, $percent, $message, $error = false, $warning = false) {
        if (!empty($this->config['pat'])) $message = str_replace($this->config['pat'], '[REDACTED]', $message);
        $row = array('step' => $step, 'percent' => $percent, 'message' => $message, 'error' => $error, 'warning' => $warning);
        if ($this->log && file_put_contents($this->log, json_encode($row) . "\n", FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException('Cannot write updater log: ' . $this->log);
        }
        if ($this->emit) call_user_func($this->emit, $row);
    }

    private function mkdirChecked($path) {
        if (is_link($path)) throw new RuntimeException('Symbolic link is not allowed: ' . $path);
        if (!is_dir($path) && !mkdir($path, 0700, true)) throw new RuntimeException('Cannot create directory: ' . $path);
    }

    private function writableErrors($path, &$errors) {
        if (is_link($path)) { $errors[] = 'Symbolic link is not allowed: ' . $path; return; }
        if (file_exists($path)) {
            if (!is_writable($path)) $errors[] = 'No write permission: ' . $path;
            if (is_dir($path) && !is_executable($path)) $errors[] = 'No directory traversal permission: ' . $path;
        } else {
            $parent = dirname($path);
            if ($parent !== $path) $this->writableErrors($parent, $errors);
        }
    }

    private function failErrors($errors) {
        if ($errors) throw new RuntimeException(implode("\n", array_unique($errors)));
    }

    /** Walk without following links; collect every inaccessible path before mutating live files. */
    private function inventory($dir, $prefix = '', $exclude = array(), &$errors = array()) {
        $files = array();
        if (!is_readable($dir) || !is_executable($dir)) { $errors[] = 'Cannot read/traverse: ' . $dir; return $files; }
        $entries = scandir($dir);
        if ($entries === false) { $errors[] = 'Cannot list: ' . $dir; return $files; }
        foreach ($entries as $name) {
            if ($name === '.' || $name === '..') continue;
            $rel = $prefix . $name;
            if (in_array($rel, $exclude, true)) continue;
            $path = $dir . '/' . $name;
            if (is_link($path)) { $errors[] = 'Symbolic link is not allowed: ' . $path; continue; }
            if (is_dir($path)) $files += $this->inventory($path, $rel . '/', $exclude, $errors);
            elseif (!is_file($path) || !is_readable($path)) $errors[] = 'Cannot read file: ' . $path;
            else $files[$rel] = $path;
        }
        return $files;
    }

    private function copyChecked($from, $to) {
        $this->mkdirChecked(dirname($to));
        if (!copy($from, $to) || hash_file('sha256', $from) !== hash_file('sha256', $to)) {
            throw new RuntimeException('Copy verification failed: ' . $from . ' -> ' . $to);
        }
    }

    public function run($repeatBackup, $confirmedDate, $executeSql) {
        $lock = null;
        $step = 'connect';
        try {
            $repo = $this->repository();
            if (!extension_loaded('curl') || !class_exists('ZipArchive')) throw new RuntimeException('PHP cURL and ZipArchive extensions are required.');
            $errors = array();
            $this->writableErrors($this->root . '/backup', $errors);
            $this->writableErrors($this->root . '/app/webroot/updates', $errors);
            $this->failErrors($errors);
            $this->mkdirChecked($this->root . '/backup');
            $this->mkdirChecked($this->root . '/backup/.updater');
            $lock = fopen($this->root . '/backup/.updater/lock', 'c');
            if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) throw new RuntimeException('Another update is running.');
            $recoveredSqlFailure = $this->recoverSqlFailure();
            if (file_exists($this->root . '/backup/.updater/needs-review')) throw new RuntimeException('A previous installation was interrupted or failed during SQL/publication. Review backup/.updater/needs-review and the run log before removing the marker to retry.');
            $this->work = $this->root . '/backup/.updater/' . date('Ymd-His') . '-' . bin2hex(openssl_random_pseudo_bytes(8));
            $this->mkdirChecked($this->work);
            $this->log = $this->work . '/run.jsonl';
            $fatalLog = $this->log;
            register_shutdown_function(function () use ($fatalLog) {
                $error = error_get_last();
                if ($error && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
                    file_put_contents($fatalLog, json_encode(array('error' => true, 'message' => 'PHP terminated unexpectedly. Review the PHP error log and recovery marker before retrying.')) . "\n", FILE_APPEND);
                }
            });
            if ($recoveredSqlFailure) $this->event($step, 0, 'Previous SQL-only failure acknowledged; its recovery marker was archived. Retrying with SQL errors treated as warnings.', false, true);
            $this->event($step, 0, 'Connecting to GitHub...');
            $this->download($repo, null);
            $this->event($step, 100, 'GitHub connection verified. Log: ' . $this->log);

            $step = 'backup';
            $this->event($step, 0, 'Checking backup permissions and reading the application...');
            $today = date('Y-m-d');
            $backup = $this->root . '/backup/' . $today;
            $reuseBackup = false;
            if (is_dir($backup)) {
                if (!$repeatBackup) {
                    if ($confirmedDate !== $today) throw new RuntimeException('The backup choice expired. Reload and try again.');
                    $reuseBackup = $this->findCompletedBackup($backup);
                    if (!$reuseBackup) throw new RuntimeException('Today has no completed backup to reuse. Choose another backup.');
                    $backup = $reuseBackup;
                } else {
                    $backup .= '/' . date(DIRECTORY_SEPARATOR === '\\' ? 'H-i' : 'H:i');
                    if (file_exists($backup)) $backup .= '-' . date('s') . '-' . bin2hex(openssl_random_pseudo_bytes(3));
                }
            }
            if ($reuseBackup) {
                $this->event($step, 100, 'Reusing completed backup: ' . $backup);
            } else {
                $errors = array();
                $files = $this->inventory($this->root, '', array('backup', '.git', 'app/webroot/updates', 'app/tmp'), $errors);
                foreach ($files as $rel => $path) $this->writableErrors($backup . '/' . $rel, $errors);
                $this->failErrors($errors);
                $this->mkdirChecked($backup);
                $count = count($files); $i = 0;
                foreach ($files as $rel => $path) {
                    $this->copyChecked($path, $backup . '/' . $rel);
                    if (++$i % 100 === 0) $this->event($step, (int)($i * 99 / max(1, $count)), 'Backed up ' . $i . ' / ' . $count . ' files.');
                }
                if (file_put_contents($backup . '/.complete', date('c')) === false) throw new RuntimeException('Cannot record completed backup: ' . $backup);
                $this->event($step, 100, 'Backup complete: ' . $backup);
            }

            $step = 'download';
            $updates = $this->root . '/app/webroot/updates';
            $this->event($step, 0, 'Checking update directory and removing previous downloads...');
            $this->mkdirChecked($updates);
            $errors = array();
            $old = $this->inventory($updates, '', array(), $errors);
            foreach ($old as $path) { $this->writableErrors($path, $errors); $this->writableErrors(dirname($path), $errors); }
            $this->failErrors($errors);
            // Deny HTTP access to SQL, archives and extracted PHP on Apache/IIS.
            if (file_put_contents($updates . '/.htaccess', "Require all denied\n") === false ||
                file_put_contents($updates . '/web.config', '<configuration><system.webServer><security><authorization><remove users="*" roles="" verbs=""/><add accessType="Deny" users="*"/></authorization></security></system.webServer></configuration>') === false) {
                throw new RuntimeException('Cannot protect updates directory.');
            }
            foreach ($old as $rel => $path) {
                if ($rel !== '.htaccess' && $rel !== 'web.config' && !unlink($path)) throw new RuntimeException('Cannot remove previous download: ' . $path);
            }
            $archive = $updates . '/update.zip';
            $this->download($repo, $archive);
            $this->event($step, 100, 'Latest archive downloaded.');

            $step = 'extract';
            $this->event($step, 0, 'Validating archive paths and extracting...');
            $stage = $updates . '/stage-' . basename($this->work);
            $this->extract($archive, $stage, $repo);
            $source = $stage . '/' . $repo['folder'] . '/app';
            if (!is_dir($source)) throw new RuntimeException('Archive does not contain the configured folder/app directory.');
            $this->event($step, 100, 'Archive extracted and validated.');

            $step = 'validate';
            $this->event($step, 0, 'Checking every destination before installing...');
            $errors = array();
            // Preserve installation-specific settings and data. Updater code is intentionally
            // installable so fixes in the update repository reach existing installations.
            $skip = array('Config/core.php', 'Config/database.php', 'Config/installed.txt', 'tmp', 'webroot/files', 'webroot/updates');
            $incoming = $this->inventory($source, '', $skip, $errors);
            if (!$incoming) $errors[] = 'Archive contains no application update files.';
            $checkedDirectories = array();
            foreach ($incoming as $rel => $path) {
                $dest = $this->root . '/app/' . $rel;
                $parent = dirname($dest);
                while (!is_dir($parent) && strlen($parent) >= strlen($this->root . '/app')) $parent = dirname($parent);
                if (!isset($checkedDirectories[$parent])) {
                    $this->writableErrors($parent, $errors);
                    $checkedDirectories[$parent] = true;
                }
                if (is_dir($dest)) $errors[] = 'File/directory conflict: ' . $dest;
                if (file_exists($dest . '.flinkiso-new') || is_link($dest . '.flinkiso-new')) $errors[] = 'Unexpected temporary file: ' . $dest . '.flinkiso-new';
            }
            $sqlPath = $source . '/webroot/updates/updates.sql';
            $this->failErrors($errors);
            $statements = array();
            $sqlMissing = !is_readable($sqlPath);
            foreach ($sqlMissing ? array() : self::splitSql(file_get_contents($sqlPath)) as $sql) {
                $statements = array_merge($statements, self::splitColumnAdditions($sql));
            }
            // Prepare all source files and rollback copies outside the application first.
            foreach ($incoming as $rel => $path) {
                $this->copyChecked($path, $this->work . '/new/' . $rel);
                if (is_file($this->root . '/app/' . $rel)) $this->copyChecked($this->root . '/app/' . $rel, $this->work . '/old/' . $rel);
            }
            if (file_put_contents($this->work . '/manifest.json', json_encode(array_keys($incoming))) === false) throw new RuntimeException('Cannot write recovery manifest.');
            $this->event($step, 100, 'All file permissions checked; rollback copies prepared.');

            $step = 'sql';
            $this->event($step, 0, 'Applying SQL before publishing application files.');
            $marker = $this->root . '/backup/.updater/needs-review';
            if (file_put_contents($marker, 'Run: ' . $this->work . "\nBackup: " . $backup . "\nSQL may be partially committed. Inspect run.jsonl before retrying.\n") === false) throw new RuntimeException('Cannot create recovery marker.');
            $sqlWarnings = $sqlMissing ? 1 : 0;
            if ($sqlMissing) $this->event($step, 100, 'Warning: updates.sql is absent; continuing with application files.', false, true);
            foreach ($statements as $index => $statement) {
                try {
                    if (call_user_func($executeSql, $statement) === false) throw new RuntimeException('Database rejected statement.');
                } catch (Throwable $e) {
                    $sqlWarnings++;
                    $this->event($step, (int)(($index + 1) * 100 / max(1, count($statements))), 'Warning: SQL statement ' . ($index + 1) . ' skipped: ' . $e->getMessage(), false, true);
                    continue;
                }
                $this->event($step, (int)(($index + 1) * 100 / max(1, count($statements))), 'SQL statement ' . ($index + 1) . ' / ' . count($statements) . ' completed.');
            }
            $this->event($step, 100, $sqlWarnings ? 'SQL finished with ' . $sqlWarnings . ' skipped statement(s). Review the warnings for unapplied database changes.' : 'SQL completed successfully.', false, $sqlWarnings > 0);
            $step = 'install';
            $this->event($step, 0, 'Publishing application files...');
            $this->publish($incoming);
            if (!unlink($marker)) throw new RuntimeException('Installed, but cannot clear recovery marker: ' . $marker);
            $this->event($step, 100, 'Application files installed.');
            $this->event('complete', 100, ($sqlWarnings ? 'Update installed with ' . $sqlWarnings . ' SQL warning(s). Review skipped database changes. Backup: ' : 'Update completed successfully. Backup: ') . $backup, false, $sqlWarnings > 0);
        } catch (Throwable $e) {
            $message = $e->getMessage();
            if (!empty($this->config['pat'])) $message = str_replace($this->config['pat'], '[REDACTED]', $message);
            try { $this->event($step, 0, $message, true); }
            catch (Throwable $loggingError) {
                if ($this->emit) call_user_func($this->emit, array('step' => $step, 'percent' => 0, 'message' => $message . '\nCannot write the updater log.', 'error' => true));
            }
        } finally {
            if (is_resource($lock)) { flock($lock, LOCK_UN); fclose($lock); }
        }
    }

    /** Do not unblock interrupted runs or publication failures automatically. */
    private function recoverSqlFailure() {
        $base = $this->root . '/backup/.updater';
        $marker = $base . '/needs-review';
        if (!is_file($marker) || is_link($marker)) return false;
        if (!preg_match('/^Run: (.+)$/m', file_get_contents($marker), $match)) return false;
        $run = realpath(trim($match[1]));
        if (!$run || dirname($run) !== realpath($base)) return false;
        $log = $run . '/run.jsonl';
        if (!is_readable($log) || is_link($log)) return false;
        $last = null;
        foreach (file($log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $row = json_decode($line, true);
            if (!is_array($row) || !isset($row['step']) || $row['step'] === 'install' || $row['step'] === 'complete') return false;
            $last = $row;
        }
        if (!$last || $last['step'] !== 'sql' || empty($last['error'])) return false;
        $archive = $run . '/needs-review-acknowledged-' . bin2hex(openssl_random_pseudo_bytes(8));
        if (!rename($marker, $archive)) throw new RuntimeException('Cannot archive the previous SQL failure marker.');
        return true;
    }

    private function findCompletedBackup($directory) {
        if (is_file($directory . '/.complete')) return $directory;
        $matches = glob($directory . '/*/.complete');
        if (!$matches) return false;
        usort($matches, function ($a, $b) { return filemtime($b) - filemtime($a); });
        return dirname($matches[0]);
    }

    protected function download($repo, $path) {
        $url = $repo['url'];
        for ($redirect = 0; $redirect < 6; $redirect++) {
            $this->validateUrl($url);
            $location = null;
            $file = $path ? fopen($path, 'wb') : null;
            if ($path && !$file) throw new RuntimeException('Cannot write archive: ' . $path);
            $ch = curl_init($url);
            $headers = array('Accept: application/vnd.github+json');
            // Never forward the PAT to a signed codeload redirect.
            if (!empty($repo['pat']) && parse_url($url, PHP_URL_HOST) === 'api.github.com') $headers[] = 'Authorization: Bearer ' . $repo['pat'];
            curl_setopt_array($ch, array(CURLOPT_FOLLOWLOCATION => false, CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_TIMEOUT => 900, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_USERAGENT => 'FlinkISO-Updater', CURLOPT_HTTPHEADER => $headers,
                CURLOPT_NOBODY => !$path, CURLOPT_RETURNTRANSFER => !$path,
                CURLOPT_HEADERFUNCTION => function ($handle, $line) use (&$location) {
                    if (stripos($line, 'Location:') === 0) $location = trim(substr($line, 9));
                    return strlen($line);
                }));
            if ($file) curl_setopt($ch, CURLOPT_FILE, $file);
            $ok = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $errno = curl_errno($ch);
            curl_close($ch); if ($file) fclose($file);
            if ($ok === false) throw new RuntimeException('GitHub transfer failed (cURL ' . $errno . '). Check network/TLS and disk write permissions.');
            if ($code >= 300 && $code < 400 && $location) { $url = $location; continue; }
            if ($code !== 200) throw new RuntimeException('GitHub returned HTTP ' . $code . '. Check repository URL and PAT access.');
            if ($path && filesize($path) === 0) throw new RuntimeException('GitHub returned an empty archive.');
            return;
        }
        throw new RuntimeException('Too many GitHub redirects.');
    }

    private function extract($archive, $stage, $repo) {
        $zip = new ZipArchive();
        if ($zip->open($archive, ZipArchive::CHECKCONS) !== true) throw new RuntimeException('Invalid or corrupt ZIP archive.');
        $root = null; $size = 0; $seen = array(); $links = array(); $names = array();
        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i); $name = $stat['name'];
                if (!self::safeArchivePath($name)) throw new RuntimeException('Unsafe ZIP entry: ' . $name);
                $key = strtolower(rtrim($name, '/'));
                if (isset($seen[$key])) throw new RuntimeException('Duplicate ZIP entry: ' . $name);
                $seen[$key] = true; $names[$name] = $i;
                $parts = explode('/', $name);
                if ($root === null) $root = $parts[0];
                if ($root !== $parts[0]) throw new RuntimeException('Archive must contain one repository root.');
                $opsys = 0; $attr = 0;
                if ($zip->getExternalAttributesIndex($i, $opsys, $attr) && (($attr >> 16) & 0170000) === 0120000) {
                    $target = $zip->getFromIndex($i);
                    if (!is_string($target) || $target === '' || preg_match('~(^/|\\|:|[\x00-\x1f])~', $target)) throw new RuntimeException('Unsafe ZIP symbolic link: ' . $name);
                    $links[$name] = $target;
                } else {
                    $size += $stat['size'];
                }
                if ($size > 2147483648 || $zip->numFiles > 100000) throw new RuntimeException('Archive exceeds the 2 GB / 100,000 entry limit.');
            }
            if (empty($repo['apiArchive']) && strpos($repo['url'], 'api.github.com/') === false && $root !== $repo['folder']) throw new RuntimeException('Repository folder does not match configuration.');
            // Resolve every link before writing anything. Only links to regular files inside this ZIP are allowed.
            $resolvedLinks = array();
            foreach ($links as $name => $unused) {
                $resolved = $this->resolveZipLink($name, $links, $names);
                if ($resolved !== null) $resolvedLinks[$name] = $resolved;
            }
            $this->mkdirChecked($stage);
            if (disk_free_space($stage) < $size * 3) throw new RuntimeException('Insufficient free space for extraction and rollback staging.');
            // Extract manually so ZipArchive never creates operating-system symlinks.
            foreach ($names as $name => $index) {
                $output = $stage . '/' . $name;
                if (substr($name, -1) === '/') { $this->mkdirChecked(rtrim($output, '/')); continue; }
                $this->mkdirChecked(dirname($output));
                $sourceIndex = isset($resolvedLinks[$name]) ? $names[$resolvedLinks[$name]] : $index;
                $input = $zip->getStream($zip->getNameIndex($sourceIndex));
                $destination = fopen($output, 'wb');
                if (!$input || !$destination) throw new RuntimeException('Cannot extract ZIP entry: ' . $name);
                $copied = stream_copy_to_stream($input, $destination);
                fclose($input); fclose($destination);
                if ($copied === false) throw new RuntimeException('Cannot extract ZIP entry: ' . $name);
            }
            if ($root !== $repo['folder'] && !rename($stage . '/' . $root, $stage . '/' . $repo['folder'])) throw new RuntimeException('Cannot normalize GitHub archive folder.');
        } finally { $zip->close(); }
    }

    private function resolveZipLink($name, $links, $names, $trail = array()) {
        if (isset($trail[$name])) return null;
        $trail[$name] = true;
        $parts = explode('/', dirname($name) . '/' . $links[$name]); $normal = array();
        foreach ($parts as $part) {
            if ($part === '' || $part === '.') continue;
            if ($part === '..') {
                if (!$normal) return null;
                array_pop($normal);
            } else $normal[] = $part;
        }
        $target = implode('/', $normal);
        if (!isset($names[$target]) || substr($target, -1) === '/') return null;
        return isset($links[$target]) ? $this->resolveZipLink($target, $links, $names, $trail) : $target;
    }

    public static function safeArchivePath($name) {
        return $name !== '' && !preg_match('~(^/|\\\\|:|[\x00-\x1f]|(?:^|/)\.{1,2}(?:/|$)|//)~', $name);
    }

    private function publish($incoming) {
        $written = array(); $dirs = array();
        try {
            foreach ($incoming as $rel => $unused) {
                $dest = $this->root . '/app/' . $rel;
                $dir = dirname($dest); $missing = array();
                while (!is_dir($dir)) { $missing[] = $dir; $dir = dirname($dir); }
                foreach (array_reverse($missing) as $dir) { if (!mkdir($dir, 0755)) throw new RuntimeException('Cannot create: ' . $dir); $dirs[] = $dir; }
                $ownedTemporary = false;
                $temporary = $dest . '.flinkiso-new';
                if (file_exists($temporary) || is_link($temporary)) throw new RuntimeException('Unexpected temporary file: ' . $temporary);
                $ownedTemporary = true;
                $this->copyChecked($this->work . '/new/' . $rel, $temporary);
                $mode = is_file($dest) ? (fileperms($dest) & 0777) : 0644;
                if (!chmod($temporary, $mode) || !rename($temporary, $dest)) { unlink($temporary); throw new RuntimeException('Cannot publish: ' . $dest); }
                $ownedTemporary = false;
                $written[] = $rel;
                if (function_exists('opcache_invalidate')) opcache_invalidate($dest, true);
            }
        } catch (Throwable $e) {
            $errors = array($e->getMessage());
            if (!empty($ownedTemporary) && isset($temporary) && is_file($temporary)) {
                try { if (!unlink($temporary)) $errors[] = 'Cannot remove temporary file: ' . $temporary; }
                catch (Throwable $cleanupError) { $errors[] = $cleanupError->getMessage(); }
            }
            foreach (array_reverse($written) as $rel) {
                $dest = $this->root . '/app/' . $rel; $old = $this->work . '/old/' . $rel;
                try {
                    if (is_file($old)) {
                        if (!copy($old, $dest) || hash_file('sha256', $old) !== hash_file('sha256', $dest)) $errors[] = 'ROLLBACK FAILED: ' . $dest;
                    } elseif (!unlink($dest)) $errors[] = 'ROLLBACK FAILED: ' . $dest;
                } catch (Throwable $rollbackError) { $errors[] = 'ROLLBACK FAILED: ' . $dest . ': ' . $rollbackError->getMessage(); }
                if (function_exists('opcache_invalidate')) opcache_invalidate($dest, true);
            }
            foreach (array_reverse($dirs) as $dir) {
                try { if (!rmdir($dir)) $errors[] = 'Cannot remove new directory: ' . $dir; }
                catch (Throwable $rollbackError) { $errors[] = $rollbackError->getMessage(); }
            }
            throw new RuntimeException(implode("\n", $errors) . "\nFile rollback attempted. SQL has already run; review recovery log.");
        }
    }

    /** Split pure ADD-column lists so one existing column cannot hide other missing columns. */
    public static function splitColumnAdditions($sql) {
        $identifier = '(?:`(?:[^`]|``)+`|[A-Za-z_][A-Za-z0-9_$]*)';
        if (!preg_match('/^(ALTER\s+TABLE\s+' . $identifier . '(?:\s*\.\s*' . $identifier . ')?\s+)(.*)$/is', $sql, $match)) return array($sql);
        $parts = array(); $part = ''; $quote = null; $depth = 0; $body = $match[2]; $n = strlen($body);
        for ($i = 0; $i < $n; $i++) {
            $c = $body[$i]; $next = $i + 1 < $n ? $body[$i + 1] : '';
            if ($quote !== null) {
                $part .= $c;
                if ($c === '\\' && $next !== '') { $part .= $next; $i++; }
                elseif ($c === $quote) {
                    if ($next === $quote) { $part .= $next; $i++; } else $quote = null;
                }
                continue;
            }
            if ($c === "'" || $c === '"' || $c === '`') $quote = $c;
            elseif ($c === '(') $depth++;
            elseif ($c === ')') $depth--;
            elseif ($c === ',' && $depth === 0) { $parts[] = trim($part); $part = ''; continue; }
            $part .= $c;
        }
        $parts[] = trim($part);
        if (count($parts) < 2) return array($sql);
        foreach ($parts as $part) {
            if (!preg_match('/^ADD\s+(?:COLUMN\s+)?(?!(?:PRIMARY|UNIQUE|INDEX|KEY|CONSTRAINT|FOREIGN|CHECK|PARTITION)\b)' . $identifier . '\s+/i', $part)) return array($sql);
        }
        $out = array();
        foreach ($parts as $part) $out[] = $match[1] . $part;
        return $out;
    }

    /** SQL lexer: semicolons inside strings/comments do not terminate statements. */
    public static function splitSql($sql) {
        if (preg_match('/^\s*DELIMITER\b/im', $sql)) throw new RuntimeException('DELIMITER scripts are not supported. Supply ordinary SQL statements.');
        $out = array(); $buffer = ''; $quote = null; $comment = null; $n = strlen($sql);
        for ($i = 0; $i < $n; $i++) {
            $c = $sql[$i]; $next = $i + 1 < $n ? $sql[$i + 1] : '';
            if ($comment === 'line') { if ($c === "\n") { $comment = null; $buffer .= "\n"; } continue; }
            if ($comment === 'block') { if ($c === '*' && $next === '/') { $comment = null; $i++; $buffer .= ' '; } continue; }
            if ($quote !== null) {
                $buffer .= $c;
                if ($c === '\\' && $next !== '') { $buffer .= $next; $i++; }
                elseif ($c === $quote) { if ($next === $quote) { $buffer .= $next; $i++; } else $quote = null; }
                continue;
            }
            if ($c === "'" || $c === '"' || $c === '`') { $quote = $c; $buffer .= $c; }
            elseif ($c === '#' || ($c === '-' && $next === '-' && ($i + 2 === $n || ctype_space($sql[$i + 2])))) $comment = 'line';
            elseif ($c === '/' && $next === '*') {
                if ($i + 2 < $n && $sql[$i + 2] === '!') throw new RuntimeException('Executable SQL comments are not supported.');
                $comment = 'block'; $i++;
            } elseif ($c === ';') { if (trim($buffer) !== '') $out[] = trim($buffer); $buffer = ''; }
            else $buffer .= $c;
        }
        if ($quote !== null || $comment === 'block') throw new RuntimeException('Unterminated SQL quote or comment.');
        if (trim($buffer) !== '') $out[] = trim($buffer);
        return $out;
    }
}
