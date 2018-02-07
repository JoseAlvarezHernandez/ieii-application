<?php
/**
 * Archivo de definición de clase
 * 
 * @package library.my.cache
 *
 */

/**
 * Clase para manipulación de objetos de caché
 *
 * @package library.my.cache
 *
 */
class My_Cache_Adapter
{
    /**
     * @var Zend_Cache
     */
    private $_cacheAdapter;

    /**
     * Obtener instancia de objeto solicitado identificado por sección
     * 
     * @param string $seccion Sección a la que corresponde el objeto de caché (home, nota, etc)
     */
    public function __construct($seccion) {
        $this->_cacheAdapter = Zend_Registry::getInstance()->cacheAdapter[$seccion];
        return $this->_cacheAdapter;
    }

    /**
     * Establece duración en objeto de caché
     *
     * @param int $duracion Duración en segundos
     * @return void
     */
    public function setLifeTime($duracion) {
	$this->_cacheAdapter->setDirectives(array('lifetime' => $duracion));
    }
}
