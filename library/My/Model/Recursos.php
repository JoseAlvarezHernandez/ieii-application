<?php
/**
 * Model de recursos
 * 
 * @author [EPG]
 * @package library.model.
 */

/**
 * Clase modelo para manipulación de información de tabla recursos
 *
 * @author [EPG]
 * @package library.model.usuarios
 */
class My_Model_Recursos extends My_Db_Table
{
	  
	protected $_name    = 'recursos';
   	protected $_primary = 'idRecurso';
   	
   	/**
   	 * Obtiene recursos para un rol solicitado
   	 *  
   	 * @param integer $idRol  Identificador de rol
   	 * @return array
   	 */
   	public function getRecursos($idRol = 0) 
   	{
   		$cols	   = array('cNombre', 'cControllerAction', 
   						   'cDescripcion', 'idRecurso');
		$myAdapter = $this->getAdapter();
		$mySelect  = $myAdapter->select();
		$mySelect->from($this->_name, $cols);

        if ($idRol != 0) {
            $mySelect->where("rrr.idRol = '" . $idRol . "'")
			    	 ->joinLeft('rel_roles_recursos as rrr', 
				     			'recursos.idRecurso = rrr.idRecurso',
				 	    		'idRol');
        }
		return $myAdapter->fetchAll($mySelect);
   	}
   	
   	/**
   	 * Obtiene una lista de recursos para los parametros de busqueda recibidos
   	 */
   	public function getLista($titulo = '',$page = 0){
   		$elementos = 40;
   		$inicio = $page*$elementos;
   		$sql = "SELECT idRecurso, cNombre, cControllerAction, cDescripcion 
   				FROM recursos ".($titulo!='null'?"WHERE cNombre LIKE '%$titulo%' OR cControllerAction LIKE '%$titulo%' OR cDescripcion LIKE '%$titulo%' ":'')."
   				ORDER BY idRecurso ASC limit $inicio,$elementos";
   		
   		$result = $this->queryTotal($sql);
   		
   		
   		return array("total"=>$this->getTotal(),'items' => $result, 'elementos'=>$elementos);
   	}

   	/**
   	 * Obtiene la informacion de un recurso especificado
   	 */
   	public function getInfo($idRecurso){
   		$sql = "SELECT idRecurso, cNombre, cControllerAction, cDescripcion
   				FROM recursos WHERE idRecurso = $idRecurso LIMIT 1";
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
