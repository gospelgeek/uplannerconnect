<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

use local_uplannerconnect\plugin_config\events_observers;

defined('MOODLE_INTERNAL') || die();

$observers = [];

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Instancia un evento para el manejo de notas de un curso
*/
foreach (events_observers::EVENTS_OBSERVERS as $eventData) {
    
    $callback = preg_replace('/^\\\\core\\\\event\\\\/', '', $eventData['eventname']);
    
    if (isset($eventData['isLocal'])) {
        $callback = preg_replace('/^\\\\local_uplannerconnect\\\\event\\\\/', '', $eventData['eventname']);
    }
   
    if (isset($eventData['isForum'])) {
        $callback = preg_replace('/^\\\\mod_forum\\\\event\\\\/', '', $eventData['eventname']);
    }

    $observers[] = [
        'eventname' => $eventData['eventname'],
        'callback' => $eventData['includefile'].'::'.$callback,
    ];
}