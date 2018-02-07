<?php
/**
 * Model de mantenimientos
 * 
 * @author [EPG] 2014-08-12
 * @package library.model.
 */

/**
 * Clase modelo para manipulación de información de tabla mantenimientos
 *
 * @author [EPG] 2014-08-12
 * @package library.model.mantenimientos
 */
class My_Model_Mantenimientos extends My_Db_Table
{
	  
	protected $_name    = 'mantenimientos';
   	protected $_primary = 'idMantenimiento';
   	
   	/**
   	 * Obtiene una lista de mantenimientos para los parametros de busqueda recibidos
   	 */
   	public function getLista($page = 0){
   		$elementos = 40;
   		$inicio = $page*$elementos;
   		$sql = "SELECT 
					m.idMantenimiento, m.idItem, m.dtFechaProgramada, m.idUsuarioAtiende, m.cObservaciones, m.iStatus, i.cNombre as nombreItem, CONCAT(u.cNombre,' ',u.cApellido) AS responsable
				FROM mantenimientos m
				LEFT JOIN items i ON m.idItem = i.idItem
				LEFT JOIN usuarios u ON m.idUsuarioAtiende = u.idUsuario
   				ORDER BY idMantenimiento ASC limit $inicio,$elementos";
   		
   		$result = $this->queryTotal($sql);
   		
   		
   		return array("total"=>$this->getTotal(),'items' => $result, 'elementos'=>$elementos);
   	}

   	/**
   	 * Obtiene la informacion de un mantenimiento especificado
   	 * [EPG] 2014-08-15
   	 */
   	public function getInfo($idMantenimiento){
   		$sql = "SELECT idMantenimiento, idItem, dtFechaProgramada, idUsuarioAtiende, cObservaciones, iStatus, SUBSTRING(dtFechaProgramada,1,10) AS soloFecha
				FROM mantenimientos
				WHERE idMantenimiento = $idMantenimiento LIMIT 1";
   		$result = $this->query($sql);
   		return $result[0];
   	}
   	/**
   	 * Regresa la lista completa de recursos
   	 * [EPG] 2014-06-16
   	 */
   	public function getFullList(){
   		$sql = "SELECT idRecurso, cNombre, cDescripcion
   		FROM recursos";
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
