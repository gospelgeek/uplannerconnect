<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Instancia un evento para el manejo de notas de un curso
*/
$observers = array(
    array(
        'eventname' => '\core\event\grade_item_updated',
        'includefile' => '/local/uplannerconnect/event/handle_event_course_notes.php',
        'callback' => 'grade_item_updated',
    ),
    array(
        'eventname' => '\core\event\user_graded',
        'includefile' => '/local/uplannerconnect/event/handle_event_course_notes.php',
        'callback' => 'user_graded',
    ),
    array(
        'eventname' => '\core\event\grade_deleted',
        'includefile' => '/local/uplannerconnect/event/handle_event_course_notes.php',
        'callback' => 'grade_deleted',
    ),
    array(
        'eventname' => '\core\event\grade_item_created',
        'includefile' => '/local/uplannerconnect/event/handle_event_course_notes.php',
        'callback' => 'grade_item_created',
    ),
    array(
        'eventname' => '\core\event\grade_item_deleted',
        'includefile' => '/local/uplannerconnect/event/handle_event_course_notes.php',
        'callback' => 'grade_item_deleted',
    ),
    array(
        'eventname' => '\core\event\grade_letter_created',
        'includefile' => '/local/uplannerconnect/event/handle_event_course_notes.php',
        'callback' => 'grade_letter_created',
    ),
    array(
        'eventname' => '\core\event\grade_letter_deleted',
        'includefile' => '/local/uplannerconnect/event/handle_event_course_notes.php',
        'callback' => 'grade_letter_deleted',
    ),
    array(
        'eventname' => '\core\event\grade_letter_updated',
        'includefile' => '/local/uplannerconnect/event/handle_event_course_notes.php',
        'callback' => 'grade_letter_updated',
    ),

);
