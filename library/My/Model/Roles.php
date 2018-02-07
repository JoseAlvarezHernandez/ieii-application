<?php
/**
 * Archivo de definición de modelo
 * 
 * @author [EPG]
 * @package library.model.roles
 */

/**
 * Clase modelo para manipulación de información de tabla roles
 *
 * @author [EPG]
 * @package library.model.roles
 */
class My_Model_Roles extends My_Db_Table
{
	  
	protected $_name    = 'roles';
   	protected $_primary = 'idRol';

	/**
	 * Obtiene lista de roles correspondientes a un usuario
	 * 
	 * @param integer $idUsuario
	 * @return array
	 */
	public function getRoles($idUsuario = 0) {
		$cols	   = array('cNombre', 'cDescripcion', 'idRol');
		$myAdapter = $this->getAdapter();
		$mySelect  = $myAdapter->select();
		$mySelect->from($this->_name, $cols);		         

		if ($idUsuario != 0) {
		    $mySelect->where("rur.idUsuarios = '" . $idUsuario . "'")
		              ->joinLeft('rel_usuarios_roles as rur', 
				 			'roles.idRol = rur.idRoles',
				 			'idRoles');
		}				 

		return $myAdapter->fetchAll($mySelect);
	}
	
	/**
	 * Obtiene una lista de roles para los parametros de busqueda recibidos
	 */
	public function getLista($titulo = '',$page = 0){
		$elementos = 20;
		$inicio = $page*$elementos;
		$sql = "SELECT idRol AS id, cNombre, cDescripcion
		FROM roles ".($titulo!='null'?"WHERE cNombre LIKE '%$titulo%' OR cDescripcion LIKE '%$titulo%' ":'')."
		ORDER BY cNombre ASC limit $inicio,$elementos";
		 
		$result = $this->queryTotal($sql);
		 
		 
		return array("total"=>$this->getTotal(),'items' => $result);
	}
	
	/**
	 * Metodo que obtiene la informacion de un rol especifico
	 * [EPG] 2014-02-26 
	 * $idRol int id del Rol del usuario
	 */
	public function getInfo($idRol){
		$sql = "SELECT idRol, cNombre, cDescripcion
		FROM roles WHERE idRol = $idRol LIMIT 1";
		$result = $this->query($sql);
		return $result[0];
	}
	/**
	 * Regresa una lista completa de roles para utilizarlo en un select incluyendo el elemento para seleccionar A99
	 * [EPG] 2014-06-17
	 * PARAMS: void
	 * RETURN: Array (Con id y name de cada rol)
	 */
	public function getSelectList(){
		$sql = "SELECT 'A99' AS id, '- SELECCIONE -' AS name UNION SELECT idRol, cNombre from roles";
		
		return $this->query($sql);
	}
}
