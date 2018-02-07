<?php
class My_Model_Grabaciones extends My_Db_Table
{
	  
	protected $_name    = 'cdr';
	protected $_primary = 'uniqueid';
	
	public function getGrabaciones($page,$order,$way){
		$elementos = 30;
		$inicio = $page * $elementos;
		
		$order = "ORDER BY $order $way";
		
		$sql = "SELECT uniqueid,calldate,i.cNombre as nomUs,dst as nombre,duration,recordingfile
			FROM cdr as c
            LEFT JOIN items as i ON c.src=i.iExtension
            LEFT JOIN items as e ON c.dst=e.iExtension
            WHERE recordingfile != '' AND lastapp = 'Queue'
			$order
			LIMIT $inicio, $elementos
		";
		
		$result = $this->queryTotal($sql);
		$total = $this->getTotal();

		return array("items"=>$result,"total"=>$total,"elementos"=>$elementos);
	}
	
	/**
	 * Metodo que obtiene la fecha y hora de la ultima grabacion en el servidor en caso de no existir ninguna regresa el 01-08-2015
	 * [EPG] 2015-09-09
	 */
	public function getLastDate(){
		$sql = "SELECT calldate FROM cdr ORDER BY uniqueid DESC LIMIT 1";
		$result = $this->query($sql);
		
		if(count($result)>0)
			$data = $result[0]['calldate'];
		else
			$data = "2016-02-10 00:00:00";
		
		return $data;
	}
	
	public function getDownload($id){
		$sql = "select recordingfile, bDownloaded from cdr where uniqueid = $id";
		
		return $this->query($sql);
	}
	public function getMatch($idPoste,$fecha){
		list($date,$hora) = explode(" ", $fecha);
		$var = str_replace('/', '-', $date);		
		$sql = "SELECT idIncidencia 
		FROM `incidencias` 
		WHERE idItem=$idPoste and dFecha = '".date('Y-m-d', strtotime($var))."' and tHora > '$hora' 		
		ORDER BY dFecha,tHora ASC
		LIMIT 1";
		$result = $this->query($sql);
		if(isset($result[0])){
			return $result[0]['idIncidencia'];
		}else{
			return null;
		}				
	}
}