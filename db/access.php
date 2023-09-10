<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
defined('MOODLE_INTERNAL') || die();

//Variables globales
require_once(__DIR__ .'/../classes/plugin_config/plugin_config.php');

$pluginConfig = new plugin_config();


/**
 * @param $capabilities
 * @TODO: Revisar cuando nos den la base de datos los tipos de riesgos definidos en
 * @TODO:  en el campus real!!!! por ahora se deja en 1
 * @desc Defines capabilities for the plugin
*/
$capabilities = array(
    'local/'.$pluginConfig->getPluginName().':index' => array(
        'riskbitmask' => 1,
        'contextlevel' => CONTEXT_SYSTEM,
        'captype' => 'write',
        'archetypes' => array(
            'manager' => CAP_ALLOW
        )
    )
);