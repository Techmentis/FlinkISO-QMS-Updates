<?php
App::uses('AppController', 'Controller');
/**
 * Files Controller
 *
 * @property File $File
 * @property PaginatorComponent $Paginator
 */
class FilesController extends AppController {
    
    public function initial_add($model = null, $controller = null, $user_id = null, $qc_document_id = null, $name = null, $type = null, $file_key = null, $custom_table_id = null, $record_id = null) {
        $data['File']['name'] = $this->request->params['named']['name'];
        $data['File']['file_type'] = $this->request->params['named']['file_type'];
        $data['File']['file_status'] = 0;
        $data['File']['file_key'] = $this->request->params['named']['file_key'];
        $data['File']['qc_document_id'] = $this->request->params['named']['qc_document_id'];
        $data['File']['custom_table_id'] = $this->request->params['named']['custom_table_id'];
        $data['File']['model'] = $this->request->params['named']['file_model'];
        $data['File']['controller'] = $this->request->params['named']['file_controller'];
        $data['File']['prepared_by'] = $data['File']['modified_by'] = $this->Session->read('User.employee_id');
        $data['File']['created'] = date('Y-m-d h:i:s');
        $data['File']['user_id'] = $this->request->params['named']['user_id'];
        $data['File']['record_id'] = $this->request->params['named']['record_id'];
        $this->File->create();
        $this->File->save($data, false);
        return $this->File->id;
    }    
}


