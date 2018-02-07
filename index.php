<?php
header('Content-Type: text/html; charset=utf-8'); 
define('APPLICATION_ENVIRONMENT', 'testing');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application/'));
set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_PATH . '/../library' . PATH_SEPARATOR . APPLICATION_PATH . '/../library/Zend' . PATH_SEPARATOR . APPLICATION_PATH . '/modules');


if( file_exists("../configure") && APPLICATION_ENVIRONMENT  != 'production' ){
	$userdb = @$_POST['userdb'];
	$passdb = @$_POST['passdb'];
	$namedb = @$_POST['namedb'];
	
	
	if( $userdb == "" || $passdb == "" || $namedb == "" ){
		?>
			<form method = "POST" >
				User Db: <input type="text" name="userdb"><br>
				Pass Db: <input type="text" name="passdb"><br>
				Name Db: <input type="text" name="namedb"><br>
				<input type="submit" value="Guardar">
			</form>
		<?php 
	}else{
		$encripta = $namedb.'|'.$userdb.'|'.$passdb;
		$encrip = gzcompress($encripta,2);
		if(!$handle = fopen("repath.au", "w+"))
			die("No se puede crear el archivo de configuracion =O");
		fwrite($handle, $encrip);
		fclose($handle);
		
		shell_exec("rm ../configure");
		echo "CONFIGURADO TODO =)";
	}
	exit(1);	
}

try {
    require_once 'Bootstrap.php';
    Bootstrap::getInstance()->run();
} catch (Exception $exception) {
    echo '<html><body><center>'
       . 'Disculpe las molestias, por el momento el sitio no se encuentra disponible. Estamos trabajando para solucionar el inconveniente.<br />';

    // En caso de error mostrar traza de la ex del sitio en modo de desarrollo exclusivamente
    if (defined('APPLICATION_ENVIRONMENT') && APPLICATION_ENVIRONMENT  != 'production') {
    	echo 'Ha ocurrido una excepci&oacute;n durante el lanzamiento de la aplicaci&oacute;n (Bootstraping).';
        echo '<br /><br /><pre>' . $exception->getMessage() . '</pre><br />'
           . '<div align="left">Stack Trace:' 
           . '<pre>' . $exception->getTraceAsString() . '</pre></div>';
    }else{
    	echo "<!--";
    	echo 'Ha ocurrido una excepci&oacute;n durante el lanzamiento de la aplicaci&oacute;n (Bootstraping).';
    	echo '<br /><br /><pre>' . $exception->getMessage() . '</pre><br />'
    	. '<div align="left">Stack Trace:'
    	. '<pre>' . $exception->getTraceAsString() . '</pre></div>';
    	echo "-->";
    }
    echo '</center></body></html>';
    exit(1);
} 
?>