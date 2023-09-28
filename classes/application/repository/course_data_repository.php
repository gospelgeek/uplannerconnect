<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;


use local_uplannerconnect\plugin_config\plugin_config;

defined('MOODLE_INTERNAL') || die();


/**
 * 
 * Repositorio de datos de los cursos
 * 
 * @package local_uplannerconnectn
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * 
*/
class course_data_repository {

    // Atributos.
    private $moodleQueryHandler;

    // Constructor.
    public function __construct() {
        //Instancia de la clase MoodleQueryHandler
        $this->moodleQueryHandler = new MoodleQueryHandler();
    }


    /**
     * Trae el shortname de un curso
     * 
     * @package local_uplannerconnect
     * @return string
    */
    public function getCourseShortname($courseId) : string {

        try {

            //Verificar si el parámetro llega vacío
            if (empty($courseId)) {
                return '';
            }

            //Traer la información
            $query = sprintf(
                plugin_config::QUERY_SHORNAME_COURSE_BY_ID,
                plugin_config::TABLE_COURSE_MOODLE,
                $courseId
            );

            //Ejecutar la consulta
            $result = $this->moodleQueryHandler->executeQuery($query);

            //Verificar si la información llega vacía
            if (!isset($result) || empty($result)) { return ''; }
            //Obtener el shortname
            $shortname = reset($result);
            //Verificar si el resultado llega vacío
            if (!isset($shortname) || empty($shortname)) { return ''; }

            return $shortname->shortname;
        }
        catch (\Exception $e) {
            error_log('Error al traer el shortname del curso: ' . $e->getMessage());
        }
        return '';
    }

}