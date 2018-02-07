<?php
/**
 * Validador/ForoComentarios
 */

define("TXT_CONTENIDO", " ,.,-,[,],!,¡,_,?,¿,:,/,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,@,(,),<,>,&,:,;,=,\",comma,;,%,#,$,|,+,{,},*");
define("TXT_WYSIWYG", " ,.,-,[,],!,¡,_,?,¿,',:,/,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,@,(,),<,>,&,:,;,=,\",comma,;,…,’,‘,%,#,“,”,–,$,|,—,+,{,},*,	,°,´,\\");

class My_Validador_IO extends Zend_Validate{
	
	/**
     * Funcion que valida los enteros
     * @param int $value
     * @return int (validado, filtrado y escapdo) 
     */
    public function intValido($value){
    	// valida entero
    	if(Zend_Validate::is($value, 'Int')){
    		// filtrarlo
    		$Zfilter = new Zend_Filter_Int();
    		$value = $Zfilter->filter($value, 'Int');
    		return $value;
    	}
    	return false;
    }
    
	/**
     * Funcion que valida las cadenas de solo caracteres
     * @param int $value
     * @return int (validado, filtrado y escapdo) 
     */
    public function alphaValido($value, $excepciones = null){
        $val = $value;
        if (is_array($excepciones)) {
            foreach ($excepciones as $excepcion => $exc)
                $val = str_replace($exc,"",$val);
        }
    	// valida cadena
    	if(Zend_Validate::is($val, 'Alpha')){
    		// filtrarlo
    		$filtroST = new Zend_Filter_StripTags();
			$filtroTrim = new Zend_Filter_StringTrim();
			
			$value =  $filtroST -> filter($value);
			$value =  $filtroTrim -> filter($value);
    		
    		return $value;
    	}
    	return false;
    }
    

	/**
     * Funcion que valida las cadenas de caracteres y numeros
     * @param int $value
     * @return int (validado, filtrado y escapdo) 
     */
    public function alphanumValido($value, $excepciones = null){
    	$val = $value;
    	if(!is_array($excepciones) && $excepciones!=null){
    		$excepciones = explode(",", $excepciones);
    	}
    	
        if (is_array($excepciones)) {
            foreach ($excepciones as $excepcion => $exc){
            	if(strcmp("comma",$exc)==0)
            		$val = str_replace(",","",$val);
            	else
                	$val = str_replace($exc,"",$val);
            }
            
        }
        
    	// valida cadena
    	if(Zend_Validate::is($val, 'Alnum')){
    		// filtrarlo
    		$filtroST = new Zend_Filter_StripTags();
			$filtroTrim = new Zend_Filter_StringTrim();
			
			$value =  $filtroST -> filter($value);
			$value =  $filtroTrim -> filter($value);
    		
    		return $value;
    	}
    	return false;
    }
    

	/**
     * Funcion que valida los tipo float
     * @param int $value
     * @return int (validado, filtrado y escapdo) 
     */
    public function floatValido($value){
    	// valida cadena
    	if(Zend_Validate::is($value, 'Float')){
    		// filtrarlo
    		$filtroST = new Zend_Filter_StripTags();
			$filtroTrim = new Zend_Filter_StringTrim();
			
			$value =  $filtroST -> filter($value);
			$value =  $filtroTrim -> filter($value);
    		
    		return $value;
    	}
    	return false;
    }
    
    

	/**
     * Funcion que valida los mails
     * @param int $value
     * @return int (validado, filtrado y escapdo) 
     */
    public function mailValido($value){
    	// valida cadena
    	if(Zend_Validate::is($value, 'EmailAddress')){
    		// filtrarlo
    		$filtroST = new Zend_Filter_StripTags();
			$filtroTrim = new Zend_Filter_StringTrim();
			
			$value =  $filtroST -> filter($value);
			$value =  $filtroTrim -> filter($value);
    		
    		return $value;
    	}
    	return false;
    }
    
    
 	/**
     * Metodo que aplica el filtro StripTags
     * @param string $string
     */
     
    private function stringTags($string) {
    	$filter  = new Zend_Filter_StripTags();
    	return $filter->filter($string);
    }
    
    
    /**
     * Metodo que recibe un elemento, valida si es un objeto, arreglo o variable y aplica validaciones 
     * @param $object
     */
     
    
	public function dataFront($object) {
        if (is_object ( $object ))
		    $object = get_object_vars ($object);
		    
		    
		if (is_array ( $object )){
		    foreach ( $object as $key => $value ) {
		    	//Excepciones aqui
		        	$object [$key] =  $this->dataFront ($object [$key]);
		    }
		} else {
			$object = addslashes($this->stringTags($object));
		}
		
		return $object;
    }
    
    
	/**
     * Metodo que recibe una cadena y valida si es un directorio validado, si no lo esta regresa falso 
     * @param $object
     */
    
	public function validPath($path) {
		if (file_exists($path)){
        	return $path;
        }
        else{
        	return false;
        }
    }

  	/**
     * Metodo que recibe un elemento, valida si es un objeto, arreglo o variable y valida que solo sean enteros cada uno de esos elementos 
     * @param $object
     * [EPG] 2012-04-11
     */
     
    
	public function arrayInts($object) {
        if (is_object ( $object )){
		    $object = get_object_vars ($object);
		   
        }
		    
		    
		if (is_array ( $object )){
		    foreach ( $object as $key => $value ) {
		        	$object [$key] =  $this->arrayInts ($object [$key]);
		    }
		} else {
			$object = $this->intValido($object);
		}
		
		return $object;
    }
    
	/**
     * Metodo que recibe un elemento, valida si es un objeto, arreglo o variable y valida que solo sean enteros cada uno de esos elementos 
     * @param $object
     * [EPG] 2012-05-14
     */
     
    
	public function arrayAlphanum($object,$excepciones) {
        if (is_object ( $object )){
		    $object = get_object_vars ($object);
		   
        }
		    
		    
		if (is_array ( $object )){
		    foreach ( $object as $key => $value ) {
		        	$object [$key] =  $this->arrayAlphanum($object [$key],$excepciones);
		    }
		} else {
			$object = $this->alphanumValido($object,$excepciones);
		}
		
		return $object;
    }
    
    
	/**
	 * 
	 * Funcion que valida si una url es válida, devuelve falso si no, si es válida entonces regresa la url
	 * @param string $url
	 * @return bool | string
	 */
	public function validUrl($url) { return (filter_var($url,FILTER_VALIDATE_URL,FILTER_FLAG_QUERY_REQUIRED) === false) ? false : $url; }

    
	/**
     * Funcion que valida la cadena de caracteres generada por wysiwyg
     */
    public function wysiwyg($value){
    	str_replace(" ", " ", $value);
    	
        $val = $value;
        
        $datos = bin2hex($val);
        for($i = 0 ; $i < strlen($datos) ; $i+=2 )
        	$chars[] = substr($datos,$i,2);
        
        $val = "";
        
        foreach($chars as $c)
        	if($c!="0a")
        		$val = $val.chr(hexdec($c));
        
		$excepciones = explode(",", TXT_WYSIWYG);
    		
        if (is_array($excepciones)) {
            foreach ($excepciones as $excepcion => $exc){
            	if(strcmp("comma",$exc)==0)
            		$val = str_replace(",","",$val);
            	else
                	$val = str_replace($exc,"",$val);
            }
        }
        return $value;
    	// valida cadena
    	if(Zend_Validate::is($val, 'Alnum')){
    		// filtrarlo
			$filtroTrim = new Zend_Filter_StringTrim();
			
			$value =  $filtroTrim -> filter($value);
   
    		return $value;
    	}
    	else{
			return false;	
    	}
    	
    }
    
    /**
     * Metodo que recibe un elemento, valida si es un objeto, arreglo o variable y valida que solo sean enteros cada uno de esos elementos
     * @param $object
     * [EPG] 2012-05-14
     */
     
    public function arraywyswyg($object) {
    	if (is_object ( $object ))
    		$object = get_object_vars ($object);
    		    
    	if (is_array ( $object ))
    		foreach ( $object as $key => $value ) 
    			$object [$key] =  $this->arraywyswyg($object [$key]);
    	else 
    		$object = $this->wysiwyg($object);
    
    	return $object;
    }    
}