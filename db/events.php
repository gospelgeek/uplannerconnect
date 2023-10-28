<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

use local_uplannerconnect\plugin_config\plugin_config;

defined('MOODLE_INTERNAL') || die();

$observers = [];

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Instancia un evento para el manejo de notas de un curso
*/
foreach (plugin_config::EVENTS_OBSERVERS as $eventData) {
    
    $callback = preg_replace('/^\\\\core\\\\event\\\\/', '', $eventData['eventname']);
    
    if (isset($eventData['isLocal'])) {
        $callback = preg_replace('/^\\\\local_uplannerconnect\\\\event\\\\/', '', $eventData['eventname']);
    }
   
    $observers[] = [
        'eventname' => $eventData['eventname'],
        'callback' => $eventData['includefile'].'::'.$callback,
    ];
    
}