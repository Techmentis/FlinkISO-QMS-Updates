<div id="ofcon<?php echo $filekey;?>">
    <?php
    
    if(!$filekey)$filekey = $fileData['File']['new_file_key'];                
    $placeholderid = $filekey;
    $checkdatetime = date('Y-m-d H:i:s',strtotime("-30 seconds"));
    $currentdatetime = date('Y-m-d H:i:s');
    $last_modified = date('Y-m-d H:i:s',strtotime($last_modified));

    if(
        ($last_saved == null || date('Y-m-d H:i:s',strtotime($last_saved)) < $checkdatetime) 
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
    }

    $callbackUrl .= '/user:'.$this->Session->read('User.id');
    $file = $file;
                    $mode = $mode; // : sent from form

                    if($file_type == 'doc' || $file_type == 'docx'){
                        $documentType = 'word';
                    }

                    if($file_type == 'xls' || $file_type == 'xlsx'){
                        $documentType = 'cell';
                    }?>

                    
                    <?php                            
                    $fileuriUser = $url;
                    $config = [
                        "type" => $filetype,        
                        "documentType" => $documentType,
                        "document" => [
                            "title" => 'archived.'.$filetype,
                            "url" => $url,
                            "fileType" => $filetype,
                            "key" => $key,
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
                            "mode" => 'view',
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
                    <div class="panel-footer">

                    </div>

                    
                </div>
            </div>
        <?php }else{ ?>

            <style type="text/css">
                .info-box-number{font-size: 14px !important; font-weight: 400;}
                .info-box-text{font-weight: 600;}
                .nomargin-checkbox .checkbox{margin-top: 0px !important;}
                .nomargin-checkbox label{margin: 0 0 0 0 !important;}
            </style>
            
            <div class="box collapsed-box">          
              <div class="box-header with-border">
                <h3 class="box-title text-danger">Archived Document</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                  </button>
              </div>            
          </div>          
          <div class="box-body">

          </div>          
      </div>
  <?php } 
}else{ ?>

<?php } ?>
</div>