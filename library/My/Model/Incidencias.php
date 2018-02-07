<?php 

class My_Model_Incidencias extends My_Db_Table  {
	protected $_name = 'incidencias';
	protected $_primary = 'idIncidencia';
	
	/**
	 * Regresa una lista de incidencias a partir de la pagina solicitada
	 * PARAMS int $pagina El numero de pagina a partir del cual se desea la lista de incidencias
	 */
	function getReports($page,$order, $way){
		$elementos = 40;
		$where = null;
		$inicio = $page*$elementos;
				 
		$sql = "SELECT
					inc.idIncidencia, cte.cNombre as tipoIncidencia, inc.idItem, inc.cDescripcion, inc.cVehiculo, inc.cPersona, inc.dFecha, inc.tHora, inc.iStatus, sr.cNombre AS statusTxt, inc.iDuracion, i.cNombre AS nombreItem, CONCAT(u.cNombre,' ',u.cApellido) AS nombreLevanto, CONCAT(u2.cNombre,' ',u2.cApellido) AS nombreModifica
				FROM incidencias inc
				LEFT JOIN cTipoIncidencias cte ON inc.iTipoIncidencia = cte.idTipoIncidencia
				LEFT JOIN items i ON inc.idItem = i.idItem
				LEFT JOIN statusIncidencias sr ON inc.iStatus = sr.idstatusIncidencia
				LEFT JOIN usuarios u ON inc.idUsuarioLevanto = u.idUsuario
				LEFT JOIN usuarios u2 ON inc.idUsuarioModifica = u2.idUsuario";
		if( $order != "" && $way != "" )
			$sql.=" ORDER BY $order $way";
		else
			$sql.=" ORDER BY idIncidencia DESC";
		
		$sql.="  limit $inicio,$elementos";
		
		$result = $this->queryTotal($sql);
		
		return array("total"=>$this->getTotal(),'items' => $result, 'elementos'=>$elementos);
	}
	
	/**
	 * Regresa la info de un incidencia en especifica
	 * PARAMS int $idIncidencia El id de incidencia de la cual se solicita la informacion
	 * RETURN array Con la informacion del incidencia correspondiente
	 */
	function getInfo($idIncidencia){
		$sql = "SELECT 
					inc.idIncidencia, inc.iTipoIncidencia, cti.cNombre AS tipoIncidencia, inc.idItem, 
					i.cNombre AS itemNombre, i.cDescripcion AS itemDescripcion, i.cUbicacion AS itemUbicacion
					, CONCAT(i.fLatitud,',',i.fLongitud) AS itemCoordenadas, i.cIP as  itemIp ,inc.cUbicacion
					, inc.cDescripcion, inc.cVehiculo, inc.cPersona, inc.dFecha, inc.tHora, inc.iStatus,
					inc.lecionados , inc.decesos , inc.afectados
				FROM incidencias inc
				LEFT JOIN cTipoIncidencias cti ON inc.iTipoIncidencia = cti.idTipoIncidencia
				LEFT JOIN items i ON inc.idItem = i.idItem
				WHERE inc.idIncidencia = $idIncidencia";
		$return = $this->query($sql);
		
		/*Obtengo las Actividades*/
		$sql = "SELECT 
					ar.idUsuario, CONCAT(u.cNombre,' ',u.cApellido) AS usuarioNombre, ar.cActividad, ar.dFecha, ar.tHora
				FROM actividadesIncidencias ar
				LEFT JOIN usuarios u ON ar.idUsuario = u.idUsuario
				WHERE idIncidencia = $idIncidencia";
		
		$return[0]['actividades'] = $this->query($sql);
		return $return[0];
	}
}