<?php 
/**
 * Archivo de definición de controlador de errores
 * 
 * @author [EPG] 2014-06-09
 * @package application.modules.default.controllers
 */

/**
 * ErrorController
 * 
 * @author [EPG] 2014-06-09
 * @package application.modules.default.controllers
 */ 
class ErrorController extends My_Controller_Action { 
    /**
     * init () inicializara el layout al que tenemos que redirigir al usuario cuando se presente un problema
     * @see My_Controller_Action::init()
     */
    public function init() { parent::init(); $this->view->layout()->setLayout('layouterror'); }
    
    /**
     * errorAction() is the action that will be called by the "ErrorHandler" 
     * plugin.  When an error/exception has been encountered
     * in a ZF MVC application (assuming the ErrorHandler has not been disabled
     * in your bootstrap) - the Errorhandler will set the next dispatchable 
     * action to come here.  This is the "default" module, "error" controller, 
     * specifically, the "error" action.  These options are configurable, see 
     * {@link http://framework.zend.com/manual/en/zend.controller.plugins.html#zend.controller.plugins.standard.errorhandler the docs on the ErrorHandler Plugin}
     *
     * @return void
     */
    public function errorAction() 
    { 
        // Ensure the default view suffix is used so we always return good 
        // content
        $this->_helper->viewRenderer->setViewSuffix('phtml');

        // Grab the error object from the request
        $errors = $this->_getParam('error_handler'); 

        // $errors will be an object set as a parameter of the request object, 
        // type is a property
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 

                // 404 error -- controller or action not found 
                $this->getResponse()->setHttpResponseCode(404); 
                $this->view->message = 'P&aacute;gina no encontrada'; 
                
                break; 
            default: 
             	$e = $errors->exception;
             	//$idRol  = $this->_identidad[0]["idRoles"][0]->idRoles;
             	
           		if ((defined('APPLICATION_ENVIRONMENT') && APPLICATION_ENVIRONMENT != 'production')) {
                	Zend_Debug::dump($e);
                	die();
            	} else {
					$code   = $e->getCode();
			    	$line   = $e->getLine();
			    
            		// Guardar Log de error
            	    $mensaje = 'Error ' . $code . '(' . $e->getFile() . ' - ' . $line . '): ' . "\n" .
            	    			'URL:' . $_SERVER['REQUEST_URI']  . "\n" .
            	    	        $e->getMessage() . '-' . $e->getTraceAsString();           	   
		   	    }
		   	    
                // application error 
                $this->getResponse()->setHttpResponseCode(500); 
                $this->view->message = 'La p&aacute;gina que solicitaste se encuentra en mantenmiento temporal.' . "<br>" .$mensaje; 
                break; 
        } 

        // pass the environment to the view script so we can conditionally 
        // display more/less information
        $this->view->env       = $this->getInvokeArg('env'); 
        
        // pass the actual exception object to the view
        $this->view->exception = $errors->exception; 
        
        // pass the request to the view
        $this->view->request   = $errors->request;
         
    } 
    
}

