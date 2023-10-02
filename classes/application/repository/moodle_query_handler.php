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