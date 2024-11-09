<div id="ofcon<?php echo $filekey;?>">
    <style>
        .split, .load_version_document{border-bottom: 1px solid #ccc;}
        <?php if(isset($this->request->params['named']['compare']) && $this->request->params['named']['compare'] == 'yes'){ ?>
            .load_version_document{border-left: 4px solid #fff;}
        <?php }else{ ?>
            .load_version_document{border-top: 10px solid #fff;}
        <?php } ?>
    </style>
    <?php
    if(!$filekey)$filekey = $fileData['File']['new_file_key'];                
    $placeholderid = $filekey;
    $checkdatetime = date('Y-m-d H:i:s',strtotime("-30 seconds"));
    $currentdatetime = date('Y-m-d H:i:s');
    if($last_modified)$last_modified = date('Y-m-d H:i:s',strtotime($last_modified));

    if(
        ($last_saved == null || date('Y-m-d H:i:s',strtotime($last_modified)) < $checkdatetime) 
        // && ($last_modified != $currentdatetime )
    ){

    // DRAFT MODE : if user is admin preparer , approvers , Hod or anyone else with whom the document is shared can edit the document in draft mode
    // if document is Published, then only admin & prepare can adit the document
        $file = urldecode($file);
        if($this->request->controller == 'qc_documents'){
        if($this->request->data['QcDocument']['document_status'] == 0){ // check if draft and apply draft rules
            if($this->action != 'view' &&  ($this->Session->read('User.is_mr') == 1 || $this->Session->read('User.is_approver') == 1 || in_array($this->Session->read('User.id'), json_decode($this->request->data['QcDocument']['user_id'],true)))){
                $mode = 'edit';
            }else{
                $mode = 'view';
            }
        } else if($this->action != 'view' &&  ($this->request->data['QcDocument']['document_status'] == 1 || $this->request->data['QcDocument']['document_status'] == 2 )){ // published documents
            if( $this->Session->read('User.is_mr') == 1 || 
                $this->Session->read('User.employee_id') == $this->request->data['QcDocument']['approved_by'] ||
                $this->Session->read('User.employee_id') == $this->request->data['QcDocument']['prepared_by'] ||
                $this->Session->read('User.employee_id') == $this->request->data['QcDocument']['issued_by']
            ){
                $mode = 'edit';
        }else{
            $mode = 'embedded';
        }
    }
}

if($this->action == 'mini_view')$mode = 'view';

if($this->action == 'update_revision')$mode = 'view';

if($filetype != null){
    $file_type = $filetype;
    $border_color = '#0094ff;';

    if($file_type == 'doc' || $file_type == 'docx'){
        $documentType = 'word';
        $icon = ' fa-file-word-o';
        $border_color = '#3f628a;';
        $docType = 'word';
        $heading = 'Word';
    }

    if($file_type == 'xls' || $file_type == 'xlsx'){
        $documentType = 'cell';
        $icon = ' fa-file-excel-o';
        $border_color = '#387951;';
        $docType = 'cell';
        $heading = 'Speadsheet';
    }

    if($file_type == 'pdf'){
        $documentType = 'word';
        $icon = ' fa-file-pdf-o';
        $border_color = '#ab5252;';
        $docType = 'word';
        $heading = 'PDF';
    }
    if($this->Session->read('User')){ ?>
        <style type="text/css">
            #ofdcontainer<?php echo $placeholderid;?>{
                border-left: 2px solid <?php echo $border_color;?>
            }
        </style>
        <div id="newdoctitle<?php echo $docid;?>"></div>        
        <div class="box collapsed-box" id="ofdcontainer<?php echo $placeholderid;?>">
          <div class="box-header with-border data-header" data-widget="collapse">
            <h3 class="box-title"><i class="fa <?php echo $icon;?>"></i>&nbsp;&nbsp;<?php echo $heading;?>&nbsp;&nbsp;<small>Your document is ready, click here to load the document.</small></h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus" id="ofccontainerbtn<?php echo $placeholderid;?>"></i></button>
          </div>            
      </div>          
      <div class="box-body no-padding">
        <script type="text/javascript" src="<?php echo Configure::read('OnlyofficePath');?>"></script>
        <style type="text/css">
            .onlyofficediv{width: 100%;height: 924px;display: block;float: left;}
        </style>
        <?php                 
        if($version_keys)$versionHistory = json_decode($version_keys,true);
        $currentVersion = $versionHistory['serverVersion'];
        if(!$versions)$versions = '{}';
        else{
            $previous = json_decode($versions,true);
            $previous = $previous[count($previous)-1];                    
        }        
        ?>
        <?php

        // add reload document script as a function for each IF.
        $comarray = array('qc_documents','custom_tables','processes','pdf_templates');
        if($this->request->controller == 'custom_tables' && $this->action == 'view'){            
            echo "<div class='data_type_notification'>Loading file in view mode.</div>";
            $url = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/custom_tables'. '/' . $record_id . '/' . $file;
            $historyurl = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/custom_tables'. '/' . $record_id . '/' . 'diff.zip';
            $callbackUrl = Router::url('/',true) ."custom_tables/save_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller ;
            
        }else if($this->request->controller == 'custom_tables' && $this->action == 'recreate'){ 
            echo "<div class='data_type_notification'>Loading Custom Table File.</div>";
            $url = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/custom_tables'. '/' . $record_id . '/' . $file;
            $historyurl = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/custom_tables'. '/' . $record_id . '/' . 'diff.zip';
            $callbackUrl = Router::url('/',true) ."custom_tables/save_custom_table_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller ;
            
        }else if($this->request->controller == 'templates' && $this->action == 'edit'){
            echo "<div class='data_type_notification'>Loading Template file.</div>";
            $url = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/templates'. '/' . $record_id . '/' . $file;
            $historyurl = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/templates'. '/' . $record_id . '/' . 'diff.zip';
            $callbackUrl = Router::url('/',true) ."templates/save_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller ;
            
        }else if(!in_array($this->request->controller, $comarray) && ($this->action == 'add' || $this->action == 'edit' || $this->action == 'view') && $document['QcDocument']['data_type'] != 1)
        {      
            if($this->action == 'edit' || $this->action == 'add'){
                $mode = 'edit';
                echo "<div class='data_type_notification'>Document setting is Document/Both. Loding document in Edit mode.</div>";
            }else{
                $mode = 'view';
                echo "<div class='data_type_notification'>Loading file in view mode.</div>";
            }
            // section for custom forms         
            
            $url = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . 'files'. '/' . $fileEdit['File']['id'] . '/' . $fileEdit['File']['name'].'.'.$fileEdit['File']['file_type'];
            $historyurl = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . 'files'. '/' . $fileEdit['File']['id'] . '/' . 'diff.zip';

            $callbackUrl = Router::url('/',true) ."custom_tables/save_rec_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller;
        }else if(!in_array($this->request->controller, $comarray) && ($this->action == 'add' || $this->action == 'edit' || $this->action == 'view') && $document['QcDocument']['data_type'] == 1)
        {   

            echo "<div class='data_type_notification'>Document setting is Data. Loding document in View mode.</div>";
            
            $mode = 'view';
            // section for custom forms                    
            $url = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . 'custom_tables'. '/' . $fileEdit['File']['id'] . '/' . $fileEdit['File']['name'].'.'.$fileEdit['File']['file_type'];
            $historyurl = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . 'custom_tables'. '/' . $fileEdit['File']['id'] . '/' . 'diff.zip';

            $callbackUrl = Router::url('/',true) ."custom_tables/save_rec_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller;
            

        }else if(isset($fileData)){  

            $url = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') .'/'. $fileData['File']['controller'].'/'.$fileData['File']['user_id'] . '/'.$fileData['File']['record_id'].'/'.$fileData['File']['qc_document_id'] .'/'.$fileData['File']['name'].'.'.$fileData['File']['file_type'];

            $historyurl = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') .'/'. $fileData['File']['controller'].'/'.$fileData['File']['user_id'] . '/'.$fileData['File']['record_id'].'/'.$fileData['File']['qc_document_id'] .'/'.'diff.zip';
            
            $callbackUrl = Router::url('/',true) .$this->request->controller. '/' ."save_custom_docs/file_id:". $fileData['File']['id'];
            $filekey = $fileData['File']['new_file_key'];
            $filetype = $fileData['File']['file_type'];                

        }else if($controller == 'files'){  

            $url = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . 'files'. '/' . $fileEdit['File']['id'] . '/' . $fileEdit['File']['name'].'.'.$fileEdit['File']['file_type'];
            $historyurl = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . 'files'. '/' . $fileEdit['File']['id'] . '/' . 'diff.zip';

            $callbackUrl = Router::url('/',true) ."custom_tables/save_rec_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller;

        }else if($this->request->controller == 'pdf_templates'){  
            $url = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . 'pdf_template'. '/' . $record_id . '/template.docx';
            $historyurl = Router::url('/', true) . 'files/' . $this->Session->read('User.company_id') . '/' . 'pdf_template'. '/' . $record_id . '/' . 'diff.zip';

            $callbackUrl = Router::url('/',true) ."pdf_templates/save_template/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller;

        }else{    
            $url = Configure::read('url') . '/' . $path . '/' .$file;
            $historyurl = Configure::read('url') . '/' . $path . '/' .'diff.zip';                        
            if($this->request->controller == 'qc_documents')
                $callbackUrl = Router::url('/',true) ."qc_documents/save_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller ;

            else if($this->request->controller == 'custom_tables')
                $callbackUrl = Router::url('/',true) ."custom_tables/save_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller ;

            else if ($this->request->controller == 'processes')
                $callbackUrl = Router::url('/',true) ."processes/save_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller ;
            
            $preversion = $version - 1;
            if($previous['user']['url'])$historyurl = str_replace('prev.'.$filetype, 'diff.zip', $previous['user']['url']);
            
            $historyurl = str_replace($preversion.'-hist','',$historyurl);
            $historyurl = str_replace('/var/www/html/cloud/'.$this->Session->read('User.dir_name').'/app/webroot/files/', Router::url('/', true) . 'files/', $historyurl);
        }

        $callbackUrl .= '/user:'.$this->Session->read('User.id');

        $file = $file;
                    $mode = $mode; // : sent from form

                    if($file_type == 'doc' || $file_type == 'docx'){
                        $documentType = 'word';
                    }

                    if($file_type == 'xls' || $file_type == 'xlsx'){
                        $documentType = 'cell';
                    }

                    if($mode == 'edit'){

                        $fileuriUser = $url;
                        $onAppReady = 'onAppReady';

                        $config = [
                            "type" => $filetype,        
                            "documentType" => $documentType,
                            "document" => [
                                "title" => $file,
                                "url" => $url,
                                "fileType" => $filetype,
                                "key" => $filekey,
                                "info" => [
                                    "owner" => $this->Session->read('User.company_name'),
                                    "uploaded" => date('Y-m-d H:i:s'),
                                    "favorite" => null
                                ],
                                "permissions" => [
                                    "comment" => true,
                                    "download" => false,
                                    "edit" => true,
                                    "fillForms" => true,
                                    "modifyFilter" => true,
                                    "modifyContentControl" => true,
                                    "review" => true,
                                    "reviewGroups" => true,
                                    "print"=>false
                                ],
                            ],
                            "editorConfig" => [
                                "callbackUrl"=> $callbackUrl,            
                                "mode" => $mode,
                                "autosave" => true, 
                                "forcesave" => true,           
                                "chat" => true,
                                "comments" => true,
                                "coEditing" => [
                                    "mode" => "strict", 
                                    "change" => true
                                ],
                                "templates" => [
                                    [
                                        "image" => "",
                                        "title" =>  "",
                                        "url" =>  ""
                                    ]
                                ],
                                "user" => [
                                    "id" => $this->Session->read('User.id'),
                                    "name" => $this->Session->read('User.name'),
                                    "group" => $this->Session->read('User.company_name')
                                ],
                                "auth" => [
                                    "company_id" => $this->Session->read('User.company_id'),
                                    "company_name" => $this->Session->read('User.company_name'),
                                    "user_session_id" => $this->Session->read('User.user_session_id'),
                                ],
                                "embedded" => [
                                    "saveUrl" => $fileuriUser,
                                    "embedUrl" => $fileuriUser,
                                    "shareUrl" => $fileuriUser,
                                    "toolbarDocked" => "top",
                                ],
                                "customization" => [
                                    "about" => true,
                                    "feedback" => true,                
                                    "submitForm" => false,                
                                ]
                            ]
                            
                        ];    

                        $aurl = array(
                            'controller' => $this->request->controller,
                            'action' => 'jwtencode',
                            'payload'=>$config
                        );
                        $token =$this->requestAction($aurl);
                        $config['token'] = $token;
                        $config['lang'] = "en";
                        ?>
                        <?php 
                        $version_keys = array();
                        $versionHistories = json_decode($versions,true);
                        foreach($versionHistories as $versionHistory){
                            unset($versionHistory['changes']);
                            $finalHistory[] = $versionHistory;
                            // $version_keys[] = $versionHistory;

                        }
                        $versionHistories = json_encode($versionHistories['changes']);
                        ?>
                        <script type="text/javascript">
                            $().ready(function(){

                                var onRequestSaveAs = function(event) {
                                    var title = event.data.title;
                                    var url = event.data.url;
                                };
                                var docEditor;
                                var onDocumentStateChange = function (event) {        
                                    var title = document.title.replace(/\*$/g, "");
                                    document.title = title + (event.data ? "*" : "");
                                };

                                var onDownloadAs = function () {            
                                    return false;
                                }
                                
                                var connectEditor = function () {                                    
                                    var config = <?php echo json_encode($config) ?>;    
                                    config.width = "100%";
                                    config.height = "100%";
                                    config.events = {
                                    };                
                                    docEditor = new DocsAPI.DocEditor("placeholder_<?php echo $placeholderid;?>", config);                                    
                                };

                                var fixSize = function () {
                                    var wrapEl = document.getElementsByClassName("form");
                                    if (wrapEl.length) {
                                        wrapEl[0].style.height = screen.availHeight + "px";
                                        window.scrollTo(0, -1);
                                        wrapEl[0].style.height = window.innerHeight + "px";
                                    }
                                };                                 

                                $("#ofdcontainer<?php echo $placeholderid;?>").on('click',function(){
                                    if($('#ofdcontainer<?php echo $placeholderid;?>').hasClass('collapsed-box') == true){
                                        $("#ofccontainerbtn<?php echo $placeholderid;?>").removeClass('fa-plus');
                                        $("#ofccontainerbtn<?php echo $placeholderid;?>").addClass('fa-minus');
                                        if (window.addEventListener) {
                                            connectEditor();
                                        } else if (window.attachEvent) {
                                            connectEditor();
                                        }
                                    }else{
                                        $("#ofccontainerbtn<?php echo $placeholderid;?>").removeClass('fa-minus');
                                        $("#ofccontainerbtn<?php echo $placeholderid;?>").addClass('fa-plus');
                                    }
                                });
                            });
                        </script>
                        <div class="">
                            <div class="split col-md-12 no-margin no-padding">
                                <div class="onlyofficediv">    
                                    <div id="placeholder_<?php echo $filekey;?>"></div>
                                </div>
                            </div>
                            <div class="load_version_document col-md-6 hide no-margin  no-padding">
                                <div class="onlyofficediv" id="load_version_document_<?php echo $filekey;?>">    
                                    <div id="placeholder_<?php echo $filekey;?>"></div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="row">
                                <?php if(!empty($finalHistory)){ ?>
                                    <div class="col-md-12" style="padding-top:10px">
                                        <p style="padding-left:10px; padding-top:10px; margin-top:10px; border-top:1px solid #ccc;">
                                            <strong>Note:</strong> Previous document versions are available. You can either view previous document version side-by-side or at the bottom on the current document. To toggle the view, click toggle button below.                                            
                                        </p>
                                    </div>
                                <?php } ?>
                                <div class="col-md-11"  style="padding-top:20px">
                                    <?php if(!empty($finalHistory)){ ?>

                                        <div class="btn-group" role="group">     
                                            <?php  
                                            if(isset($this->request->params['named']['compare']) && $this->request->params['named']['compare'] == 'yes'){
                                                echo $this->Html->link(
                                                    '<i class="fa fa-toggle-on fa-lg"></i>',
                                                    array('action'=>$this->action,$this->request->params['pass'][0],'compare'=>'off','timestamp'=>date('ymdhis')),
                                                    array(
                                                        'data-original-title'=>'Turn off side-by-side compare.',
                                                        'compare'=>'yes',
                                                        'class'=>'btn text-success tooltip1',
                                                        'data-toggle'=>'tooltip',
                                                        'data-trigger'=>'hover',
                                                        'data-placement'=>'bottom',
                                                        'escape'=>false,
                                                        'onClick'=>'document.location.reload();'
                                                    ));

                                            }else{
                                                echo $this->Html->link(
                                                    '<i class="fa fa-toggle-off fa-lg"></i>',
                                                    array('action'=>$this->action,$this->request->params['pass'][0],'compare'=>'yes','timestamp'=>date('ymdhis')),
                                                    array(
                                                        'data-original-title'=>'Turn On side-by-side compare.',
                                                        'compare'=>'yes',
                                                        'class'=>'btn tooltip1',
                                                        'data-toggle'=>'tooltip',
                                                        'data-trigger'=>'hover',
                                                        'data-placement'=>'bottom',
                                                        'escape'=>false,
                                                        'onClick'=>'document.location.reload();'
                                                    ));
                                            }                                            
                                            ?>                                          
                                            <?php                                             
                                            $i = 0;
                                            foreach($finalHistory as $version_key){
                                                if($version_key['key'] != $filekey){
                                                    echo $this->Html->link(
                                                        '<div>'.$i.'</div>',
                                                        '#load_version_document_'.$filekey,
                                                        array(
                                                            'id'=>'icon_'.$version_key['key'],
                                                            'escape'=>false,
                                                            'class'=>'vlables btn btn-default btn-sm',
                                                            'onClick'=>'loaddocumentversions(
                                                            \''.base64_encode($version_key['user']['url']).'\',
                                                            '.$version_key['key'].',
                                                            '.$version_key['key'].',
                                                            \''.$filetype.'\',
                                                            \''.$documentType.'\',
                                                            \'prev.'.$filetype.'\',
                                                            \''.$version_key['version'].'\',
                                                            \''.$version_key['user']['name'].'\',
                                                            \''.$version_key['created'].'\'                                                            
                                                        )'
                                                    ));
                                                    $i++;
                                                }                            
                                            }
                                            echo $this->Html->link(
                                                '<div class="">'.($i).'</div>',
                                                '#load_version_document_'.$filekey,
                                                array(
                                                    'class'=>'btn btn-info btn-sm',
                                                    'escape'=>false,
                                                    'onClick'=>'document.location.reload();'
                                                ));                                           
                                                ?>                                                
                                            </div>

                                            

                                        <?php } ?>  
                                    </div>
                                    <div class="col-md-1 text-right" style="padding-top:20px">                                        
                                        <?php echo $this->Html->link('<i class="fa fa-trash-o fa-lg"></i>',
                                            array(
                                                'action'=> 'delete_document',
                                                'url'=>base64_encode($url)
                                            ),
                                            array(
                                                'data-original-title'=>'Delete this document?',
                                                'class'=>'btn text-danger tooltip1', 
                                                'escape'=>false,
                                                'data-toggle'=>'tooltip',
                                                'data-trigger'=>'hover',
                                                'data-placement'=>'bottom',
                                            ));?>                                            
                                        </div>                                    
                                    </div>
                                </div>   
                            <?php } else {  ?>
                                <?php                            
                                $fileuriUser = $url;
                                $config = [
                                    "type" => $filetype,        
                                    "documentType" => $documentType,
                                    "document" => [
                                        "title" => $file,
                                        "url" => $url,
                                        "fileType" => $filetype,
                                        "key" => $filekey,
                                        "info" => [
                                            "owner" => $this->Session->read('User.company_name'),
                                            "uploaded" => date('Y-m-d H:i:s'),
                                            "favorite" => null
                                        ],
                                        "permissions" => [
                                            "comment" => false,
                                            "download" => false,
                                            "edit" => false,
                                            "fillForms" => false,
                                            "modifyFilter" => false,
                                            "modifyContentControl" => false,
                                            "review" => false,
                                            "reviewGroups" => false,
                                            "print"=>false
                                        ]
                                    ],
                                    "editorConfig" => [
                                        "callbackUrl"=> $callbackUrl,            
                                        "mode" => $mode,
                                        "autosave" => false, 
                                        "forcesave" => false,           
                                        "chat" => false,
                                        "comments" => false,
                                        "user" => [
                                            "id" => $this->Session->read('User.id'),
                                            "name" => $this->Session->read('User.name'),
                                            "group" => $this->Session->read('User.company_name')
                                        ],
                                        "auth" => [
                                            "company_id" => $this->Session->read('User.company_id'),
                                            "company_name" => $this->Session->read('User.company_name'),
                                            "user_session_id" => $this->Session->read('User.user_session_id'),
                                        ]
                                    ]

                                ];  

                                $aurl = array(
                                    'controller' => 'qc_documents',
                                    'action' => 'jwtencode',
                                    'payload'=>$config
                                );
                                $token =$this->requestAction($aurl);
                                $config['lang'] = "en";
                                $config['token'] = $token;                         
                                ?>
                                <?php 
                                $version_keys = array();
                                $versionHistories = json_decode($versions,true);
                                foreach($versionHistories as $versionHistory){
                                    unset($versionHistory['changes']);
                                    $finalHistory[] = $versionHistory;
                                // $version_keys[] = $versionHistory;

                                }
                                $versionHistories = json_encode($versionHistories['changes']);
                                ?>

                                <script type="text/javascript">                

                                    $().ready(function(){                
                                        var docEditor;                
                                        var сonnectEditor = function () {
                                            var config = <?php echo json_encode($config) ?>;
                                            config.width = "100%";
                                            config.height = "100%";
                                            docEditor = new DocsAPI.DocEditor("placeholder_<?php echo $placeholderid;?>", config);                                        
                                        };
                                        var fixSize = function () {
                                            var wrapEl = document.getElementsByClassName("form");
                                            if (wrapEl.length) {
                                                wrapEl[0].style.height = screen.availHeight + "px";
                                                window.scrollTo(0, -1);
                                                wrapEl[0].style.height = window.innerHeight + "px";
                                            }
                                        };                                        
                                        <?php if($this->action == 'view' && $this->request->controller != 'custom_tables'){ ?>
                                            $("#ofdcontainer<?php echo $placeholderid;?>").removeClass("collapsed-box");
                                            if (window.addEventListener) {
                                                сonnectEditor();
                                            } else if (window.attachEvent) {
                                                сonnectEditor();
                                            }
                                            $("#ofdcontainer<?php echo $placeholderid;?>").on('click',function(){
                                            if($('#ofdcontainer<?php echo $placeholderid;?>').hasClass('collapsed-box') == true){
                                                $("#ofccontainerbtn<?php echo $placeholderid;?>").removeClass('fa-plus');
                                                $("#ofccontainerbtn<?php echo $placeholderid;?>").addClass('fa-minus');                                                
                                            }else{
                                                $("#ofccontainerbtn<?php echo $placeholderid;?>").removeClass('fa-minus');
                                                $("#ofccontainerbtn<?php echo $placeholderid;?>").addClass('fa-plus');
                                            }
                                        });
                                        <?php }else{ ?>
                                            $("#ofdcontainer<?php echo $placeholderid;?>").on('click',function(){
                                            if($('#ofdcontainer<?php echo $placeholderid;?>').hasClass('collapsed-box') == true){
                                                $("#ofccontainerbtn<?php echo $placeholderid;?>").removeClass('fa-plus');
                                                $("#ofccontainerbtn<?php echo $placeholderid;?>").addClass('fa-minus');
                                                if (window.addEventListener) {
                                                    сonnectEditor();
                                                } else if (window.attachEvent) {
                                                    сonnectEditor();
                                                }
                                            }else{
                                                $("#ofccontainerbtn<?php echo $placeholderid;?>").removeClass('fa-minus');
                                                $("#ofccontainerbtn<?php echo $placeholderid;?>").addClass('fa-plus');
                                            }
                                        });
                                        <?php } ?>
                                        
                                    });            

                                </script>
                                <div class="">
                                    <div class="split col-md-12 no-margin  no-padding">
                                        <div class="onlyofficediv">
                                            <div id="placeholder_<?php echo $filekey;?>"></div>
                                        </div>
                                    </div>
                                    <div class="load_version_document col-md-6 hide no-margin  no-padding">
                                        <div class="onlyofficediv" id="load_version_document_<?php echo $filekey;?>">    
                                            <div id="placeholder_<?php echo $filekey;?>"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-11"  style="padding-top:20px">
                                            <?php if(!empty($finalHistory)){ ?>
                                                <div class="btn-group" role="group">     
                                                    <?php  
                                                    if(isset($this->request->params['named']['compare']) && $this->request->params['named']['compare'] == 'yes'){
                                                        echo $this->Html->link(
                                                            '<i class="fa fa-toggle-on fa-lg"></i>',
                                                            array($this->request->params['pass'][0],'compare'=>'off','timestamp'=>date('ymdhis')),
                                                            array(
                                                                'data-original-title'=>'Turn off side-by-side compare. This will reload the page.',
                                                                'compare'=>'yes',
                                                                'class'=>'btn text-success tooltip1',
                                                                'data-toggle'=>'tooltip',
                                                                'data-trigger'=>'hover',
                                                                'data-placement'=>'bottom',
                                                                'escape'=>false,
                                                                'onClick'=>'document.location.reload();'
                                                            ));

                                                    }else{
                                                        echo $this->Html->link(
                                                            '<i class="fa fa-toggle-off fa-lg"></i>',
                                                            array($this->request->params['pass'][0],'compare'=>'yes','timestamp'=>date('ymdhis')),
                                                            array(
                                                                'data-original-title'=>'Turn On side-by-side compare.This will reload the page.',
                                                                'compare'=>'yes',
                                                                'class'=>'btn tooltip1',
                                                                'data-toggle'=>'tooltip',
                                                                'data-trigger'=>'hover',
                                                                'data-placement'=>'bottom',
                                                                'escape'=>false,
                                                                'onClick'=>'document.location.reload();'
                                                            ));
                                                    }                                            
                                                    ?>                                          
                                                    <?php                                             
                                                    $i = 0;
                                                    foreach($finalHistory as $version_key){
                                                        if($version_key['key'] != $filekey){
                                                            echo $this->Html->link(
                                                                '<div>'.$i.'</div>',
                                                                '#load_version_document_'.$filekey,
                                                                array(
                                                                    'id'=>'icon_'.$version_key['key'],
                                                                    'escape'=>false,
                                                                    'class'=>'vlables btn btn-default btn-sm',
                                                                    'onClick'=>'loaddocumentversions(
                                                                    \''.base64_encode($version_key['user']['url']).'\',
                                                                    '.$version_key['key'].',
                                                                    '.$version_key['key'].',
                                                                    \''.$filetype.'\',
                                                                    \''.$documentType.'\',
                                                                    \'prev.'.$filetype.'\',
                                                                    \''.$version_key['version'].'\',
                                                                    \''.$version_key['user']['name'].'\',
                                                                    \''.$version_key['created'].'\'                                                            
                                                                )'
                                                            ));
                                                            $i++;
                                                        }                            
                                                    }
                                                    echo $this->Html->link(
                                                        '<div class="">'.($i).'</div>',
                                                        '#load_version_document_'.$filekey,
                                                        array(
                                                            'class'=>'btn btn-info btn-sm',
                                                            'escape'=>false,
                                                            'onClick'=>'document.location.reload();'
                                                        ));                                           
                                                        ?>                                                
                                                    </div>
                                                <?php } ?>  
                                            </div>
                                            <div class="col-md-1 text-right" style="padding-top:20px">                                            
                                                <?php echo $this->Html->link('<i class="fa fa-trash-o fa-lg"></i>',
                                                    array(
                                                        'action'=> 'delete_document',
                                                        'url'=>base64_encode($url)
                                                    ),
                                                    array(
                                                        'data-original-title'=>'Delete this document?',
                                                        'class'=>'btn text-danger tooltip1', 
                                                        'escape'=>false,
                                                        'data-toggle'=>'tooltip',
                                                        'data-trigger'=>'hover',
                                                        'data-placement'=>'bottom',
                                                    ));?>  
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php }else{ ?>

                        <style type="text/css">
                            .info-box-number{font-size: 14px !important; font-weight: 400;}
                            .info-box-text{font-weight: 600;}
                            .nomargin-checkbox .checkbox{margin-top: 0px !important;}
                            .nomargin-checkbox label{margin: 0 0 0 0 !important;}
                        </style>
                        <script type="text/javascript">
                            function addfiletype(filetype){
                                $("#QcDocumentFileType").val(filetype); 
                                $("#filetypediv").hide();
                            }

                            $().ready(function(){
                                $("#QcDocumentFile").on('change',function(){            
                                    let pathext = this.value;
                                    const pathfile = pathext.split(".");
                                    const arr = pathfile.length;
                                    const ext = pathfile[arr-1];

                                    var earr = ["doc","docx","xls","xlsx"];

                                    if($.inArray(ext,earr)){
                                        alert('This file type is not allowed');
                                        location.reload(true);
                                    }else{

                                    }

                                    let path = pathfile[arr-2];
                                    const file = path.split("\\");
                                    const filename  = file[file.length - 1];

                                    var str = filename;

                                    if (!isNaN(str[0]) && !isNaN(str[str.length - 1])) {
                                      this.value = "";
                                      alert('File with onlynumbers is not allowed.');
                                      location.reload(true);
                                  } else {

                                  }
                                  $("#QcDocumentTitle").val(filename);
                                  addfiletype(ext);
                              });
                            })
                        </script>
                        <div class="box collapsed-box">          
                          <div class="box-header with-border">
                            <h3 class="box-title text-danger">Document</h3>

                            <div class="box-tools pull-right">
                              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                              </button>
                          </div>            
                      </div>          
                      <div class="box-body">
                        <?php 
                        if(($this->request->controller == 'qc_documents' || $this->request->controller == 'processes') && $this->action == 'edit'){ ?>
                            <div class="row">
                                <div class="col-md-12"><p class="text-danger">System could not load the document. Uplaod Document from View page.</p></div>                
                            </div>
                        <?php }else{ ?>
                            <p class="text-danger">System could not load the document.</p>
                        <?php } ?>
                    </div>          
                </div>
            <?php } 
        }else{ ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning collapsed-box" id="ofdcontainer<?php echo $placeholderid;?>">
                      <div class="box-header with-border data-header" data-widget="collapse">
                        <h3 class="box-title"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;Document&nbsp;&nbsp;<small> Last Updated : <?php echo date('Y-m-d H:i:s',strtotime($last_modified))?>. We are updating your document. Please wait...</small></h3>
                        <div class="box-tools pull-right">                    
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus" id="ofccontainerbtn<?php echo $placeholderid;?>"></i></button>
                            <i class="fa fa-refresh" id="ofcrefresh<?php echo $placeholderid;?>"></i>
                        </div>            
                    </div>          
                    <div class="box-body">                
                        <p>This document is recently updated. Please allow 1-3 mins for changes to reflect.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <script type="text/javascript">
        <?php if($controller != 'qc_documents'){ ?>
            $().ready(function(){
                $("#ofcrefresh<?php echo $placeholderid;?>").on('click',function(){
                    $("#ofcrefresh<?php echo $placeholderid;?>").addClass('fa-spin');
                    $("#ofcon<?php echo $placeholderid;?>").load("<?php echo Router::url('/', true); ?><?php echo $this->request->controller;?>/reloadrecordfile/<?php echo $fileEdit['File']['id'];?>")
                })
            });
        <?php }else{ ?>
            $().ready(function(){
                $("#ofcrefresh<?php echo $placeholderid;?>").on('click',function(){
                    $("#ofcrefresh<?php echo $placeholderid;?>").addClass('fa-spin');
                    $("#ofcon<?php echo $placeholderid;?>").load("<?php echo Router::url('/', true); ?>qc_documents/reloaddocument/<?php echo $docid;?>")
                })
            });
        <?php } ?>


        function loaddocumentversions(url,key,version_key,fileType,documentType,file,version,user,created,id){

            <?php if(isset($this->request->params['named']['compare']) && $this->request->params['named']['compare'] == 'yes'){ ?>
                $(".vlables").removeClass('btn-warning').addClass('btn-default');
                $("#icon_"+version_key).removeClass('btn-default').addClass('btn-warning');
                $(".split").removeClass('col-md-12').addClass('col-md-6');
                $.ajax({
                    type: "POST",
                    data : {
                      "url":url,
                      "key":key,
                      "version_key":version_key,
                      "fileType":fileType,
                      "documentType":documentType,
                      "file":file,
                      "version":version,
                      "user":user,
                      "created":created,
                      "id":"<?php echo $this->request->data['QcDocument']['id'];?>"
                  },
                  dataType: "text",
                  url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/load_document_version",
                  success: function(data, result) {
                    $('.load_version_document').removeClass('hide').addClass('show');
                    $("#load_version_document_<?php echo $filekey;?>").html('');
                    $("#load_version_document_<?php echo $filekey;?>").html(data); 
                },
            });
            <?php }else{ ?>

                $(".vlables").removeClass('btn-warning').addClass('btn-default');
                $("#icon_"+version_key).removeClass('btn-default').addClass('btn-warning');
            // $(".split").removeClass('col-md-12').addClass('col-md-6');
                $.ajax({
                    type: "POST",
                    data : {
                      "url":url,
                      "key":key,
                      "version_key":version_key,
                      "fileType":fileType,
                      "documentType":documentType,
                      "file":file,
                      "version":version,
                      "user":user,
                      "created":created,
                      "id":"<?php echo $this->request->data['QcDocument']['id'];?>"
                  },
                  dataType: "text",
                  url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/load_document_version",
                  success: function(data, result) {
                    $('.load_version_document').removeClass('hide').addClass('show');
                    $('.load_version_document').removeClass('col-md-6').addClass('col-md-12');
                    $("#load_version_document_<?php echo $filekey;?>").html('');
                    $("#load_version_document_<?php echo $filekey;?>").html(data); 
                },
            });

            <?php } ?>        
        }

    </script>

</div>
