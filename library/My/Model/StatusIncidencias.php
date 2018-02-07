<?php 

class My_Model_StatusIncidencias extends My_Db_Table  {
	protected $_name = 'statusIncidencias';
	protected $_primary = 'idStatusIncidencia';
	/**
	 * Regresa una lista de status para usarse en un select
	 * ARRAY con la lista de elementos
	 */
	function getLista(){
		$sql = "SELECT
					idstatusIncidencia AS id, cNombre AS name
				FROM statusIncidencias";
		return $this->query($sql);
	}
	/**
	 * Obtiene una lista paginada de los items solicitados
	 * [EPG] 2014-07-09
	 * PARAMS
	 * 		page:	La pagina a partir de la cual se requieren los elementos
	 * RETURN:
	 * 		array	Con los datos de los status
	 */
	public function getItemsPaged($page = 0){
		$elementos = 40;
		$where = null;
		$inicio = $page*$elementos;

		$sql = "SELECT idStatusIncidencia, cNombre 
		FROM statusIncidencias ORDER BY idStatusIncidencia ASC limit $inicio,$elementos";
	
		$result = $this->queryTotal($sql);
	
		return array("total"=>$this->getTotal(),'items' => $result, 'elementos'=>$elementos);
	}
	/**
	 * Metodo que regresa la info de un status de Incidencia especifico
	 * [EPG] 2014-07-09
	 */
	public function getInfo($idStatusIncidencia){
		$sql = "SELECT idStatusIncidencia, cNombre FROM statusIncidencias WHERE idStatusIncidencia = $idStatusIncidencia";
		$result =  $this->query($sql);
		return $result[0];
	}
}