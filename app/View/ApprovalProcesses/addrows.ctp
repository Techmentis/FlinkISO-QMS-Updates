<tr class="colrows thiscol<?php echo $i?>">
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.process_step',array('class'=>'form-control','label'=>false,'default'=>$i,'required'=>$required)) ?></td>
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.title',array('class'=>'form-control','label'=>false,'default'=>'Step-'.$i,'required'=>$required)) ?></td>
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.comments',array('rows'=>2, 'class'=>'form-control','label'=>false,'required'=>$required)) ?></td>

	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_reviwers',array('checkbox'=>'checkbox','label'=>false, 'checked'=>true, 
	'onchange'=>'udpatecheck('.$i.',"reviewer",this);')) ?></td>										
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_approvers',array('checkbox'=>'checkbox','label'=>false,
	'onchange'=>'udpatecheck('.$i.',"publisher",this);')) ?></td>
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_department_hod',array('checkbox'=>'form-control','label'=>false,
	'onchange'=>'udpatecheck('.$i.',"hods",this);')) ?></td>										
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_admins',array('checkbox'=>'checkbox','label'=>false,
	'onchange'=>'udpatecheck('.$i.',"admins",this);')) ?></td>

	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_designation',array('class'=>'form-control', 'options'=>$PublishedDesignationList,  'label'=>false,
	'onchange'=>'udpatecheck('.$i.',"designation",this);')) ?></td>										
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_users[]',array('name'=>'data[ApprovalStep][steps]['.$i.'][send_to_users][]', 'class'=>'form-control', 'multiple', 'options'=>$PublishedUserList, 'label'=>false,'onchange'=>'udpatecheck('.$i.',"users",this);')) ?></td>	

	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.send_to_publishers',array('checkbox'=>'checkbox','label'=>false,
	'onchange'=>'udpatecheck('.$i.',"publisher",this);')) ?></td>			

	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.approval_mode',array('class'=>'','type'=>'radio', 'legend'=>false, 'options'=>array(0=>'View Only',1=>'Edit'),'default'=>1)) ?></td>
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.approval_type',array('class'=>'','type'=>'radio', 'legend'=>false, 'options'=>array(0=>'All',1=>'Any'),'default'=>1));?></td>

	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.ignore_department',array('class'=>'','type'=>'radio', 'legend'=>false, 'options'=>array(0=>'Yes',1=>'No'),'default'=>1));?></td>
	<td><?php echo $this->Form->input('ApprovalStep.steps.'.$i.'.ignore_branch',array('class'=>'','type'=>'radio', 'legend'=>false, 'options'=>array(0=>'Yes',1=>'No'),'default'=>1));?></td>
	<td>Remove</td>
</tr>