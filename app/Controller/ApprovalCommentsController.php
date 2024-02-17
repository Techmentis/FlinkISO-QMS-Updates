<?php
App::uses('AppController', 'Controller');
/**
 * ApprovalComments Controller
 *
 * @property ApprovalComment $ApprovalComment
 * @property PaginatorComponent $Paginator
 */
class ApprovalCommentsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');
    public function approval_comments($approval_id = null) {

        $this->layout = 'ajax';

        $approvalComments = $this->ApprovalComment->find('all', array('order' => array('ApprovalComment.sr_no' => 'ASC'), 'conditions' => array('ApprovalComment.approval_id' => $this->request->params['named']['approval_id'])));
        $this->set('approvalComments', $approvalComments);


        // get record details
        $approval = $this->ApprovalComment->Approval->find('first',array('recursive'=>0, 'conditions'=>array('Approval.id'=>$this->request->params['named']['approval_id'])));
        if (!$approvalComments) {            
            // if no comments get approval details
            $approval = $this->ApprovalComment->Approval->find('first', array('conditions' => array('Approval.id' => $this->request->params['named']['approval_id'])));
            $this->set('approval', $approval);
        }
        $approversLists = $this->ApprovalComment->User->find('list', array(
            'conditions' => array(
                'OR'=>array(
                    'User.is_approver' => 1, 
                    'User.is_mr'=>1, 
                    'User.is_view_all'=>1,  
                    'User.id !=' => $creator, 
                    'User.id !=' => $this->Session->read('User.id')
                )
            )
        )
    );

        $approversLists[$approval['Approval']['from']] = $approval['From']['name'];
        $this->set('approversLists', $approversLists);
    }
    public function add_response($id = null, $response = null, $to = null) {        
        $id = $this->request->data['id'];
        $response = $this->request->data['response'];
        $to = $this->request->data['to'];
        $approval_id = $this->request->data['approval_id'];
        $approval_status = $this->request->data['approval_status'];
        
        if($approval_status == 1){
            // close approval

            $approval = $this->ApprovalComment->Approval->find('first',array('recursive'=>-1,'conditions'=>array('Approval.id'=>$approval_id)));

            if($approval){
                $approval['Approval']['status'] = $approval['Approval']['approval_status'] = 1;
                $approval['Approval']['approver_comments'] = $response;
                $this->ApprovalComment->Approval->create();
                $this->ApprovalComment->Approval->save($approval,false);
            }

            $approvalComment = $this->ApprovalComment->find('first',array('recursive'=>-1,'conditions'=>array('ApprovalComment.id'=>$id)));
            if($approvalComment){
                $approvalComment['ApprovalComment']['response_status'] = 2;
                $approvalComment['ApprovalComment']['response'] = $response;
                $this->ApprovalComment->create();
                $this->ApprovalComment->save($approvalComment,false);
            }
            // update record

            $model = $approval['Approval']['model_name'];
            $this->loadModel($model);
            $rec = $this->$model->find('first', array('conditions' => array($model . '.id' => $approval['Approval']['record']), 'recursive' => - 1));
            if ($rec) {
                $rec[$model]['record_status'] = 1;
                $rec[$model]['publish'] = 1;
                $rec[$model]['approved_by'] = $this->Session->read('User.employee_id');
                // $rec[$this->modelClass]['record_status'] = 1;
                $this->$model->create();
                $this->$model->save($rec,false);
            }
            
            $this->_sent_approval_email($to,1,$response,$model);

        }else{
            // add response_status 1 to main response
            // create new response
            // first get approval record
            $approvalComment = $this->ApprovalComment->find('first', array('conditions' => array('ApprovalComment.id' => $id)));
            if ($approvalComment) {
                //update response_status
                $approvalComment['ApprovalComment']['response_status'] = 1;
                $approvalComment['ApprovalComment']['response'] = $response;
                $this->ApprovalComment->create();
                if ($this->ApprovalComment->save($approvalComment, false)) {
                    // create new record and send to to user from this user
                    unset($this->ApprovalComment->id);
                    unset($approvalComment['ApprovalComment']['id']);
                    unset($approvalComment['ApprovalComment']['sr_no']);
                    unset($approvalComment['ApprovalComment']['created']);
                    unset($approvalComment['ApprovalComment']['created_by']);
                    unset($approvalComment['ApprovalComment']['comments']);
                    unset($approvalComment['ApprovalComment']['response']);
                    unset($approvalComment['ApprovalComment']['from']);
                    unset($approvalComment['ApprovalComment']['user_id']);
                    unset($approvalComment['ApprovalComment']['modified']);
                    unset($approvalComment['ApprovalComment']['modified_by']);
                    $approvalComment['ApprovalComment']['from'] = $this->Session->read('User.id');
                    $approvalComment['ApprovalComment']['user_id'] = $to;
                    $approvalComment['ApprovalComment']['comments'] = $response;
                    $approvalComment['ApprovalComment']['response_status'] = 0;                    
                    $this->ApprovalComment->create();
                    if ($this->ApprovalComment->save($approvalComment['ApprovalComment'])) {
                        $this->_sent_approval_email($to,0,$response,$model);
                    } else {
                        echo "Add failed";
                    }
                }
            } else {
                // add first
                if ($approval_id) {
                    $approval = $this->ApprovalComment->Approval->find('first', array('conditions' => array('Approval.id' => $approval_id), 'recursive' => - 1));
                    if ($approval) {
                        $approvalComment['ApprovalComment']['from'] = $this->Session->read('User.id');
                        $approvalComment['ApprovalComment']['user_id'] = $to;
                        $approvalComment['ApprovalComment']['approval_id'] = $approval_id;
                        $approvalComment['ApprovalComment']['response_status'] = 0;
                        $approvalComment['ApprovalComment']['comments'] = $response;
                        $this->ApprovalComment->create();
                        if ($this->ApprovalComment->save($approvalComment['ApprovalComment'])) {
                            $this->_sent_approval_email($to,0,$response,$model);
                        } else {
                            echo "Add failed";
                        }
                    }
                }
            }
        }
        
    }


    public function _sent_approval_email($to = null,$message = null,$response = null,$model = null){
        // $web = env(REQUEST_SCHEME) . '://'.env(SERVER_NAME);
        // $login_url = $web .str_replace('app/webroot/index.php','users/login',env(PHP_SELF));

        $this->loadModel('User');
        $user = $this->User->find('first',array('conditions'=>array('User.id'=>$to)));
        if($user){
            if ($user['Employee']['office_email'] != '') {
                $email = $user['Employee']['office_email'];
            } else if ($user['Employee']['personal_email'] != '') {
                $email = $user['Employee']['personal_email'];
            }    
        }
        
        if ($email) {
            try {
                App::uses('CakeEmail', 'Network/Email');
                $email = $email;
                $EmailConfig = new CakeEmail("fast");
                $EmailConfig->to($email);
                $EmailConfig->subject('FlinkISO: Approval');
                $EmailConfig->template('approvalRequest');
                $EmailConfig->viewVars(array(
                    'message' => $message,
                    'url' => $login_url,
                    'response' => $response,
                    'by' => $this->Session->read('User.username'),
                    'mode' => Inflector::humanize($model),
                ));
                $EmailConfig->emailFormat('html');
                $EmailConfig->send();
            }
            catch (Exception $e) {
                $this->Session->setFlash(__('The user has been saved but fail to send email. Please check smtp details.', true), 'smtp');
                // $this->redirect(array(
                //     'action' => 'index'
                // ));
                
            }
            
        }
    }
}
