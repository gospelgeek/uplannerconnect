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


//Cabecera
echo $OUTPUT->header();

$moodleQuery = new moodle_query_handler();
$data = $moodleQuery->executeQuery('SELECT * FROM mdl_uplanner_log');

//Contenido
echo $OUTPUT->heading(get_string('title', plugin_config::PLUGIN_NAME_LOCAL));

/**
 * TODO: Esto se va a borrar!!!!!!!!! 
 */ 
if (empty($data)) {
    echo '<h1>No hay datos para mostrar</h1>';
}
else {
    // Esto es de momento para salir rapido del requirimiento.
    echo '<table>';
    echo '<thead><tr>
                    <th style="width: 2em; text-align: center;">id</th>
                    <th style="width: 2em; text-align: center;">date</th>
                    <th style="width: 2em; text-align: center;">num_grades</th>
                    <th style="width: 2em; text-align: center;">num_materials</th>
                    <th style="width: 2em; text-align: center;">num_anouncements</th>
                </tr>
        </thead>';
    echo '<tbody>';

    // Recorrer los datos y mostrarlos en la tabla
    foreach ($data as $row) {
        echo '<tr>';
        echo '<td style="width: 2em; text-align: center;">' . $row->id . '</td>';
        echo '<td style="width: 2em; text-align: center;">' . $row->date . '</td>';
        echo '<td style="width: 2em; text-align: center;">' . $row->num_grades . '</td>';
        echo '<td style="width: 2em; text-align: center;">' . $row->num_materials . '</td>';
        echo '<td style="width: 2em; text-align: center;">' . $row->num_anouncements . '</td>';
        echo '</tr>';
    }

    // Cierre de la tabla HTML
    echo '</tbody>';
    echo '</table>';

}




$PAGE->requires->js_call_amd(''.plugin_config::PLUGIN_NAME_LOCAL.'/main', 'init');

//Pie de página
echo $OUTPUT->footer();