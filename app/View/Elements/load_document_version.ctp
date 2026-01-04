<style type="text/css">
  .onlyofficeversiondiv{width: 100%;height: 924px;display: block;float: left;}
</style>

<?php
$url = base64_decode($this->data['url']);
$url = str_replace(Configure::read('files'), Router::url('/', true) . 'files/'.$this->Session->read('User.company_id'), $url);
$fileuriUser = $this->data['url'];
$config  = array();
    $config = [
        "document" => [
            "key" => $this->data['version_key'],
            "permissions" => [
                "comment"=> true,
                "commentGroups"=>["edit"=>[$this->Session->read('User.company_name')],"remove"=>[$this->Session->read('User.company_name')],"view"=>[$this->Session->read('User.company_name')]],
                "copy"=>true,
                "deleteCommentAuthorOnly"=>false,
                "download"=>true,
                "edit"=>true,
                "editCommentAuthorOnly"=>false,
                "fillForms"=>true,
                "modifyContentControl"=>true,
                "modifyFilter"=>true,
                "print"=>true,
                "review"=>true,
                "reviewGroups"=>[$this->Session->read('User.company_name')]                                    
        ],
        "url" => $url,
        "fileType" => $this->data['fileType'],
        "documentType" => $this->data['documentType'],
        "title" => ' Ver- ' . $this->data['version'] . ' By: '. str_replace($this->Session->read('User.company_name'),'',$this->data['user']) .' Updated:' . $this->data['created'],
        ],                            
        "editorConfig"=>[
            "callbackUrl"=>$callbackUrl,
            "mode"=>'view',
            "user" => [
                "id" => $this->Session->read('User.id'),
                "name" => $this->Session->read('User.name'),
                "group" => $this->Session->read('User.company_name')
            ]
        ],
        "token"=>false                                
    ];
    
    $aurl = array(
        'controller' => 'qc_documents',
        'action' => 'jwtencode',
        'payload'=>base64_encode(json_encode($config))
    );

    $token =$this->requestAction($aurl);
    $config['token'] = $token; 
?>


<script type="text/javascript">                

    $().ready(function(){   
        var docEditor;                
        var сonnectEditor = function () {
            var config = <?php echo json_encode($config) ?>;
            config.width = "100%";
            config.height = "100%";
            if(docEditor){                                        
            }else{
                docEditor = new DocsAPI.DocEditor("placeholder_<?php echo $this->data['version_key'];?>", config);
            }
            
        };
        var fixSize = function () {
            var wrapEl = document.getElementsByClassName("form");
            if (wrapEl.length) {
                wrapEl[0].style.height = screen.availHeight + "px";
                window.scrollTo(0, -1);
                wrapEl[0].style.height = window.innerHeight + "px";
            }
        };
        сonnectEditor();
    });            

</script>
<div class="onlyofficeversiondiv">
    <div id="placeholder_<?php echo $this->data['version_key'];?>"></div>
</div>      
