<?php
/**
 * Definición de clase Plugin
 * 
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.my.controller.plugin
 */

/**
 * Plugin controlador para manejo de autenticación y ACL
 *  
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.my.controller.plugin
 */
class My_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{

	/**
	 * Ejecución previa
	 * 
	 * @return void
	 */	
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{			
		$moduleName		= $request->getModuleName();
		$controllerName = $request->getControllerName();
		$actionName     = $request->getActionName();
		
		// Validar identidad de usuario
        $auth 	 = Zend_Auth::getInstance();
		$acl  = "";
		if ($controllerName != 'auth' && false == $auth->hasIdentity()) { // Enviar a página de inicio de sesión
			$acl  = $this->_getAcl(null);
			$accesoPermitido = false;

			// En caso de que el módulo exista 
	        // revisar si cuenta con permisos de acceso
	        if ($acl->has($moduleName)) {
	            // Revisando permisos para rol nulo
            	$accesoPermitido |= $acl->isAllowed('anonymous', $moduleName,$controllerName. '_' .$actionName);
            	
			}

			// Enviar a página de inicio de sesión si es requerida
			if (false == $accesoPermitido) {
        	    $paramNS = new Zend_Session_Namespace('paramNS');
        	    $paramNS->oldParams = $request->getParams();
				/*if($movil){
					$request->setModuleName('usuarios')
					->setControllerName('login')
					->setActionName('movil');
				}else
					*/
					$request->setModuleName('default')
					->setControllerName('index')
					->setActionName('index');
				    	    
			}
		} else { // Cargar configuración de usuario validado
        	$identidad 	   = $auth->getIdentity();
        	$identidad = $this->getMenu($identidad);
        	
        	// Revisar fecha de expiración de contraseña
	        $password      = Zend_Registry::get('main')->get('vigenciapsw')->toArray();
	       	$date          = new Zend_Date();
	       	$myDate        = new Zend_Date($identidad[0]['dtContrasena']);
	       	$diasPassword  = round(($date->toValue(Zend_Date::TIMESTAMP) - strtotime($identidad[0]['dtContrasena'])) / 3600 / 24);
	       	$expira  	   = $password['expira'] - $diasPassword;

        	// Asignar valores expira e identidad en parámetros genéricos
	       	$request->setParam('expira', $expira)
		            ->setParam('identidad', $identidad);

        	if (false == ($moduleName == 'usuarios' && 
	        			  $controllerName == 'own' &&
	        		  	  $actionName == 'passchange')
	        		  	   &&
                false == ($moduleName == 'usuarios' && 
	        			  $controllerName == 'auth')
	        		  	  ) {
		        // Forzar a mostrar módulo de cambio de contraseña cuando esta haya expirado
		        if ($expira < 1) {    	
			        if (false == ($moduleName == 'usuarios' && 
			        	$controllerName == 'own' && 
			        	$actionName == 'formpass'
			        	)) {
			        		$request->setModuleName('usuarios')
			        		        ->setControllerName('own')
			        		        ->setActionName('formpass');
			        }
		        }
		        
		        $accesoPermitido = false;
		        
		        // Obtener objeto ACL para la asesión activa desde caché
		        $aclCache 	 = Zend_Registry::getInstance()->cacheAdapter['acl'];
		        if($aclCache){
		        	$mdIdentidad = substr(md5(Zend_Json::encode($identidad)), 0, 12);
		        	$acl 	     = $aclCache->load($mdIdentidad);
		        }

				// Obtener acceso ACL desde base de datos 
				// si no existe en caché
				if (false == $acl) {					
					$acl  = $this->_getAcl($identidad);
					if($aclCache)
						$algo = $aclCache->save($acl, $mdIdentidad);
				}
				
				$request->setParam('acl', $acl);
				// En caso de que el módulo exista 
				// revisar si cuenta con permisos de acceso
				if ($acl->has($moduleName)) {
					// Revisando permisos para rol nulo
					$accesoPermitido |= $acl->isAllowed('all', $moduleName,
					                     		$controllerName. '_' .$actionName);
					
					// Revisando cada uno de los roles asignados 
					// a la cuenta de usuario activa en sesión
					if (false == $accesoPermitido) {
						foreach($acl->getRoles() as $rolId => $rolDetalle) {
							$accesoPermitido |= $acl->isAllowed($rolId, $moduleName,
						                     			  $controllerName. '_' .$actionName);
							$accesoPermitido |= $acl->isAllowed($rolId, $moduleName,
									$controllerName. '_all');
						}
					}
				}

				
				$nsIdentidad            = new Zend_Session_Namespace('identidad');
				$nsIdentidad->identidad = $identidad;

				// Permitir o denegar acceso
	        	if (false == $accesoPermitido) {
					$request->setModuleName('default')
				    	    ->setControllerName('error')
				            ->setActionName('error')
				            ->setDispatched(false);
				}
	        }
	    }
	}

	/**
	 * Devuelve ACL de usuario activo
	 * 
	 * @param array $identidad
	 * @return Zend_Acl
	 */
	private function _getAcl($identidad = array())
	{
		$acl = new My_User_Acl();
		$validador = new My_Validador_IO();

		// Recursos con accesos para todos los roles
		$acl->addResource(new Zend_Acl_Resource('default'));
		$acl->addResource(new Zend_Acl_Resource('acercade'));
		$acl->addResource(new Zend_Acl_Resource('partners'));
		$acl->addResource(new Zend_Acl_Resource('contactanos'));
		$acl->addResource(new Zend_Acl_Resource('servicios'));		
		$acl->addResource(new Zend_Acl_Resource('ingenieria'));
		$acl->addResource(new Zend_Acl_Resource('mantenimiento'));
		$acl->addResource(new Zend_Acl_Resource('instalaciones'));
		
		$acl->addRole(new Zend_Acl_Role('all'));
        $acl->addRole(new Zend_Acl_Role('anonymous'));
                		
		$acl->allow('anonymous', 'default', 'index_index');
		$acl->allow('anonymous', 'default', 'error_error');
		$acl->allow('anonymous', 'acercade', 'index_index');
		$acl->allow('anonymous', 'partners', 'index_index');
		$acl->allow('anonymous', 'contactanos', 'index_index');
		$acl->allow('anonymous', 'contactanos', 'index_index');
		$acl->allow('anonymous', 'contactanos', 'index_sendEmail');		
		$acl->allow('anonymous', 'servicios', 'index_index');
		
		$acl->allow('anonymous', 'ingenieria', 'index_index');
		$acl->allow('anonymous', 'mantenimiento', 'index_index');
		$acl->allow('anonymous', 'instalaciones', 'index_index');
        
		if (null !== $identidad) {
			$roles 	  = new My_Model_Roles();
			$recursos = new My_Model_Recursos();
			$rol 	  = array();
			
			// Recorrer roles asignados a la identidad del usuario 
			// firmado en sesión
			foreach($identidad as $dtIdentidad) {
				// Obteniendo lista de roles asignados
				$rol     = $roles->getRoles($validador->intValido($dtIdentidad['idUsuario']));

				// Agregando roles y recursos en objeto ACL
				foreach($rol as $dtRol) {
					// Agregando rol a objeto ACL
					$acl->addRole(new Zend_Acl_Role($dtRol['cNombre']));

					// Obtener recursos para el rol activo
					$recurso = $recursos->getRecursos($dtRol['idRoles']);
					
					// Agregar recursos al objeto ACL para el rol activo
					foreach ($recurso as $dtRecurso) {
						if (false == $acl->has($dtRecurso['cNombre'])) {
							$acl->addResource(new Zend_Acl_Resource($dtRecurso['cNombre']));
						}

						$acl->allow($dtRol['cNombre'], $dtRecurso['cNombre'], $dtRecurso['cControllerAction']);
					}
				}
			}
		}
		/*Zend_Debug::Dump($acl);
		die();*/
		return $acl;
	}
	
	private function getMenu($identidad) {
		//$identidad[0]['menu'] = 'master';        
        foreach($identidad[0]["idRoles"] as $rol) $lastRol = $rol->idRoles;
        switch ($lastRol) {
            case 1: $identidad[0]['menu'] = 'menu'; break;
            case 5: $identidad[0]['menu'] = 'menu'; break;
            default: $identidad[0]['menu'] = 'menu'; break;
        }
        /**/
        return $identidad;
	}
}
