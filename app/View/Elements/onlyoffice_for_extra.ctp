<div id="ofcon<?php echo $filekey;?>">
    <?php

    if(!$filekey)$filekey = $fileData['File']['new_file_key'];                
    $placeholderid = $filekey;
    $checkdatetime = date('Y-m-d H:i',strtotime("-1 mins"));
    $currentdatetime = date('Y-m-d H:i');
    $last_modified = date('Y-m-d H:i',strtotime($last_modified));
    
    if(
        ($last_saved == null || date('Y-m-d H:i',strtotime($last_saved)) < $checkdatetime) && 
        ($last_modified != $currentdatetime )){
        $file = urldecode($file);
        if( $this->Session->read('User.is_mr') == 1 || 
            $this->Session->read('User.employee_id') == $qcDocument['QcDocument']['approved_by'] ||
            $this->Session->read('User.employee_id') == $qcDocument['QcDocument']['prepared_by'] ||
            $this->Session->read('User.employee_id') == $qcDocument['QcDocument']['issued_by']
        ){
            $mode = 'edit';
    }else{
        $mode = 'embedded';
    }
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
                    <h3 class="box-title"><i class="fa <?php echo $icon;?>"></i>&nbsp;&nbsp;<?php echo $heading;?>&nbsp;&nbsp;<small>Your document is ready, click on + sign to load the document.</small></h3>
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
                $versionHistory = json_decode($version_keys,true);
                $currentVersion = $versionHistory['serverVersion'];
                if(!$versions)$versions = '{}';
                else{
                    $previous = json_decode($versions,true);
                    $previous = $previous[count($previous)-1];                    
                }
                ?>

                <?php                 

                $comarray = array('qc_documents','custom_tables','processes');
                $url = str_replace($this->request->controller, 'qc_documents', Configure::read('url'))  . '/' . $path . '/' .$file;
                $historyurl = str_replace($this->request->controller, 'qc_documents', Configure::read('url')) . '/' . $path . '/' .'diff.zip';
                $callbackUrl = Router::url('/',true) ."qc_documents/save_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller ;

                $preversion = $version - 1;
                $historyurl = str_replace('prev.'.$filetype, 'diff.zip', $previous['user']['url']);

                $historyurl = str_replace($preversion.'-hist','',$historyurl);
                $historyurl = str_replace('/var/www/html/cloud/'.$this->Session->read('User.dir_name').'/app/webroot/files/', Router::url('/', true) . 'files/', $historyurl);


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
                                ]
                            ],
                            "editorConfig" => [
                                "callbackUrl"=> $callbackUrl,            
                                "mode" => $mode,
                                "autosave" => true, 
                                "forcesave" => true,           
                                "chat" => true,
                                "comments" => true,
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
                            'controller' => 'qc_documents',
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
                    // var onRequestHistory = function () {
                    //     <?php
                    //     $pvers = str_replace('\/var\/www\/html\/cloud\/'.$this->Session->read('User.dir_name').'\/app\/webroot\/files\/', Router::url('/', true) . 'files/', $versions);
                    //     $pvers = str_replace('\/','/',$pvers);
                    //     ?>
                    //     docEditor.refreshHistory({
                    //         "currentVersion": <?php echo $version;?>,
                    //         "history": <?php echo $pvers?>
                    //     });
                    // }
                    // var onRequestHistoryClose = function () {
                    //     document.location.reload();
                    // };

                    // var onOutdatedVersion = function () {
                    //     location.reload(true);
                    // };

                    // var onRequestHistoryData = function (event) {                    
                    // var ver = event.data;                
                    //     var histData = 
                    //     <?php echo str_replace('\/var\/www\/html\/cloud\/'.$this->Session->read('User.dir_name').'\/app\/webroot\/files\/', Router::url('/', true) . 'files/', json_encode($finalHistory))?>;
                    //     docEditor.setHistoryData(histData[ver - 1]);  // send the link to the document for viewing the version history
                    //     // console.log(histData[ver - 1]);
                    // };

                                var onDownloadAs = function () {            
                                    return false;
                                }
                                
                                var сonnectEditor = function () {
                                    var config = <?php echo json_encode($config) ?>;    
                                    config.width = "100%";
                                    config.height = "100%";
                                    config.events = {

                            // "onOutdatedVersion": onOutdatedVersion,
                            // "onRequestHistory": onRequestHistory,
                            // "onRequestHistoryData": onRequestHistoryData,
                            // "onRequestHistoryClose":onRequestHistoryClose,

                                    };                
                                    docEditor = new DocsAPI.DocEditor("placeholder_<?php echo $placeholderid;?>", config);
                                    console.log(config);
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
                                            сonnectEditor();
                                        } else if (window.attachEvent) {
                                            сonnectEditor();
                                        }
                                    }else{
                                        $("#ofccontainerbtn<?php echo $placeholderid;?>").removeClass('fa-minus');
                                        $("#ofccontainerbtn<?php echo $placeholderid;?>").addClass('fa-plus');
                                    }
                                });
                            });
                        </script>
                        <div class="onlyofficediv" id="load_version_document_<?php echo $filekey;?>">    
                            <div id="placeholder_<?php echo $placeholderid;?>"></div>
                        </div>
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col-md-11"  style="margin-top:20px">
                                    <?php if(!empty($finalHistory)){ ?>
                                        <p>Prevision Versions:
                                            <?php 
                                            $i = 0;
                                            foreach($finalHistory as $version_key){
                                                if($version_key['key'] != $filekey){
                                                    echo $this->Html->link(
                                                        '<span class="label label-default">'.$i.'</span>',
                                                        '#load_version_document_<?php echo $filekey;?>',
                                                        array(
                                                            'escape'=>false,
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
                                                    )) . '&nbsp;';
                                                    $i++;
                                                }                            
                                            }
                                            echo $this->Html->link(
                                                '<span class="label label-info">'.($i).'</span>',
                                                '#load_version_document_<?php echo $filekey;?>',
                                                array(
                                                    'escape'=>false,
                                                    'onClick'=>'document.location.reload();'
                                                )) . '&nbsp;';
                                            
                                                ?>
                                            </p>
                                        <?php } ?>  
                                    </div>
                                    <div class="col-md-1 text-right" style="margin-top:20px">
                                        <?php echo $this->Html->link('<i class="fa fa-trash-o"></i>',array('action'=> 'delete_document','url'=>base64_encode($url)),array('escape'=>false));?>
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


                            <script type="text/javascript">                

                                $().ready(function(){                
                                    var docEditor;                
                                    var сonnectEditor = function () {
                                        var config = <?php echo json_encode($config) ?>;
                                        config.width = "100%";
                                        config.height = "100%";
                                        docEditor = new DocsAPI.DocEditor("placeholder_<?php echo $placeholderid;?>", config);
                                        console.log(config);
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
                                                сonnectEditor();
                                            } else if (window.attachEvent) {
                                                сonnectEditor();
                                            }
                                        }else{
                                            $("#ofccontainerbtn<?php echo $placeholderid;?>").removeClass('fa-minus');
                                            $("#ofccontainerbtn<?php echo $placeholderid;?>").addClass('fa-plus');
                                        }
                                    });
                                });            

                            </script>
                            <div class="onlyofficediv">
                                <div id="placeholder_<?php echo $placeholderid;?>" style="display: block; float: left; width: 100%; height: auto;"></div>
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

    $().ready(function(){
        $("#ofcrefresh<?php echo $placeholderid;?>").on('click',function(){
            $("#ofcrefresh<?php echo $placeholderid;?>").addClass('fa-spin');
            $("#ofcon<?php echo $placeholderid;?>").load("<?php echo Router::url('/', true); ?>qc_documents/reloaddocument/<?php echo $docid;?>")
        })
    });

    function loaddocumentversions(url,key,version_key,fileType,documentType,file,version,user,created){
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
              "created":created
          },
          dataType: "text",
          url: "<?php echo Router::url('/', true); ?><?php echo $this->request->params['controller'] ?>/load_document_version",
          success: function(data, result) {
            $("#load_version_document_<?php echo $filekey;?>").html();
            $("#load_version_document_<?php echo $filekey;?>").html(data); 
        },
    });
    }
</script>

</div>
