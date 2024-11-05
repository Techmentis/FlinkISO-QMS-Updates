<?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
<?php echo $this->fetch('script'); ?><div id="employees_ajax">
    <?php echo $this->Session->flash();?>   

    <div class="employees ">
        <?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'Employees','modelClass'=>'Employee','options'=>array(),'pluralVar'=>'employees'))); ?>
        <?php echo $this->Html->link('Add Bulk',array('action'=>'add_bulk','timestamp'=>date('ymdhis')),array('class'=>'btn btn-sm btn-primary pull-right'));?>
        <div class="nav panel panel-default">
            <div class="employees form col-md-12">
                <?php echo $this->Form->create('Employee',array('role'=>'form','class'=>'form')); ?>
                <div class="row">
                    <?php
                    echo "<div class='col-md-4'>".$this->Form->input('name',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('parent_id',array('class'=>'form-control', 'style'=>'')) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('employee_number',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('branch_id',array('class'=>'form-control', 'style'=>'','required'=>'required')) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('department_id',array('class'=>'form-control', 'style'=>'','required'=>'required')) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('designation_id',array('class'=>'form-control', 'style'=>'','required'=>'required')) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('qualification',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('joining_date',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('date_of_birth',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('pancard_number',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('personal_telephone',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('office_telephone',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('mobile',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('personal_email',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-4'>".$this->Form->input('office_email',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-6'>".$this->Form->input('residence_address',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-6'>".$this->Form->input('permenant_address',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-3'>".$this->Form->input('maritial_status',array('class'=>'','type'=>'radio','options'=>$customArray['maritialStatuses'])) . '</div>';      
                    echo "<div class='col-md-3'>".$this->Form->input('employment_status',array('class'=>'','type'=>'radio','options'=>$customArray['employmentStatuses'])) . '</div>'; 
                    echo "<div class='col-md-3'>".$this->Form->input('is_approver',array('class'=>'','type'=>'radio','options'=>$customArray['isApprovar'])) . '</div>'; 
                    echo "<div class='col-md-3'>".$this->Form->input('is_hod',array('class'=>'','type'=>'radio','legend'=>'Is HOD?', 'options'=>$customArray['isHod'],'default'=>0)) . '</div>'; 
                    echo "<div class='col-md-6'>".$this->Form->input('prepared_by',array('class'=>'form-control',)) . '</div>'; 
                    echo "<div class='col-md-6'>".$this->Form->input('approved_by',array('class'=>'form-control',)) . '</div>';
                    ?>
                    <?php
                    echo $this->Form->input('id');
                    echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
                    echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
                    echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
                    echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
                    ?>

                </div>
                <div class="">
                    <?php echo $this->Form->input('publish');?>
                    <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'class' => 'btn btn-primary btn-success','id'=>'submit_id')); ?>
                    <?php echo $this->Html->image('indicator.gif', array('id' => 'submit-indicator')); ?>
                    <?php echo $this->Form->end(); ?>

                    <?php echo $this->Js->writeBuffer();?>
                </div>
            </div>

        </div>
    </div>
    <script>
        $.validator.setDefaults({
            ignore: null,
            errorPlacement: function(error, element) {              
                if(element['context']['className'] == 'form-control select error'){                 
                    $(element).next('.chosen-container').addClass('error');
                }else if(element.attr("fieldset") != ''){                       
                    $(element).parent('fieldset').addClass('error-radio');
                }else{          
                    $(element).after(error); 
                }
            },
        });
        $().ready(function() {
            jQuery.validator.addMethod("greaterThanZero", function(value, element) {
                return this.optional(element) || $("#"+element.id+" option:selected").text() != 'Select';
            }, "Please select the value");

            $('#EmployeeAddForm').validate({            
                rules: {
                    "data[Employee][branch_id]": {
                        greaterThanZero: true,
                    },
                    "data[Employee][department_id]": {
                        greaterThanZero: true,
                    },
                    "data[Employee][designation_id]": {
                        greaterThanZero: true,
                    },
                    
                }
            }); 
            
            $("#submit-indicator").hide();
            $("#submit_id").click(function(){
                if($('#EmployeeAddForm').valid()){
                    $("#submit_id").prop("disabled",true);
                    $("#submit-indicator").show();
                    $('#EmployeeAddForm').submit();
                }

            });

            $('#EmployeeBranchId').change(function() {
                if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
                    $(this).next().next('label').remove();
                }
            });
            $('#EmployeeDepartmentId').change(function() {
                if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
                    $(this).next().next('label').remove();
                }
            });
            $('#EmployeeDesignationId').change(function() {
                if ($(this).val() != -1 && $(this).next().next('label').hasClass("error")) {
                    $(this).next().next('label').remove();
                }
            }); 

        });
    </script>
