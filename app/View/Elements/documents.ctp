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
$placeholderid = 0;
$folder_path = $path;


$dir = new Folder($folder_path);
$all_files = $dir->find();
$mode = $mode;
$key = $key;
if($all_files){
    foreach($all_files as $file){
        $placeholderid++;
        
        $file_path = $folder_path . DS . $file;
        $filetype = strtolower(pathinfo($file_path, PATHINFO_EXTENSION)); 
        $allowedfiletypes = array('doc','docx','xls','xlsx');

        if(in_array($filetype,$allowedfiletypes)){
            if($filetype == 'doc' || $filetype == 'docx'){
                $documentType = 'word';
            }

            if($filetype == 'xls' || $filetype == 'xlsx'){
                $documentType = 'cell';
            }
            
            $url = Router::url('/', true) . $location. DS . rawurlencode($file);
            
            echo $this->element('onlyoffice',array(
                'url'=>$url,
                'placeholderid'=>$placeholderid,
                'panel_title'=>'Document Viewer',
                'mode'=>$mode,
                'path'=>$file_path,
                'file'=>$file,
                'filetype'=>$filetype,
                'documentType'=>$documentType,
                'userid'=>$this->Session->read('User.id'),
                'username'=>$this->Session->read('User.username'),
                'preparedby'=>$masterListOfFormat['PreparedBy']['name'],
                'filekey'=>$key,
                'createurl'=>$file_path,
                'record_id'=>$record_id
            ));
        } 
    }
}else{

    $allowedfiletypes = array('doc','docx','xls','xlsx');    
    if($filetype == 'doc' || $filetype == 'docx'){
        $documentType = 'word';
    }

    if($filetype == 'xls' || $filetype == 'xlsx'){
        $documentType = 'cell';
    }

    mkdir($folder_path);


    echo $this->element('onlyoffice',array(
        'url'=>$url,
        'placeholderid'=>$placeholderid,
        'panel_title'=>'Document Viewer',
        'mode'=>$mode,
        'path'=>$file_path,
        'file'=>$file,
        'filetype'=>$filetype,
        'documentType'=>$documentType,
        'userid'=>$this->Session->read('User.id'),
        'username'=>$this->Session->read('User.username'),
        'preparedby'=>$masterListOfFormat['PreparedBy']['name'],
        'filekey'=>$filekey
    ));
}   
?> 
