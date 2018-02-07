<?php
class Descarga{
	public function test($file){
		return(is_file($file));	
	}
	public function descargar($file){
		$archivo  = $this->test($file);		
		// Quick check to verify that the file exists
		$url = 'http://sitioweb.com/linkalarchivo.csv';
		$source = file_get_contents($url);
		file_put_contents('/carpeta/para/el/archivo/nombredelarchivo.csv', $source);
		echo 'Se ha descargado el CSV';
	}
}
?>