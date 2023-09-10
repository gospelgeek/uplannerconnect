<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

//Variables globales
require_once(__DIR__ . '/plugin_config/plugin_config.php');

$pluginConfig = new plugin_config();


$plugin->version = 2023090400; //año-mes-día-numeroVersion
$plugin->component = $pluginConfig->getPluginNameLocal();

$plugin->requires = 2015030901; 
$plugin->maturity = MATURITY_ALPHA;


$plugin->release = '1.0.0';
$plugin->release = '1.0 (Build: 2023090300)';
$plugin->author = 'Samuel Ramirez & Cristian Machado Mosquera & Daniel dorado';
$plugin->authorcontact = 'samuel.ramirez@correounivalle.edu.co & cristian.machado@correounivalle.edu.co & doradodaniel14@gmail.com';


$plugin->license = 'GNU GPL v3 or later';
$plugin->description = 'Este plugin se utiliza para enviar información a uPlanner.';