<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use dml_exception;

/**
 *  Ejecutar consultas
*/
class moodle_query_handler
{
    private $db;

    public function __construct() {
        global $DB; 
        $this->db = $DB;
    }

    /**
     *  Ejecuta una consulta sql y retorna el resultado.
     * 
     *  @param $sql
     *  @return array
     *  @throws dml_exception 
    */
    public function executeQuery($sql) : array
    {
        if (empty($sql)) {
            error_log('Exception capturada: ' . 'No hay consulta sql' . "\n");
            return [];
        }
        return $this->db->get_records_sql($sql);
    }

    /**
     * Inserta un registro en la base de datos
     */
    public function insert_record_db(array $data)
    {
        if (empty($data)) {
            error_log('Exception capturada: 333' . 'No hay datos para insertar' . "\n");
            return;
        }
        return $this->db->insert_record_raw($data['table'], $data['data']);
    }

    /**
     * Actualiza un registro en la base de datos
     */
    public function update_record_db(array $data)
    {
        if (empty($data)) {
            error_log('Exception capturada: ' . 'No hay datos para actualizar' . "\n");
            return;
        }
        return $this->db->update_record_raw($data['table'], $data['data']);
    }

    /**
     * Retorna un registro de la base de datos
     */
    public function extract_data_db(array $data)
    {
        if (empty($data)) {
            error_log('Exception capturada: ' . 'No hay datos para extraer' . "\n");
            return;
        }
        return $this->db->get_record($data['table'], $data['conditions']);
    }

    /**
     * Delete records in DB
     *
     * @param $table
     * @param array|null $conditions
     * @return bool
     * @throws dml_exception
     */
    public function delete_records($table, array $conditions = null): bool
    {
        return $this->db->delete_records($table, $conditions);
    }
}