<?php
/**
 * @package     uPlannerConnect
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_uplannerconnect\plugin_config\plugin_config;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_uplannerconnect_edit_log' => [
        'classname' => 'local_uplannerconnect\external\api',
        'methodname' => 'edit_log',
        'classpath' => '',
        'description' => 'Edit uPlanner Log',
        'type' => 'write',
        'capabilities' => 'local/' . plugin_config::PLUGIN_NAME . ':index',
        'loginrequired' => true,
        'ajax' => true,
    ]
];
