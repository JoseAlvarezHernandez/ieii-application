<?php 

class My_Model_CtipoIncidencias extends My_Db_Table  {
	protected $_name = 'cTipoIncidencias';
	protected $_primary = 'idTipoIncidencia';
	/**
	 * Regresa una lista de elementos id -> name para usarse en un select
	 * [Jose de Jesus Alvarez Hernandez] 2014-07-02
	 */
	function getListaSelect(){
		$sql = "SELECT 
					idTipoIncidencia AS id, cNombre AS name
				FROM cTipoIncidencias";
		return $this->query($sql);
	}
	/**
	 * Obtiene una lista paginada de los items solicitados
	 * [Jose de Jesus Alvarez Hernandez] 2014-07-10
	 * PARAMS
	 * 		int		page:	La pagina a partir de la cual se requieren los elementos
	 * RETURN:
	 * 		array	Con los datos de los tipos de Incidencias
	 */
	public function getItemsPaged($page = 0){
		$elementos = 40;
		$where = null;
		$inicio = $page*$elementos;
	
		$sql = "SELECT idTipoIncidencia, cNombre
		FROM cTipoIncidencias ORDER BY idTipoIncidencia ASC limit $inicio,$elementos";
	
		$result = $this->queryTotal($sql);
	
		return array("total"=>$this->getTotal(),'items' => $result, 'elementos'=>$elementos);
	}
	/**
	 * Metodo que regresa la info de un status de Incidencia especifico
	 * [Jose de Jesus Alvarez Hernandez] 2014-07-10
	 * PARAMS
	 * 		int	idTipoIncidencia:	El id del tipo de incidencia del que se requiere la informacion
	 * RETURN:
	 * 		array	Con los datos del tipo de Incidencia
	 */
	public function getInfo($idTipoIncidencia){
		$sql = "SELECT idTipoIncidencia, cNombre FROM cTipoIncidencias WHERE idTipoIncidencia = $idTipoIncidencia";
		$result =  $this->query($sql);
		return $result[0];
	}
}