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
    
    
    public function approval_comments($approval_id = null,$custom_table_id = null,$approval_step_id = null,$prepared_by = null) {
    
        $this->layout = 'ajax';

        if(!$this->request->params['named']['approval_step_id']){
            echo "Approval Step Missing.";
            exit;
        }

        $approvalComments = $this->ApprovalComment->find('all', array('order' => array('ApprovalComment.sr_no' => 'ASC'), 'conditions' => array('ApprovalComment.approval_id' => $this->request->params['named']['approval_id'])));
        $this->set('approvalComments', $approvalComments);


        // get record details
        $approval = $this->ApprovalComment->Approval->find('first',array('recursive'=>0, 'conditions'=>array('Approval.id'=>$this->request->params['named']['approval_id'])));
        $this->set('approval', $approval);
        if (!$approvalComments) {            
            // if no comments get approval details
            $approval = $this->ApprovalComment->Approval->find('first', array('conditions' => array('Approval.id' => $this->request->params['named']['approval_id'])));
            $this->set('approval', $approval);
        }
        $approversLists = $this->_get_approver_list();        
        $approversLists[$approval['Approval']['from']] = $approval['From']['name'];
        $this->set('approversLists', $approversLists);

        $this->_fetch_approval_steps();

        $this->loadModel('ApprovalStep');
        $currentStep = $this->ApprovalStep->find('first',array('conditions'=>array('ApprovalStep.id'=>$this->request->params['named']['approval_step_id'])));
        $this->set('currentStep',$currentStep);
		$nextApprovalStep = array();
		$previousApprovalStep = array();
		$nextApproversList = array();
		$nextApproverRequired = false;
		if(!empty($currentStep['ApprovalStep'])){
			$previousApprovalStep = $this->ApprovalStep->find('first',array(
				'recursive'=>-1,
				'conditions'=>array(
					'ApprovalStep.approval_process_id'=>$currentStep['ApprovalStep']['approval_process_id'],
					'ApprovalStep.process_step <'=>$currentStep['ApprovalStep']['process_step'],
					'ApprovalStep.publish'=>1,
					'ApprovalStep.soft_delete'=>0
				),
				'order'=>array('ApprovalStep.process_step'=>'DESC')
			));
			$nextApprovalStep = $this->ApprovalStep->find('first',array(
				'recursive'=>-1,
				'conditions'=>array(
					'ApprovalStep.approval_process_id'=>$currentStep['ApprovalStep']['approval_process_id'],
					'ApprovalStep.process_step >'=>$currentStep['ApprovalStep']['process_step'],
					'ApprovalStep.publish'=>1,
					'ApprovalStep.soft_delete'=>0
				),
				'order'=>array('ApprovalStep.process_step'=>'ASC')
			));
			if(!empty($nextApprovalStep['ApprovalStep'])){
				$nextApproversList = $this->_get_approver_lists($approval['Approval']['from'], $nextApprovalStep['ApprovalStep']);
				$otherPendingApprovals = $this->ApprovalComment->Approval->find('count',array('conditions'=>array(
					'Approval.record'=>$approval['Approval']['record'],
					'Approval.approval_step_id'=>$approval['Approval']['approval_step_id'],
					'Approval.id !='=>$approval['Approval']['id'],
					'Approval.soft_delete'=>0,
					'OR'=>array('Approval.approval_status !='=>1,'Approval.approval_status'=>null)
				)));
				$nextApproverRequired = (isset($approval['Approval']['approval_type']) && (int)$approval['Approval']['approval_type'] === 1) || $otherPendingApprovals == 0;
			}
		}
		$this->set(compact('nextApprovalStep','previousApprovalStep','nextApproversList','nextApproverRequired'));
        $this->set('prepared_by',$this->request->params['named']['prepared_by']);
        $this->set('approval_step_id',$approval_step_id);
    }



    public function add_response($id = null, $response = null, $to = null, $approval_step_id = null) {
        
        $id = $this->request->data['id'];
        $response = $this->request->data['response'];
        $to = $this->request->data['to'];
        $approval_id = $this->request->data['approval_id'];
        $approval_status = $this->request->data['approval_status'];
        $approval_step_id = $this->request->data['approval_step_id'];
		$nextApproverIds = isset($this->request->data['next_approver_ids']) ? $this->request->data['next_approver_ids'] : (isset($this->request->data['next_approver_id']) ? $this->request->data['next_approver_id'] : array());
		if(!is_array($nextApproverIds)) $nextApproverIds = array($nextApproverIds);
		$nextApproverIds = array_values(array_unique(array_filter($nextApproverIds)));

		$activeApproval = $this->ApprovalComment->Approval->find('first',array('recursive'=>-1,'conditions'=>array('Approval.id'=>$approval_id)));
		$currentUserIds = array_filter(array($this->Session->read('User.id'), $this->Session->read('User.employee_id')));

        // if(empty($activeApproval['Approval']) || !in_array($activeApproval['Approval']['user_id'], $currentUserIds) || !in_array($activeApproval['Approval']['approval_status'], array(0, null))){
		// 	$this->set('responseresult',__('This approval is no longer pending for the current user.'));
		// 	return;
		// }
        
        if($to == -1 || $id == -1 || $approval_id == -1 || $approval_step_id == ''){
            $this->set('responseresult','Approval process step is missing.');            
        }
        
        if(!$approval_step_id){
            $this->set('responseresult','Failed to procees. Approval process step is missing.');            
        }

		if((int)$approval_status === 3){
			$this->_return_to_previous_step($activeApproval, $id, $response);
			return;
		}
        if($approval_status == 1){

            // new steps
            // check if there is a next step
            // if not run the existing code
            // if yes, send email to preparer 
            // also update record and update current step with a new step id
            // if no next id, remove the step id from the record

            // preparer will then send the record to next step
            $this->loadModel('ApprovalStep');
            $currentStep = $this->ApprovalStep->find('first',array('recursive'=>-1, 'conditions'=>array('ApprovalStep.id'=>$approval_step_id)));
            

            // check all approvals for this record
            $approval = $this->ApprovalComment->Approval->find('first',array('recursive'=>-1,'conditions'=>array('Approval.id'=>$approval_id)));


            if($currentStep){
                 // find if there is a next step
                $nextStep = $this->ApprovalStep->find('first',array('recursive'=>-1, 'conditions'=>array(
                    'ApprovalStep.approval_process_id'=>$currentStep['ApprovalStep']['approval_process_id'],
					'ApprovalStep.process_step >'=>$currentStep['ApprovalStep']['process_step'],
					'ApprovalStep.publish'=>1,
					'ApprovalStep.soft_delete'=>0
				), 'order'=>array('ApprovalStep.process_step'=>'ASC')));
                
                if($nextStep){

                    $approval = $this->ApprovalComment->Approval->find('first',array('recursive'=>-1,'conditions'=>array('Approval.id'=>$approval_id)));
					$pendingBeforeApproval = $this->ApprovalComment->Approval->find('count',array(
						'conditions'=>array(
							'Approval.record'=>$approval['Approval']['record'],
							'Approval.approval_step_id'=>$approval['Approval']['approval_step_id'],
							'Approval.user_id !='=>$approval['Approval']['user_id'],
							'Approval.soft_delete'=>0,
							'OR'=>array(
								'Approval.approval_status !='=>1,
								'Approval.approval_status'=>null
							)
						)
					));
					if($pendingBeforeApproval == 0 || (isset($approval['Approval']['approval_type']) && (int)$approval['Approval']['approval_type'] === 1)){
						$allowedNextApprovers = $this->_get_approver_lists($approval['Approval']['from'], $nextStep['ApprovalStep']);
						if(empty($nextApproverIds) || array_diff($nextApproverIds, array_keys($allowedNextApprovers))){
							$this->set('responseresult',__('Select one or more valid next approvers before approving this step.'));
							return;
						}
					}
                    if($approval){
                        $approval['Approval']['status'] = $approval['Approval']['approval_status'] = 1;
                        $approval['Approval']['approver_comments'] = $response;
                        $this->ApprovalComment->Approval->create();
                        $this->ApprovalComment->Approval->save($approval,false);
                    }

                    $approvalComment = $this->ApprovalComment->find('first',array('recursive'=>-1,'conditions'=>array('ApprovalComment.id'=>$id)));

                    // check if there are other pending approvals for the same step
                    $addtionalApprovals = $this->ApprovalComment->Approval->find('count',array(
                        'conditions'=>array(
                            'Approval.record'=>$approval['Approval']['record'],
                            'Approval.approval_step_id'=>$approval['Approval']['approval_step_id'],
                            'Approval.user_id != '=>$approval['Approval']['user_id'],
							'Approval.soft_delete'=>0,
							'OR'=>array(
								'Approval.approval_status !='=>1,
								'Approval.approval_status'=>null
							)
                        )
                    ));
					if(isset($approval['Approval']['approval_type']) && (int)$approval['Approval']['approval_type'] === 1){
						$this->_close_remaining_any_mode_approvals($approval);
						$addtionalApprovals = 0;
					}
                    
                    if($approvalComment){
                        $approvalComment['ApprovalComment']['response_status'] = 2;
                        $approvalComment['ApprovalComment']['response'] = $response;
                        $this->ApprovalComment->create();
                        $this->ApprovalComment->save($approvalComment,false);
                    }

                    $model = $approval['Approval']['model_name'];
                    $this->loadModel($model);
                    $rec = $this->$model->find('first', array('conditions' => array($model . '.id' => $approval['Approval']['record']), 'recursive' => - 1));
                    if ($rec) {


                        // somewhere here we need to check if all the current steps approvals are closed before moving on to the next step.

                        if($currentStep['ApprovalStep']['send_to_publishers'] == true){
                            $rec[$model]['publish'] = 1;
                            $rec[$model]['published_date'] = date('Y-m-d');
                            $rec[$model]['published_by'] = $this->Session->read('User.employee_id');
                        }

                        if($currentStep['ApprovalStep']['send_to_approvers'] == true){
                            $rec[$model]['approval_date'] = date('Y-m-d');
                            $rec[$model]['approved_by'] = $this->Session->read('User.employee_id');
                        }

                        if($currentStep['ApprovalStep']['send_to_reviwers'] == true){
                            $rec[$model]['date_of_review'] = date('Y-m-d');
                            $rec[$model]['reviewed_by'] = $this->Session->read('User.employee_id');
                        }


						$hasNextApprovers = true;
						if($addtionalApprovals == 0){
							$hasNextApprovers = !empty($nextApproverIds);
						}

                        if($addtionalApprovals == 0 && $hasNextApprovers){
                            $rec[$model]['approval_step_id'] = $nextStep['ApprovalStep']['id'];    
                        }else{
                            $rec[$model]['approval_step_id'] = $currentStep['ApprovalStep']['id'];    
                        }                    
                        
                        $this->$model->create();
                        if($this->$model->save($rec,false)){
                            $nextApprovalCount = 0;
							if($addtionalApprovals == 0 && $hasNextApprovers){
								$nextApprovalCount = $this->_create_next_step_approvals($approval, $nextStep, $nextApproverIds);
                            }
                            $this->_sent_approval_email($approval['Approval']['from'],1,$response,$model);
                            if($addtionalApprovals == 0 && $nextApprovalCount > 0){
                                $this->set('responseresult',__('Approved and sent directly to the next approval step.'));
                            }else if($addtionalApprovals == 0){
                                $this->set('responseresult',__('Approved, but the next step has no available approvers.'));
                            }else{
                                $this->set('responseresult',__('Approved. Waiting for the remaining approvals in this step.'));
                            }
                        }else{
                            $this->set('responseresult','Failed to add response.');                            
                        }   
                    }
                                        
                }else{
                    // next record not found
                    // close approval     
                    // somewhere here we need to check if all the current steps approvals are closed before moving on to the next step.


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
                        if($this->ApprovalComment->save($approvalComment,false)){
                            $this->set('responseresult','Comment added.');  
                        }else{
                            $this->set('responseresult','Failed to add response.');                            
                        }
                    }
                    
                    // check if there are other pending approvals for the same step
                    // this needs testing
                    $addtionalApprovals = $this->ApprovalComment->Approval->find('count',array(
                        'conditions'=>array(
                            'Approval.record'=>$approval['Approval']['record'],
                            'Approval.approval_step_id'=>$approval['Approval']['approval_step_id'],
                            'Approval.user_id != '=>$approval['Approval']['user_id'],
							'Approval.soft_delete'=>0,
							'OR'=>array(
								'Approval.approval_status !='=>1,
								'Approval.approval_status'=>null
							)
                        )
                    ));
					if(isset($approval['Approval']['approval_type']) && (int)$approval['Approval']['approval_type'] === 1){
						$this->_close_remaining_any_mode_approvals($approval);
						$addtionalApprovals = 0;
					}

                    // update record

                    $model = $approval['Approval']['model_name'];
                    $this->loadModel($model);
                    $rec = $this->$model->find('first', array('conditions' => array($model . '.id' => $approval['Approval']['record']), 'recursive' => - 1));

                    if ($rec) {

                        if($currentStep['ApprovalStep']['send_to_publishers'] == true){
                            $rec[$model]['published_date'] = date('Y-m-d');
                            $rec[$model]['published_by'] = $this->Session->read('User.employee_id');
                        }else{
                            if(!$rec[$model]['published_by']){
                                $rec[$model]['published_date'] = date('Y-m-d');
                                $rec[$model]['published_by'] = $this->Session->read('User.employee_id');
                            }
                        }

                        if($currentStep['ApprovalStep']['send_to_approvers'] == true){
                            $rec[$model]['approval_date'] = date('Y-m-d');
                            $rec[$model]['approved_by'] = $this->Session->read('User.employee_id');
                        }else{
                            if(!$rec[$model]['approved_by']){
                                $rec[$model]['approval_date'] = date('Y-m-d');
                                $rec[$model]['approved_by'] = $this->Session->read('User.employee_id');
                            }
                        }

                        if($currentStep['ApprovalStep']['send_to_reviwers'] == true){
                            $rec[$model]['date_of_review'] = date('Y-m-d');
                            $rec[$model]['reviewed_by'] = $this->Session->read('User.employee_id');
                        }else{
                            if(!$rec[$model]['reviewed_by']){
                                $rec[$model]['date_of_review'] = date('Y-m-d');
                                $rec[$model]['reviewed_by'] = $this->Session->read('User.employee_id');
                            }
                        }
                        // this needs testing
                        if($addtionalApprovals == 0){
                            $rec[$model]['record_status'] = 1;
                            $rec[$model]['publish'] = 1;
                            $rec[$model]['approval_step_id'] = null;
                        }                        

                        $this->$model->create();
                        if($this->$model->save($rec,false)){
                            $this->set('responseresult','Comment added.'); 
                            $this->_sent_approval_email($approval['Approval']['from'],1,$response,$model);
                        }else{
                            $this->set('responseresult','Failed to add response.');                            
                        }
                    }
                }                
            }
            
            
            if(!$nextStep){
                
            }            

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
                    if ($this->ApprovalComment->save($approvalComment['ApprovalComment'],false)) {
                        $this->_sent_approval_email($to,0,$response,$model);
                    } else {
                        $this->set('responseresult','Failed to add response.');
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
                        if ($this->ApprovalComment->save($approvalComment['ApprovalComment'],false)) {
                            $this->_sent_approval_email($to,0,$response,$model);
                        } else {
                            $this->set('responseresult','Failed to add response.');
                        }
                    }
                }
            }
        }
    }


	protected function _close_remaining_any_mode_approvals($currentApproval = null){
		if(empty($currentApproval['Approval'])) return;
		$approval = $currentApproval['Approval'];
		$remaining = $this->ApprovalComment->Approval->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'Approval.record'=>$approval['record'],
				'Approval.model_name'=>$approval['model_name'],
				'Approval.approval_step_id'=>$approval['approval_step_id'],
				'Approval.id !='=>$approval['id'],
				'Approval.soft_delete'=>0,
				'OR'=>array('Approval.approval_status !='=>1,'Approval.approval_status'=>null)
			)
		));
		foreach($remaining as $parallelApproval){
			$parallelApproval['Approval']['status'] = 1;
			$parallelApproval['Approval']['approval_status'] = 1;
			$parallelApproval['Approval']['approver_comments'] = __('Step completed by another approver under Any approval mode.');
			$parallelApproval['Approval']['approved_date_time'] = date('Y-m-d H:i:s');
			$this->ApprovalComment->Approval->create();
			$this->ApprovalComment->Approval->save($parallelApproval,false);
		}
	}

	protected function _return_to_previous_step($currentApproval = array(), $approvalCommentId = null, $response = ''){
		if(empty($currentApproval['Approval'])){
			$this->set('responseresult',__('Approval record not found.'));
			return false;
		}
		$approval = $currentApproval['Approval'];
		$this->loadModel('ApprovalStep');
		$currentStep = $this->ApprovalStep->find('first',array('recursive'=>-1,'conditions'=>array('ApprovalStep.id'=>$approval['approval_step_id'])));
		if(empty($currentStep['ApprovalStep'])){
			$this->set('responseresult',__('Current approval step was not found.'));
			return false;
		}
		$previousStep = $this->ApprovalStep->find('first',array(
			'recursive'=>-1,
			'conditions'=>array(
				'ApprovalStep.approval_process_id'=>$currentStep['ApprovalStep']['approval_process_id'],
				'ApprovalStep.process_step <'=>$currentStep['ApprovalStep']['process_step'],
				'ApprovalStep.publish'=>1,
				'ApprovalStep.soft_delete'=>0
			),
			'order'=>array('ApprovalStep.process_step'=>'DESC')
		));
		if(empty($previousStep['ApprovalStep'])){
			$this->set('responseresult',__('This is the first approval step and cannot be returned further.'));
			return false;
		}

		$previousCycle = $this->ApprovalComment->Approval->find('first',array(
			'recursive'=>-1,
			'fields'=>array('MAX(Approval.approval_cycle) AS max_cycle'),
			'conditions'=>array(
				'Approval.record'=>$approval['record'],
				'Approval.model_name'=>$approval['model_name'],
				'Approval.approval_step_id'=>$previousStep['ApprovalStep']['id'],
				'Approval.approval_cycle <='=>(int)$approval['approval_cycle'],
				'Approval.soft_delete'=>0
			)
		));
		$previousApprovalConditions = array(
			'Approval.record'=>$approval['record'],
			'Approval.model_name'=>$approval['model_name'],
			'Approval.approval_step_id'=>$previousStep['ApprovalStep']['id'],
			'Approval.soft_delete'=>0
		);
		if(isset($previousCycle[0]['max_cycle'])) $previousApprovalConditions['Approval.approval_cycle'] = $previousCycle[0]['max_cycle'];
		$previousApprovalRows = $this->ApprovalComment->Approval->find('all',array(
			'recursive'=>-1,
			'fields'=>array('Approval.user_id'),
			'conditions'=>$previousApprovalConditions,
			'order'=>array('Approval.modified'=>'DESC')
		));
		$previousApproverIds = array();
		foreach($previousApprovalRows as $previousApprovalRow){
			if(!empty($previousApprovalRow['Approval']['user_id'])) $previousApproverIds[] = $previousApprovalRow['Approval']['user_id'];
		}
		$previousApproverIds = array_values(array_unique($previousApproverIds));
		if(empty($previousApproverIds)) $previousApproverIds = array_keys((array)$this->_get_approver_lists($approval['from'], $previousStep['ApprovalStep']));
		if(empty($previousApproverIds)){
			$this->set('responseresult',__('No eligible users are available for the previous approval step.'));
			return false;
		}

		$model = $approval['model_name'];
		$this->loadModel($model);
		$record = $this->$model->find('first',array('recursive'=>-1,'conditions'=>array($model.'.id'=>$approval['record'])));
		if(empty($record[$model])){
			$this->set('responseresult',__('The record could not be moved to the previous step.'));
			return false;
		}

		$pendingCurrentApprovals = $this->ApprovalComment->Approval->find('all',array('recursive'=>-1,'conditions'=>array(
			'Approval.record'=>$approval['record'],
			'Approval.model_name'=>$approval['model_name'],
			'Approval.approval_step_id'=>$approval['approval_step_id'],
			'Approval.soft_delete'=>0,
			'OR'=>array('Approval.approval_status'=>0,'Approval.approval_status'=>null)
		)));
		$latestCycle = $this->ApprovalComment->Approval->find('first',array(
			'recursive'=>-1,
			'fields'=>array('MAX(Approval.approval_cycle) AS max_cycle'),
			'conditions'=>array('Approval.record'=>$approval['record'],'Approval.model_name'=>$approval['model_name'])
		));
		$newCycle = !empty($latestCycle[0]['max_cycle']) ? ((int)$latestCycle[0]['max_cycle'] + 1) : ((int)$approval['approval_cycle'] + 1);
		$createdApprovalIds = array();
		foreach($previousApproverIds as $previousApproverId){
			$returnApproval = array('Approval'=>array(
				'title'=>isset($approval['title']) ? $approval['title'] : $previousStep['ApprovalStep']['title'],
				'controller_name'=>$approval['controller_name'],
				'model_name'=>$approval['model_name'],
				'record'=>$approval['record'],
				'from'=>$approval['from'],
				'user_id'=>$previousApproverId,
				'status'=>0,
				'approval_status'=>0,
				'approval_mode'=>isset($previousStep['ApprovalStep']['approval_mode']) ? $previousStep['ApprovalStep']['approval_mode'] : 1,
				'approval_type'=>isset($previousStep['ApprovalStep']['approval_type']) ? $previousStep['ApprovalStep']['approval_type'] : 0,
				'comments'=>__('Returned from %s: %s', $currentStep['ApprovalStep']['title'], $response),
				'approval_step_id'=>$previousStep['ApprovalStep']['id'],
				'approval_process_id'=>$previousStep['ApprovalStep']['approval_process_id'],
				'approval_cycle'=>$newCycle
			));
			$this->ApprovalComment->Approval->create();
			if($this->ApprovalComment->Approval->save($returnApproval,false)){
				$createdApprovalIds[] = $this->ApprovalComment->Approval->id;
			}else{
				if(!empty($createdApprovalIds)) $this->ApprovalComment->Approval->deleteAll(array('Approval.id'=>$createdApprovalIds),false,false);
				$this->set('responseresult',__('Unable to create approvals for the previous step.'));
				return false;
			}
		}
		if(empty($createdApprovalIds)){
			$this->set('responseresult',__('Unable to create approvals for the previous step.'));
			return false;
		}

		$originalRecord = $record;
		$record[$model]['approval_step_id'] = $previousStep['ApprovalStep']['id'];
		$record[$model]['publish'] = 0;
		$record[$model]['record_status'] = 1;
		$this->$model->create();
		if(!$this->$model->save($record,false)){
			$this->ApprovalComment->Approval->deleteAll(array('Approval.id'=>$createdApprovalIds),false,false);
			$this->set('responseresult',__('The record could not be moved to the previous step.'));
			return false;
		}

		$savedCurrentApprovalIds = array();
		foreach($pendingCurrentApprovals as $pendingApproval){
			$pendingApproval['Approval']['status'] = 1;
			$pendingApproval['Approval']['approval_status'] = 3;
			$pendingApproval['Approval']['approver_comments'] = $response;
			$this->ApprovalComment->Approval->create();
			if($this->ApprovalComment->Approval->save($pendingApproval,false)){
				$savedCurrentApprovalIds[] = $pendingApproval['Approval']['id'];
				continue;
			}
			foreach($pendingCurrentApprovals as $originalPendingApproval){
				if(in_array($originalPendingApproval['Approval']['id'],$savedCurrentApprovalIds)){
					$this->ApprovalComment->Approval->create();
					$this->ApprovalComment->Approval->save($originalPendingApproval,false);
				}
			}
			$this->$model->create();
			$this->$model->save($originalRecord,false);
			$this->ApprovalComment->Approval->deleteAll(array('Approval.id'=>$createdApprovalIds),false,false);
			$this->set('responseresult',__('The current approval could not be closed. No workflow changes were retained.'));
			return false;
		}

		if(!empty($approvalCommentId) && $approvalCommentId != -1){
			$approvalComment = $this->ApprovalComment->find('first',array('recursive'=>-1,'conditions'=>array('ApprovalComment.id'=>$approvalCommentId)));
			if(!empty($approvalComment['ApprovalComment'])){
				$approvalComment['ApprovalComment']['response_status'] = 2;
				$approvalComment['ApprovalComment']['response'] = $response;
				$this->ApprovalComment->create();
				$this->ApprovalComment->save($approvalComment,false);
			}
		}

		foreach($previousApproverIds as $previousApproverId) $this->_sent_approval_email($previousApproverId,2,$response,$approval['model_name']);
		if(!empty($approval['from']) && !in_array($approval['from'], $previousApproverIds)) $this->_sent_approval_email($approval['from'],2,$response,$approval['model_name']);
		$this->set('responseresult',__('Returned to previous step: %s.', $previousStep['ApprovalStep']['title']));
		return true;
	}

    protected function _create_next_step_approvals($currentApproval = null, $nextStep = null, $nextApproverIds = array()){
        if(empty($currentApproval['Approval']) || empty($nextStep['ApprovalStep'])) return 0;

        $approval = $currentApproval['Approval'];
        $step = $nextStep['ApprovalStep'];
        $nextApprovers = $this->_get_approver_lists($approval['from'], $step);
		if(!is_array($nextApproverIds)) $nextApproverIds = array($nextApproverIds);
		$nextApproverIds = array_values(array_unique(array_filter($nextApproverIds)));
		if(empty($nextApproverIds) || array_diff($nextApproverIds, array_keys($nextApprovers))) return 0;

        $assigned = 0;
		foreach($nextApproverIds as $nextApprover){
            $conditions = array(
                'Approval.record'=>$approval['record'],
                'Approval.model_name'=>$approval['model_name'],
                'Approval.approval_step_id'=>$step['id'],
                'Approval.user_id'=>$nextApprover
            );
            if(isset($approval['approval_cycle'])) $conditions['Approval.approval_cycle'] = $approval['approval_cycle'];
            if($this->ApprovalComment->Approval->find('count',array('conditions'=>$conditions))){
                $assigned++;
                continue;
            }

            $nextApproval = array('Approval'=>array(
                'title'=>isset($approval['title']) ? $approval['title'] : $step['title'],
                'controller_name'=>$approval['controller_name'],
                'model_name'=>$approval['model_name'],
                'record'=>$approval['record'],
                'from'=>$approval['from'],
                'user_id'=>$nextApprover,
                'status'=>0,
                'approval_status'=>0,
                'approval_mode'=>isset($step['approval_mode']) ? $step['approval_mode'] : 1,
                'approval_type'=>isset($step['approval_type']) ? $step['approval_type'] : 0,
                'comments'=>isset($step['comments']) ? $step['comments'] : '',
                'approval_step_id'=>$step['id'],
                'approval_process_id'=>$step['approval_process_id'],
                'approval_cycle'=>isset($approval['approval_cycle']) ? $approval['approval_cycle'] : 1
            ));

            $this->ApprovalComment->Approval->create();
            if($this->ApprovalComment->Approval->save($nextApproval,false)){
				$assigned++;
                $this->_sent_approval_email($nextApprover,0,$nextApproval['Approval']['comments'],$approval['model_name']);
            }
        }
		return $assigned;
    }

    public function _sent_approval_email($to = null,$message = null,$response = null,$model = null){
        $this->loadModel('User');
        $email = null;
        $login_url = Router::url('/', true);
        $user = $this->User->find('first',array('conditions'=>array('OR'=>array('User.id'=>$to,'User.employee_id'=>$to))));
        if($user){
            if ($user['Employee']['office_email'] != '') {
                $email = $user['Employee']['office_email'];
            } else if ($user['Employee']['personal_email'] != '') {
                $email = $user['Employee']['personal_email'];
            }    
        }
        
        if ($email) {
            if($message == 1)$subject = 'FlinkISO: Record Approved.';
            else if($message == 2)$subject = 'FlinkISO: Record Returned to Previous Approval Step';
            else $subject = 'FlinkISO: Approvals';

            try {
                App::uses('CakeEmail', 'Network/Email');
                $email = $email;
                $EmailConfig = new CakeEmail("fast");
                $EmailConfig->to($email);
                $EmailConfig->subject($subject);
                $EmailConfig->template('approvalRequest');
                $EmailConfig->viewVars(array(
                    'message' => $message,
                    'url' => $login_url,
                    'response' => $response,
                    'by' => $this->Session->read('User.name'),
                    'mode' => Inflector::humanize($model),
                    'to_name'=>$user['Employee']['name']
                ));
                $EmailConfig->emailFormat('html');
                $EmailConfig->send();
            }
            catch (Exception $e) {
                $this->Session->setFlash(__('The user has been saved but fail to send email. Please check smtp details.', true), 'smtp');                
            }
            
        }
    }
}
