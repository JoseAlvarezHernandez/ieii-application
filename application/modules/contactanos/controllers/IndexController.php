<?php

/**
 * Definición de clase IndexController
 * Contactanos
 * @author [JJAH] 2016-04-01
 * @package application.modules.contactanos.controllers
 * @version 1.0.1
 */
class contactanos_IndexController extends My_Controller_Action {
    public function indexAction() {   
    	$textResult = '';
    	$classResult = 'error';
    	if( isset($_POST['name']) && $_POST['name'] != '' )
    		if( isset($_POST['email']) && $_POST['email'] != '')
    			if( filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) )
	    			if( isset( $_POST['phone'] ) && $_POST['phone']  != '' )
	    				if( isset($_POST['asunto']) && trim( $_POST['asunto'] ) != '' ){
	    					$textResult .= $this->sendEmail( $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['asunto'] );
	    					if($textResult == 'Se ha enviado el correo exitosamente')
	    						$classResult = 'success';
	    				}    					    					
	    				else
		    				$textResult .= 'Necesitas introducir un asunto';	
		    		else
		    			$textResult .= 'Necesitas introducir un telefono';
		    	else
		    		$textResult .= 'El correo electronico no es valido!';
    		else
    			$textResult .= 'Necesitas introducir un correo electronico';
    	else
    		$textResult .= 'Necesitas introducir un nombre.';
    	
    	$this->view->name = isset($_POST['name']) ? $_POST['name'] : '';  
    	$this->view->email = isset($_POST['email']) ? $_POST['email'] : '';
    	$this->view->phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    	$this->view->asunto =  isset($_POST['asunto'])? $_POST['asunto'] : '';
    	$this->view->textResult = $textResult;
    	$this->view->class = $classResult;
    }	
    public function sendEmail( $name, $email, $phone, $asunto ){
    	$para  .= 'alvarez_3993@hotmail.com' . ', ';
    	$para  .= 'contacto@inncol.com' . ', ';
    	$para  .= 'jbd@ieii.com.mx' . ', ';    	
		$para  .= 'contacto@ieii.com.mx';	
		$titulo = 'Mensaje desde la pagina IEII de '.$name;		
		$mensaje = '
		<html>
			<head>
			  <title>Mensaje de '.$name.'</title>
			</head>
			<body>		  
			  <table>
			    <tr>
			      <th>Nombre</th>
			      <th>Correo</th>
			      <th>Telefono</th>
			      <th>Asunto</th>
			    </tr>
			    <tr>
			      <td>'.$name.'</td>
			      <td>'.$email.'</td>
			      <td>'.$phone.'</td>
			      <td>'.$asunto.'</td>
			    </tr>		    
			  </table>
			</body>
		</html>
		';
		// Para enviar un correo HTML, debe establecerse la cabecera Content-type
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Cabeceras adicionales
		$cabeceras .= 'To: Jose Alvarez <alvarez_3993@hotmail.com>, Juan Betancourt <jbd@ieii.com.mx>' . "\r\n";
		$cabeceras .= 'From: Pagina web <'.$email.'>' . "\r\n";		
		try{
			mail($para, $titulo, $mensaje, $cabeceras);
			return 'Se ha enviado el correo exitosamente';
		}catch(Exception $error){
			return $error->getMessage();
		}
    }
}
