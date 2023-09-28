<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
defined('MOODLE_INTERNAL') || die();

use local_uplannerconnect\plugin_config\plugin_config;

/**
 * @param $capabilities
 * @TODO: Revisar cuando nos den la base de datos los tipos de riesgos definidos en
 * @TODO:  en el campus real!!!! por ahora se deja en 1
 * @description Define las capacidades para el plugin
*/
$capabilities = array(
    plugin_config::PLUGIN_NAME.':index' => array(
        'riskbitmask' => 1,
        'contextlevel' => CONTEXT_SYSTEM,
        'captype' => 'write',
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    )
);