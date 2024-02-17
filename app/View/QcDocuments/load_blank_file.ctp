<?php

// DRAFT MODE : if user is admin preparer , approvers , Hod or anyone else with whom the document is shared can edit the document in draft mode
// if document is Published, then only admin & prepare can adit the document
$file = urldecode($file);
if($this->request->controller == 'qc_documents'){
    if($this->request->data['QcDocument']['document_status'] == 0){ // check if draft and apply draft rules
        if($this->Session->read('User.is_mr') == 1 || $this->Session->read('User.is_approver') == 1 || in_array($this->Session->read('User.id'), json_decode($this->request->data['QcDocument']['user_id'],true))){
            $mode = 'edit';
        }else{
            $mode = 'view';
        }
    } else if($this->request->data['QcDocument']['document_status'] == 1 || $this->request->data['QcDocument']['document_status'] == 2 ){ // published documents
        if( $this->Session->read('User.is_mr') == 1 || 
            $this->Session->read('User.employee_id') == $this->request->data['QcDocument']['approved_by'] ||
            $this->Session->read('User.employee_id') == $this->request->data['QcDocument']['prepared_by'] ||
            $this->Session->read('User.employee_id') == $this->request->data['QcDocument']['issued_by']
        ){
            $mode = 'edit';
    }else{
        $mode = 'view';
    }
}
}
if($filetype != null){
    $file_type = $filetype;

    if($file_type == 'doc' || $file_type == 'docx'){
        $documentType = 'word';
        $icon = ' fa-file-word-o';
    }

    if($file_type == 'xls' || $file_type == 'xlsx'){
        $documentType = 'cell';
        $icon = ' fa-file-excel-o';
    }

    if($this->Session->read('User')){ ?>
        <script type="text/javascript">
            $().ready(function(){
                $("#QcDocumentFileType").val('<?php echo $file_type;?>');
                $("#add_blank .box").removeClass(' box-danger');
                $("#filetypediv").hide();
            });
        </script>
        <div class="box collapsed-box" id="ofdcontainer<?php echo $placeholderid;?>" style="margin: 0;">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa <?php echo $icon;?>"></i> Document</h3>
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
        $comarray = array('qc_documents','custom_tables','processes');
        $record_id = $this->Session->read('User.id');
        $company_id = $this->Session->read('User.company_id');
        $controller = $this->request->controller;                    
                    // if($this->request->controller == 'qc_documents')
        $callbackUrl = Router::url('/',true) ."qc_documents/save_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller.'/tmp:tmp/filetype:'.$filetype ;
                    // else if ($this->request->controller == 'processes')
                        // $callbackUrl = Router::url('/',true) ."processes/save_doc/record_id:". $record_id .'/company_id:'.$company_id .'/controller:'.$controller ;

        $file = $file;
                $mode = $mode; // : sent from form

                // if($file_type == 'doc' || $file_type == 'docx'){
                //     $documentType = 'word';
                // }

                // if($file_type == 'xls' || $file_type == 'xlsx'){
                //     $documentType = 'cell';
                // }

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
                                "reviewGroups" => true
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
                    $tokan =$this->requestAction($aurl);    
                    $config['token'] = $tokan;
                    $config['lang'] = "en";
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
                            var onRequestHistory = function () {}
                            var onDownloadAs = function () {            
                                return false;
                            }
                            var onRequestCompareFile = function() {
                                docEditor.setRevisedFile(<?php echo json_encode($dataCompareFile)?>);
                            };
                            var сonnectEditor = function () {
                                var config = <?php echo json_encode($config) ?>;    
                                config.width = "100%";
                                config.height = "100%";
                                config.events = {};

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

                            $("#ofccontainerbtn<?php echo $placeholderid;?>").on('click',function(){                    
                                if($('#ofccontainerbtn<?php echo $placeholderid;?>').hasClass('fa fa-minus') == false){
                                    if (window.addEventListener) {
                                        сonnectEditor();
                                    } else if (window.attachEvent) {
                                        сonnectEditor();
                                    }
                                }
                            });
                        });
                    </script>

                    <div class="onlyofficediv">    
                        <div id="placeholder_<?php echo $placeholderid;?>"></div>
                    </div>
                <?php } else {  ?>

                    <script type="text/javascript" src="<?php echo Configure::read('OnlyofficePath');?>"></script>
                    <style type="text/css">
                        .onlyofficediv{
                            width: 100%;
                            height: 924px;
                            display: block;
                            float: left;        
                        }
                    </style>


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
                                "reviewGroups" => false
                            ]
                        ],
                        "editorConfig" => [
                            "mode" => $mode,
                            "autosave" => true, 
                            "forcesave" => true,           
                            "chat" => true,
                            "comments" => true,            
                            "user" => [
                                "id" => $this->Session->read('User.id'),
                                "name" => $this->Session->read('User.name'),
                                "group" => $this->Session->read('User.company_name')
                            ]
                        ]        
                        
                    ];
                    
                    $aurl = array(
                        'controller' => 'qc_documents',
                        'action' => 'jwtencode',
                        'payload'=>$config
                    );
                    $config['lang'] = "en";
                    
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
                            $("#ofccontainerbtn<?php echo $placeholderid;?>").on('click',function(){                    
                                if($('#ofccontainerbtn<?php echo $placeholderid;?>').hasClass('fa fa-minus') == false){
                                    if (window.addEventListener) {
                                        сonnectEditor();
                                    } else if (window.attachEvent) {
                                        сonnectEditor();                            
                                    }
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
    <?php echo $this->Form->hidden('QcDocument.blank_file',array('default'=>1));?>
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
                
                let path = pathfile[arr-2];
                const file = path.split("\\");
                const filename  = file[file.length - 1];            
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
            <div class="col-md-12"><p class="text-danger">System could not load the document. Uplaod Document.</p></div>
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-12">
                                <div class="info-box">
                                  <div class="box-content">
                                    <span class="info-box-text">Upload Document</span>
                                    <span class="info-box-number">
                                      <?php echo $this->Form->input('file',array('type'=>'file'));?>
                                  </span>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-6 col-sm-6 col-12">
                        <div class="info-box">
                          <div class="box-content" style="padding:10px 10px 0 10px">
                            <p class="text-left">
                                <i class="fa fa-file-word-o fa-lg" style="color:#3f628a; font-size:40px; margin:2px 10px 10px 2px;"></i> 
                                <i class="fa fa-file-excel-o fa-lg" style="color:#387951; font-size:40px; margin:2px 10px 10px 2px;"></i> 
                                <i class="fa fa-file-powerpoint-o fa-lg" style="color:#a5494a; font-size:40px; margin:2px 10px 10px 2px;"></i></p>
                                <p>You can upload doc, docx, txt, odt, xls, xlsx, ppt files</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <dlv class="col-md-12" style="min-height: auto;">
        <?php echo "<div class='hide'>".$this->Form->hidden('file_type',array('class'=>'form-control')) . '</div>'; ?>
        <div id="filetypediv" class="text-danger">Select file type.</div>
    </dlv>
</div>
<?php }else{ ?>
    <p class="text-danger">System could not load the document.</p>
<?php } ?>
</div>
</div>
<?php } ?>
