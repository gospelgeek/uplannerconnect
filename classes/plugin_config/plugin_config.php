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
    const PLUGIN_NAME = 'uplannerconnect';
    const PLUGIN_NAME_LOCAL = 'local_uplannerconnect';

    // Nombre de las tablas.
    const TABLE_COURSE_GRADE = 'uplanner_grades';
    const TABLE_COURSE_EVALUATION = 'uplanner_evaluation';
    const TABLE_COURSE_MOODLE = 'mdl_course';    
    const TABLE_COURSE = 'course';
    const TABLE_USER_MOODLE = 'user';

    // Conultas a la base de datos.
    const QUERY_INSERT_COURSE_GRADES = "INSERT INTO %s (json, response, success , request_type) VALUES ('%s', '%s', '%s' , '%s')";
    const QUERY_UPDATE_COURSE_GRADES = "UPDATE %s SET json = '%s', response = '%s', success = '%s' WHERE id = '%s'";
    const QUERY_SELECT_COURSE_GRADES = "SELECT * FROM %s WHERE success = '%s' LIMIT '%s' OFFSET '%s'";
    const QUERY_SHORNAME_COURSE_BY_ID = "SELECT shortname FROM %s WHERE id = '%s'";
    const QUERY_NAME_CATEGORY_GRADE = "SELECT t2.fullname FROM %s as t1 INNER JOIN %s as t2 ON t1.id = %s AND t2.id = t1.categoryid";
    const AGGREGATION_CATEGORY_FATHER = "SELECT aggregation FROM mdl_grade_categories WHERE courseid = '%s' and depth = 1";
    const MAX_ITEM_COURSE = "SELECT DISTINCT COUNT(t2.id) as count FROM mdl_grade_items as t2 WHERE t2.courseid='%s' AND t2.itemtype NOT IN ('course', 'category') AND t2.hidden = 0";
    const SUM_TOTAL_GRADE = "SELECT SUM(t1.finalgrade) AS total FROM mdl_grade_grades AS t1 INNER JOIN mdl_grade_items AS t2 ON t1.itemid = t2.id WHERE t2.courseid = '%s' AND t2.itemtype NOT IN ('course', 'category') AND t2.hidden = 0 AND t1.userid = '%s' AND t1.finalgrade IS NOT NULL";
    const MAX_STUDENT_GRADE = "SELECT MAX(t1.finalgrade) AS nota_maxima FROM mdl_grade_grades AS t1 INNER JOIN mdl_grade_items AS t2 ON t1.itemid = t2.id WHERE t2.courseid = '%s' AND t2.itemtype NOT IN ('course', 'category') AND t2.hidden = 0 AND t1.userid = '%s' AND t1.finalgrade IS NOT NULL";
    const LAST_GRADE_UPLANNER = "SELECT aggregation FROM {uplanner_grades} WHERE courseid = :courseid ORDER BY id DESC LIMIT 1";
}