<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\plugin_config;

/**
 * Variables globales para el plugin
*/
class plugin_config
{
    // Variables globales.
    CONST PLUGIN_NAME = 'uplannerconnect';
    CONST PLUGIN_NAME_LOCAL = 'local_uplannerconnect';

    // Nombre de las tablas.
    CONST TABLE_COURSE_GRADE = 'uplanner_grades';
    CONST TABLE_COURSE_EVALUATION = 'uplanner_evaluation';
    CONST TABLE_COURSE_MOODLE = 'mdl_course';    
    CONST TABLE_COURSE = 'course';
    CONST TABLE_USER_MOODLE = 'user';
    const TABLE_LOG = "mdl_uplanner_log";

    // Rutas de los eventos.
    CONST ROUTE_HANDLER_EVENT_1 = 'local_uplannerconnect\infrastructure\event\handle_event_course_notes';

    // Configuración de los eventos. 
    CONST EVENTS_OBSERVERS = [
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
        // [
        //     'eventname' => '\mod_folder\event\course_module_viewed',
        //     'includefile' => self::ROUTE_HANDLER_EVENT_1,
        // ]
    ];

    // Conultas a la base de datos.
    CONST QUERY_INSERT_COURSE_GRADES = "INSERT INTO %s (json, response, success , request_type) VALUES ('%s', '%s', '%s' , '%s')";
    CONST QUERY_UPDATE_COURSE_GRADES = "UPDATE %s SET json = '%s', response = '%s', success = '%s' WHERE id = '%s'";
    CONST QUERY_SELECT_COURSE_GRADES = "SELECT * FROM %s WHERE success = '%s' LIMIT '%s' OFFSET '%s'";
    CONST QUERY_SHORNAME_COURSE_BY_ID = "SELECT shortname FROM %s WHERE id = '%s'";
    CONST QUERY_COUNT_LOGS = "SELECT count(id) FROM %s";
    const QUERY_INSERT_LOGS = "INSERT INTO %s (date, num_grades, num_materials, num_anouncements) VALUES ('%s', '%s', '%s' , '%s')";

    // Estruturas de datos uPlanner.
    CONST UPLANNER_GRADES = [
        ['name' => 'sectionId', 'type' => 'string'],
        ['name' => 'studentCode', 'type' => 'string'],
        ['name' => 'finalGrade', 'type' => 'numeric'],
        ['name' => 'finalGradeMessage', 'type' => 'string'],
        ['name' => 'finalGradeMessage', 'type' => 'string'],
        ['name' => 'finalGradePercentage', 'type' => 'numeric'],
        ['name' => 'evaluationGroupCode', 'type' => 'string'],
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
        ['name' => 'sectionId', 'type' => 'string'],
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
        ['name' => 'enum_etities' , 'type' => 'string'],
    ];

}