(function (window, document) {
    'use strict';
    if (window.flinkisoSessionKeepAlive) return;
    var script = document.currentScript;
    if (!script) return;
    var url = script.getAttribute('data-keepalive-url');
    var token = script.getAttribute('data-keepalive-token');
    var storageKey = 'flinkiso-keepalive:' + script.getAttribute('data-keepalive-key');
    if (!url || !token) return;
    window.flinkisoSessionKeepAlive = true;

    var timer = null, request = null, stopped = false, paused = false, lastSuccess = 0;
    var interval = 60000;
    function schedule(delay) {
        window.clearTimeout(timer);
        if (!stopped && !paused) timer = window.setTimeout(ping, delay);
    }
    function recentSuccess() {
        var shared = 0;
        try { shared = Number(window.localStorage.getItem(storageKey)) || 0; } catch (error) {}
        return Math.max(lastSuccess, shared);
    }
    function ping() {
        if (stopped || paused || request) return;
        // Tabs share successful heartbeat times. Storage restrictions fall back
        // to one lightweight request per tab instead of disabling keep-alive.
        var age = Date.now() - recentSuccess();
        if (age >= 0 && age < 45000) { schedule(interval - age); return; }
        if (window.navigator.onLine === false) { schedule(interval); return; }
        var xhr = new window.XMLHttpRequest();
        request = xhr;
        xhr.open('GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-FlinkISO-KeepAlive', token);
        xhr.timeout = 15000;
        function finish() {
            if (request !== xhr) return;
            request = null;
            if (xhr.status === 401 || xhr.status === 403 || xhr.status === 404) {
                stopped = true;
                window.clearTimeout(timer);
                return;
            }
            if (xhr.status === 200) {
                try {
                    if (JSON.parse(xhr.responseText).active === true) {
                        lastSuccess = Date.now();
                        try { window.localStorage.setItem(storageKey, String(lastSuccess)); } catch (error) {}
                    }
                } catch (error) {}
            }
            schedule(interval);
        }
        xhr.onload = xhr.onerror = xhr.ontimeout = finish;
        xhr.send(null);
    }
    function wake() { if (!request) schedule(0); }
    window.addEventListener('focus', wake);
    window.addEventListener('online', wake);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) wake(); });
    window.addEventListener('pagehide', function () {
        paused = true;
        window.clearTimeout(timer);
        if (request) { var pending = request; request = null; pending.abort(); }
    });
    window.addEventListener('pageshow', function () { paused = false; wake(); });
    schedule(0);
}(window, document));
