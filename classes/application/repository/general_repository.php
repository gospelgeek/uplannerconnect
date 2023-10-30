<?php
/**
 * @package     local_uplannerconnec
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Repositorio de datos de los cursos
 */
class general_repository
{
    const QUERY_COUNT_ALL_TABLES = "SELECT (SELECT COUNT(id) FROM mdl_uplanner_grades) AS count_t1, (SELECT COUNT(id) FROM mdl_uplanner_materials) AS count_t2, (SELECT COUNT(id) FROM mdl_uplanner_notification) AS count_t3, (SELECT COUNT(id) FROM mdl_uplanner_evaluation) AS count_t4";

    private $moodle_query_handler;

    public function __construct() {
        $this->moodle_query_handler = new moodle_query_handler();
    }

    /**
     * Actualiza los datos en la base de datos
     *
     * @todo Falta validar los tipos de datos
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

            $dataJson = $data['data'];

            //insertar datos en la base de datos
            $this->moodle_query_handler->update_record_db([
                'table' => $data['table'],
                'data' => $dataJson
            ]);
        }
        catch (moodle_exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Guarda los datos en la base de datos
     *
     * @todo Falta validar los tipos de datos
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
            $dataJson = $data['data'];

            $this->moodle_query_handler->insert_record_db([
                'table' => $data['table'],
                'data' => $dataJson
            ]);
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Obtiene los datos en la base de datos
     *
     * @todo Falta validar los tipos de datos
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

            $dataJson = $data['data'];

            //Obtener datos en la base de datos
            $dataQuery = $this->moodle_query_handler->executeQuery(
                sprintf(
                    $data['query'],
                    $data['table'],
                    $dataJson['state'],
                    $dataJson['limit'],
                    $dataJson['offset']
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
     * @param $table
     * @return bool
     * @todo Falta hacerlo dinámico
     */
    public function delete_data_bd($state,$table): bool
    {
        $result = false;
        try {
            $result = $this->moodle_query_handler->delete_records(
                $table,
                ['success' => $state]
            );
        } catch (moodle_exception $e) {
            error_log('delete_data_bd: ' . $e->getMessage() . "\n");
        }
        return $result;
    }

    /**
     * Delete register
     *
     * @param $primary_key
     * @param $id
     * @param $table
     * @return bool
     */
    public function delete_row($table, $id, $primary_key = 'id'): bool
    {
        $result = false;
        try {
            $result = $this->moodle_query_handler->delete_records(
                $table,
                [$primary_key => $id]
            );
        } catch (moodle_exception $e) {
            error_log('delete_row: ' . $e->getMessage() . "\n");
        }
        return $result;
    }

    /**
     * Delete registers by field state
     *
     * @param array $data
     * @return void
     */
    public function add_log_data(array $data) : void
    {
        try {
            $result = $this->moodle_query_handler->executeQuery(self::QUERY_COUNT_ALL_TABLES);

            $numGrades = reset($result);
            $timestamp = time();

            $insertLog = $this->moodle_query_handler->executeQuery(
                sprintf(
                    $data['query_log'],
                    $data['table_log'],
                    $timestamp,
                    $numGrades->count_t1,
                    $numGrades->count_t2,
                    $numGrades->count_t3,
                    $numGrades->count_t4,
                )
            );
        } catch (moodle_exception $e) {
            error_log('add_log_data: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Delete registers by field state
     *
     * @param $query
     * @return false|int|mixed
     */
    public function count($query)
    {
        try {
            $result = $this->moodle_query_handler->executeQuery($query);
            return reset($result);
        } catch (moodle_exception $e) {
            error_log('add_log_data: ' . $e->getMessage() . "\n");
        }

        return 0;
    }
}