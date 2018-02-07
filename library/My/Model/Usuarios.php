<?php
/**
 * Archivo de definición de modelo
 * 
 * @author [EPG]
 * @package library.model.usuarios
 */

/**
 * Clase modelo para manipulación de información de tabla usuarios
 *
 * @author [EPG]
 * @package library.model.usuarios
 */
class My_Model_Usuarios extends My_Db_Table
{
	  
	protected $_name    = 'usuarios';
   	protected $_primary = 'idUsuario';
   	
	/**
	 * Obtiene identidad de un usuario
	 * 
	 * @param string $username
	 * @return array
	 */
	public function getIdentidad($username)
	{
		$cols      = array('idUsuario', 'cNombre', 'cApellido', 'cCorreo',
						   'cUsuario', 'dtContrasena', 'iActivo','iExtension');
		$myAdapter = $this->getAdapter();
		$mySelect  = $myAdapter->select();
		$mySelect->from($this->_name, $cols)
				 ->where("usuarios.cUsuario = '" . $username . "'");
		$identidad 				   = $mySelect->query()->fetchAll();
		$identidad[0]['idRoles']   = $this->_getRoles($identidad[0]['idUsuario']);
		
		return $identidad;
	}
	
	/**
	 * Obtiene listado de roles asignados a recurso indicado
	 * 
	 * @param $idUsuario
	 * @return array
	 */
	private function _getRoles($idUsuario)
	{
		$validador = new My_Validador_IO();
		$myAdapter = $this->getAdapter();
		$mySelect  = $myAdapter->select();
		$mySelect->from('rel_usuarios_roles as rur', 'idRoles')
				 ->where('rur.idUsuarios = ' . $validador->intValido($idUsuario));
		$roles	   = $mySelect->query()->fetchAll(Zend_Db::FETCH_OBJ);
		return $roles;
	}
	
   	
	/**
	 * Conforma existencia de histórico de passwords para el usuario asignado
	 * 
	 * @param string $username Usuario a consultar
	 * @param string $password Contraseña por validar
	 * @param int    $last	   Comprar contra al menos N cantidad de passwords históricos
	 * @return bool
	 */
	public function getHistoricoPassword($username, $password, $last = 10)
	{
		try { 
			$myAdapter   = $this->getAdapter();
			$mySelect    = $myAdapter->select();
			$mySelect->from($this->_name, array('idUsuario'))
					 ->where("usuarios.cUsuario = '" . $username . "'")
					 ->join('historicoUsuarios as hu',
					 		"usuarios.idUsuario = hu.idUsuario AND hu.cContrasena = '" . $password . "'", 
					 		 array())
					 ->order('hu.idHistoricoUsuario DESC')
					 ->limit($last);
			$lastPasswords = $mySelect->query()->fetchAll();
		} catch(Zend_Exception $e) {
			echo $e->getMessage();
		}

		return (count($lastPasswords) > 0)?true:false;
	}

	/**
	 * Develve página de usuarios
	 * 
	 * @param integer $pagina
	 * @param integer $registros
	 * @param string $palaba
	 * @return array
	 */
    public function getUsuarios($pagina = 1, $registros = 20, $palabra = null){
    	
    	$valid      = new My_Validador_IO();
    	$palabra    = $valid->wysiwyg($palabra);
    	$pagina     = $valid->intValido($pagina);
    	$registros  = $valid->intValido($registros);
   	
		$select = $this->select()
			 		   ->limitPage($pagina, $registros);
			 		   
        if ($palabra !== null) {
            $select->where("cNombre LIKE '%" . $palabra . "%'");
            $select->orWhere("cApellido LIKE '%" . $palabra . "%'");
            $select->orWhere("cCorreo LIKE '%" . $palabra . "%'");
            $select->orWhere("cUsuario LIKE '%" . $palabra . "%'");
        }

		$results      = $this->fetchRows($select);
        $this->_setTotal($this->getTotalRows());
        
		return $results;
	}
	
	/**
	 * Este metodo regresa una lista de usuarios segun los parametros de busqueda
	 */
	public function buscar($page,$titulo,$where = null){
		$elementos = 30;
		if($titulo!='')
			$where[] = "(u.cNombre like '%$titulo%' OR u.cApellido like '%$titulo%' OR u.cUsuario like '%$titulo%')";
		$limit = $page * $elementos;
		
		$sql = "SELECT u.idUsuario, u.cNombre, u.cApellido, u.cCorreo, u.cUsuario, u.iActivo AS status, r.cNombre AS roltxt, u.iExtension
				FROM usuarios u
				LEFT JOIN rel_usuarios_roles rur ON u.idUsuario = rur.idUsuarios
				LEFT JOIN roles r ON rur.idRoles = r.idRol
		";
		if($where)
				$sql .= " WHERE ".implode(" AND ", $where);
		
		$sql .=" LIMIT ".$limit.",".$elementos;
		
		$items = $this->queryTotal($sql);
		$total = $this->getTotal();
		
		return array('items' => $items, 'total' => $total, 'elementos' => $elementos);
	}
	
	/**
	 * Este metodo regresa la info de un usuario en especifico para el modulo de edicion
	 * [EPG] 2014-03-12 =)
	 */
	public function getInfo($idUsuario){
		$sql = "SELECT 
				    u.idUsuario, u.cNombre, u.cApellido, u.cCorreo, u.cUsuario, u.iActivo, rur.idRoles, u.iExtension
				FROM usuarios u
				LEFT JOIN rel_usuarios_roles rur ON u.idUsuario = rur.idUsuarios
				WHERE 
				    u.idUsuario = $idUsuario
				LIMIT 1";
		$result = $this->query($sql);
		
		return $result[0];
	}
	/**
	 * Metodo que regresa el numero de extension del usuario especificado
	 * [EPG] 2014-07-31
	 */
	public function getExtension($idUsuario){
		$sql = "SELECT iExtension FROM usuarios where idUsuario = $idUsuario";
		$result = $this->query($sql);
		
		return $result[0]["iExtension"];
	}
	/**
	 * Devuelve una lista completa de usuarios para usarse en un select
	 * [EPG] 2014-08-15
	 */
	public function getListaSelect(){
		$sql = "SELECT 'A99' AS valor, '- Seleccione -' AS nombre, '0' AS ord
				UNION
				SELECT idUsuario, CONCAT(cNombre,' ',cApellido), '1'
				FROM usuarios WHERE iActivo = 1
				ORDER BY ord ASC, nombre ASC";
		return $this->query($sql);
	}
}