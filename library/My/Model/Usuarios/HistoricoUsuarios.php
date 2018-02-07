<?php
/**
 * Archivo de definición de modelo
 * 
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.model.usuarios
 */

/**
 * Clase modelo para manipulación de información de tabla usuarios
 *
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.model.usuarios
 */
class My_Model_Usuarios_HistoricoUsuarios extends My_Db_Table
{
	  
	protected $_name    = 'historicoUsuarios';
   	protected $_primary = 'idHistoricoUsuario';
   	
   	protected $_dependentTables = array('rel_usuarios_roles',
   	                                    'rel_empresa_usuario',
   										'movimientos',
	                                    );
}
