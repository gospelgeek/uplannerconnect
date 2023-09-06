<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
namespace local_uplannerconnect;

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
     *  @des Ejecuta una consulta sql y retorna el resultado
    */
    public function ejecutarConsulta($sql) {
        return $this->db->get_records_sql($sql);
    }
}
