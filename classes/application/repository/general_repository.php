<?php
/**
 * @package     local_uplannerconnec
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class general_repository
 */
class general_repository
{
    const TABLE_LOG = "uplanner_log";
    const QUERY_COUNT_ALL_TABLES = "SELECT (SELECT COUNT(id) FROM mdl_uplanner_grades) AS count_t1, (SELECT COUNT(id) FROM mdl_uplanner_materials) AS count_t2, (SELECT COUNT(id) FROM mdl_uplanner_notification) AS count_t3, (SELECT COUNT(id) FROM mdl_uplanner_evaluation) AS count_t4";
    const QUERY_COUNT_ERRORS_ALL_TABLES = "SELECT (SELECT COUNT(id) FROM mdl_uplanner_grades WHERE is_sucessful = 0) AS count_t1, (SELECT COUNT(id) FROM mdl_uplanner_materials WHERE is_sucessful = 0) AS count_t2, (SELECT COUNT(id) FROM mdl_uplanner_notification WHERE is_sucessful = 0) AS count_t3, (SELECT COUNT(id) FROM mdl_uplanner_evaluation WHERE is_sucessful = 0) AS count_t4";

    /**
     * @var moodle_query_handler
     */
    private $moodle_query_handler;

    /**
     * Construct
     */
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
        } catch (moodle_exception $e) {
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
            $tableName = '{'.$data['table'].'}';
            //Obtener datos en la base de datos
            $dataQuery = $this->moodle_query_handler->executeQuery(
                sprintf(
                    $data['query'],
                    $tableName,
                ),
                [
                    "success" => $dataJson['state'],
                    "max_result" => $dataJson['limit'],
                    "offset" => $dataJson['offset']
                ]
            );
        }
        catch (moodle_exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
        return $dataQuery;
    }

    /**
     * Delete registers
     *
     * @param $table
     * @param $condition
     * @return bool
     */
    public function delete_rows($table, $condition): bool
    {
        $result = false;
        try {
            $result = $this->moodle_query_handler->delete_records(
                $table,
                $condition
            );
        } catch (moodle_exception $e) {
            error_log('delete_rows: ' . $e->getMessage() . "\n");
        }
        return $result;
    }

    /**
     * Add count logs in table TABLE_LOG
     *
     * @return bool|int|null
     */
    public function add_log_data()
    {
        $result = null;
        try {
            $query_result = $this->moodle_query_handler->executeQuery(self::QUERY_COUNT_ALL_TABLES);
            $count_data = reset($query_result);
            $timestamp = date("Y-m-d H:i:s");
            $data = [
                'table' => self::TABLE_LOG,
                'data' => (object)[
                    'num_grades' => intval($count_data->count_t1),
                    'num_materials' => intval($count_data->count_t2),
                    'num_anouncements' => intval($count_data->count_t3),
                    'num_evaluation' => intval($count_data->count_t4),
                    'num_grades_err' => 0,
                    'num_materials_err' => 0,
                    'num_anouncements_err' => 0,
                    'num_evaluation_err' => 0,
                    'date' => (string)$timestamp,
                    'updated_at' => (string)$timestamp
                ]
            ];
            $result = $this->moodle_query_handler->insert_record_db($data);
        } catch (moodle_exception $e) {
            error_log('add_log_data: ' . $e->getMessage() . "\n");
        }

        return $result;
    }

    /**
     * Add count logs errors in table TABLE_LOG
     *
     * @return bool|null
     */
    public function add_log_errors_data($id)
    {
        $result = null;
        try {
            $query_result = $this->moodle_query_handler->executeQuery(self::QUERY_COUNT_ERRORS_ALL_TABLES);
            $count_data = reset($query_result);
            $timestamp = date("Y-m-d H:i:s");
            $data = [
                'table' => self::TABLE_LOG,
                'data' => (object)[
                    'num_grades_err' => intval($count_data->count_t1),
                    'num_materials_err' => intval($count_data->count_t2),
                    'num_anouncements_err' => intval($count_data->count_t3),
                    'num_evaluation_err' => intval($count_data->count_t4),
                    'updated_at' => (string)$timestamp,
                    'id' => $id
                ]
            ];
            $result = $this->moodle_query_handler->update_record_db($data);
        } catch (moodle_exception $e) {
            error_log('add_log_errors_data: ' . $e->getMessage() . "\n");
        }

        return $result;
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

    /**
     * Transform data
     *
     * @param $fields
     * @param $data
     * @return array
     */
    public function get_transfor_data($fields, $data)
    {
        $newData = [];
        foreach ($fields as $filed => $type) {
            if (!array_key_exists($filed, $data)) {
                continue;
            }
            switch ($type) {
                case 'json':
                    $value = json_encode($data[$filed]);
                    break;
                case 'int':
                    $value = intval($data[$filed]);
                    break;
                default:
                    $value = $data[$filed];
            }
            $newData[$filed] = $value;
        }

        return $newData;
    }
}