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

    // Variables globales.
    CONST PLUGIN_NAME = 'uplannerconnect';
    CONST PLUGIN_NAME_LOCAL = 'local_uplannerconnect';

    // Nombre de las tablas.
    CONST TABLE_COURSE_GRADE = 'mdl_uplanner_grades';

    // Rutas de los eventos.
    CONST ROUTE_HANDLER_EVENT_1 = '/local/uplannerconnect/classes/infrastructure/event/handle_event_course_notes.php';


    // Configuración de los eventos. 
    CONST EVENTS_OBSERVERS = [
        [
            'eventname' => '\core\event\user_graded',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        // [
        //     'eventname' => '\core\event\grade_deleted',
        //     'includefile' => self::ROUTE_HANDLER_EVENT_1,
        // ],
        [
            'eventname' => '\core\event\grade_item_created',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        // [
        //     'eventname' => '\core\event\grade_item_deleted',
        //     'includefile' => self::ROUTE_HANDLER_EVENT_1,
        // ]
    ];

    // Conultas a la base de datos.
    CONST QUERY_INSERT_COURSE_GRADES = "INSERT INTO %s (json, response, success , request_type) VALUES ('%s', '%s', '%s' , '%s')";
    CONST QUERY_UPDATE_COURSE_GRADES = "UPDATE %s SET json = '%s', response = '%s', success = '%s' WHERE id = %s";
    CONST QUERY_SELECT_COURSE_GRADES = "SELECT * FROM %s WHERE success = %s LIMIT %s OFFSET %s";


    // Estruturas de datos uPlanner.
    CONST UPLANNER_GRADES = [
        ['name' => 'sectionId', 'type' => 'numeric'],
        ['name' => 'studentCode', 'type' => 'numeric'],
        ['name' => 'finalGrade', 'type' => 'numeric'],
        ['name' => 'finalGradeMessage', 'type' => 'string'],
        ['name' => 'finalGradeMessage', 'type' => 'string'],
        ['name' => 'finalGradePercentage', 'type' => 'numeric'],
        ['name' => 'evaluationGroupCode', 'type' => 'numeric'],
        ['name' => 'grades', 'type' => 'numeric'],
        ['name' => 'evaluationId', 'type' => 'numeric'],
        ['name' => 'value', 'type' => 'numeric'],
        ['name' => 'evaluationName', 'type' => 'string'],
        ['name' => 'date', 'type' => 'string'],
        ['name' => 'isApproved', 'type' => 'bool'],
        ['name' => 'average', 'type' => 'numeric'],
        ['name' => 'lastModifiedDate', 'type' => 'string'],
        ['name' => 'action', 'type' => 'string'],
    ];

    // Estruturas de datos uPlanner.
    CONST UPLANNER_EVALUATION_ESTRUTURE = [
        ['name' => 'sectionId', 'type' => 'numeric'],
        ['name' => 'evaluationGroupCode', 'type' => 'string'],
        ['name' => 'evaluationGroupName', 'type' => 'string'],
        ['name' => 'evaluationId', 'type' => 'numeric'],
        ['name' => 'evaluationName', 'type' => 'string'],
        ['name' => 'weight', 'type' => 'numeric'],
        ['name' => 'action', 'type' => 'string'],
    ];

    // Estructuras de datos para la creacion.
    // De un Evento.
    CONST CREATE_EVENT_DATA = [
        ['name' => 'dataEvent' , 'type' => 'object'],
        ['name' => 'typeEvent' , 'type' => 'string'],
        ['name' => 'dispatch' , 'type' => 'string'],
        ['name' => 'EnumEtities' , 'type' => 'string'],
    ];

}
