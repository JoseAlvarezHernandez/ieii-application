<?php
/**
 * Model de movimientos
 * 
 * @author [EPG] 2014-03-20
 * @package library.my.model.usuarios
 */

/**
 * Clase modelo para manipulación de información de tabla movimientos
 *
 * @author [EPG] 2014-03-20
 * @package library.my.model.usuarios
 */
class My_Model_Usuarios_Movimientos extends My_Db_Table
{
	protected $_name    = 'movimientos';
   	protected $_primary = 'idMovimiento';
   	protected $_referenceMap = array('Usuarios' => 
	                                        array('columns'       => 'idUsuario',
	                                              'refTableClass' => 'My_Model_Usuarios',
	                                              'refColumns'    => 'idUsuario'),
	                                  );

    /**
     * Obtiene arreglo de movimientos efectuados en los repositorios
     * 
     * @param integer $pagina Página actual
     * @param integer $registros Número de registros por página
     * @return array
     */
    public function getMovimientos($pagina = 1, $registros = 20)
    {
        $db          = $this->getAdapter();
        $selMov      = $db->select()->from('movimientos as m', array('idMovimiento', 'dtFecha', 'cDetalle' ))
                                 ->join('catMovimientos as cm', 'cm.idCatMovimiento = m.idCatMovimiento', array('cDescripcion'))
                                 ->join('usuarios as us', 'us.idUsuario = m.idUsuario', array('cUsuario'))
				                 ->order('dtFecha DESC')
				                 ->limitPage($pagina, $registros);

        $results      = $this->fetchRows($selMov);
        $this->_setTotal($this->getTotalRows());

        return $results;
    }
    
    /**
     * Obtiene estadísticas de los movimientos realizados por los usuarios
     * 
     * @return array
     */
    public function getEstadisticasUsuarios()
    {
        $db      = $this->getAdapter();
        $selMov  = $db->select()
                      ->from('usuarios as u', array('usuario'))
                      ->join('movimientos as m', 'u.idUsuario = m.idUsuario', array('total' => 'count(*)'))
                      ->group(array('m.idUsuario'))
                      ->order('total DESC');
        $results = $db->fetchAll($selMov);
        
        return $results;
    }
}
