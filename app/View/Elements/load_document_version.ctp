<style type="text/css">
  .onlyofficeversiondiv{width: 100%;height: 924px;display: block;float: left;}
</style>

<?php
$url = base64_decode($this->data['url']);
$url = str_replace(Configure::read('files'), Router::url('/', true) . 'files/'.$this->Session->read('User.company_id'), $url);
$fileuriUser = $this->data['url'];
$config = [
    "type" => $this->data['fileType'], 
    "documentType" => $this->data['documentType'],
    "document" => [
        "title" => ' Ver- ' . $this->data['version'] . ' By: '. str_replace($this->Session->read('User.company_name'),'',$this->data['user']) .' Updated:' . $this->data['created'],
        "url" => $url,
        "fileType" => $this->data['fileType'],
        "key" => $this->data['version_key'],
        "info" => [
            "owner" => $this->Session->read('User.company_name'),
            "uploaded" => date('Y-m-d H:i:s'),
            "favorite" => null
        ],
        "permissions" => [
            "comment" => false,
            "download" => true,
            "edit" => false,
            "fillForms" => false,
            "modifyFilter" => false,
            "modifyContentControl" => false,
            "review" => false,
            "reviewGroups" => false,
            "print"=>false,
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
$config['lang'] = "en";
?>


<script type="text/javascript">                

    $().ready(function(){   
        var docEditor;                
        var сonnectEditor = function () {
            var config = <?php echo json_encode($config) ?>;
            config.width = "100%";
            config.height = "100%";
            console.log(config);
            docEditor = new DocsAPI.DocEditor("placeholder_<?php echo $this->data['version_key'];?>", config);
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
