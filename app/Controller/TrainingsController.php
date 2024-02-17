<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class TrainingsController extends AppController {

    public function trainings(){
        if ($this->request->is('post')) {

        }else{
            $path = Configure::read('ApiPath')."/usage_details/timezones/".$this->Session->read('User.company_id')."/api:true/". $str;
            $timezones = $response = $this->_curl_get($path);
            $timezones = json_decode($timezones,true);
            $this->set('timezones',$timezones);
        }
    }

    public function index(){
        $str = 'company_id:'.$this->Session->read('User.company_id');
        $path = Configure::read('ApiPath')."/trainings/index/".$this->Session->read('User.company_id')."/api:true/". $str;
        $response = $this->_curl_get($path);  

        $data = json_decode($response,true);        

        $trainings = $data['response']['trainings'];

        $this->request->params['paging'] = $data['response']['paging'];
        $this->set('trainings',$trainings);
        $this->set('trainingStatus',$data['response']['trainingStatus']);
    }

    public function add_training(){
        $this->request->data['Training']['schedule_date'] = date('Y-m-d H:i:s',strtotime($this->request->data['Training']['schedule_date']));
        $training['Training'] = $this->request->data['Training'];
        $training['Training']['company_id'] = $this->Session->read('User.company_id');
        $training['Training']['user_id'] = $this->Session->read('User.id');
        $training['Training']['employee_id'] = $this->Session->read('User.employee_id');
        $training['Training']['employee_name'] = $this->Session->read('User.name');
        $training['Training']['company_name'] = $this->Session->read('User.company_name');
        unset($training['Training']['checklist']);
        
        $curl = curl_init();
        $path = Configure::read('ApiPath')."/trainings/add_training/company_id:".$this->Session->read('User.company_id')."/" .$this->Session->read('User.company_id')."/api:true/". $str;

        $response = $this->_curl_post($path,null,$training);
        $result = json_decode($response,true);
        
        if($result){
            if($result['error'] == 0){
                $this->Session->setFlash(__('Invoice Added.'), 'default', array('class' => 'alert alert-success'));
                $this->redirect(array('controller'=>'billing', 'action' => 'view_invoice',$result['response']));
            }else{
                $this->Session->setFlash(__('Invoice count not be generated.'), 'default', array('class' => 'alert alert-danger'));
                $this->redirect(array('controller'=>'trainings', 'action' => 'index'));
            }
        }else{
            $this->Session->setFlash(__('Invoice count not be generated.'), 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('controller'=>'trainings', 'action' => 'index'));
            

        }
    }

}
