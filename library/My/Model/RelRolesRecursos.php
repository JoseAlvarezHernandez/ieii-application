<?php
/**
 * Archivo de definición de modelo
 * 
 * @author [EPG] 2014-02-24
 * @package library.model
 */

/**
 * Clase modelo para manipulación de información de tabla roles
 *
 * @author [EPG] 2014-02-24
 * @package library.model
 */
class My_Model_RelRolesRecursos extends My_Db_Table
{
	  
	protected $_name    = 'rel_roles_recursos';
   	protected $_primary = 'idRelRolesRecursos';
   	
   	/**
   	 * Este metodo regresa un id de Rol al que esta asignado un recurso
   	 * [EPG] 2014-02-24
   	 */
   	public function recursoAsignado($idRecurso){
   		$sql = "SELECT r.idRol, r.cNombre
				FROM rel_roles_recursos rrr
				LEFT JOIN roles r ON rrr.idRol = r.idRol
				WHERE rrr.idRecurso = $idRecurso";
   		
   		return $this->query($sql);
   	}
   	
   	/**
   	 * Este metodo regresa un id de Recurso al que esta asignado un rol
   	 * [EPG] 2014-02-24
   	 */
   	public function rolAsignado($idRol){
   		$sql = "SELECT r.idRol, r.cNombre
   		FROM rel_roles_recursos rrr
   		LEFT JOIN roles r ON rrr.idRol = r.idRol
   		WHERE rrr.idRol = $idRol";
   		 
   		return $this->query($sql);
   	}
   	
   	/**
   	 * Este metodo regresa una lista de recursos asignados a un rol
   	 * @param int $idRol
   	 * [EPG] 2014-03-03
   	 */
   	public function getRecursosAsignados($idRol){
   		$sql = "SELECT r.idRecurso as id, CONCAT(r.cNombre,':',r.cDescripcion) AS name  FROM rel_roles_recursos rrr
				LEFT JOIN recursos r ON rrr.idRecurso = r.idRecurso
				WHERE rrr.idRol = $idRol";
   		return $this->query($sql);   		
   	}
   	/**
   	 * Este metodo regresa una lista de recursos que no estan asignados a un rol
   	 * @param int $idRol
   	 * [EPG] 2014-03-03
   	 */
	public function getRecursosNoAsignados($idRol){
		$sql = "SELECT r.idRecurso as id, CONCAT(r.cNombre,':',r.cDescripcion) AS name
				FROM recursos r
				LEFT JOIN rel_roles_recursos rrr ON r.idRecurso = rrr.idRecurso AND idRol = $idRol
				WHERE rrr.idRelRolesRecursos IS NULL";
		return $this->query($sql);
	}
}