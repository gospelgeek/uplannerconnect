<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description variables globales para el plugin
*/
class plugin_config {

    const PLUGIN_NAME = 'uplannerconnect';
    const PLUGIN_NAME_LOCAL = 'local_uplannerconnect';
    const TABLE_COURSE_GRADE = 'mdl_uplanner_notes';
    const ROUTE_HANDLER_EVENT_1 = '/local/uplannerconnect/event/handle_event_course_notes.php';


    // Configuración de los eventos
    CONST EVENTS_OBSERVERS = [
        [
            'eventname' => '\core\event\grade_item_updated',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\user_graded',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_deleted',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_item_created',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_item_deleted',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_letter_created',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_letter_deleted',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_letter_updated',
            'includefile' =>  self::ROUTE_HANDLER_EVENT_1,
        ],
    ];


    
   
}
