<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado  <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Repositorio de datos de los cursos
 */
class course_data_repository
{
    private $moodle_query_handler;

    public function __construct() {
        $this->moodle_query_handler = new moodle_query_handler();
    }

    /**
     * Trae el shortname de un curso
     *
     * @param $courseId
     * @return string
     */
    public function getCourseShortname($courseId) : string
    {
        $short_name = '';
        try {
            if (empty($courseId)) {
                return $short_name;
            }

            //Ejecutar la consulta
            $result = $this->moodle_query_handler->executeQuery(
                sprintf(
                plugin_config::QUERY_SHORNAME_COURSE_BY_ID,
                plugin_config::TABLE_COURSE_MOODLE,
                $courseId
                )
            );

            //Verificar si la información llega vacía
            if (!empty($result)) {
                $shortname = reset($result);
                if (!empty($shortname)) {
                    $short_name = $shortname->shortname;
                }
            }
        }
        catch (moodle_exception $e) {
            error_log('Error al traer el shortname del curso: ' . $e->getMessage());
        }
        return $short_name;
    }
}