<?php
App::uses('AppController', 'Controller');
/**
 * UserSessions Controller
 *
 * @property UserSession $UserSession
 */
class UserSessionsController extends AppController {
    public function get_usernames() {
      $this->loadModel('User');
      $users = $this->User->find('all', array('conditions' => array('User.soft_delete'=> 0,'User.publish'=>1),'fields' => array('User.id','User.name','User.username')));
      foreach ($users as $user) {
        $employeeUserNames[$user['User']['id']] = $user['User']['name'] . " (" . $user['User']['username'] . ")";
    }
    return($employeeUserNames);
}

public function _get_system_table_id() {
    $this->loadModel('SystemTable');
    $this->SystemTable->recursive = - 1;
    $systemTableId = $this->SystemTable->find('first', array('conditions' => array('SystemTable.system_name' => $this->request->params['controller'])));
    return $systemTableId['SystemTable']['id'];
}
public function index() {
    $userId = $this->request->query['user_id'];
    if ($userId) {
        $conditions = array('UserSession.user_id' => $userId);
        $this->paginate = array('conditions' => $conditions, 'order' => array('UserSession.sr_no' => 'DESC'), 'limit' => 10);
        $this->UserSession->recursive = 0;
        $this->set('userSessions', $this->paginate());
        $this->set('userId', $userId);
    }
    $users = $this->get_usernames();
    $this->set(compact('users'));
}
public function view($id = null) {
    $userId = $id;
    $this->UserSession->History->recursive = 0;
    $userSession = $this->UserSession->History->find('all', array('conditions' => array('History.user_session_id' => $id), 'order' => array('History.created' => 'DESC')));
    $this->set('userSession', $userSession);
    $this->set('selectedUserId', $selectedUserId);
    $users = $this->get_usernames();
    $this->set(compact('users'));
}
}
