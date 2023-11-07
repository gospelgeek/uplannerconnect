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

    // Conultas a la base de datos.
    CONST QUERY_INSERT_COURSE_GRADES = "INSERT INTO %s (json, response, success , request_type) VALUES ('%s', '%s', '%s' , '%s')";
    CONST QUERY_UPDATE_COURSE_GRADES = "UPDATE %s SET json = '%s', response = '%s', success = '%s' WHERE id = '%s'";
    CONST QUERY_SELECT_COURSE_GRADES = "SELECT * FROM %s WHERE success = '%s' LIMIT '%s' OFFSET '%s'";
    CONST QUERY_SHORNAME_COURSE_BY_ID = "SELECT shortname FROM %s WHERE id = '%s'";
    CONST QUERY_COUNT_LOGS = "SELECT count(id) FROM %s";
    const QUERY_INSERT_LOGS = "INSERT INTO %s (date, num_grades, num_materials, num_anouncements, num_evaluation) VALUES ('%s', '%s', '%s' , '%s' , '%s')";
    const QUERY_NAME_CATEGORY_GRADE = "SELECT t2.fullname FROM %s as t1 INNER JOIN %s as t2 ON t1.id = %s AND t2.id = t1.categoryid";

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
        ['name' => 'date', 'type' => 'string'],
    ];

    CONST UPLANNER_MATERIALS_ESTRUTURE = [
        ['name' => 'id', 'type' => 'string'],
        ['name' => 'name', 'type' => 'string'],
        ['name' => 'type', 'type' => 'string'],
        ['name' => 'url', 'type' => 'string'],
        ['name' => 'parentId', 'type' => 'string'],
        ['name' => 'blackboardSectionId', 'type' => 'string'],
        ['name' => 'size', 'type' => 'numeric'],
        ['name' => 'lastUpdatedTime', 'type' => 'string'],
        ['name' => 'action', 'type' => 'string'],
    ];

    CONST UPLANNER_ANNOUNCEMENTS_ESTRUTURE = [
        ['name' => 'blackboardSectionId', 'type' => 'string'],
        ['name' => 'createdDate', 'type' => 'string'],
        ['name' => 'type', 'type' => 'string'],
        ['name' => 'createdTime', 'type' => 'string'],
        ['name' => 'title', 'type' => 'string'],
        ['name' => 'content', 'type' => 'string'],
        ['name' => 'id', 'type' => 'string'],
        ['name' => 'usernameCreator', 'type' => 'string'],
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