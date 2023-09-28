<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


require_once(__DIR__ . '/../../application/service/DataValidator.php');
require_once(__DIR__ . '/../../plugin_config/plugin_config.php');


/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class extraction_course_evaluation_structure {

    //Atributos
    private $typeEvent;
    private $validator;

    //Constructor
    public function __construct() {
 
        //Inicializar la variable typeEvent
        $this->typeEvent = [
            'grade_item_created' => 'ResourceGradeItemCreated',
        ];

        //instancia la clase DataValidator
        $this->validator = new DataValidator();

    }

    /**
     * @package uPlannerConnect
     * @description Retorna los datos acorde al evento que se requiera
     * @return array
    */
    public function getResource(array $data) : array {

      try {
           if ($this->validator->verificateKeyArrayBoolean([
               'array_verification' => plugin_config::CREATE_EVENT_DATA,
               'get_data' => $data,
           ])) {
               $typeEvent = $this->typeEvent[$data['typeEvent']];
               return $this->$typeEvent($data);
           }
           else {
             error_log('Falta algun tipo de informacion del evento');
             return [];
           }
      }
      catch (Exception $e) {
         error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }

    }

    /**
     *  @package uPlannerConnect
     *  @description Retorna los datos del evento grade_item_created
     *  @return array
    */
    private function ResourceGradeItemCreated(array $data) : array {

        try {

            //matar el proceso si no llega la información
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento grade_item_created');
                return [];
            }

            $event = $data['dataEvent'];
            $get_grade_item = $this->validator->isObjectData($event->get_grade_item());

            $dataToSave = [
                'sectionId' => $this->validator->isIsset($get_grade_item->courseid),
                'evaluationGroupCode' => $this->validator->isIsset($get_grade_item->categoryid),
                'evaluationId' => $this->validator->isIsset($get_grade_item->courseid),
                'evaluationName' => $this->validator->isIsset($get_grade_item->itemname),
                'action' => 'create'
            ];

            return [
                'data' => $dataToSave,
                'typeEvent' => 'grade_item_created',
            ];

        } catch (Exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }

    }

}
