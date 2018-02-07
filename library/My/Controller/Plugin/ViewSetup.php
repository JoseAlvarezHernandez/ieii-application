<?php
/**
 * Definición de clase Plugin
 * 
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.my.controller.plugin
 * @version 1.0.0
 */

/**
 * Plugin controlador para obtener nombre de modulo, controlador y accion; y enviarlo a la vista
 *  
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.my.controller.plugin
 */
class My_Controller_Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract {
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->init();
        $viewRenderer->view->module = $request->getModuleName();
        $viewRenderer->view->controller = $request->getControllerName();
        $viewRenderer->view->action = $request->getActionName();
        
        switch ($viewRenderer->view->module) {
            case 'default':
                $viewRenderer->view->moduleName = 'DASHBOARD';
            break;
            default:
                $viewRenderer->view->moduleName = $request->getModuleName();
            break;
        }
    }
} 