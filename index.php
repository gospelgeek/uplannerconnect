<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

//Librerias
require_once '../../config.php';
//Variables globales
require_once('./classes/plugin_config/plugin_config.php');

$context = context_system::instance();

// Crea una instancia de la clase plugin_config
$pluginConfig = new plugin_config();

//Revision de permisos
require_capability('local/'.$pluginConfig->getPluginName().':index', $context);

//Revisa esta logueado
require_login();


//Variables
$url = new moodle_url('/local/'.$pluginConfig->getPluginName().'/index.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');


//Cabecera
echo $OUTPUT->header();

//Contenido
echo $OUTPUT->heading(get_string('title', $pluginConfig->getPluginNameLocal()));
$PAGE->requires->js_call_amd(''.$pluginConfig->getPluginNameLocal().'/main', 'init');

//Pie de página
echo $OUTPUT->footer();