<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use dml_exception;

/**
 *  Execute Queries
*/
class moodle_query_handler
{
    private $db;

    /**
     * Construct
     */
    public function __construct()
    {
        global $DB; 
        $this->db = $DB;
    }

    /**
     *  Execute a sql query and return the result
     * 
     *  @param $sql
     *  @return array
     *  @throws dml_exception 
    */
    public function executeQuery($sql) : array
    {
        if (empty($sql)) {
            error_log('execute_query: No sql statement found' . "\n");
            return [];
        }

        return $this->db->get_records_sql($sql);
    }

    /**
     * Insert a record into the database
     *
     * @param array $data
     * @return bool|int|null
     * @throws dml_exception
     */
    public function insert_record_db(array $data)
    {
        if (empty($data)) {
            error_log('insert_record_db: There is no data to insert' . "\n");
            return null;
        }

        return $this->db->insert_record_raw($data['table'], $data['data']);
    }

    /**
     * Update a record in the database
     *
     * @param array $data
     * @return bool|null
     * @throws dml_exception
     */
    public function update_record_db(array $data)
    {
        if (empty($data)) {
            error_log('update_record_db: There are no records to update' . "\n");
            return null;
        }

        return $this->db->update_record_raw($data['table'], $data['data']);
    }

    /**
     * Return a record from the database
     *
     * @param array $data
     * @return mixed
     * @throws dml_exception
     */
    public function extract_data_db(array $data)
    {
        if (empty($data)) {
            error_log('Exception capturada: ' . 'No hay datos para extraer' . "\n");
            return null;
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