<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


require_once(__DIR__ . '/MoodleQueryHandler.php');
require_once(__DIR__ . '/../../plugin_config/plugin_config.php');

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseNotesRepository {

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

          if (empty($data)) {
             error_log('Excepción capturada: ' . 'No hay datos para actualizar' . "\n");
              return; 
          }

          $dataQuery = [
            'json' => $data['json'], 
            'response' => $data['response'],
            'success' => $data['success'],
          ];
        
          //insertar datos en la base de datos
          $query =  sprintf(
            plugin_config::QUERY_UPDATE_COURSE_GRADES, 
            plugin_config::TABLE_COURSE_GRADE, 
            json_encode($dataQuery['json']), 
            $dataQuery['response'], 
            $dataQuery['success']
          );

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
          
          if (empty($data)) {
             error_log('Excepción capturada: ' . 'No hay datos para guardar' . "\n");
              return; 
          }

          //data
          $dataQuery = [
            'json' => $data, 
            'response' => '{"status": "Default response"}',
            'success' => 0,
            'action' => $data['action']
          ];

          //Insertar datos en la base de datos
          $query =  sprintf(
            plugin_config::QUERY_INSERT_COURSE_GRADES,
            plugin_config::TABLE_COURSE_GRADE,
            json_encode($dataQuery['json']),
            $dataQuery['response'],
            intval($dataQuery['success']),
            $dataQuery['action']
          );

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

          if (!is_numeric($state)) {
             error_log('Excepción capturada: ' . 'El estado debe ser un número' . "\n");
              return; 
          }

          //Obtener datos en la base de datos
          $query =  sprintf(plugin_config::QUERY_SELECT_COURSE_GRADES, plugin_config::TABLE_COURSE_GRADE, $state);
          return $this->MoodleQueryHandler->executeQuery($query);

        }
        catch (Exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }

    }

}
