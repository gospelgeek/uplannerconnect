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

    //Constantes
    const STATE_DEFAULT = 0;  //Estado por defecto
    const STATE_SEND = 1;     //Estado de envío
    const STATE_ERROR = 2;    //Estado de error
  
    //Atributos
    private $MoodleQueryHandler;
    private $plugin_config;


    //Constructor
    public function __construct() {

        //Instancia de la clase MoodleQueryHandler
        $this->MoodleQueryHandler = new MoodleQueryHandler();
        //Instancia de la clase plugin_config
        $this->plugin_config = new plugin_config();

    }


    /**
     * @package uPlannerConnect
     * @description Actualiza los datos en la base de datos
     * @return void
    */
    public function updateDataBD(array $data) {

        try {

          $dataQuery = [
            'json' => $data['json'], 
            'response' => $data['response'],
            'success' => $data['success'],
          ];
        
          $query =  "UPDATE %s SET json = '%s', response = '%s', success = '%s' WHERE id = 1";
          $query =  sprintf($query, plugin_config::TABLE_COURSE_GRADE, json_encode($dataQuery['json']), $dataQuery['response'], $dataQuery['success']);
          return $this->MoodleQueryHandler->executeQuery($query);
        }
        catch (Exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
         
    }

    /**
     * @package uPlannerConnect
     * @description Guarda los datos en la base de datos
     * @return void 
    */
    public function saveDataBD(array $data) {

        try {
          
          $dataQuery = [
            'json' => $data, 
            'response' => '{"status": "Default response"}',
            'success' => 0,
          ];
        
          $query =  "INSERT INTO %s (json, response, success) VALUES ('%s', '%s', '%s')";
          $query =  sprintf($query, plugin_config::TABLE_COURSE_GRADE, json_encode($dataQuery['json']), $dataQuery['response'], $dataQuery['success']);
    
          return $this->MoodleQueryHandler->executeQuery($query);
        }
        catch (Exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }

    }


    /**
     * @package uPlannerConnect
     * @description Obtiene los datos en la base de datos
     * @return void 
    */
    public function getDataBD($state = self::STATE_DEFAULT) {

        try {
          $query = "SELECT * FROM %s WHERE success = %s LIMIT 100";
          $query =  sprintf($query, plugin_config::TABLE_COURSE_GRADE, $state);
          return $this->MoodleQueryHandler->executeQuery($query);
        }
        catch (Exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }

    }

}
