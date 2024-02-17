<script type="text/javascript" src="<?php echo Configure::read('OnlyofficePath');?>"></script>
<style type="text/css">
    .onlyofficediv{
        width: 100%;
        height: 924px;
        display: block;
        float: left;        
    }
</style>


<style type="text/css">
    .box-body{background: #fff;}
    .box-footer{background: #f5f5f5;}
    .cke_contents{ height: 400px !important}
    ul{ padding: 0px !important}	
</style>
<div id="clauses_ajax">
    <div class="row">   
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo $clause['Clause']['title'];?></h3>
                </div>
                <div class="box-body">
                    <?php echo nl2br($clause['Clause']['details']) ?> 

                    <?php if($clause['additional_details']){ ?>                  
                        <h3 class="box-title">Notes</h3><?php echo $clause['Clause']['additional_details'] ?>
                    <?php } ?>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <?php echo $this->Html->link('<i class="fa fa-edit "></i>',array('controller'=>'clauses','action'=>'edit',$clause['Clause']['id']),array('class'=>'btn btn-sm btn-default','escape'=>false,'target'=>'_blank'))?>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <?php if($qcDocuments){ ?> 
        <div class="row">
            <div class="col-md-12">        
                
                <div id="tabs"> 
                    <ul>
                        <?php
                        if ($qcDocuments) {
                            $x = 0;
                            foreach ($qcDocuments as $qcDocument):
                                ?>
                                <li><?php echo $this->Html->link($qcDocument['QcDocument']['name'],array('controller'=>'qc_documents','action'=>'mini_view',$qcDocument['QcDocument']['id'])); ?></li> 
                                <?php
                                $x++;
                            endforeach;
                        } else {

                        } ?> 
                        <li><?php echo $this->Html->image('indicator.gif', array('id' => 'busy-indicator','class'=>'pull-right')); ?></li>
                    </ul>
                </div>
            </div>
            <div id="standards_tab_ajax"></div></div>
            <script>
              $(function() {
                $( "#tabs" ).tabs({
                  beforeLoad: function( event, ui ) {
                    ui.jqXHR.error(function() {
                      ui.panel.html(
                        "Error Loading ... " +
                        "Please contact administrator." );
                  });
                }
            });
            });
        </script>
        
    </div>
<?php } ?>

</div>

<?php if($tables){ ?> 
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Custom Tables</h3>
                </div>
                <div class="box-body">
                    <div class="btn-group">
                        <?php foreach ($tables as $customTable): ?>
                            <div class="btn btn-sm btn-default">
                                <?php echo $this->Html->link($customTable['CustomTable']['name'],array(
                                    'controller'=>$customTable['CustomTable']['table_name'],
                                    'action'=>'index',
                                    'custom_table_id'=>$customTable['CustomTable']['id'],
                                    'qc_document_id'=>$customTable['CustomTable']['qc_document_id'],
                                    'process_id'=>$customTable['CustomTable']['process_id'],
                                ));?>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>            
            </div>
        </div>
    </div>    
<?php } ?>

</div>
<script type="text/javascript">$("#busy-indicator-table").hide()</script>
