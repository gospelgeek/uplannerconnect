<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Variables globales para el plugin
*/
class plugin_config {

    //Variables globales
    CONST PLUGIN_NAME = 'uplannerconnect';
    CONST PLUGIN_NAME_LOCAL = 'local_uplannerconnect';

    //Nombre de las tablas
    CONST TABLE_COURSE_GRADE = 'mdl_uplanner_notes';

    //Rutas de los eventos
    CONST ROUTE_HANDLER_EVENT_1 = '/local/uplannerconnect/classes/infrastructure/event/handle_event_course_notes.php';


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
        [
            'eventname' => '\core\event\scale_created',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\scale_deleted',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\scale_updated',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ]
    ];

    //Conultas a la base de datos
    CONST QUERY_INSERT_COURSE_GRADES = "INSERT INTO %s (json, response, success) VALUES ('%s', '%s', '%s')";
    CONST QUERY_UPDATE_COURSE_GRADES = "UPDATE %s SET json = '%s', response = '%s', success = '%s' WHERE id = 1";
    CONST QUERY_SELECT_COURSE_GRADES = "SELECT * FROM %s WHERE success = %s LIMIT 100";

}
