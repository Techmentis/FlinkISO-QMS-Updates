<?php
App::uses('AppController', 'Controller');

/** A heartbeat must not run page rendering, history tracking or record locks. */
class SessionKeepAliveController extends AppController {
    public $uses = array();
    public $autoRender = false;

    public function beforeFilter() {}
    public function beforeRender() {}

    public function ping() {
        $this->response->type('json');
        $this->response->disableCache();
        $status = 200;
        if (!$this->Session->read('User.id')) {
            $status = 401;
        } else {
            $expected = $this->Session->read('KeepAlive.token');
            $provided = $this->request->header('X-FlinkISO-KeepAlive');
            if (!$this->request->is('get') || !$this->request->is('ajax')
                || !is_string($expected) || $expected === '' || !is_string($provided)
                || !hash_equals($expected, $provided)) {
                $status = 403;
            }
        }
        if ($status === 200) {
            // A real write also refreshes PHP's session-file lifetime.
            $this->Session->write('KeepAlive.last_seen', time());
            $cookie = session_get_cookie_params();
            if ($cookie['lifetime'] > 0 && session_id() !== '' && !headers_sent()) {
                $options = array(
                    'expires' => time() + $cookie['lifetime'],
                    'path' => $cookie['path'], 'domain' => $cookie['domain'],
                    'secure' => $cookie['secure'], 'httponly' => $cookie['httponly']
                );
                if (isset($cookie['samesite'])) $options['samesite'] = $cookie['samesite'];
                setcookie(session_name(), session_id(), $options);
            }
        }
        $this->response->statusCode($status);
        $this->response->body(json_encode(array('active' => $status === 200)));
        return $this->response;
    }
}
