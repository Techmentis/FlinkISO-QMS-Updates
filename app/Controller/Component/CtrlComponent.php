<?php
class CtrlComponent extends Component {

    /**
     * Return an array of user Controllers and their methods.
     * The function will exclude ApplicationController methods
     * @return array
     */
    public function get() {        
        $controllers = array();
        $aCtrlClasses = App::objects('controller');
        foreach ($aCtrlClasses as $controller) {
            if ($controller != 'AppController') {  
                App::import('Controller', str_replace('Controller', '', $controller));                
                $aMethods = get_class_methods($controller);                
                foreach ($aMethods as $idx => $method) {
                    if ($method[0] == '_') {
                        unset($aMethods[$idx]);
                    }
                }
                App::import('Controller', 'AppController');
                $parentActions = get_class_methods('AppController');                
                $controllers[$controller] = array_diff($aMethods, $parentActions);
                $aMethods = null;
                $controller = null;

            }
        }
        unset($controllers['CustomTablesController']);
        return $controllers;
    }

    public function get_defaults(){
      $default_access ='{}';

      return $default_access;
  }
}
