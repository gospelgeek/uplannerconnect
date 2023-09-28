<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use dml_exception;

/**
 *  @author Cristian Machado <cristian.machado@correounivalle.edu.co>
*/
class MoodleQueryHandler {
    private $db;

    //Constructor
    public function __construct() {
        global $DB; 
        $this->db = $DB;
    }

    /**
     *  @author Cristian machado <cristian.machado@correounivalle.edu.co>
     *  @description Ejecuta una consulta sql y retorna el resultado
    */
    public function executeQuery($sql) {

        if (empty($sql) || !isset($sql)) {
            error_log('Excepción capturada: ' . 'No hay consulta sql' . "\n");
            return;
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
