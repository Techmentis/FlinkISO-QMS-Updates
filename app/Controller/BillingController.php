<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class BillingController extends AppController {


    public function index(){
        exit;
    }

    public function monthly_usage(){

    }

    public function usage_details(){

    }

    public function daily_usage(){

    }

    public function generate_invoice(){


    }

    public function check_invoice_date(){

    }

    public function invoices($status = null){                



    }

    public function renew($invoice_number = null){

    }

    public function add_customer_details(){


    }

    public function view_invoice($id = null, $company_id = null){        

    }

    public function update(){
        if($this->Session->read('User.is_mr') == 0){
            $this->Session->setFlash(__('Unauthorised Access'));
            $this->redirect(array('controller'=>'users', 'action' => 'dashboard'));
        }

    }


    public function back_up(){

    }    


    public function authentication(){

        $result = $this->curl('get','users','authenticate');        
        $result = json_decode($result,true);  
        if($result['error']==0){
            echo "<span class='text-success'><h4>Success</h4></span><br />";            
        }else{
            echo "Authentication Failed.";
        }
        
    }

    public function backup(){
        $backupFolder = new Folder();
        $folder = ROOT . DS . 'backup' . DS . date('Y-m-d') . DS . 'app';
        if($backupFolder->create($folder)){
            $folderToCopy = new Folder(APP);
            $folderToCopy->copy(array(
                'to' => $folder,
                'from' => APP,
                'recursive' => true
            ));

            $folder = ROOT . DS . 'backup' . DS . date('Y-m-d') . DS . 'lib';
            $folderToCopy = new Folder(ROOT . DS . 'lib');
            $folderToCopy->copy(array(
                'to' => $folder,
                'from' => ROOT . DS . 'lib',
                'recursive' => true
            ));
            echo "<h4>Downloading Files...</h4><br />Backup created at <br /><strong>".$folder."/</strong></br>";
        }else{
            echo "<span class='text-danger'>Unable create <strong>" . $folder . "</strong>. Please create the folder manually will write permission for backup.</span>";            
        }

        $deteleFileFolder = new Folder();
        $deteleFileFolder->delete($folder. DS . 'webroot');

        $updateFolder = new Folder();
        $updateFolder->delete(WWW_ROOT. 'updates');

        $updateFolder = new Folder();
        $updateFolder->create(WWW_ROOT. 'updates');
    }

    public function downloading_update(){    

        if(!@copy('https://github.com/Techmentis/FlinkISO-QMS-Updates/archive/refs/heads/main.zip', WWW_ROOT. 'updates' . DS . 'update.zip'))
        {
            $errors = error_get_last();
            echo "<span class='text-danger'><strong>Failed error: ". json_encode($errors['message'].'</strong></span></br>');

        } else {
            try{
                exec('unzip ' . WWW_ROOT . 'updates' . DS . 'update.zip -d ' . WWW_ROOT. 'updates');    
            }catch(Exception $e){
                
            }
            try{
                exec('powershell -Command "Expand-Archive -Path '.$from.' -DestinationPath '.$to.'"');
            }catch(Exception $e){
                
            }
            
            exec('rm -rf ' . WWW_ROOT . 'updates' . DS . '__MACOSX');
            exec('rm -rf ' . WWW_ROOT . 'updates' . DS . 'update.zip');

            echo "<h4>Download Complete</h4><span class='text-success'><strong>Files/ Folders copied...</strong></span><hr /></br>";           
        }        
    }

    public function updating_sql(){

        echo "<span class='text-info'><h4>Downloading sql updates....</span></h4><hr /></br>";
        $copyfrom = WWW_ROOT . DS . 'updates' . DS . 'FlinkISO-QMS-Updates-main' . DS . 'app' . DS . 'webroot' . DS. 'updates' . DS . 'updates.sql';
        if(!file_exists($copyfrom))
        {
            $errors= error_get_last();
            $updatestr .= "<span class='text-danger'><strong>Sql failed error ". json_encode($errors['message'].'</strong></span></br>');
        } else {
            $sql = fopen($copyfrom, "r");
            $contents = stream_get_contents($sql);
            $contents = explode(PHP_EOL,$contents);
            foreach($contents as $sql){
                if($sql && $sql != ''){
                    try{
                        $this->Billing->query($sql);
                    }catch (Exception $e) {
                       echo "<span class='text-danger'>SQL Failed: " . $sql .'</span></br>';
                   }
               }                
           }

       }    
   }

 public function copy_files(){
        $downloadedFolder = new Folder(WWW_ROOT . DS . 'updates' . DS . 'FlinkISO-QMS-Updates-main' . DS . 'app');
        if($foldersTocopy = $downloadedFolder->copy(ROOT . DS . 'app')){            
        }else{
            CakeLog::write('debug','App folder copy failed');
        }
        
        $downloadedFolder = new Folder(WWW_ROOT . DS . 'updates' . DS . 'FlinkISO-QMS-Updates-main' . DS . 'lib');
        if($foldersTocopy = $downloadedFolder->copy(ROOT . DS . 'lib')){
            
        }else{
            CakeLog::write('debug','Lib folder copy failed');
        }
        echo "<span class='text-success'><h4>Update Complete!</span></h4></br>";
    }

}
