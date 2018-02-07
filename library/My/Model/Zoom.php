<?php
/**
 * Archivo de definición de modelo
 * 
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.model.zoom
 */

/**
 * Clase modelo para manipulación de información de tabla zoom
 *
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.model.zoom
 */
class My_Model_Zoom extends My_Db_Table{
	protected $_name    = 'zoom';
	protected $_primary = 'idZoom';
	
	
	function getLast(){
		$sql = "SELECT idZoom, cLatitud, cLongitud, iZoom, cCapturo, dtFecha
				FROM zoom 
				ORDER BY idZoom DESC LIMIT 1";
		$result = $this->query($sql);
		return $result[0];
	}
}