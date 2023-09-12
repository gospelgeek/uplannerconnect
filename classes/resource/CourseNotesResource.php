<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


require_once(__DIR__ . '/../db/MoodleQueryHandler.php');
require_once(__DIR__ . '/../plugin_config/plugin_config.php');

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseNotesResource {

    private $MoodleQueryHandler;
    private $plugin_config;

    public function __construct() {

        //Instancia de la clase MoodleQueryHandler
        $this->MoodleQueryHandler = new MoodleQueryHandler();
        //Instancia de la clase plugin_config
        $this->plugin_config = new plugin_config();

    }


    /**
     * @package uPlannerConnect
     * @description Actualiza la data en la base de datos
     * @return void
    */
    public function updateDataBD(array $data) {

        try {

          $dataQuery = [
            'json' => $data['json'], 
            'response' => $data['response'],
            'success' => $data['success'],
          ];
        
          $query = "UPDATE {$this->plugin_config->getTableCourseGrade()} SET json = '".json_encode($dataQuery['json'])."', response = '".json_encode($dataQuery['response'])."', success = '".$dataQuery['success']."' WHERE id = 1";
        
          return $this->MoodleQueryHandler->ejecutarConsulta($query);
        }
        catch (Exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
         
    }

    /**
     * @package uPlannerConnect
     * @description Guarda la data en la base de datos
     * @return void 
    */
    public function saveDataBD(array $data) {

        try {
          
          $dataQuery = [
            'json' => $data, 
            'response' => '{"status": "Default response"}',
            'success' => 0,
          ];
        
          $query = "INSERT INTO {$this->plugin_config->getTableCourseGrade()} (json, response, success) VALUES ('".json_encode($dataQuery['json'])."', '".$dataQuery['response']."', '".$dataQuery['success']."')";
         
          return $this->MoodleQueryHandler->ejecutarConsulta($query);
        }
        catch (Exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }

    }

}
