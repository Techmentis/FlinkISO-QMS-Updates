<style type="text/css">.fa-dark.fa{color: #5c5b5b;}
</style>
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
    
    if($this->request->controller != 'custom_tables'){
        echo $this->Html->link('<i class="fa fa-plus"></i>',array('action'=>'add',
            'qc_document_id' => $this->request->params['named']['qc_document_id'],
            'custom_table_id' => $this->request->params['named']['custom_table_id'],
            'process_id' => $this->request->params['named']['process_id'],
            'timestamp'=>date('ymdhis')
        ),array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
        'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Add'
        ));         
    }else{
        if($this->action == 'recreate_child' && isset($customTable['ParentTable']['id'])){
            echo $this->Html->link('<i class="fa fa-arrow-up"></i>',array('action'=>'view',
                $customTable['ParentTable']['id'],                
                'timestamp'=>date('ymdhis')
            ),array('class'=>'tooltip1 btn btn-app btn-sm btn-default','escape'=>false,
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Add'
            )); 
        }
    }

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
        'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Ubnpublished/ Pending Approvals'
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

    // echo $this->Html->link('<i class="fa fa-chain"></i>',array('controller'=>'approval_processes','action'=>'add','controller_name'=>Inflector::classify($this->request->controller), 'timestamp'=>date('ymdhis'),'custom_table_id'=>$this->request->params['named']['custom_table_id']),array('escape'=>false,'class'=>'tooltip1 btn btn-app btn-sm btn-default','data-toggle'=>'tooltip', 'data-trigger'=>'hover','data-placement'=>'bottom', 'title'=> 'Add Auto Approval Process'));    
}


if(($this->action == 'index' || $this->action == 'advance_search' || $this->action == 'quick_search') && $this->request->controller != 'usage_details' && $this->request->controller != 'custom_tables'  && $this->request->controller != 'invoices' && ($this->Session->read('User.is_mt') == true || $this->Session->read('User.is_hod') == true)) {

    echo $this->Html->link('<i class="fa fa-check-square"></i>',"#",
        array('class'=>'tooltip1 btn btn-app btn-sm btn-default fa-dark ','escape'=>false,
            'onClick'=>'selectaddtrs()',
            'data-toggle'=>'tooltip', 'data-trigger'=>'hover', 'data-placement'=>'bottom', 'title'=> 'Select All'
        ));
    if(!in_array($this->request->controller, array('qc_documents','custom_tables','processes','standards'))){
    echo $this->Form->create(Inflector::classify($this->request->controller),array('action'=>'bulk_delete/custom_table_id:'.$this->request->params['named']['custom_table_id'].'/qc_document_id:'.$this->request->params['named']['qc_document_id'], 'style'=>'display:inline'),array('class'=>'in-line pull-left','style'=>'display:inline'));    
        
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
            echo $this->Form->input('src',array('class'=>'form-control','id'=>'quick_src_button', 'autocomplete'=>'off',  'label'=>false,'placeholder'=>'Quick search...','style'=>'margin-top: -12px'));
            ?>
        </div>
    <?php } } ?>
</div>
</div>
<div id="srcdivhideshow" class="hidden">
    <div class="row">
    <?php
        if($this->action == 'index' || $this->action == 'quick_search'){
            echo $this->Form->create(Inflector::classify($this->request->controller),array('action'=>'index','type'=>'get','default' => false,'id'=>'indexsort',),array('class'=>'form','role'=>'form'));
            if(isset($sortingFields) && is_array($sortingFields) && !empty($sortingFields)){
                foreach($sortingFields as $sortmodel => $sortingField){
                    $checkisset = Inflector::pluralize( Inflector::variable($sortmodel));
                    if(isset($this->viewVars[$checkisset]) && !empty($this->viewVars[$checkisset])){
                        echo "<div class='col-md-3'>" . $this->Form->input($sortingField, 
                            array(
                                'default'=>$this->request->params['named'][$sortingField],
                                'class'=>'form-control no-margin no-padding','div'=>false, 'id'=>false, 'label'=>array('class'=>'no-margin no-padding')))."</div>";
                    }
                }
                $strict = isset($this->request->params['named']['strict']) ? $this->request->params['named']['strict'] : 0;
                echo "<div class='col-md-3' ><div class='pull-left'>".$this->Form->input('strict',array('class'=>'', 'type'=>'radio', 'default'=>$strict,'options'=>array(0=>'Yes',1=>'No'))). "</div><div class='pull-right'><br />". $this->Form->submit('Go',array('class'=>'btn btn-sm btn-info','style'=>'margin-top:8px'))."</div></div>";
                
            }
            echo $this->Form->end();
        }    
        ?>
        <div class="col-md-12"><hr /></div>
    </div>
</div>
<?php 

$str = '';
$named = isset($this->request->params['named']['search']) ? $this->request->params['named']['search'] : array();
if(is_array($named)){
    foreach($named as $name => $name_value){
        $str .= $name .':' . trim($name_value) .'/';
    }
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
    
    $().ready(function(){
        var quickSearchContext = [];
        <?php if(isset($this->request->params['named']['custom_table_id'])) { ?>
            quickSearchContext.push('custom_table_id:<?php echo rawurlencode($this->request->params['named']['custom_table_id']); ?>');
        <?php } ?>
        <?php if(isset($this->request->params['named']['qc_document_id'])) { ?>
            quickSearchContext.push('qc_document_id:<?php echo rawurlencode($this->request->params['named']['qc_document_id']); ?>');
        <?php } ?>
        <?php if(isset($this->request->params['named']['process_id'])) { ?>
            quickSearchContext.push('process_id:<?php echo rawurlencode($this->request->params['named']['process_id']); ?>');
        <?php } ?>

        function selectedQuickSearchOptions(){
            var options = [];
            $('#indexsort').serializeArray().forEach(function(field){
                if(field.value !== '' && field.value !== '-1'){
                    options.push({name: field.name, value: field.value});
                }
            });
            return options;
        }

        function quickSearchUrl(search, options){
            var action = $('#indexsort').attr('action').replace(/\/$/, '');
            if(!/\/index$/.test(action)) action += '/index';
            var hasDropdownOption = options.some(function(field){
                return field.name !== 'strict';
            });
            if(search === '' && !hasDropdownOption){
                return action + '/timestamp:' + new Date().getTime();
            }

            var segments = quickSearchContext.slice(0);
            options.forEach(function(field){
                segments.push(encodeURIComponent(field.name) + ':' + encodeURIComponent(field.value));
            });
            segments.push('search:' + encodeURIComponent(search));
            segments.push('timestamp:' + new Date().getTime());
            return action + '/' + segments.join('/');
        }

        function loadQuickSearchResults(requestUrl){
            var searchInput = $('#quick_src_button');
            var search = $.trim(searchInput.val());
            var options = selectedQuickSearchOptions();
            var keepFocus = searchInput.is(':focus');

            if(search.length === 1) return;

            if(window.navHeaderSearchRequest){
                window.navHeaderSearchRequest.abort();
            }
            $('#busy-indicator').show();
            window.navHeaderSearchRequest = $.ajax({
                url: requestUrl || quickSearchUrl(search, options),
                type: 'GET',
                success: function(response){
                    var responseMain = $('<div>').append($.parseHTML(response, document, false)).find('#main').first();
                    if(!responseMain.length) return;

                    $('#main').html(responseMain.html());
                    $('#quick_src_button').val(search);
                    options.forEach(function(field){
                        $('#indexsort [name="' + field.name + '"]').val(field.value);
                    });

                    if($.fn.chosen){
                        $('#indexsort select').chosen();
                    }
                    if(options.some(function(field){ return field.name !== 'strict'; })){
                        $('#srcdivhideshow').removeClass('hidden');
                    }
                    if(keepFocus){
                        var refreshedInput = $('#quick_src_button').focus().get(0);
                        if(refreshedInput && refreshedInput.setSelectionRange){
                            refreshedInput.setSelectionRange(search.length, search.length);
                        }
                    }
                },
                complete: function(){
                    $('#busy-indicator').hide();
                    window.navHeaderSearchRequest = null;
                }
            });
        }

        $(document)
            .off('focus.navHeaderQuickSearch', '#quick_src_button')
            .on('focus.navHeaderQuickSearch', '#quick_src_button', function(){
                $('#srcdivhideshow').removeClass('hidden', 200);
            })
            .off('input.navHeaderQuickSearch', '#quick_src_button')
            .on('input.navHeaderQuickSearch', '#quick_src_button', function(){
                clearTimeout(window.navHeaderSearchTimer);
                window.navHeaderSearchTimer = setTimeout(loadQuickSearchResults, 300);
            })
            .off('change.navHeaderQuickSearch', '#indexsort select, #indexsort input[type="radio"]')
            .on('change.navHeaderQuickSearch', '#indexsort select, #indexsort input[type="radio"]', function(){
                clearTimeout(window.navHeaderSearchTimer);
                window.navHeaderSearchTimer = setTimeout(loadQuickSearchResults, 100);
            })
            .off('submit.navHeaderQuickSearch', '#indexsort')
            .on('submit.navHeaderQuickSearch', '#indexsort', function(event){
                event.preventDefault();
                clearTimeout(window.navHeaderSearchTimer);
                loadQuickSearchResults();
            })
            .off('click.navHeaderQuickSearch', '#main table.index th a, #main .pagination a')
            .on('click.navHeaderQuickSearch', '#main table.index th a, #main .pagination a', function(event){
                var search = $.trim($('#quick_src_button').val());
                var options = selectedQuickSearchOptions();
                var hasDropdownOption = options.some(function(field){ return field.name !== 'strict'; });
                if(search === '' && !hasDropdownOption) return;

                event.preventDefault();
                var url = $(this).attr('href').replace(/\/$/, '');
                if(search !== '' && url.indexOf('/search:') === -1){
                    url += '/search:' + encodeURIComponent(search);
                }
                loadQuickSearchResults(url);
            });
    }) ;      

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
        <?php if($this->request->controller == 'custom_tables'){ ?>
            $("#quick_src_button").on('change', function(){
                $("#quick_src_button").val($("#quick_src_button").val().replace(/ /g,"+"));
                $('#main').load("<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/quick_search/<?php echo $str;?>/search:" + $("#quick_src_button").val());
                return false;
            });
        <?php } ?>                
    });    
</script>
<div id="pdf_open"></div>
<div id="history_open"></div>
