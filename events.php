<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
// global $DB;

// $pluginname_ = 'uplannerconnect';
// $controllerEvent = '/events/handler.php';

// /**
//  * @package uPlannerConnect
//  * @subpackage events
//  * @category local
//  * @description Lanza la configuración del evento
//  * @todo: Reunion tecnica con todo el equipo de desarollo para definir 
//  *        El uso o no de los eventos, yo me encargo dejar el caso base creado y 
//  *        documentado por si horientamos la solucion del problema atreves de los eventos. 
// */
// $events = array(
//     array(
//         'eventname' => 'grade_change_uplanner', //Deber ser unico
//         'sourcetable' => 'grade_grades', //Tabla a la que apunta
//         'sourcefield' => array('userid', 'itemid', 'finalgrade'), //Esto importante por que se activa si hay un cambio en estos atributos 
//         'handlerfile' => '/local/uplannerconnect/events/handler.php',
//         'handlerfunction' => 'event_handler_grade_changes_in_course',
//         'schedule' => 'instant',
//         'description' => 'Modification or Creation of a Grade in Course',
//     )
// );


// /**
//  * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
//  * @package uPlannerConnect
//  * @subpackage events
//  * @category local
//  * @description Registra los eventos en la base de datos de Moodle
//  */
// foreach ($events as $event) {
    
//     //variables
//     $eventname = $event['eventname'];

//     // Verificar si el evento ya existe en la base de datos
//     if (!$DB->record_exists('events_handlers', array('eventname' => $eventname))) {

//         $eventrecord = new stdClass();
//         $eventrecord->eventname = $event['eventname'];
//         $eventrecord->sourcetable = $event['sourcetable'];
//         $eventrecord->sourcefield = $event['sourcefield'] ?? null;
//         $eventrecord->description = $event['description'] ?? null;
//         $eventrecord->handlerfile = $event['handlerfile'];
//         $eventrecord->handlerfunction = $event['handlerfunction'];
//         $eventrecord->schedule = $event['schedule'];
//         $eventrecord->status = 0; // Activa el evento

//         // Insertar el evento en la base de datos de Moodle
//         $DB->insert_record('events_handlers', $eventrecord);

//     }
// }