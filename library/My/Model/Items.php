<?php
/**
 * Archivo de definición de modelo
 * 
 * @author [Jose de Jesus Alvarez Hernandez] 2014-06-17
 * @package library.model.items
 */

/**
 * Clase modelo para manipulación de información de tabla items
 *
 * @author [Jose de Jesus Alvarez Hernandez] 2014-06-17
 * @package library.model.items
 */
class My_Model_Items extends My_Db_Table
{
	  
	protected $_name    = 'items';
   	protected $_primary = 'idItem';
   	
   	/**
   	 * Obtiene una lista paginada de los items solicitados
   	 * [Jose de Jesus Alvarez Hernandez] 2014-06-17
   	 * PARAMS
   	 * 		titulo: El titulo para hacer la busqueda del poste
   	 * 		page:	La pagina a partir de la cual se requieren los elementos
   	 * 		type:	El tipo de item
   	 * RETURN:
   	 * 		array	Con los datos de los items
   	 */
   	public function buscaXnumero($origen , $destino ){
   		$sql = "SELECT idItem FROM items WHERE iExtension='$destino' or iExtension='$origen' ";
   		$result = $this->query($sql);   	
   		if(isset($result[0])){
   			$resulta = $result[0];
   		}else{
   			$resulta = null;
   		}
   		return $resulta;
   	}
   	public function getItemsPaged($titulo = '',$type = 0,$page = 0){
   		$elementos = 40;
   		$where = null;
   		$inicio = $page*$elementos;
   		if($titulo!='')
   			$where[] = "(cNombre LIKE '%$titulo%' OR cDescripcion LIKE '%$titulo%')";
   		if($type != 0)
   			$where[] = " iTipoItem = $type";
   		$wheretxt = "";
   		if( count($where) > 0 )
   			$wheretxt = " WHERE ".implode(" AND ", $where);
   		
   		$sql = "SELECT idItem, iTipoItem, cNombre, cDescripcion, cUbicacion, cTramo, fLatitud, fLongitud, cIp, iExtension
   		FROM items $wheretxt ORDER BY idItem ASC limit $inicio,$elementos";
   		 
   		$result = $this->queryTotal($sql);
   		 
   		return array("total"=>$this->getTotal(),'items' => $result, 'elementos'=>$elementos);
   	}
   	/**
   	 * Obtiene los datos de un item en especifico
   	 * [Jose de Jesus Alvarez Hernandez] 2014-06-18
   	 * PARAMS
   	 * 		int $idItem		El id del item del cual se requiere la info
   	 * RETURN
   	 * 		array			Un arreglo con los datos del item
   	 */
   	public function getInfo($idItem){
   		$sql = "SELECT i.idItem, i.iTipoItem, i.cNombre, i.cDescripcion, i.cUbicacion, cTramo, i.fLatitud, i.fLongitud, i.cIP, ti.cNombre AS tipoTxt, i.iExtension
				FROM items i
				LEFT JOIN cTipoItems ti ON i.iTipoItem = ti.idTipoItem 
				WHERE idItem = $idItem";
   		$result = $this->query($sql);
   		return $result[0];
   	}
   	/**
   	 * Obtiene una lista de los items del tipo solicitado
   	 * [Jose de Jesus Alvarez Hernandez] 2014-06-17
   	 * PARAMS
   	 * 		type:	El tipo de item
   	 * RETURN:
   	 * 		array	Con los datos de los items
   	 */
   	public function getAllItems($type = 0){
   		
		$sql = "SELECT idItem, iTipoItem, cNombre, cDescripcion, cUbicacion, cTramo, fLatitud, fLongitud, cIp, 
				iExtension, ipCamara, puertoCamara, usuarioCamara, passCamara,bVideo
   				FROM items 
				WHERE iTipoItem = $type 
				ORDER BY idItem ASC";
   	
   		$result = $this->query($sql);
   	
   		return $result;
   	}
}
