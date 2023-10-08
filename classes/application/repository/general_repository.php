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
     * @todo Falta hacerlo dinámico
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
    public function add_log_data(array $data) : void
    {
        try {
            $result = $this->moodle_query_handler->executeQuery(
                sprintf(
                    $data['query_insert'],
                    $data['table_insert'],
                )
            );

            $numGrades = reset($result);
            $timestamp = time();

            $insertLog = $this->moodle_query_handler->executeQuery(
                sprintf(
                    $data['query_log'],
                    $data['table_log'],
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