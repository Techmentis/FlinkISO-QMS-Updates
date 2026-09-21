<div id="customTriggers_ajax">
    <?php echo $this->Html->script(array('jquery.validate.min', 'jquery-form.min')); ?>
    <?php echo $this->fetch('script'); ?>
    <style type="text/css">
        .chosen-container, .chosen-container-multi{width: 100% !important;}
        #field-condition{padding-top: 10px;}
    </style>

    <script>
        $.validator.setDefaults({
            ignore: null,
            errorPlacement: function(error, element) {
                if(element['context']['className'] == 'form-control select error'){
                    $(element).next('.chosen-container').addClass('error');
                }else if(element['context']['className'] == 'radio error'){
                    $(element).next('legend').addClass('error');
                }else{
                    $(element).after(error);
                }
            },
            submitHandler: function(form) {
                $("#CustomTriggerAddForm").ajaxSubmit({
                    url: "<?php echo Router::url('/', true); ?>custom_triggers/add/custom_table_id:<?php echo h($this->request->params['named']['custom_table_id']); ?>",
                    type: 'POST',
                    beforeSend: function(){
                        $("#trigger_submit_id").prop("disabled",true);
                        $("#trigger_submit_indicator").show();
                    },
                    success: function(response) {
                        $('#customTriggers_ajax').first().replaceWith(response);
                    },
                    error: function(request, status, error) {
                        $("#trigger_submit_id").removeAttr("disabled");
                        $("#trigger_submit_indicator").hide();
                        alert('Action failed!');
                    }
                });
            }
        });
        $().ready(function() {
            var triggerForm = $('#CustomTriggerAddForm');

            triggerForm.find('select').each(function(){
                var select = $(this);
                if(select.data('chosen')) select.trigger('chosen:updated');
                else select.chosen({width: '100%'});
            });
            function toggleCondition(){
                var fieldEvent = $("#CustomTriggerEventName").val() === 'field.changed';
                var dateEvent = $("#CustomTriggerEventName").val() === 'date.reminder';
                $("#field-condition").toggle(fieldEvent);
                $("#date-condition").toggle(dateEvent);
                $("#CustomTriggerFieldName").prop('required', fieldEvent);
                $("#CustomTriggerDateField").prop('required', dateEvent);
            }
            $("#CustomTriggerEventName").on('change', toggleCondition);
            toggleCondition();
            $("#CustomTriggerConditionOperator").on('change', function(){
                var needsValue = $.inArray($(this).val(), ['equals', 'not_equals']) !== -1;
                $("#get_data").toggle(needsValue);
            }).trigger('change');

            $("#CustomTriggerFieldName").on('change',function(){
                $.ajax({
                    url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/get_data/"+$("#CustomTriggerFieldName").val()+"/<?php echo $this->request->params['named']['custom_table_id'];?>",
                    success: function(data, result) {
                        $("#get_data").html(data);
                        $('#CustomTriggerAddForm').validate({
                            rules: {
                                "data[CustomTrigger][changed_field_value]": {
                                    greaterThanZero: true,
                                }
                            }
                        });
                    },
                });
            });
            jQuery.validator.addMethod("greaterThanZero", function(value, element) {
                return this.optional(element) || (parseFloat(value) !== -1);
            }, "Please select the value");

            if(triggerForm.length){
                triggerForm.validate();
            }
            $("#trigger_submit_indicator").hide();
            $('#customTriggers_ajax').on('click', '.custom-trigger-edit', function(event){
                event.preventDefault();
                var panel = $('#customTriggers_ajax').first();
                $.ajax({
                    url: $(this).attr('href'),
                    type: 'GET',
                    beforeSend: function(){
                        panel.html('<div class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading email trigger...</div>');
                    },
                    success: function(response){
                        panel.replaceWith(response);
                    },
                    error: function(){
                        panel.html('<div class="alert alert-danger">Unable to load the email trigger. Please refresh the tab and try again.</div>');
                    }
                });
            });
        });
    </script>
    <style type="text/css">
        .error, .error .chosen-container{
            border: 1px dotted red;
        }
    </style>

    <div class="customTriggers ">
        <?php echo $this->Session->flash();?>
        <div class="box box-default collapsed-box">
            <div class="box-header data-header" data-widget="collapse">
                <h3 class="box-title"><span class=""><i class="fa fa-bell-o"></i></span>  Email Triggers</h3>
                <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
            </div>
            <div class="box-body">
                <p class="text-right"><?php echo $this->Html->link('<i class="fa fa-list"></i> All rules', array('action'=>'index'), array('class'=>'btn btn-default btn-sm', 'escape'=>false)); ?> <?php echo $this->Html->link('<i class="fa fa-envelope"></i> Delivery log', array('action'=>'deliveries','custom_table_id'=>$this->request->params['named']['custom_table_id']), array('class'=>'btn btn-default btn-sm', 'escape'=>false)); ?></p>
                <div class="table-responsive" style="overflow:scroll;">
                    <table cellpadding="0" cellspacing="0" class="table table-hover">
                        <tr>
                            <th><?php echo __('When'); ?></th>
                            <th><?php echo __('Subject'); ?></th>
                            <th><?php echo __('Message'); ?></th>
                            <th></th>
                        </tr>
                        <?php if($customTriggers){ ?>
                            <?php foreach ($customTriggers as $customTrigger): ?>
                                <tr id="<?php echo $customTrigger['CustomTrigger']['id'].'-tr';?>">
                                <?php $rowEventName = !empty($customTrigger['CustomTrigger']['event_name']) ? $customTrigger['CustomTrigger']['event_name'] : ((int)$customTrigger['CustomTrigger']['action'] === 2 ? 'approval.excluded' : ''); ?>
                                <td><?php echo h(isset($eventNames[$rowEventName]) ? $eventNames[$rowEventName] : Inflector::humanize($rowEventName)); ?>&nbsp;</td>
                                <td><?php echo h($customTrigger['CustomTrigger']['name']); ?>&nbsp;</td>
                                <td><?php echo h($customTrigger['CustomTrigger']['message']); ?>&nbsp;</td>
                                <td class="text-right">
                                    <?php echo $this->Html->link('<i class="fa fa-edit"></i>',array('action'=>'edit',$customTrigger['CustomTrigger']['id'],'custom_table_id'=>$customTrigger['CustomTrigger']['custom_table_id']),array('id'=>$customTrigger['CustomTrigger']['id'].'-edit','class'=>'btn btn-sm btn-default custom-trigger-edit', 'escape'=>false, 'title'=>'Edit'));?>
                                    <?php echo $this->Form->postLink(!empty($customTrigger['CustomTrigger']['enabled']) ? '<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>', array('action'=>'toggle_enabled', $customTrigger['CustomTrigger']['id']), array('escape'=>false, 'class'=>'btn btn-sm btn-default', 'title'=>!empty($customTrigger['CustomTrigger']['enabled']) ? 'Disable' : 'Enable')); ?>
                                    <?php echo $this->Html->link('<i class="fa fa-trash-o"></i>','#',array('id'=>$customTrigger['CustomTrigger']['id'].'-del','class'=>'btn btn-sm btn-default','escape'=>false));?>

                                    <script type="text/javascript">
                                        $("#<?php echo $customTrigger['CustomTrigger']['id'];?>-del").on('click',function(){
                                            $.ajax({
                                                url: "<?php echo Router::url('/', true); ?>custom_triggers/delete_trigger",
                                                type: "POST",
                                                target: '#customTriggers_ajax',
                                                dataType: "json",
                                                contentType: "application/json; charset=utf-8",
                                                data: JSON.stringify({ id: '<?php echo $customTrigger['CustomTrigger']['id'];?>'}),
                                                beforeSend: function( xhr ) {
                                                },
                                                success: function (result) {
                                                    $("#<?php echo $customTrigger['CustomTrigger']['id'];?>-tr").remove();
                                                },
                                                error: function (err) {

                                                }
                                            });
                                        });
                                    </script>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php }else{ ?>
                        <tr><td colspan="4">No results found</td></tr>
                    <?php } ?>
                </table>
            </div>

            <hr />

            <div class="row">
                <div class="customTriggers form col-md-12">
                    <?php echo $this->Form->create('CustomTrigger',array('role'=>'form','class'=>'form')); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <?php echo "<div class='col-md-12'>".$this->Form->input('event_name',array('label'=>'When', 'options'=>$eventNames, 'default'=>'record.created', 'required'=>'required', 'class'=>'form-control')) . '</div>'; ?>
                                    </div>
                                    <?php if($fieldNames != null){ ?>
                                        <div class="row" id="field-condition">
                                            <?php
                                            echo "<div class='col-md-4'>".$this->Form->input('field_name',array('label'=>'Field', 'options'=>$fieldNames, 'empty'=>'Select field', 'class'=>'form-control')) . '</div>';
                                            echo "<div class='col-md-4'>".$this->Form->input('condition_operator',array('label'=>'Condition', 'options'=>$conditionOperators, 'default'=>'equals', 'class'=>'form-control')) . '</div>';
                                            echo "<div class='col-md-4' id='get_data'>".$this->Form->input('changed_field_value',array('label'=>'Value', 'class'=>'form-control','options'=>array())) . '</div>';
                                            ?>
                                        </div>
                                    <?php } ?>
                                    <?php if($dateFieldNames != null){ ?>
                                        <div class="row" id="date-condition">
                                            <?php
                                            echo "<div class='col-md-6'>".$this->Form->input('date_field',array('label'=>'Date field', 'options'=>$dateFieldNames, 'empty'=>'Select date field', 'class'=>'form-control')) . '</div>';
                                            echo "<div class='col-md-6'>".$this->Form->input('date_offset_days',array('label'=>'Days before date', 'type'=>'number', 'default'=>0, 'class'=>'form-control')) . '</div>';
                                            ?>
                                            <div class="col-md-12"><p class="help-block">Use 0 for the due date and a negative value for an overdue reminder.</p></div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-12"><br /><label>Send Email To</label></div>
                                <?php
                                if(!empty($notifyDepartments)){
                                    echo "<div class='col-md-6'>".$this->Form->input('notify_departments',array('class'=>'nd','default'=>0,'label'=>'Everyone in selected Department')) . '</div>';
                                }

                                if(!empty($notifyBranches)){
                                    echo "<div class='col-md-6'>".$this->Form->input('notify_branches',array('class'=>'nb','default'=>0,'label'=>'Everyone in selected Branches')) . '</div>';
                                }

                                if(!empty($notifyDesignations)){
                                    echo "<div class='col-md-12'>".$this->Form->input('notify_designations',array('class'=>'nb','default'=>0,'label'=>'Everyone with selected designation')) . '</div>';
                                }
                                echo "<div class='col-md-6'>".$this->Form->input('notify_admins',array('class'=>'admin','default'=>0, 'label'=>'Admins')) . '</div>';
                                echo "<div class='col-md-6'>".$this->Form->input('notify_hods',array('class'=>'hod','default'=>0,'label'=>'Department HoDs')) . '</div>';
                                echo "<div class='col-md-12'>".$this->Form->input('hod_departments',array('label'=>'Select Departments','multiple', 'options'=>$departments)) . '</div>';
                                ?>
                                <div class="col-md-12"><p><br /><strong>Note: </strong>If table has a department field, and if you chose Notify HoDs, HoD of that department will automaticaly receive the email. To send email to additional HoDs from different departments, chose thode departments from "Select Departments" field.</p></div>
                                <?php
                                echo "<div class='col-md-6'>".$this->Form->input('notify_user',array('label'=>'Employee/User field from this record', 'empty'=>'None', 'class'=>'form-control')) . '</div>';
                                echo "<div class='col-md-6'>".$this->Form->input('notify_users',array('class'=>'form-control','type'=>'select', 'multiple', 'options'=>$employees )) . '</div>';
                                // echo "<div class='col-md-6'>".$this->Form->input('exclude_actor',array('label'=>'Do not email the person who performed the action')) . '</div>';
                                echo "<div class='col-md-6 hidden'>".$this->Form->input('enabled',array('label'=>'Enabled', 'default'=>1)) . '</div>';
                                ?>
                                <div class="col-md-12"><h4>Delivery</h4></div>
                                <?php
                                echo "<div class='col-md-4'>".$this->Form->input('delivery_timing',array('options'=>array('immediate'=>'Immediately','delayed'=>'After a delay'), 'default'=>'immediate', 'class'=>'form-control')) . '</div>';
                                echo "<div class='col-md-4'>".$this->Form->input('delay_minutes',array('label'=>'Delay (minutes)', 'type'=>'number', 'min'=>0, 'default'=>0, 'class'=>'form-control')) . '</div>';
                                echo "<div class='col-md-4'>".$this->Form->input('cooldown_minutes',array('label'=>'Suppress repeats for (minutes)', 'type'=>'number', 'min'=>0, 'default'=>0, 'class'=>'form-control')) . '</div>';
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">

                                <?php
                                echo "<div class='col-md-12'>".$this->Form->hidden('custom_table_id',array('class'=>'form-control', 'style'=>'','default'=>$this->request->params['named']['custom_table_id'])) . '</div>';
                                echo "<div class='col-md-12'>".$this->Form->input('name',array('label'=>'Subject', 'required'=>'required', 'class'=>'form-control',)) . '</div>';


                                echo "<div class='col-md-12'>".$this->Form->input('message',array('required'=>'required', 'rows'=>18, 'label'=>'Message body to send to recipient', 'class'=>'form-control')) . '</div>';
                                echo "<div class='col-md-12'><p class='help-block'>Variables: <code>{{module.name}}</code>, <code>{{event.name}}</code>, <code>{{record.id}}</code>, <code>{{record.FIELD_NAME}}</code>, <code>{{changes.FIELD_NAME.old}}</code>, <code>{{changes.FIELD_NAME.new}}</code>, <code>{{actor.name}}</code>, <code>{{reminder.target_date}}</code>.</p></div>";
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <?php
                        // echo "<div class='col-md-4'>".$this->Form->input('if_edited',array('class'=>'',)) . '</div>';
                        // echo "<div class='col-md-4'>".$this->Form->input('if_publish',array('class'=>'',)) . '</div>';
                        // echo "<div class='col-md-4'>".$this->Form->input('if_approved',array('class'=>'',)) . '</div>';
                        // echo "<div class='col-md-4'>".$this->Form->input('if_soft_delete',array('class'=>'',)) . '</div>';

                        echo $this->Form->input('id');
                        echo $this->Form->hidden('History.pre_post_values', array('value'=>json_encode($this->data)));
                        echo $this->Form->input('branchid', array('type' => 'hidden', 'value' => $this->Session->read('User.branch_id')));
                        echo $this->Form->input('departmentid', array('type' => 'hidden', 'value' => $this->Session->read('User.department_id')));
                        echo $this->Form->input('master_list_of_format_id', array('type' => 'hidden', 'value' => $documentDetails['MasterListOfFormat']['id']));
                        ?>

                    </div>
                    <div class="">
                        <?php echo $this->Form->submit(__('Submit'), array('div' => false, 'style'=>'margin:10px 0', 'class' => 'btn btn-primary btn-success','id'=>'trigger_submit_id')); ?>
                        <?php echo $this->Html->image('indicator.gif', array('id' => 'trigger_submit_indicator')); ?>
                        <?php echo $this->Form->end(); ?>
                        <?php echo $this->Js->writeBuffer();?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
