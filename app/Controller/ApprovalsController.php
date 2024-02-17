<?php
App::uses('AppController', 'Controller');
/**
 * Approvals Controller
 *
 * @property Approval $Approval
 * @property PaginatorComponent $Paginator
 */
class ApprovalsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');

    public function index($startDate = null, $endDate = null) {
        if ($this->request->is('post')) {

            $dates = explode(' - ',$this->request->data['Approval']['dates']);
            $startDate = date('Y-m-d',strtotime($dates[0]));
            $endDate = date('Y-m-d',strtotime($dates[1]));

            if($this->request->data['Approval']['model_name'] && $this->request->data['Approval']['model_name'] != -1)$conditions[] = array('Approval.model_name'=>$this->request->data['Approval']['model_name']);
            if($this->request->data['Approval']['from'] && $this->request->data['Approval']['from'] != -1)$conditions[] = array('Approval.from'=>$this->request->data['Approval']['from']);
            if($this->request->data['Approval']['to'] && $this->request->data['Approval']['to'] != -1)$conditions[] = array('Approval.user_id'=>$this->request->data['Approval']['to']);
            if($this->request->data['Approval']['status'] && $this->request->data['Approval']['status'] != -1)$conditions[] = array('Approval.status'=>$this->request->data['Approval']['status']);
            if($this->request->data['Approval']['record_status'] && $this->request->data['Approval']['record_status'] != -1)$conditions[] = array('Approval.record_status'=>($this->request->data['Approval']['record_status']-1));            

        }else{
            $conditions[] = array('Approval.status'=>'Sent Back','Approval.from'=>$this->Session->read('User.id'));
            $startDate = date('Y-m-d',strtotime('-1 month'));
            $endDate = date('Y-m-d');
        }

        if($this->Session->read('User.is_mr') == 0){
            // $condition2 = array('OR'=>array('Approval.user_id' =>$this->Session->read('User.id'),'Approval.from' =>$this->Session->read('User.id')));
        }else{
            // $condition2 = array();
        }

        if(!$conditions)$limit = 50;
        else $limit = 0;

        $this->Approval->virtualFields = array(
            'new_date' => 'DATE_FORMAT(Approval.created,"%Y-%m-%d")'
        );

        $approvals = $this->Approval->find('all', array(
            'fields'=>array(
                'Approval.id',
                'Approval.sr_no',
                'Approval.model_name',
                'Approval.controller_name',
                'Approval.record',
                'Approval.from',
                'Approval.user_id',
                'Approval.record_status',
                'Approval.status',
                'Approval.comments',
                'Approval.created',
                'Approval.new_date'
            ),            
            'conditions' => array(
                $conditions, 
                'Approval.soft_delete' => 0,
                // 'DATE_FORMAT(Approval.created,"%Y-%m-%d") between ? and ?' => array(date('Y-m-d',strtotime($startDate)),date('Y-m-d',strtotime($endDate))),
                // 'Approval.new_date BETWEEN ? AND ?' => array($startDate,$endDate),
                
                // 'or'=>array(
                //     "Approval.status" => NULL,
                //     "Approval.status" => 'Forwarded',
                //     'Approval.status_user_id'=>$this->Session->read('User.id')) 
            ),                         
            'order' => array('Approval.sr_no DESC'), 
            // 'group'=> array('Approval.record'), 
            'recursive' => -1,
            'limit'=>$limit
        ));
        
        $newApprovals = $froms = $tos = $users = array();
        
        foreach ($approvals as $key=>$approval){            
            $this->loadModel($approval['Approval']['model_name']);
            
            $records= $this->{$approval['Approval']['model_name']}->find('first', array('conditions'=>array('id'=>$approval['Approval']['record']), 'recursive' => -1));
            
            if($this->request->data['Approval']['publish_status'] && $this->request->data['Approval']['publish_status'] != -1){
                
                if($this->request->data['Approval']['publish_status'] == 1){
                    if($records[$approval['Approval']['model_name']]['publish'] == 0 || $records[$approval['Approval']['model_name']]['publish'] == null){
                        $approval['Approval']['title'] =  $records[$approval['Approval']['model_name']][$this->{$approval['Approval']['model_name']}->displayField];
                        $approval['Approval']['app_record_status'] = $records[$approval['Approval']['model_name']]['record_status'];
                        $approval['Approval']['record_published'] = $records[$approval['Approval']['model_name']]['publish'];    
                    }else{
                        unset($approval);
                    }
                    
                }else{                    
                    if($records[$approval['Approval']['model_name']]['publish'] == 1){
                        $approval['Approval']['title'] =  $records[$approval['Approval']['model_name']][$this->{$approval['Approval']['model_name']}->displayField];
                        $approval['Approval']['app_record_status'] = $records[$approval['Approval']['model_name']]['record_status'];
                        $approval['Approval']['record_published'] = $records[$approval['Approval']['model_name']]['publish'];    
                    }else{
                        unset($approval);
                    }
                }
            }else{
                $approval['Approval']['title'] =  $records[$approval['Approval']['model_name']][$this->{$approval['Approval']['model_name']}->displayField];
                $approval['Approval']['app_record_status'] = $records[$approval['Approval']['model_name']]['record_status'];
                $approval['Approval']['record_published'] = $records[$approval['Approval']['model_name']]['publish'];    
            }
            
            $newApprovals[$approval['Approval']['model_name']][$approval['Approval']['record']][] = $approval;
        }
        $this->set('approvals', $newApprovals);
        $allapprovals = $this->Approval->find('all',array(
            'recursive'=>-1,
            'fields'=>array('Approval.id','Approval.user_id','Approval.from','Approval.model_name')
        ));

        foreach ($allapprovals as $app) {
            $usersList[$app['Approval']['user_id']] = $app['Approval']['user_id'];
            $usersList[$app['Approval']['from']] = $app['Approval']['from'];
            $modelNames[$app['Approval']['model_name']] = $app['Approval']['model_name'];
        }
        
        if($usersList){
            $this->loadModel('User');
            $users = $this->User->find('list',array(
                'fields'=>array('User.id','User.name'),
                'conditions'=>array(
                    'User.id'=>array_keys($usersList)
                )));
    
            $statuses = array('Approved'=>'Approved','Sent Back'=>'Sent Back','Forwarded'=>'Forwarded');
            $recordStatuses = array(1=>'Unloacked',2=>'Loacked');
            $publishStatuses = array(1=>'Unpublished',2=>'Published');
    
            $froms = $tos = $users;
        }
        
        $this->set(compact('froms','tos','modelNames','statuses','recordStatuses','publishStatuses','startDate','endDate'));
        $this->_get_count();
    }

    public function change_user(){
        if ($this->request->is('post')) {
            
            
            $model = Inflector::Classify($this->request->data['Approval']['controller_name']);
            
            $this->loadModel($model);
            $rec = $this->$model->find('first',array('conditions'=>array($model.'.id'=>$this->request->data['Approval']['record'])));
            if($rec && strlen($this->request->data['Approval']['approver_id']) == 36){
                $data[$model] = $rec[$model];
                $data[$model]['status_user_id'] = $this->request->data['Approval']['approver_id'];
                $this->$model->create();
                if($this->$model->save($data[$model],false)){
                    $app = $this->Approval->find('first',array('recursive'=>-1, 'conditions'=>array('Approval.id'=>$this->request->data['Approval']['id'])));
                    $app['Approval']['user_id'] = $this->request->data['Approval']['approver_id'];
                    
                    $this->Approval->save($app['Approval']);

                    $this->_send_approval_reminder($this->request->data['Approval']['approver_id'],date('Y-m-d'),$this->Session->read('User.name'),'FlinkISO: New approval is assigned to you from.');
                    $this->Session->setFlash('Recored saved. Please try again');
                    $this->redirect(array('action' => 'index'));
                }else{
                    $this->Session->setFlash('Recored could not be saved. Please try again');
                    $this->redirect(array('action' => 'index'));
                }
            }else{
                $this->Session->setFlash('Recored could not be saved. Please try again');
                $this->redirect(array('action' => 'index'));
            }

        }
        $approval  = $this->Approval->find('first',array('conditions'=>array('Approval.id'=>$this->request->params['pass'][0])));
        $this->set('approval',$approval);
        
    }

    public function approved() {
        $conditions = $this->_check_request();
        if($this->Session->read('User.is_mr') == 0){
            $condition2 = array(
                'OR'=>array('Approval.user_id' =>$this->Session->read('User.employee_id'),'Approval.from' =>$this->Session->read('User.employee_id')),
                'OR'=>array('Approval.user_id' =>$this->Session->read('User.id'),'Approval.from' =>$this->Session->read('User.id')));
        }else{
            $condition2 = array();
        }
        $this->paginate = array('order' => array('Approval.modified' => 'DESC'), 'conditions' => array($conditions, $condition2, "Approval.status " => 1 ), 'recursive' => -1);

        $approvals = $this->paginate();
        foreach ($approvals as $key=>$approval){
         
            $this->loadModel($approval['Approval']['model_name']);
            
            $records= $this->{$approval['Approval']['model_name']}->find('first', array('conditions'=>array('id'=>$approval['Approval']['record']), 'recursive' => -1));
            $approvals[$key]['Approval']['title'] =  $records[$approval['Approval']['model_name']][$this->{$approval['Approval']['model_name']}->displayField];
        }

        $PublishedEmployeeList = $this->_get_employee_list();
        $this->set('PublishedEmployeeList', $PublishedEmployeeList);

        $userList = $this->_get_user_list();
        $this->set('userList', $userList);
        
        $this->set('approvals', $approvals);        
        $this->_get_count();
    }

    public function action($id = null, $comments = null, $status = null) {
        if ($status == 1) {
            $id = $this->request->params['named']['id'];
            $comments = $this->request->params['named']['comments'];
            $approval = $this->Approval->find('first', array('recursive' => - 1, 'conditions' => array('Approval.id' => $id)));            
            // check if user can close this
            if ($this->Session->read('User.id') == $approval['Approval']['user_id']) {
                $approval['Approval']['approver_comments'] = $comments;
                $approval['Approval']['modified'] = date('Y-m-d H:i:s');
                $approval['Approval']['status'] = 1;
                $this->Approval->create();
                if ($this->Approval->save($approval)) {
                    // check if approval is all or any
                    // if all check if other's approved it
                    // if yes then publish the recod
                    // 0 = all / 1 = any
                    if ($approval['Approval']['approval_type'] == 0) {
                        // check other closers
                        $all = $approval = $this->Approval->find('count', array('conditions' => array('Approval.model_name' => $approval['Approval']['model_name'], 'Approval.model_name' => $approval['Approval']['record'],)));
                        $approved = $this->Approval->find('count', array('conditions' => array('Approval.status' => 1, 'Approval.model_name' => $approval['Approval']['model_name'], 'Approval.model_name' => $approval['Approval']['record'],)));
                        if ($all == $approved) {
                            $model = $approval['Approval']['model_name'];
                            $this->loadModel($model);
                            $rec = $this->$model->find('first', array('recursive' => - 1, 'conditions' => array($model . '.id' => $approval['Approval']['record']),));
                            if ($rec) {
                                $rec[$model]['publish'] = 1;
                                $rec[$model]['approved_by'] = $this->Session->read('User.employee_id');
                                $this->$model->create();
                                if ($this->$model->save($rec)) {
                                    echo "Recod saved";
                                } else {
                                    echo "Recod could not be published";
                                }
                            } else {
                                echo "Recod could not be published";
                            }
                        } else {
                            echo "Changed saved";
                        }
                    } else {
                        // publish the record
                        $model = $approval['Approval']['model_name'];
                        $this->loadModel($model);
                        $rec = $this->$model->find('first', array('recursive' => - 1, 'conditions' => array($model . '.id' => $approval['Approval']['record']),));
                        if ($rec) {
                            $rec[$model]['publish'] = 1;
                            $rec[$model]['approved_by'] = $this->Session->read('User.employee_id');
                            $this->$model->create();
                            if ($this->$model->save($rec)) {
                                echo "Recod saved";
                            } else {
                                echo "Recod could not be published";
                            }
                        } else {
                            echo "Changed saved";
                        }
                    }
                } else {
                    echo "Changed could not be saved";
                }
            } else {
                echo "You are not authorized to make these changes";
            }
        } else {
            $id = $this->request->params['named']['id'];
            $comments = $this->request->params['named']['comments'];
            $approval = $this->Approval->find('first', array('recursive' => - 1, 'conditions' => array('Approval.id' => $id)));            
            // check if user can close this
            if ($this->Session->read('User.id') == $approval['Approval']['user_id']) {
                $approval['Approval']['approver_comments'] = $comments;
                $approval['Approval']['modified'] = date('Y-m-d H:i:s');
                $approval['Approval']['status'] = 1;
                $this->Approval->create();
                if ($this->Approval->save($approval)) {
                    echo "Rejection saved";
                }
            } else {
                echo "You are not authorized to make these changes";
            }
        }
    }

    public function delete_approval($id = null){
        $this->autoRender = false;
        $approval = $this->Approval->find('first',array('conditions'=>array('Approval.id'=>$id)));
        $this->Approval->delete($id);
        $this->Approval->ApprovalComment->deleteAll(array('ApprovalComment.approval_id'=>$id));
        $this->Session->setFlash(__('Approval Record Deleted'));
        $this->redirect(array('controller'=>'users', 'action' => 'dashboard'));
    }

    public function delete_approval_comment($id = null){
        $this->autoRender = false;
        $approval = $this->Approval->ApprovalComment->find('first',array('conditions'=>array('ApprovalComment.id'=>$id)));
        $this->Approval->ApprovalComment->delete($id);
        $this->Session->setFlash(__('Approval Comment Deleted'));
        $this->redirect(array('controller'=>'users', 'action' => 'dashboard'));
    }
}
