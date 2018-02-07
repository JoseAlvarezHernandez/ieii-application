<?php
/**
 * Archivo de definición de clase Bootstrap
 * 
 * @package library
 * @author Jose de Jesus Alvarez Hernandez
 */

/**
 * Clase Bootstrap para inicialización de aplicación
 *
 * @package library
 * @author Jose de Jesus Alvarez Hernandez
 */
class Bootstrap {
    /**
     * Instancia estática
     *
     * @var Bootstrap
     */
    protected static $_instance = null;

    /**
     * Petición a partir de URL
     * 
     * @var string
     */
    protected $_module;
    
    /**
     * Adaptadores de base de datos
     *
     * @var array
     */
    protected $_dbAdapter;

    /**
     * Adaptadores de caché
     *
     * @var array
     */
    protected $_cacheAdapter;
    
    /**
     * Configuración de la aplicación
     *
     * @var Zend_Config
     */
    protected $_config = array();

    /**
     * Devuelve instancia estática de esta clase, creándola si es necesario
     *
     * @return Bootstrap
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Lanza aplicación
     *
     * @return Bootstrap
     */
    public function run($ip = null)
    {
    	
        $this->_setupAutoloader()
             ->_setupEnvironment()
             ->_loadConfig()
             ->_setupFrontController()
             ->_setupLayout()
             ->_setupCache()
             ->_setupDatabase($ip)
             ->_setupRegistry()
             ->_setupRoute()
             ->_dispatchFrontController();

        return $this;
    }
    
    /**
     * Inicializa y determina el ambiente de la aplicación
     *
     * @return Bootstrap
     */
    protected function _setupEnvironment()
    {
        // CONSTANTES DE APLICACIÓN - Asigna valores a las constantes de aplicación
		defined('APPLICATION_PATH')
		    or define('APPLICATION_PATH', dirname(__FILE__));
		
		// En caso de no estar definidas, se utilizará "development" como ambiente predeterminado
		defined('APPLICATION_ENVIRONMENT')
		    or define('APPLICATION_ENVIRONMENT', 'development');
		
        return $this;
    }

    /**
     * Configura Autolader
     *
     * @return Bootstrap
     */
    protected function _setupAutoloader()
    {
		require_once 'Zend/Version.php';

		if (Zend_Version::compareVersion('1.8') <= 0) {
	    	include_once 'Zend/Loader/Autoloader.php';
	        $autoLoader = Zend_Loader_Autoloader::getInstance();
	        $autoLoader->registerNamespace('My_');
	        $autoLoader->registerNamespace('default_');
		} else {
            include_once 'Zend/Loader.php';
	        Zend_Loader::registerAutoload();
		}
        return $this;
    }
	
    /**
     * Carga configuración de la aplicación
     *
     * @return Bootstrap
     */
    protected function _loadConfig()
    {
        // Obtener módulo
		$request       = new Zend_Controller_Request_Http();
		$elements      = explode('/', $request->getRequestUri());
        $this->_module = ($elements[1] == '')?'default':$elements[1];        
        
    	// CONFIGURATION - Setup the configuration object
		$configuration['main']		= new Zend_Config_Ini(APPLICATION_PATH . '/config/app.ini', APPLICATION_ENVIRONMENT);
		Zend_Registry::set('main', $configuration['main']);
		
		// @todo Implementar caché en carga de información INI
		
		// Cargar configuración solo para el módulo solicitado en caso de existir
		$appIniFile = APPLICATION_PATH . '/modules/' . $this->_module . '/app.ini';
		 
		if (true == file_exists($appIniFile)) {
		    $configuration[$this->_module] = new Zend_Config_Ini($appIniFile, APPLICATION_ENVIRONMENT);
		    Zend_Registry::set($this->_module, $configuration[$this->_module]);
		}
        
        $this->_config = $configuration;

        return $this;
    }	
	
	/**
	 * Configura Front Controller y Layout
	 *
	 * @return Bootstrap
	 */
	protected function _setupFrontController()
	{
		Zend_Controller_Front::getInstance()
             ->throwExceptions(false)
             ->setBaseUrl($this->_config['main']->baseUrl)
             ->addModuleDirectory(APPLICATION_PATH . '/modules')
             ->setParam('env', APPLICATION_ENVIRONMENT)
             ->registerPlugin(new My_Controller_Plugin_Auth())
             ->registerPlugin(new My_Controller_Plugin_ViewSetup())
             ->returnResponse(true);
		
		return $this;
    }
    
    /**
     * Configura Layout
     *
     * @return Bootstrap
     */
    protected function _setupLayout()
    {
    	Zend_Layout::startMvc(APPLICATION_PATH . '/layouts/scripts');
		$view = Zend_Layout::getMvcInstance()->getView();
		$view->doctype('XHTML1_STRICT');
		$view->addHelperPath(APPLICATION_PATH . '/../library/My/View/Helper', 'My_View_Helper');
		
		Zend_Dojo::enableView($view);
		
		return $this;
    }
    
	/**
     * Configura base de datos dinamicamente
     *
     * @return Bootstrap
     */
    protected function _setupDatabase($ip =null)
    {	
 // Arreglo de bases de datos
        $dbAdapters = array();
        // DATABASE ADAPTER - Setup the database adapter
        if ($this->_config['main']->db instanceof Zend_Config) {
            
            $dataBases = $this->_config['main']->db->toArray();
            foreach($dataBases as $dbName=>$dbConfig) {           	
                if ($dbName != 'adapter') {
                	if($ip != null) $dbConfig['host'] = $ip;
                    if (isset($dbConfig['authfile'])) {
                        $authConfig = $this->_loadAuthFile($dbConfig['authfile']);
                        $dbConfig   =  array_merge($dbConfig, $authConfig);
                        unset($dbConfig['authfile']);
                    }
                    $dbAdapters[$dbName] = Zend_Db::factory($dataBases['adapter'], $dbConfig);
                }
            }
        }
        
        // Bases de datos del módulo
        if (isset($this->_config[$this->_module]) && isset($this->_config[$this->_module]->db)) {
            if ($this->_config[$this->_module]->db instanceof Zend_Config) {
                
                $dataBases = $this->_config[$this->_module]->db->toArray();
                
                foreach($dataBases as $dbName=>$dbConfig) {
                    if ($dbName != 'adapter') {
                        $dbAdapters[$dbName] = Zend_Db::factory($dataBases['adapter'], $dbConfig);
                    }
                }
            }
        }
        $this->_dbAdapter  = $dbAdapters;
        // Adaptador predeterminado, el primer elemento encontrado
        Zend_Db_Table_Abstract::setDefaultAdapter(array_shift($dbAdapters));

        return $this;
    }
    
    /**
     * Carga datos a partir de archivo de autenticación
     * 
     * @param string $authfile
     * @param string $path
     * @throws Exception
     * @return array
     */
    private function _loadAuthFile($authfile, $path = null) {
        $path     = (is_null($path))?$_SERVER['DOCUMENT_ROOT']:$path;
        $authfile = $path . '/' .$authfile;
        $size     = filesize($authfile);
        
        if (false == file_exists($authfile)) {
            throw new Exception('No existe archivo de autenticación:' . $authfile, 666);
        }
        $size = filesize($authfile);
        $fp   = fopen($authfile, 'r');
        $auth = gzuncompress(fread($fp, $size), $size);
        $authParam = preg_split('/[|]+/', $auth);
        
        if (isset($authParam[0])) {
            $param['dbname']   = $authParam[0];
        }
        
        if (isset($authParam[1])) {
            $param['username'] = $authParam[1];
        }
        
        if (isset($authParam[2])) {
            $param['password'] = $authParam[2];
        }

        return $param;
    }
    
	/**
     * Configura opciones generales de Zend_Cache
     *
     * @return Bootstrap
     */
	protected function _setupCache()
	{
		$cacheAdaptersMain   = array();
		$cacheAdaptersModule = array();
		
    	// Adaptadores de caché main
        if (isset($this->_config['main']->cache) && $this->_config['main']->cache instanceof Zend_Config) {
        	$cacheAdaptersMain   = $this->_config['main']->cache->toArray();
        }

        // Adaptadores de caché por módulo
        if (isset($this->_config[$this->_module]->cache) && $this->_config[$this->_module]->cache instanceof Zend_Config) {
            $cacheAdaptersModule = $this->_config[$this->_module]->cache->toArray();
        }
        
		$cacheAdapters = array_merge($cacheAdaptersMain, $cacheAdaptersModule);
        
		foreach ($cacheAdapters as $adapterName=>$cache) {
        	switch ($cache['backend']) {
            	case 'File':
                	$backendOptions = array('cache_dir' => $cache['file']['dir'],
                    			           'file_name_prefix' => $cache['file']['prefix'],);
					break;
					
				case 'Memcached':
					$backendOptions = array();
					if (isset($cache['memcahed']['compression'])) {
                		$backendOptions['compression'] = $cache['memcached']['compression'];
					}
                    $servers                       = $cache['memcached']['servers'];
                                
                    // Transformando en arreglo de índice numérico
                    foreach ($servers as $server) {
                    	$backendOptions['servers'][] = $server;
                    }
                    break;
			}
			
			$frontendOptions            = $cache['frontend'];
       		$cacheAdapter[$adapterName] = Zend_Cache::factory('Core', $cache['backend'], $frontendOptions, $backendOptions);
       		
		}
		
        if (isset($cacheAdapter)) {
        	$this->_cacheAdapter = $cacheAdapter;
		}
		return $this;
	}

    /**
     * Configuración de registro
     *
     * @return Bootstrap
     */
    protected function _setupRegistry(){

		// REGISTRY - setup the application registry
		$registry = Zend_Registry::getInstance();
		$registry->configuration = $this->_config['main'];
		$registry->dbAdapter     = $this->_dbAdapter;
		$registry->cacheAdapter  = $this->_cacheAdapter;
		
		return $this;
    }

	/**
     * Configuración dinámica de Routers
     * Se deberán agregar routers en archivo app.ini según sea necesario con sintaxis similar:
     * 
     * 		router.nombre.route	     = nombremod/:controller/:varios  ;; ruta
     * 		router.nombre.module     = nombremod					  ;; modulo
     * 		router.nombre.controller = notashistoricas				  ;; controlador
     * 		router.nombre.action     = index						  ;; acción
     *
     * @return Bootstrap
     */
    protected function _setupRoute()
    {
        $routerMain   = array();
        $routerModule = array();
        
		// Cargando rutas principales
		if (isset($this->_config['main']->router) && $this->_config['main']->router instanceof Zend_Config) {
		    $routerMain = $this->_config['main']->router->toArray(); 
		}
		
		// Cargando rutas por módulo
		if (isset($this->_config[$this->_module]->router) && $this->_config[$this->_module]->router instanceof Zend_Config) {
            $routerModule = $this->_config[$this->_module]->router->toArray();
		}
		
		$router = array_merge($routerMain, $routerModule);
		
		// Generando routers
		$controller = Zend_Controller_Front::getInstance();
    	foreach($router as $routeName=>$routeOptions) {
    	    $controller->getRouter()
    	               ->addRoute($routeName, 
    	                          new Zend_Controller_Router_Route(array_shift($routeOptions), $routeOptions)
    	                          );
    	}
        
		return $this;

    }

    /**
     * Despacha Front Controller
     *
     * @return Bootstrap
     */
    protected function _dispatchFrontController()
    {
        // Lanza aplicación, si existe una excepción será lanzada desde aquí 
        // y atrapada en ErrorController del módulo default
		$expires = null;

		// Obtener parámetros de manejo de caché por página
    	if (isset($this->_config['main']->expires)) {
    		$expires = $this->_config['main']->expires->toArray();
    	}

    	// Aplicar caché por módulo (opcional)
    	if (isset($this->_config[$this->_module]->expires)) {
    		$expires = $this->_config[$this->_module]->expires->toArray();
    	}

    	// Si no hay caché definido fijarlo 
    	// en 30 segundos de duración
    	if (false == is_array($expires)) {
    	    $expires = array('time' => 30);
    	}        

    	// Aplicando reglas de caché para Headers
    	if ($expires['time'] <= 0) {
    	    $expires['pragma'] 		 = 'no-cache';
    		$expires['cachecontrol'] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
			$expires['time']         = gmdate("D, d M Y H:i:s",
                                              (time() + $expires['time'])) . ' GMT';
    	} else {
    	    $expires['pragma'] 		 = '';
    		$expires['cachecontrol'] = 'max-age=' . $expires['time'];
			$expires['time']         = gmdate("D, d M Y H:i:s",
                                             (time() + $expires['time'])) . ' GMT';
    	}
    	
    	// Inicializando Front Controller con Headers personalizados
    	Zend_Controller_Front::getInstance()->dispatch()
			                                ->setHeader('Expires', $expires['time'], true)
			                                ->setHeader('Pragma', $expires['pragma'], true)
	                                        ->setHeader('Cache-Control', $expires['cachecontrol'], true)
			                                ->sendResponse();

        return $this;
    }

    /**
     * Implementación de patrón Singleton, restringe el uso del operador "clone"
     *
     */
	private function __construct()
    {}

    /**
     * Implementación de patrón Singleton restringe el uso del operador "clone"
     *
     * @return void
     */
    private function __clone()
    {}
}
