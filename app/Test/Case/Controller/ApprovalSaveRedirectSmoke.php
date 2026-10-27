<?php
// Standalone routing/permission regression test. No database or application writes.
class App {
    public static function uses($name, $package) {}
    public static function import($name) {}
}
class Controller {}
require (isset($argv[1]) ? $argv[1] : dirname(__DIR__, 4)) . '/app/Controller/AppController.php';
class ApprovalRedirectSession {
    public $admin = false;
    public function read($key) {
        if ($key === 'User.id') return 'approver-user';
        if ($key === 'User.is_mr') return $this->admin;
        return false;
    }
}
class ApprovalRedirectTable {
    public $viewers = '[]';
    public $missing = false;
    public function find($type, $options) {
        if ($this->missing) return array();
        return array('CustomTable' => array('id'=>'table-1', 'creators'=>'[]', 'editors'=>'["approver-user"]', 'viewers'=>$this->viewers, 'approvers'=>'["approver-user"]'));
    }
}
class ApprovalRedirectFixture extends AppController {
    public $denied = false;
    public $superUser = false;
    public function loadModel($name) {}
    protected function _is_super_user() { return $this->superUser; }
    public function _access_redirect($n = null) { $this->denied = true; }
    public function target() { return $this->_custom_record_save_redirect(); }
}
function checkRedirect($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
$c = new ApprovalRedirectFixture;
$c->Session = new ApprovalRedirectSession;
$c->CustomTable = new ApprovalRedirectTable;
$c->action = 'edit';
$c->request = (object)array('action'=>'edit', 'params'=>array('named'=>array(
    'approval_id'=>'approval-1', 'qc_document_id'=>'document-1', 'custom_table_id'=>'table-1', 'process_id'=>'process-1'
)));
$c->_customtableacces();
checkRedirect(!$c->denied, 'Editor should retain edit access.');
$c->request->action = 'index';
$c->_customtableacces();
checkRedirect($c->denied, 'Editor without viewer permission must still be denied table-list access.');
$dashboard = array('controller'=>'users', 'action'=>'dashboard');
checkRedirect($c->target() === $dashboard, 'Approval editor without list access must return to dashboard.');
$c->CustomTable->viewers = '["another-user"]';
checkRedirect($c->target() === $dashboard, 'Another viewer must not grant this user list access.');
$c->CustomTable->viewers = '["approver-user"]';
$route = $c->target();
checkRedirect($route['action'] === 'index' && !isset($route['approval_id']) && !isset($route[0]), 'Viewer must return to list, without edit context.');
foreach (array('qc_document_id','custom_table_id','process_id') as $key) checkRedirect($route[$key] === $c->request->params['named'][$key], 'Lost list context: '.$key);
$c->CustomTable->viewers = 'invalid-json';
checkRedirect($c->target() === $dashboard, 'Invalid viewer data must use dashboard.');
$c->CustomTable->missing = true;
checkRedirect($c->target() === $dashboard, 'Missing table must use dashboard.');
$c->Session->admin = true;
checkRedirect($c->target()['action'] === 'index', 'Administrator must retain list redirect.');
$c->Session->admin = false;
$c->superUser = true;
checkRedirect($c->target()['action'] === 'index', 'Super User must retain list redirect.');
$c->superUser = false;
$c->CustomTable->missing = false;
$c->CustomTable->viewers = '[]';
unset($c->request->params['named']['approval_id']);
checkRedirect($c->target() === $dashboard, 'Ordinary editor without list access must also use dashboard.');
$c->action = 'add';
checkRedirect($c->target() === $dashboard, 'Creator without list access must also use dashboard.');
$c->request->params = array();
checkRedirect($c->target() === array('action'=>'index'), 'Forms without a custom table must retain normal redirect.');
echo "Post-save index/dashboard routing and permission regression checks passed.\n";
