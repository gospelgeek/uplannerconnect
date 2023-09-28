<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

use local_uplannerconnect\plugin_config\plugin_config;

$observers = [];

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Instancia un evento para el manejo de notas de un curso
*/
foreach (plugin_config::EVENTS_OBSERVERS as $eventData) {
    
    $callback = preg_replace('/^\\\\core\\\\event\\\\/', '', $eventData['eventname']);
   
    $observers[] = [
        'eventname' => $eventData['eventname'],
        'includefile' => $eventData['includefile'],
        'callback' => $callback,
    ];
    
}