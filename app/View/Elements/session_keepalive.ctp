<?php
if ($this->Session->read('User.id')) {
    $keepAliveToken = $this->Session->read('KeepAlive.token');
    if (!is_string($keepAliveToken) || $keepAliveToken === '') {
        $keepAliveToken = bin2hex(random_bytes(24));
        // SessionHelper can read, but cannot write session values.
        CakeSession::write('KeepAlive.token', $keepAliveToken);
    }
    $keepAliveScript = $this->webroot . 'js/session-keepalive.js';
    $keepAliveScript .= '?v=' . filemtime(WWW_ROOT . 'js' . DS . 'session-keepalive.js');
    $keepAliveUrl = Router::url(array('controller' => 'session_keep_alive', 'action' => 'ping'), true);
?>
<script src="<?php echo h($keepAliveScript); ?>"
    data-keepalive-url="<?php echo h($keepAliveUrl); ?>"
    data-keepalive-token="<?php echo h($keepAliveToken); ?>"
    data-keepalive-key="<?php echo h(hash('sha256', $keepAliveUrl . '|' . $keepAliveToken)); ?>" defer></script>
<?php } ?>
