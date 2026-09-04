<?php echo $this->element('billing_header_lists', array('header' => 'Update')); ?>
<style>
@keyframes updater-stripes { from { background-position: 0 0; } to { background-position: 40px 0; } }
@keyframes updater-spin { to { transform: rotate(360deg); } }
#updater-steps .updater-running {
    background-image: linear-gradient(45deg, rgba(255,255,255,.25) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.25) 50%, rgba(255,255,255,.25) 75%, transparent 75%, transparent);
    background-size: 40px 40px;
    animation: updater-stripes 1s linear infinite;
}
#updater-working { margin-top: 15px; font-weight: 600; }
.updater-spinner { display: inline-block; width: 18px; height: 18px; margin-right: 8px; vertical-align: middle; border: 3px solid #cbd5e1; border-top-color: #337ab7; border-radius: 50%; animation: updater-spin .8s linear infinite; }
@media (prefers-reduced-motion: reduce) {
    #updater-steps .updater-running, .updater-spinner { animation: none; }
}
</style>
<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">FlinkISO Updater</h3></div>
    <div class="panel-body">
        <div class="col-md-6">
        <p>Download and install the latest application updates from your configured GitHub repository.</p>
        <p>A dated application backup is created first. Configuration and uploaded files are preserved. Please run during a maintenance window with other users signed out.</p>
        <p class="text-muted">SQL runs before files are published. Failed SQL statements are logged and skipped; installation continues. Review the warnings for database changes that were not applied.</p>
        <button type="button" class="btn btn-success" id="updater-start">Back up and update</button>
        <div id="updater-working" style="display:none" role="status"><span class="updater-spinner" aria-hidden="true"></span>Updater is working. Please keep this page open.</div>
        <div id="updater-status" role="status" aria-live="polite" style="margin:15px 0"></div>
        <div id="updater-steps">
        <?php foreach (array('connect' => 'Connect to GitHub', 'backup' => 'Back up application', 'download' => 'Download latest archive', 'extract' => 'Unzip archive', 'validate' => 'Validate files and permissions', 'sql' => 'Run SQL', 'install' => 'Install application files') as $key => $label): ?>
            <div style="margin-top:12px"><strong><?php echo h($label); ?></strong>
                <div class="progress" style="height:18px;margin-bottom:6px">
                    <div id="updater-<?php echo h($key); ?>" class="progress-bar" role="progressbar" aria-label="<?php echo h($label); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width:0%">0%</div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        </div>
        <div class="col-md-6">
        <h4>Update log</h4>
        <pre id="updater-log" style="max-height:360px;overflow:auto;white-space:pre-wrap" aria-label="Update log"></pre>
        </div>
    </div>
</div>
<script>
(function () {
    var url = <?php echo json_encode($this->Html->url(array('controller' => 'billing', 'action' => 'update'))); ?>;
    var token = <?php echo json_encode($updaterToken); ?>;
    var button = document.getElementById('updater-start');
    var status = document.getElementById('updater-status');
    var log = document.getElementById('updater-log');
    function working(active) {
        document.getElementById('updater-working').style.display = active ? 'block' : 'none';
        document.getElementById('updater-steps').setAttribute('aria-busy', active ? 'true' : 'false');
        if (!active) $('#updater-steps .progress-bar').removeClass('updater-running active');
    }
    function message(text, failed) {
        status.textContent = text;
        status.className = failed ? 'alert alert-danger' : 'alert alert-info';
    }
    function row(item) {
        log.textContent += item.message + '\n'; log.scrollTop = log.scrollHeight;
        var bar = document.getElementById('updater-' + item.step);
        if (bar) {
            bar.style.width = (item.percent === 0 && !item.error ? 100 : item.percent) + '%';
            bar.setAttribute('aria-valuenow', item.percent);
            bar.textContent = item.error ? 'Failed' : item.percent === 0 ? 'Working…' : item.percent + '%';
            bar.className = 'progress-bar' + (item.error ? ' progress-bar-danger' : item.percent < 100 ? ' progress-bar-striped active updater-running' : ' progress-bar-success');
            if (item.error) bar.style.width = '100%';
        }
        message(item.message, item.error);
        if (item.error || item.step === 'complete') working(false);
        if (item.warning) status.className = 'alert alert-warning';
        if (item.step === 'complete') status.className = item.warning ? 'alert alert-warning' : 'alert alert-success';
    }
    function run(repeat, date) {
        var xhr = new XMLHttpRequest(), offset = 0, pending = '', terminal = false;
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        function consume() {
            pending += xhr.responseText.substring(offset); offset = xhr.responseText.length;
            var lines = pending.split('\n'); pending = lines.pop();
            lines.forEach(function (line) {
                if (!line.trim()) return;
                try { var item = JSON.parse(line); row(item); if (item.error || item.step === 'complete') terminal = true; }
                catch (e) { message('Unexpected server response. Review the server log and backup/.updater before retrying.', true); }
            });
        }
        xhr.onprogress = consume;
        xhr.onload = function () {
            consume();
            if (!terminal) working(false);
            if (!terminal) message('Connection ended without a completion result. The server may still be running. Review backup/.updater logs before retrying.', true);
            if (terminal) button.disabled = false;
        };
        xhr.onerror = function () { working(false); message('Connection lost. The updater may still be running. Review backup/.updater logs before retrying.', true); };
        xhr.send('operation=run&token=' + encodeURIComponent(token) + '&repeat=' + (repeat ? '1' : '0') + '&date=' + encodeURIComponent(date));
    }
    button.addEventListener('click', function () {
        button.disabled = true; log.textContent = ''; working(true);
        $('#updater-steps .progress-bar').removeClass('progress-bar-success progress-bar-danger updater-running active').css('width', '0%').attr('aria-valuenow', '0').text('0%');
        message('Checking today’s backup...', false);
        $.ajax({url: url, method: 'POST', dataType: 'json', data: {operation: 'check', token: token}})
            .done(function (data) {
                if (data.error) { working(false); message(data.error, true); button.disabled = false; return; }
                if (data.exists && !window.confirm('A backup already exists for ' + data.date + '. Back up again in an hh:mm folder? Cancel stops the update.')) {
                    working(false); message('Update cancelled. No files changed.', false); button.disabled = false; return;
                }
                run(data.exists, data.date);
            }).fail(function () { working(false); message('Could not check the backup. Reload the page and verify your login.', true); button.disabled = false; });
    });
}());
</script>
