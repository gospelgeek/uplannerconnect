<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use local_uplannerconnect\plugin_config\plugin_config;

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class course_evaluation_structure_repository {

    //Constantes
    const STATE_DEFAULT = 0;  //Estado por defecto
    const STATE_SEND = 1;     //Estado de envío
    const STATE_ERROR = 2;    //Estado de error
  
    //Atributos
    private $MoodleQueryHandler;


    //Constructor
    public function __construct() {
        //Instancia de la clase MoodleQueryHandler
        $this->MoodleQueryHandler = new MoodleQueryHandler();
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

            //insertar datos en la base de datos
          $query =  sprintf(
            plugin_config::QUERY_UPDATE_COURSE_GRADES, 
            plugin_config::TABLE_COURSE_EVALUATION, 
            json_encode($data['json']),
            json_encode($data['response']),
            $data['success'],
            $data['id']
          );

          return $this->MoodleQueryHandler->executeQuery($query);
        }
        catch (\Exception $e) {
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
            plugin_config::TABLE_COURSE_EVALUATION,
            json_encode($dataQuery['json']),
            $dataQuery['response'],
            intval($dataQuery['success']),
            $dataQuery['action']
          );

          return $this->MoodleQueryHandler->executeQuery($query);

        }
        catch (\Exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }

    }


    /**
     * @package uPlannerConnect
     * @description Obtiene los datos en la base de datos
     * @return void 
    */
    public function getDataBD(array $data = null) {

        try {
          if (empty($data)) {
             error_log('Excepción capturada: ' . 'El estado debe ser un número' . "\n");
              return; 
          }

          //Obtener datos en la base de datos
          $query =  sprintf(
              plugin_config::QUERY_SELECT_COURSE_GRADES,
            plugin_config::TABLE_COURSE_EVALUATION,
              $data['state'],
              $data['limit'],
              $data['offset']
          );

          return $this->MoodleQueryHandler->executeQuery($query);
        }
        catch (\Exception $e) {
          error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }

    }

}
