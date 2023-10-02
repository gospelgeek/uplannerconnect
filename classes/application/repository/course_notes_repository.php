<?php
/**
 * @package     local_uplannerconnect
 * @athor       Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

/**
   * Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class course_notes_repository
{
    const STATE_DEFAULT = 0;  //Estado por defecto
    const STATE_SEND = 1; //Estado de envío
    const STATE_ERROR = 2; //Estado de error

    private $moodle_query_handler;
    private $plugin_config;

    public function __construct() {
        $this->moodle_query_handler = new moodle_query_handler();
        $this->plugin_config = new plugin_config();
    }

    /**
     * Actualiza los datos en la base de datos
     *
     * @param array $data
     * @return void
     */
    public function updateDataBD(array $data) : void
    {
        try {
            if (empty($data)) {
                error_log('Excepción capturada: ' . 'No hay datos para actualizar' . "\n");
                return;
            }

            //insertar datos en la base de datos
            $this->moodle_query_handler->executeQuery(
                sprintf(
                plugin_config::QUERY_UPDATE_COURSE_GRADES,
                plugin_config::TABLE_COURSE_GRADE,
                json_encode($data['json']),
                json_encode($data['response']),
                $data['success'],
                $data['id']
                )
            );
        }
        catch (moodle_exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Guarda los datos en la base de datos
     *
     * @param array $data
     * @return void
     */
    public function saveDataBD(array $data) : void
    {
        try {
            if (empty($data)) {
             error_log('Excepción capturada: ' . 'No hay datos para guardar' . "\n");
              return;
            }

            //Insertar datos en la base de datos
            $this->moodle_query_handler->executeQuery(
                sprintf(
                plugin_config::QUERY_INSERT_COURSE_GRADES,
                plugin_config::TABLE_COURSE_GRADE,
                json_encode($data),
                '{"status": "Default response"}',
                0,
                $data['action']
                )
            );
        }
        catch (moodle_exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Obtiene los datos en la base de datos
     *
     * @param array|null $data
     * @return array
     */
    public function getDataBD(array $data = null) : array
    {
        $dataQuery = [];
        try {
            if (empty($data)) {
             error_log('Excepción capturada: ' . 'El estado debe ser un número' . "\n");
              return $dataQuery;
            }

            //Obtener datos en la base de datos
            $dataQuery = $this->moodle_query_handler->executeQuery(
                sprintf(
                    plugin_config::QUERY_SELECT_COURSE_GRADES,
                    plugin_config::TABLE_COURSE_GRADE,
                    $data['state'],
                    $data['limit'],
                    $data['offset']
                )
            );
        }
        catch (moodle_exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
        return $dataQuery;
    }

    /**
     * Delete registers by field state
     *
     * @param $state
     * @return bool
     */
    public function delete_data_bd($state): bool
    {
        $result = false;
        try {
            $result = $this->moodle_query_handler->delete_records(
                'uplanner_grades',
                ['success' => $state]
            );
        } catch (moodle_exception $e) {
            error_log('delete_data_bd: ' . $e->getMessage() . "\n");
        }
        return $result;
    }

    /**
     * Delete registers by field state
     *
     * @param $state
     * @return void
     */
    public function add_log_data() : void
    {
        try {
            $result = $this->moodle_query_handler->executeQuery(
                sprintf(
                    plugin_config::QUERY_COUNT_LOGS,
                    plugin_config::TABLE_COURSE_GRADE,
                )
            );

            $numGrades = reset($result);
            $timestamp = time();

            $insertLog = $this->moodle_query_handler->executeQuery(
                sprintf(
                    plugin_config::QUERY_INSERT_LOGS,
                    plugin_config::TABLE_LOG,
                    $timestamp,
                    $numGrades->count,
                    0,
                    0
                )
            );
        } catch (moodle_exception $e) {
            error_log('delete_data_bd: ' . $e->getMessage() . "\n");
        }
    }
}