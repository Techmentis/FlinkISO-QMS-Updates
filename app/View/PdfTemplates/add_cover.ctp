<?php echo $this->Session->flash();?>   
<?php   
echo $this->Html->css(array(
    'prism',
    'code-input-main/prism-line-numbers.min',
    'code-input-main/code-input.min',       
));
echo $this->fetch('css');

echo $this->Html->script(array( 
    'code-input-main/code-input.min',   
    'code-input-main/plugins/indent.min',               
    'code-input-main/plugins/prism-core.min',
    'code-input-main/plugins/prism-autoloader.min',         
    'code-input-main/plugins/prism-line-numbers.min',

));
echo $this->fetch('script');


?>

<?php echo $this->element('nav-header-lists',array('postData'=>array('pluralHumanName'=>'PDF Templates','modelClass'=>'PdfTemplate','options'=>array("sr_no"=>"Sr No","name"=>"Name"),'pluralVar'=>'pdfDepartments'))); ?>
<style type="text/css">
    .txtfld{width: 96%; border: none; margin: 5px 0}
    .childtables ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
    .childtables li { margin: 5px; padding: 5px; width: 150px; border:1px solid #ccc; width:100% }  
    #return_value_for_dropdown{height: 650px}
</style>

<?php echo $this->Form->create('PdfTemplate',array('action'=>'add_cover/CoverTemplate'),array('class'=>'form-control')) ?>
<div class="main">
    <div class="row">
        <?php if($this->request->params['pass'][0]){ ?>        
            <div class="col-md-12">
                <?php   
                $file_path = Configure::read('files') . DS. 'pdf_template' . DS . 'cover'. DS. 'template.docx';
                $file = 'template.docx';
                $documentType = 'word';

                echo $this->element('onlyoffice',array(
                    'url'=>$url,
                    'placeholderid'=>'covertemp',
                    'panel_title'=>'Template Viewer',
                    'mode'=>'edit',
                    'path'=>$file_path,
                    'file'=>$file,
                    'filetype'=>'docx',
                    'documentType'=>$documentType,
                    'userid'=>$this->Session->read('User.id'),
                    'username'=>$this->Session->read('User.username'),
                    'preparedby'=>$this->Session->read('User.name'),
                    'filekey'=>$key,
                    'record_id'=>$cover['PdfTemplate']['id'],
                    'company_id'=>$this->Session->read('User.company_id'),
                    'controller'=>'pdf_templates',
                    // 'last_saved'=>date('Y-m-d H:i:s'),
                    // 'last_modified' => date('Y-m-d H:i:s'),
                    // 'version_keys' => $customTable['CustomTable']['version_keys'],
                    // 'version' => $customTable['CustomTable']['version'],
                    // 'versions' => $customTable['CustomTable']['versions'],
                    'docid'=> $cover['PdfTemplate']['id'],
                    'cover'=>true
                ));

                ?>  
                <p><strong>View Only. You can edit this template from edit section, after saving the template.</strong></p>
                <p>Do not add header and footer to the document. Header & Footer is generated seperatly and added in runtime</p>
            </div>  
            <div class="col-md-12">Available Fields
                <div class="row">
                    <?php if(!$customTable){ ?>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading"><h5>QC Document: <?php echo $qcDocument['QcDocument']['title'];?></h5></div>
                                <div class="panel-body">                            
                                    <?php
                                    $qcFields = array(
                                        "name",
                                        "title",
                                        "document_number",
                                        "document_number",
                                        "issue_number",
                                        "date_of_next_issue",
                                        "date_of_issue",
                                        "effective_from_date",
                                        "revision_number",
                                        "date_of_review",
                                        "revision_date"
                                    );
                                    foreach($qcFields as $qcField){
                                        $jid = 'QcDocument'.Inflector::camelize($qcField);
                                        echo $this->Form->input('data.PdfTempate.QcDocument.'.$qcField,array('type'=>'text', 'id'=>$jid, 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["QcDocument"]["'.$qcField.'"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.$jid.'\')"></i>';
                                    }
                                    echo $this->Form->input('data.PdfTempate.Standard.name',array('type'=>'text', 'id'=>'PdfTemplateStandardName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["Standard"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplateStandardName\')"></i>';

                                    echo $this->Form->input('data.PdfTempate.Clause.name',array('type'=>'text', 'id'=>'PdfTemplateClausedName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["Clause"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplateClausedName\')"></i>';

                                    echo $this->Form->input('data.PdfTempate.PreparedBy.name',array('type'=>'text', 'id'=>'PdfTemplatePreparedByName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["PreparedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplatePreparedByName\')"></i>';

                                    echo $this->Form->input('data.PdfTempate.ApprovedBy.name',array('type'=>'text', 'id'=>'PdfTemplateApprovedByName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["ApprovedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplateApprovedByName\')"></i>';

                                    echo $this->Form->input('data.PdfTempate.IssuedBy.name',array('type'=>'text', 'id'=>'PdfTemplateIssuedByByName', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$qcDocument["IssuedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PdfTemplateIssuedByByName\')"></i>';
                                    ?>                          
                                </div>
                                <div class="panel-footer"><strong>Note:</strong> You can fetch these values anywhere in a template.</div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if($customTable){ ?>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading"><h5>Main Table: <?php echo $customTable['CustomTable']['table_name'];?></h5></div>
                                <div class="panel-body">                            
                                    <?php
                                    foreach(json_decode($customTable['CustomTable']['fields'],true) as $mainField){
                                        if($mainField['linked_to'] == -1){
                                            echo  $this->Form->input(Inflector::classify($customTable['CustomTable']['table_name']).'.'.$mainField['field_name'],array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["'. Inflector::classify($customTable['CustomTable']['table_name']).'"]["'.$mainField['field_name'].'"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.Inflector::classify($customTable['CustomTable']['table_name']).Inflector::classify($mainField['field_name']).'\')"></i>';
                                        }else{
                                            $defaultField = $this->requestAction(array('action'=> 'get_default',Inflector::classify($mainField['linked_to'])));
                                            echo  $this->Form->input(Inflector::classify($mainField['field_name']).'.'.$defaultField,array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["'. Inflector::classify($mainField['field_name']).'"]["'.$defaultField.'"]')) .'<i class="fa fa-copy" onclick="myFunction(\''.Inflector::classify($mainField['field_name']).Inflector::classify($defaultField).'\')"></i>';
                                        }                               
                                    }
                                    echo  $this->Form->input('PreparedBy.name',array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["PreparedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'PreparedByName\')"></i>';
                            // echo  $this->Form->input('ApprovedBy.name',array('type'=>'text', 'class'=>'txtfld','div'=>false,'label'=>false, 'default'=>'$record["ApprovedBy"]["name"]')) .'<i class="fa fa-copy" onclick="myFunction(\'ApprovedByName\')"></i>';
                                    ?>                          
                                </div>
                                <div class="panel-footer"><strong>Note:</strong> You can fetch these values anywhere in a template.</div>
                            </div>
                        </div>              

                        <?php 
                        $c = 0;
                        foreach($childTables as $childTable){ ?>

                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><h5>Child Table: <?php echo $childTable['CustomTable']['table_name'];?></h5></div>
                                    <div class="panel-body childtables">
                                        <ul id="<?php echo $childTable['CustomTable']['table_name'];?>">                            
                                            <?php foreach(json_decode($childTable['CustomTable']['fields'],true) as $mailField){
                                                echo  '<li class="ui-state-default">'.$mailField['field_name'] .'</li>';
                                            }                                   
                                            ?>                          
                                        </ul>
                                    </div>
                                    <div class="panel-footer"><strong>Note:</strong> This is a looped table. Values of this table can not be fetched outside this table.
                                        <?php echo $this->Form->hidden('PdfTemplate.child_table_fields.'.$c,array('type'=>'text'));?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $( "#<?php echo $childTable['CustomTable']['table_name'];?>" ).sortable({
                              revert: true,
                              stop:function(){
                                var str = ''
                                $("#<?php echo $childTable['CustomTable']['table_name'];?> li").each(function(){
                                    str = str + this.innerHTML + ',';
                                });
                                <?php 
                                $tbl = '{"id":"'.$childTable['CustomTable']['id'].'","name":"'.$childTable['CustomTable']['table_name'].'"';
                                ?>

                                $("#PdfTemplateChildTableFields<?php echo $c;?>").val('<?php echo $tbl;?>,"fields":"'+str+'"}');
                            }
                        });
                            
                            $().ready(function(){
                                var str = ''
                                $("#<?php echo $childTable['CustomTable']['table_name'];?> li").each(function(){
                                    str = str + this.innerHTML + ',';
                                });
                                <?php 
                                $tbl = '{"id":"'.$childTable['CustomTable']['id'].'","name":"'.$childTable['CustomTable']['table_name'].'"';
                                ?>

                                $("#PdfTemplateChildTableFields<?php echo $c;?>").val('<?php echo $tbl;?>,"fields":"'+str+'"}');
                            })                  
                        </script>
                        <?php $c++; } 
                        ?>
                    <?php } ?>
                </div>

                <div class="col-md-12">
                    <?php 
                    echo $this->Form->submit('Save Template',array('class'=>'btn btn-lg btn-info pull-right'));
                    echo $this->Form->end();

                }
                ?>  
            </div>
        </div>
    </div>
    <script type="text/javascript">

        $("#PdfTemplateCustomTableId").on('change',function(){
            location.replace('<?php echo Router::url('/', true); ?>pdf_templates/add/'+$("#PdfTemplateCustomTableId").val());
        })

        function show(val){
            if(val == 1){
                $(".con").hide()
                $("#PdfTemplateMarginTop").val(0);
            }else{
                $(".con").show()
                $("#PdfTemplateMarginTop").val(20);
            }
        }

        function myFunction(id) {
            let copyGfGText = document.getElementById(id);
            copyGfGText.select();
            document.execCommand("copy");        
        }
    </script>