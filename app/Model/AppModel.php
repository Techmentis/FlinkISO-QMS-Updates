<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses("Model", "Model");

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model
{
    var $actsAs = ["WhoDidIt"];

    public function beforeSave($options = [])
    {        
        foreach ($this->data[$this->alias] as $key => $value) {
            if($value && !is_array($value))$this->data[$this->alias][$key] = ltrim(rtrim($value));
        }
    }

    public function beforeFind($query)
    {
        
    }

    public function afterSave($created, $options = []){

    }

    public function afterDelete(){
        if($this->alias == 'Template' || $this->alias == 'File' ){
            // delete files
            if($this->alias == 'Template'){
                $path = Configure::read('files') . DS . 'templates'. DS . $this->id;
                $folder = new Folder($path);
                if($this->id)$folder->delete();    
            }
            
            if($this->alias == 'File'){
                $path = Configure::read('files') . DS . 'files'. DS . $this->id;
                $folder = new Folder($path);
                if($this->id)$folder->delete();
            }

            if($this->alias == 'CustomTable'){
                $path = Configure::read('files') . DS . 'custom_tables'. DS . $this->id;
                $folder = new Folder($path);
                if($this->id)$folder->delete();
            }

            if($this->alias == 'QcDocument'){
                $path = Configure::read('files') . DS . 'qc_documents'. DS . $this->id;
                $folder = new Folder($path);
                if($this->id)$folder->delete();
            }

            if($this->alias == 'Process'){
                $path = Configure::read('files') . DS . 'processes'. DS . $this->id;
                $folder = new Folder($path);
                if($this->id)$folder->delete();
            }

            if($this->alias == 'PdfTemplate'){
                $path = Configure::read('files') . DS . 'pdf_template'. DS . $this->id;
                $folder = new Folder($path);
                if($this->id)$folder->delete();
            }
        }

        if($this->alias == 'CustomTable'){
            // delete graph panels
            $this->loadmodel('GraphPanel');
            $this->GraphPanel->deleteAll(array('GraphPanel.custom_table_id'=>$this->id));
        }

        if($this->alias == 'Standard'){
            $deletearray = array(
                'Clause',
                'QcDocument',
                'QcDocumentCategory'             
            );

            foreach($deletearray as $delmodel){
                $this->loadmodel($delmodel);
                debug($delmodel);
                $this->$delmodel->deleteAll(array($delmodel.'.standard_id'=>$this->id));
            }            
        }

        if($this->alias == 'QcDocument'){
            $deletearray = array(
                'ChildQcDocument'                
            );

            foreach($deletearray as $delmodel){
                $this->loadmodel($delmodel);
                $this->$delmodel->deleteAll(array($delmodel.'.qc_document_id'=>$this->id));
            }                
        }

        if($this->alias == 'File'){
                $path = Configure::read('files') . DS . 'files'. DS . $this->id;
                $folder = new Folder($path);
                if($this->id)$folder->delete();
            }
    }    
}
