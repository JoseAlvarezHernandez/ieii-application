<?php
/**
 * Archivo de definición de modelo
 * 
 * @author [EPG] 2014-06-20
 * @package library.model.eventos
 */

/**
 * Clase modelo para manipulación de información de tabla roles
 *
 * @author [EPG]
 * @package library.model.usuarios
 */
class My_Model_Eventos extends My_Db_Table
{
	  
	protected $_name    = 'eventos';
   	protected $_primary = 'idEvento';

   	/**
   	 * Regresa una lista de eventos segun el paginado para el tipo especificado
   	 * PARAMS
   	 *		int $type		El tipo de elemento del que se requieren los elementos
   	 * 		int $page		El numero de pagina a partir de la cual se requierern los eventos
   	 * RETURN
   	 * 		array		Lista de eventos que cumplen las condiciones de busqueda
   	 */
	public function getEventos( $typeItem ,$typeEvent, $page ,$desde){
		$items = 15;
		$limit = $items * $page;
		$sql = "SELECT 
					e.idEvento, e.iTipoEvento, e.idItem, e.iTipoItem, e.cDescripcion, e.dFechahora, it.fLatitud, it.fLongitud, e.idUsuario,it.cNombre as posteSOS
				FROM eventos e
				LEFT JOIN items it ON e.idItem = it.idItem
				WHERE e.iTipoItem = $typeItem and e.idEvento > $desde and estado = 0";
		if( $typeEvent != 0 ){
			$sql .= " AND e.iTipoEvento= $typeEvent ";
		}
					
		$sql .= " ORDER BY e.dFechahora DESC
				LIMIT $limit,$items";	
		$data['sql'] = null;
		$data['elementos'] = $items;
		$data['items'] =  $this->queryTotal($sql);
		if(count($data['items']) == 0){
			$sql = "SELECT e.idEvento, e.iTipoEvento, e.idItem, e.iTipoItem, e.cDescripcion, e.dFechahora, it.fLatitud, it.fLongitud, e.idUsuario,it.cNombre as posteSOS,e.iStatus FROM eventos e LEFT JOIN items it ON e.idItem = it.idItem WHERE e.iTipoItem = 1 and e.iTipoEvento=1  and estado = 0 ORDER BY e.dFechahora DESC";
			$data['items'] =  $this->queryTotal($sql);
		}
		$data['total'] = $this->getTotal();
		
		return $data;
	}
	/**
	 * Regresa una lista de eventos mas recientes desde la fecha especificada de un tipoEvento y tipoItem establecidos
	 * PARAMS
	 *		int $typeItem		El tipo de elemento del que se requieren los elementos
	 **		int $typeEvent		El tipo de evento del que se requieren los elementos
	 * 		int $last			La fecha y hora a partir de la cual se buscan los eventos
	 * RETURN
	 * 		array		Lista de eventos que cumplen las condiciones de busqueda
	 */
	public function getUltimosEventos( $typeItem , $last ){
		$sql = "SELECT
		e.idEvento, e.iTipoEvento, e.idItem, e.iTipoItem, e.cDescripcion, e.dFechahora, it.fLatitud, it.fLongitud, e.idUsuario
		FROM eventos e
		LEFT JOIN items it ON e.idItem = it.idItem
		WHERE e.iTipoItem = $typeItem AND e.dFechaHora > '$last' AND iStatus = 1 ORDER BY e.dFechahora ASC";
		
		return $this->query($sql);
	}
	/**
	 * Regresa una lista de llamadas que no han sido contestadas o estan en curso con el operador actual
	 * PARAMS
	 *		int $idUsuario		El id del usuario del cual se buscaran las llamada en curso
	 * RETURN
	 * 		array		Lista de eventos que cumplen las condiciones de busqueda
	 */
	public function getLlamadasNoContestadas($idUsuario){
		$sql = "SELECT
						e.idEvento, e.iTipoEvento, e.idItem, e.iTipoItem, e.cDescripcion, e.dFechahora, it.fLatitud, it.fLongitud, e.idUsuario
					FROM eventos e
					LEFT JOIN items it ON e.idItem = it.idItem
					WHERE e.iTipoEvento = 4 and e.idUsuario = $idUsuario and e.iStatus = 1
				UNION
					SELECT
						e.idEvento, e.iTipoEvento, e.idItem, e.iTipoItem, e.cDescripcion, e.dFechahora, it.fLatitud, it.fLongitud, e.idUsuario
					FROM eventos e
					LEFT JOIN items it ON e.idItem = it.idItem
					WHERE e.iTipoEvento = 4 and e.idUsuario is NULL and e.iStatus = 1";
		
		return $this->query($sql);
	}
	/**
	 * Regresa una lista de llamadas que se finalizarian al registrar la incidencia
	 * PARAMS
	 *		int $idUsuario		El id del usuario del cual se buscaran las llamadas
	 * RETURN
	 * 		array		Lista de eventos que cumplen las condiciones de busqueda
	 * 	[EPG] 2014-08-18
	 */
	public function getLlamadasPFinalizar($idItem){
		$sql = "SELECT
		e.idEvento, e.iTipoEvento, e.idItem, e.iTipoItem, e.cDescripcion, e.dFechahora, it.fLatitud, it.fLongitud, e.idUsuario
		FROM eventos e
		LEFT JOIN items it ON e.idItem = it.idItem
		WHERE e.iTipoEvento = 4 AND e.iStatus = 1 AND e.idItem = ".$idItem;
	
		return $this->query($sql);
	}
	/**
	 * Regresa el calculo de la duracion de una llamada desde que se presiono el boton hasta q se levanto el reporte
	 * PARAMS
	 *		int $idEvento	El id del evento del cual se realizara el calculo
	 * RETURN
	 * 		int 			La duracion en minutos del evento
	 * 	[EPG] 2014-08-18
	 */
	public function getDuracion($idEvento){
		$sql = "SELECT CEIL((dFinalizada - dFechaHora) / 60)
				AS duracion from eventos WHERE idEvento = $idEvento";
		
		$result = $this->query($sql);
		
		return $result[0]['duracion'];
	}
	
	
}
