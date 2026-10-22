<section class="fi-language-ai" style="padding:12px 16px;border-top:1px solid #ddd">
    <h4><?php echo __('Translate PO file with AI'); ?></h4>
    <p><?php echo __('The application generates its POT file and shares it with AI to translate into the selected language.'); ?></p>
    <form id="language-ai-form" method="post" action="<?php echo h($this->Html->url(array('controller' => 'languages', 'action' => 'translation_start'))); ?>" data-step-url="<?php echo h($this->Html->url(array('controller' => 'languages', 'action' => 'translation_step'))); ?>">
        <input type="hidden" name="data[token]" value="<?php echo h($languageToken); ?>">
        <label for="language-ai-target"><?php echo __('Target language'); ?></label>
        <select class="form-control" id="language-ai-target" name="data[language_id]" required>
            <option value=""><?php echo __('Select an available language.'); ?></option>
            <?php foreach ((array)$aiTranslationLanguages as $id => $name): ?>
            <option value="<?php echo h($id); ?>" <?php echo isset($language['id']) && (string)$language['id'] === (string)$id ? 'selected' : ''; ?>><?php echo h($name); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="language-ai-share-pot" style="display:block;margin-top:10px">
            <input type="checkbox" id="language-ai-share-pot" name="data[share_pot]" value="1" required>
            <?php echo __('Share application POT file with AI'); ?>
        </label>
        <button class="btn btn-primary" type="submit" id="language-ai-send" style="margin-top:10px"><i class="fa fa-paperclip" aria-hidden="true"></i> <?php echo __('Translate and save'); ?></button>
        <button class="btn btn-default" type="button" id="language-ai-retry" style="display:none;margin-top:10px"><?php echo __('Retry'); ?></button>
    </form>
    <div class="progress" id="language-ai-progress" style="display:none;margin-top:12px">
        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width:100%">0%</div>
    </div>
    <p id="language-ai-status" role="status" aria-live="polite" style="white-space:pre-wrap;margin-top:8px"></p>
    <a id="language-ai-download" class="btn btn-default" style="display:none"><?php echo __('Save PO file'); ?></a>
</section>
<script>
(function () {
    var form = document.getElementById('language-ai-form');
    var send = document.getElementById('language-ai-send');
    var retry = document.getElementById('language-ai-retry');
    var target = document.getElementById('language-ai-target');
    var status = document.getElementById('language-ai-status');
    var progress = document.getElementById('language-ai-progress');
    var bar = progress.querySelector('.progress-bar');
    var download = document.getElementById('language-ai-download');
    var job = null, busy = false;
    var completed = Object.create(null);
    var extractingText = <?php echo json_encode(__('Generating the application POT file...')); ?>;
    var workingText = <?php echo json_encode(__('AI is translating the catalog. Keep this page open.')); ?>;
    var failedText = <?php echo json_encode(__('Translation request failed.')); ?>;
    var savedText = <?php echo json_encode(__('The translated PO file was saved.')); ?>;
    function fail(message, result) {
        busy = false; target.disabled = false; send.disabled = !!completed[target.value];
        retry.style.display = job ? 'inline-block' : 'none';
        bar.classList.remove('active');
        status.textContent = message;
        if (result && result.download) { download.href = result.download; download.style.display = 'inline'; }
    }
    function request(url, body, next) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            var result;
            try { result = JSON.parse(xhr.responseText); } catch (error) { fail(failedText); return; }
            if (xhr.status < 200 || xhr.status >= 300 || !result.success) { fail(result.message || failedText, result); return; }
            next(result.data);
        };
        xhr.onerror = function () { fail(failedText); };
        xhr.send(body);
    }
    function show(data) {
        bar.style.width = Math.max(2, data.percent) + '%';
        bar.setAttribute('aria-valuenow', data.percent);
        bar.textContent = data.percent + '%';
        status.textContent = workingText + '\n' + data.completed + ' / ' + data.total;
    }
    function step() {
        var body = new FormData();
        body.append('data[token]', form.querySelector('[name="data[token]"]').value);
        body.append('data[job]', job);
        request(form.getAttribute('data-step-url'), body, function (data) {
            show(data);
            if (data.done) {
                busy = false; target.disabled = false; completed[target.value] = data;
                send.disabled = true; send.classList.remove('btn-primary');
                send.classList.add('btn-default'); retry.style.display = 'none';
                bar.classList.remove('active'); bar.classList.add('progress-bar-success');
                status.textContent = savedText + '\n' + data.location;
                download.href = data.download; download.style.display = 'inline-block';
                download.classList.remove('btn-default'); download.classList.add('btn-success');
                download.focus();
            } else step();
        });
    }
    form.addEventListener('submit', function (event) {
        event.preventDefault(); if (busy || completed[target.value] || !form.reportValidity()) return;
        var body = new FormData(form);
        busy = true; job = null; send.disabled = true; target.disabled = true;
        retry.style.display = 'none'; download.style.display = 'none';
        progress.style.display = 'block';
        bar.classList.add('active'); bar.classList.remove('progress-bar-success');
        bar.style.width = '100%'; bar.textContent = extractingText; status.textContent = extractingText;
        request(form.action, body, function (data) { job = data.job; show(data); step(); });
    });
    target.addEventListener('change', function () {
        if (busy) return;
        var result = completed[target.value];
        send.disabled = !!result;
        send.classList.toggle('btn-primary', !result);
        send.classList.toggle('btn-default', !!result);
        job = null; retry.style.display = 'none';
        download.style.display = result ? 'inline-block' : 'none';
        progress.style.display = result ? 'block' : 'none';
        status.textContent = result ? savedText + '\n' + result.location : '';
        if (result) download.href = result.download;
    });
    retry.addEventListener('click', function () {
        if (!job || busy) return;
        busy = true; send.disabled = true; target.disabled = true; retry.style.display = 'none'; bar.classList.add('active'); step();
    });
    window.addEventListener('beforeunload', function (event) {
        if (busy) { event.preventDefault(); event.returnValue = workingText; }
    });
}());
</script>
