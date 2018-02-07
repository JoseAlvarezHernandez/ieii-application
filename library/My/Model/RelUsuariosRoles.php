<?php
/**
 * Archivo de definición de modelo
 * 
 * @author [EPG] 2014-02-26
 * @package library.model
 */

/**
 * Clase modelo para manipulación de información de tabla asociativa entre usuarios y roles
 *
 * @author [EPG] 2014-02-26
 * @package library.model
 */
class My_Model_RelUsuariosRoles extends My_Db_Table
{
	  
	protected $_name    = 'rel_usuarios_roles';
   	protected $_primary = 'idRelUsuariosRoles';

   	/**
   	 * Este metodo regresa un id de Usuario al que esta asignado un rol
   	 * [EPG] 2014-02-24
   	 */
   	public function usuarioAsignado($idRol){
   		$sql = "SELECT u.idUsuario, u.cNombre
   		FROM rel_usuarios_roles rur
   		LEFT JOIN usuarios u ON rur.idUsuarios = u.idUsuario
   		WHERE rur.idRoles = $idRol";
   		 
   		return $this->query($sql);
   	}
}