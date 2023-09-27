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
class CourseExtractionData {

    //Atributos
    private $typeEvent;
    private $validator;

    //Constructor
    public function __construct() {
 
        //Inicializar la variable typeEvent
        $this->typeEvent = [
            'user_graded' => 'ResourceUserGraded',
            'grade_item_updated' => 'ResourceGradeItemUpdated',
            'grade_deleted' => 'ResourceUserGraded',
            'grade_item_created' => 'ResourceGradeItemCreated',
            'grade_item_deleted' => 'ResourceGradeItemDeleted',
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
     *  @description Retorna los datos del evento user_graded
     *  @return array
    */
    private function ResourceUserGraded(array $data) : array {
        
       try {

            //matar el proceso si no llega la información
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento user_graded');
                return [];
            }
             
            //Traer la información
            $event = $data['dataEvent'];
            $getData = $this->validator->isArrayData($event->get_data());
            $grade = $this->validator->isObjectData($event->get_grade());
            $gradeRecordData = $this->validator->isObjectData($grade->get_record_data());
            $gradeLoadItem = $this->validator->isObjectData($grade->load_grade_item());
        
            //información a guardar
            $dataToSave = [
                'sectionId' => $this->validator->isIsset($grade->grade_item->courseid),
                'studentCode' => $this->validator->isIsset($grade->userid),
                'finalGrade' => $this->validator->isIsset((($getData['other'])['finalgrade'])),
                'finalGradePercentage' => isset($grade->grade_item->grademax, $grade->rawgrade) ? (100 / $grade->grade_item->grademax * $grade->rawgrade) : '',
                'evaluationGroupCode' => $this->validator->isIsset($gradeLoadItem->categoryid),
                'evaluationId' => $this->validator->isIsset($gradeLoadItem->itemtype),
                'value' => $this->validator->isIsset(($getData['other'])['finalgrade']),
                'evaluationName' => $this->validator->isIsset($gradeLoadItem->itemname),
                'date' => $this->validator->isIsset($gradeLoadItem->timecreated),
                'lastModifiedDate' => $this->validator->isIsset($gradeLoadItem->timemodified),
                'action' => $data['dispatch'],
            ];
            
            return [
                'data' => $dataToSave,
                'typeEvent' => $data['typeEvent'],
            ];

      } catch (Exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }

    }


    /**
     * @package uPlannerConnect
     * @description Retorna los datos del evento grade_item_updated
     * @return array
    */
    private function ResourceGradeItemUpdated(array $data)  : array {

        try {

            //matar el proceso si no llega la información
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento grade_item_updated');
                return [];
            }

            $event = $data['dataEvent'];
            $GradeItem = $this->validator->isObjectData($event->get_grade_item());
            
            //información a guardar
            $dataToSave = [
                'sectionId' => $this->validator->isIsset($GradeItem->courseid),
                'evaluationGroupCode' => $this->validator->isIsset($GradeItem->categoryid),
                'date' => $this->validator->isIsset($GradeItem->timecreated),
                'lastModifiedDate' => $this->validator->isIsset($GradeItem->timemodified),
                'action' => 'update'
            ];

            return [
                'data' => $dataToSave,
                'typeEvent' => 'grade_item_updated',
            ];


        } catch (Exception $e) {
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

    
    /**
     *  @package uPlannerConnect
     *  @description Retorna los datos del evento grade_item_deleted
     *  @return array
    */
    private function ResourceGradeItemDeleted(array $data) : array {
            
        try {

            //matar el proceso si no llega la información
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento grade_item_deleted');
                return [];
            }

            $event = $data['dataEvent'];
            $gradeItem = $this->validator->isObjectData($event->get_grade_item());

            $dataToSave = [
                'sectionId' => $this->validator->isIsset($gradeItem->courseid),
                'evaluationName' => $this->validator->isIsset($gradeItem->itemname),
                'action' => 'delete'
            ];
            
            return [
                'data' => $dataToSave,
                'typeEvent' => 'grade_item_deleted',
            ];

        } catch (Exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
    }

}
