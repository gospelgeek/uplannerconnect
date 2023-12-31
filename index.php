<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

//Librerias
require_once '../../config.php';

use local_uplannerconnect\plugin_config\plugin_config;
use local_uplannerconnect\application\service\page_index;

$context = context_system::instance();

//Revision de permisos
require_capability('local/'.plugin_config::PLUGIN_NAME.':index', $context);

//Revisa esta logueado
require_login();

//Variables
$url = new moodle_url('/local/'.plugin_config::PLUGIN_NAME.'/index.php');
$urlSummary = new moodle_url('/local/'.plugin_config::PLUGIN_NAME.'/routes/error_summary.php');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

$page_index = new page_index();
$data = $page_index->getDataSendJsonUplanner([
    "urlSummary" => $urlSummary
]);

//Cabecera
echo $OUTPUT->header();

//Contenido
echo $OUTPUT->heading(get_string('title', plugin_config::PLUGIN_NAME_LOCAL));

//Head
echo $OUTPUT->render_from_template('local_uplannerconnect/index', $data);

//Pie de página
echo $OUTPUT->footer();