<style type="text/css">.fa-dark.fa{color: #5c5b5b;}</style>
<?php
if ($unpublished == null)
    $unpublished = 0;
if ($count == null)
    $count = 0;
if ($published == null)
    $published = 0;
if ($deleted == null)
    $deleted = 0;
?>
<?php
unset($postData['options']['sr_no']);
unset($postData['options']['user_access']);
unset($postData['options']['soft_delete']);
unset($postData['options']['publish']);
?>
<div class="row"> 
    <div class="col-md-12"><h4><small><?php echo $this->element('breadcrumbs',array('defaultTitle'=>$postData['defaultTitle'])); ?></small>
    <?php 
    if($postData["friendlyName"])echo h($postData["friendlyName"]); 
    else echo h($postData["pluralHumanName"]); 
    
    if($postData['defaultTitle']){
        echo "<small> /</small> ". $postData['defaultTitle'];
    }
    ?>
</h4>
</div>
<div class="col-md-8 col-sm-12">
    <?php     
    echo $this->Html->link('<i class="fa fa-table"></i>',array('action'=>'index',
        'qc_document_id'=>$this->request->params['named']['qc_document_id'],
        'custom_table_id'=>$this->request->params['named']['custom_table_id'],
        'process_id' => $this->request->params['named']['process_id'],
        'timestamp'=>date('ymdhis')
    ),
    array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
        'data-toggle'=>'tooltip', 'data-trigger'=>'hover',  'data-placement'=>'bottom', 'title'=> 'Index'
    ));    
    
    echo $this->Html->link('<i class="fa fa-plus"></i>',array('action'=>'add',
        'qc_document_id' => $this->request->params['named']['qc_document_id'],
        'custom_table_id' => $this->request->params['named']['custom_table_id'],
        'process_id' => $this->request->params['named']['process_id'],
        'timestamp'=>date('ymdhis')
    ),array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
    'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Add'
)); 

    if($this->request->controller != 'custom_tables' && $this->action == 'index') {
        
     echo $this->Html->link('<l class="fa fa-check-square-o"></i>',array('action'=>'index','published'=>1,
        'qc_document_id' => $this->request->params['named']['qc_document_id'],
        'custom_table_id' => $this->request->params['named']['custom_table_id'],
        'process_id' => $this->request->params['named']['process_id'],
        'timestamp'=>date('ymdhis')
    ),
     array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
        'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Published'
    ));
     echo $this->Html->link('<l class="fa fa-minus-square-o"></i>',array('action'=>'index','published'=>0,
        'qc_document_id' => $this->request->params['named']['qc_document_id'],
        'custom_table_id' => $this->request->params['named']['custom_table_id'],
        'process_id' => $this->request->params['named']['process_id'],
        'timestamp'=>date('ymdhis')
    ),
     array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
        'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Ubnpublished'
    ));

 }
 
 if($this->request->controller == 'standards'){
    echo $this->Html->link('<i class="fa fa-trash-o"></i>',array('action'=>'index','soft_delete'=>1,'timestamp'=>date('ymdhis')),
        array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Deleted'
        ));    
}


if($this->action == 'view' || $this->action == 'user_access') {
    if($this->request->controller != 'custom_tables') {                    
        echo $this->Html->link('<i class="fa fa-edit"></i>',array('action'=>'edit',$this->request->params['pass'][0],
            'qc_document_id'=>$this->request->params['named']['qc_document_id'],
            'custom_table_id'=>$this->request->params['named']['custom_table_id'],
            'process_id' => $this->request->params['named']['process_id'],
            'compare' => 'yes',
            'timestamp'=>date('ymdhis')
        ),
        array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover',  'data-placement'=>'bottom', 'title'=> 'Edit Record'
        ));
    }

    $skippdfarray = array('branches','departments','designations','departments','employees','users');

    if($this->request->controller != 'qc_documents' && $this->request->controller != 'custom_tables' && !in_array($this->request->controller,$skippdfarray)) {
        echo $this->Html->link('<i class="fa fa-file-pdf-o"></i>','#',
            array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
                'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'onClick'=>'openpdf()' , 'data-placement'=>'bottom', 'title'=> 'Download PDF'
            ));
    }

    if($this->request->controller == 'qc_documents') {                
        echo $this->Html->link('<i class="fa fa-file-pdf-o"></i>','#',
            array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
                'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'onClick'=>'openpdf()' , 'data-placement'=>'bottom', 'title'=> 'Download PDF'
            ));
    }            
}

if($this->action == 'edit' || $this->action == 'recreate' || $this->action == 'recreate_child') { 
    echo $this->Html->link('<i class="fa fa-desktop"></i>',array('action'=>'view',$this->request->params['pass'][0],
        'qc_document_id'=>$this->request->params['named']['qc_document_id'],
        'custom_table_id'=>$this->request->params['named']['custom_table_id'],
        'process_id' => $this->request->params['named']['process_id'],
        'compare' => 'yes',
        'timestamp'=>date('ymdhis')
    ),
    array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
        'data-toggle'=>'tooltip', 'data-trigger'=>'hover',  'data-placement'=>'bottom', 'title'=> 'View Record'
    ));
}


if(($this->action == 'index' || $this->action == 'advance_search' || $this->action == 'quick_search') && $this->request->controller != 'usage_details' && $this->request->controller != 'custom_tables'  && $this->request->controller != 'invoices' && ($this->Session->read('User.is_mt') == true || $this->Session->read('User.is_hod') == true)) {

    echo $this->Html->link('<i class="fa fa-check-square"></i>',"#",
        array('class'=>'tooltip1 btn btn-app btn-sm btn-default fa-dark ','escape'=>false,
            'onClick'=>'selectaddtrs()',
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Select All'
        ));
    if(!in_array($this->request->controller, array('qc_documents','custom_tables','processes','standards'))){

    echo $this->Form->create(Inflector::classify($this->request->controller),array('action'=>'bulk_delete','style'=>'display:inline'),array('class'=>'in-line pull-left','style'=>'display:inline'));    
        echo '<label for="bulkDeleteSubmit" class="btn btn-app btn-default"><i class="fa fa-trash-o text-danger"></i></label>';
        echo $this->Form->submit('&nbsp;',
            array(
                'id'=>'bulkDeleteSubmit',
                'onClick'=>'bulkdelete()',
                'div' => false, 
                'class'=>'hide',                        
            )
        );
        echo $this->Form->hidden('bulk_delete_ids',array('id'=>'bulk_delete_ids'));
        echo $this->Form->end();
    }    
}

    echo $this->Html->link('<i class="fa fa-search"></i>','#',
    array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
        'data-toggle'=>'control-sidebar', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Search', 'id'=>'ad_src'
    ));

    if($this->action == 'view' && isset($this->request->params['named']['custom_table_id'])){
        echo $this->Html->link('<i class="fa fa-clock-o"></i>',
            // array('controller'=>'document_downloads','action'=>'index','custom_table_id'=>$this->request->params['named']['custom_table_id'],'record_id'=>$this->request->params['pass'][0]),
            '#',
            array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
            'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Download History',
            'onClick'=>'historyopen(\''.$this->request->params['named']['custom_table_id'].'\',\''. $this->request->params['pass'][0].'\')'
        ));
    }

?>        
</div>
<div class="col-md-4 col-sm-12">
    <?php if(!in_array($this->request->controller, array('standards'))){ ?>
    <?php if(($this->action == 'index' || $this->action == 'quick_search' ) && $this->request->controller != 'usage_details'  && $this->request->controller != 'invoices') { ?>
        <div class=" btn-group" style="width:100%;">
            <?php 
            echo $this->Form->input('src',array('class'=>'form-control','id'=>'quick_src_button', 'label'=>false,'placeholder'=>'Add Search query and press Tab.','style'=>'margin-top: -12px'));                        
            ?>
        </div>
    <?php } } ?>
</div>
</div>

<?php 

$named = $this->request->params['named']['search'];
foreach($named as $name => $name_value){
    $str .= $name .':' . trim($name_value) .'/';
}
$str .= 'timestamp:'.date('ymdhis');    
?>

<script type="text/javascript">  
    <?php if(($this->action == 'index' || $this->action == 'advance_search'  || $this->action == 'quick_search') && ($this->Session->read('User.is_mt') == true || $this->Session->read('User.is_hod') == true)) { ?>
        function selectaddtrs(){
            $('form tr').each(function() {   
                $(this).toggleClass('warning');
            });
        }

        function addrec(id){        
            $("#"+id+"_tr").toggleClass('warning');     
        }

        function bulkdelete(){
            var str = '';
            $('.on_page_src').each(function() {        
                if($(this).attr('class') == 'on_page_src warning'){
                    str = str + "," + this.id;
                }            
            });
            $("#bulk_delete_ids").val(str);
            return false;
        }
    <?php } ?>

    function openpdf(){
        <?php if($this->request->controller == 'qc_documents' ){ ?>
            $("#pdf_open").load("<?php echo Router::url('/', true); ?>document_downloads/add/qc_document_id:<?php echo $this->request->params['pass'][0];?>/controller_name:<?php echo $this->request->controller;?>");
        <?php }else{ ?>
            $("#pdf_open").load("<?php echo Router::url('/', true); ?>document_downloads/add/custom_table_id:<?php echo $this->request->params['named']['custom_table_id'];?>/qc_document_id:<?php echo $this->request->params['named']['qc_document_id'];?>/process_id:<?php echo $this->request->params['named']['process_id'];?>/<?php echo $this->request->params['pass'][0];?>/controller_name:<?php echo $this->request->controller;?>");
        <?php }?>        
        
    }

    function historyopen(c,id){
        $("#history_open").load("<?php echo Router::url('/', true); ?>document_downloads/index/custom_table_id:"+c+"/record_id:"+id);
    }

    $(document).ready(function(){
        $("#ad_src").on('click',function(){
            $("#ad_src_result").load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/advance_search/custom_table_id:<?php echo $this->request->params['named']['custom_table_id'];?>/qc_document_id:<?php echo $this->request->params['named']['qc_document_id'];?>/process_id:<?php echo $this->request->params['named']['process_id'];?>");
        });
        
        $("#quick_src_button").on('change', function(){
            $("#quick_src_button").val($("#quick_src_button").val().replace(/ /g,"+"));
            $('#main').load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/quick_search/<?php echo $str;?>/search:" + $("#quick_src_button").val());
            return false;
        });
    });
        

    </script>
    
    <div id="pdf_open"></div>
    <div id="history_open"></div>



