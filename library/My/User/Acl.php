<?php
/**
 * Archivo de definición de clase
 *
 * @author   Jose de Jesus Alvarez Hernandez
 * @package  library.my.user
 * 
 */

/**
 * Clase que hereda la definición de la clase Zend_Acl con métodos
 * añadidas para uso de la aplicación
 * 
 * @author   Jose de Jesus Alvarez Hernandez
 * @package  library.my.user
 * 
 */
class My_User_Acl extends Zend_Acl
{

	/**
	 * Revisa todos los roles existentes en el objeto ACL.
	 * Devuelve true si por lo menos alguno de 
	 * los roles tiene permiso al recurso indicado (con $all == false - Predeterminado).
	 * devuevel true si todos los roles tienen
	 * permisos para el recurso indicado (con $all == true).
	 * 
	 * @param Zend_Acl_Resource_Interface|string $resource
	 * @param string 							 $privilege
	 * @param boolean							 $all
	 * @return boolean
	 */
	public function areAllowed($resource = null, $privilege = null, $all = false)
	{
		$areAllowed = $all;
		
		// Recorre todos los roles asignados al objeto ACL
		foreach ($this->getRoles() as $rolKey=>$rolDet) {
			if ($this->isAllowed($rolKey, $resource, $privilege)) {
				if ($all) { // Si la validación se encuentra habilitada (true)
					$areAllowed &= true;
				} else { // Validación no habilitada (false) predeterminada
					$areAllowed |= true;
				}
			}
		}
		
		return $areAllowed;
	}
	
	/**
	 * Método para validación de método descontinuado en Zend 1.10
	 * 
	 * @return array of registered roles
	 */
	public function getRoles() {
		if (Zend_Version::compareVersion('1.10') >= 0) {
	        $roles = $this->getRegisteredRoles();
	    } else {
	        $roles = parent::_getRoleRegistry()->getRoles();
	    }
	    return $roles;
	}
	
}