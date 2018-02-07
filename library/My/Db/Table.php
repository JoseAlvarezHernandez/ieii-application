<?php
/**
 * Archivo de definición de clase
 * 
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.my.db
 */

/**
 * Clase principal para manejo de models
 * 
 * @author Jose de Jesus Alvarez Hernandez
 * @package library.my.db
 */
class My_Db_Table extends Zend_Db_Table_Abstract
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $_name = null;

    /**
     * Total de registros devueltos
     * 
     * @var integer
     */
    private $_total   = 0;

    /**
     * Total de registros devueltos en el fetchRows
     *
     * @var unknown_type
     */
    private $_totalRows;
    
    
    /**
     * Datos de identidad de usuario en sesión
     * 
     * @var array
     */
    private $_identidad;
    
    /**
     * Inserts a new row
     * 
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert(array $data)
    {
        $id        = parent::insert($data);
        $idUsuario = $this->_getIdentidad();
        
        // Registrar evento 1:Alta
        if ($this->_name != 'movimientos') {
            $data['table'] = $this->_name;
            $this->registraEvento(1,  Zend_Json::encode($data), $idUsuario[0]['idUsuario']);
        }

        return $id;
    }

    
    /**
     * Inserts a new row as an insert method. 
     * If an insert constraint is hint, does not launch error
     * 
     * @param $data
     * @return mixed
     */
    public function ignore(array $data)
    {
        
        // Prpearando consulta
        $sql       = 'INSERT IGNORE INTO ' . $this->_name;
        $columns   = array();
        $values    = array();
        $idUsuario = $this->_getIdentidad();

        foreach($data as $column=>$value) {
            $columns[] = $column;
            $values[]  = $value; 
        }
        
        $sql .= ' ('. implode(', ', $columns) .')';
        $sql .= ' VALUES(' . implode(', ', $values) . ')';
        

        // Ejecutar sentencia
        $db   = self::getDefaultAdapter();
        $stmt = $db->query($sql);
        $stmt->execute();
        $id   = $db->lastInsertId($this->_name, $this->_primary);
        
        // Registrar evento 1:Alta
        if ($this->_name != 'movimientos') {
            $data['table'] = $this->_name . '(ign)';
            $this->registraEvento(1, Zend_Json::encode($data), $idUsuario[0]['idUsuario']);
        }
        
        return $id;
    }
    
    /**
     * Updates existing rows.
     *
     * @param  array        $data  Column-value pairs.
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @return int          The number of rows updated.
     */
    public function update(array $data, $where)
    {
        
        $updated   = parent::update($data, $where);
        $idUsuario = $this->_getIdentidad($updated);

        // Registrar evento 2:Actualización
        if ($this->_name != 'movimientos') {
            $data['table'] = $this->_name;
            $data['where'] = $where;
            $this->registraEvento(2, Zend_Json::encode($data), $idUsuario[0]['idUsuario']);
        }
        return $updated;
    }

    /**
     * Deletes existing rows.
     *
     * @param  array|string $where SQL WHERE clause(s).
     * @return int          The number of rows deleted.
     */
    public function delete($where)
    {
        $deleted   = parent::delete($where);
        $idUsuario = $this->_getIdentidad();

        // Registrar evento 3:Baja
        if ($this->_name != 'movimientos') {
            $data = array('table' => $this->_name,
                          'where' => $where);
            $this->registraEvento(3, Zend_Json::encode($data), $idUsuario[0]['idUsuario']);
        }
        return $deleted;
    }

    /**
     * Guarda registros de movimiento
     * 
     * @param integer $type Tipo de movimiento
     * @param string $detalle Detalles del movimiento realizado
     * @param mixed $idUsuario Identificador de usuario que realiza movimiento
     * @return void
     */
    public function registraEvento($type, $detalle, $idUsuario = null)
    {
        $data        = array('idUsuario'       => $idUsuario,
                             'idCatMovimiento' => $type,
                             'cDetalle'        => $detalle,
                             'dtFecha'         => new Zend_Db_Expr('NOW()'),
                             );
        $movimientos = new My_Model_Usuarios_Movimientos();
        $movimientos->insert($data);
    }

    /**
     * Asigna valor a variable total 
     * 
     * @param integer $total
     * @return void
     */
    protected function _setTotal($total = 0)
    {
        $this->_total = $total;
    }

    /**
     * Obtiene el total de registros generados por una consulta
     * 
     * @return integer
     */
    public function getTotal()
    {
        return $this->_total;
    }
    
    
    /**
     * Obtiene el total de registros y obtiene el TOTAL de elementos en la tabla!
     *
     * @param mixed $where
     * @param mixed $order
     * @param mixed $page Pagina 
     * @param mixed $size Tamaño de pagina
     * @return mixed Arreglo de elementos
     */
    public function fetchRows($where = null, $order = null, $page = null,$size = null){
        
        if($where instanceof Zend_Db_Select ){
            $select = $where;
        }else if (!($where instanceof Zend_Db_Table_Select)) {
             $select = $this->select();

            if ($where !== null) {
                $this->_where($select, $where);
            }

            if ($order !== null) {
                $this->_order($select, $order);
            }

            if ($page !== null || $size !== null) {
                $select->limitPage($page, $size);/*Page size*/                    
            }

        } else {
            $select = $where;
        }
        ;
        $select  = str_replace("SELECT ","SELECT SQL_CALC_FOUND_ROWS ",$select->__toString());
         $result = $this->getAdapter()->query($select)->fetchAll();        
          
        $total = $this->getAdapter()->query("SELECT FOUND_ROWS();")->fetchColumn();
        
        $this->_setTotalRows($total);
        
        return $result;    
    }
    
    /**
     * Estabblece el total de elementos en la consulta
     * @param int $total Total de elementos
     * @return void
     */
    protected function _setTotalRows($total)
    {
        $this->_totalRows = $total;
    }
    
    /**
     * Obtiene el total de elementos de la consulta antes realizada
     *
     * @return int
     */
    public function getTotalRows()
    {
        return $this->_totalRows;
    }
    
    /**
     * Obtiene identidad de usuario activo en sesión
     *
     * @return void
     */
     private function _getIdentidad() 
     {
         if ($this->_identidad == null) {
             $nsIdentidad      = new Zend_Session_Namespace('identidad');
             $this->_identidad = $nsIdentidad->identidad;
         }
         
         return $this->_identidad;
     }
/**
     * 
     * Todos los querys tiene que pasar por aqui
     * 
     * @param string $sql
     * @return array|stdClass
     */
    public function query($sql)
    {
        /**Datos resultados de Query **/
    	$validador = new My_Validador_IO();
        $db           = $this->getAdapter();
        $stmt         = $db->query($validador->wysiwyg($sql))->fetchAll();
        return $stmt;
    }

    /**
     * 
     * Funcion para ejecutar replace into 
     * 
     * @param string $sql
     * 
     */
    public function replaceInto($sql){
        $db   = $this->getAdapter();
        $stmt = $db->query($sql);
        return $stmt;
    }
    
    /**
     * Carga la query en caso de que exista en caché, de lo contrario accede a bd
     * y carga el resultado de la query en caché
     *
     * @param string $sql
     * @return array|stdClass
     */
    public function queryTotal($sql){
    		
    	$db      = $this->getAdapter();
    	$select  = str_replace("SELECT ","SELECT SQL_CALC_FOUND_ROWS ",$sql);
    	$stmt    = $db->query($select)->fetchAll();
    	$total   = $db->query("SELECT FOUND_ROWS();")->fetchColumn();
    	$this->_setTotal($total);
    	if(!is_array($stmt)) $stmt = array(0=>(array)$stmt);
    	return $stmt;
    }
}