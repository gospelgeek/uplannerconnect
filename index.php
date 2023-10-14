<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

//Librerias
require_once '../../config.php';


use local_uplannerconnect\plugin_config\plugin_config;
use local_uplannerconnect\application\repository\moodle_query_handler;

$context = context_system::instance();

//Revision de permisos
require_capability('local/'.plugin_config::PLUGIN_NAME.':index', $context);

//Revisa esta logueado
require_login();

//Variables
$url = new moodle_url('/local/'.plugin_config::PLUGIN_NAME.'/index.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

//Incluir JS
$PAGE->requires->js_call_amd(''.plugin_config::PLUGIN_NAME_LOCAL.'/main', 'init');

//Objeto de datos
$data = new \stdClass();

$moodleQuery = new moodle_query_handler();
$rawData = $moodleQuery->executeQuery('SELECT * FROM mdl_uplanner_log');
$data->row = [];

foreach ($rawData as $key => $value) {
        $value->date = date('Y-m-d H:i:s', $value->date);
        array_push($data->row, $value);
}

//imprimir en console js
error_log(print_r($rawData, true));

//Cabecera
echo $OUTPUT->header();

//Contenido
echo $OUTPUT->heading(get_string('title', plugin_config::PLUGIN_NAME_LOCAL));

//Head
echo $OUTPUT->render_from_template('local_uplannerconnect/index', $data);

//Pie de página
echo $OUTPUT->footer();