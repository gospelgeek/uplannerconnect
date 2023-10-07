<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\course;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

/**
   * Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class course_extraction_data
{
    private $typeEvent;
    private $validator;
    
    public function __construct() {
        $this->typeEvent = [
            'user_graded' => 'ResourceUserGraded',
            'grade_item_updated' => 'ResourceGradeItemUpdated',
            'grade_deleted' => 'ResourceUserGraded',
            'grade_item_deleted' => 'ResourceGradeItemDeleted',
            'grade_item_created' => 'ResourceGradeItemCreated'
        ];
        $this->validator = new data_validator();
    }

    /**
     * Retorna los datos acorde al evento que se requiera
     *
     * @param array $data
     * @return array
     */
    public function getResource(array $data) : array
    {
      $arraySend = [];  
      try {
            if ($this->validator->verificateKeyArrayBoolean([
               'array_verification' => plugin_config::CREATE_EVENT_DATA,
               'get_data' => $data,
            ])) {
               $typeEvent = $this->typeEvent[$data['typeEvent']];
                $arraySend = $this->$typeEvent($data);
            }
      }
      catch (moodle_exception $e) {
         error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }
      return $arraySend;
    }

    /**
     * Retorna los datos del evento user_graded
     * @param array $data
     * @return array
     */
    private function ResourceUserGraded(array $data) : array
    {
       $arraySend = [];  
       try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento user_graded');
                return $arraySend;
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

            $arraySend = [
                'data' => $dataToSave,
                'typeEvent' => $data['typeEvent'],
            ];
      } catch (moodle_exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }
      return $arraySend;
    }

    /**
     * Retorna los datos del evento grade_item_updated
     *
     * @param array $data
     * @return array
     */
    private function ResourceGradeItemUpdated(array $data)  : array
    {
        $arraySend = [];
        try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento grade_item_updated');
                return $arraySend;
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

            $arraySend = [
                'data' => $dataToSave,
                'typeEvent' => 'grade_item_updated',
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

    /**
     * Retorna los datos del evento grade_item_deleted
     *
     * @param array $data
     * @return array
     */
    private function ResourceGradeItemDeleted(array $data) : array
    {
        $arraySend = [];        
        try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento grade_item_deleted');
                return $arraySend;
            }
            
            $event = $data['dataEvent'];
            $gradeItem = $this->validator->isObjectData($event->get_grade_item());
            
            $dataToSave = [
                'sectionId' => $this->validator->isIsset($gradeItem->courseid),
                'evaluationName' => $this->validator->isIsset($gradeItem->itemname),
                'action' => 'delete'
            ];

            $arraySend = [
                'data' => $dataToSave,
                'typeEvent' => 'grade_item_deleted',
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

    /**
     * Retorna los datos del evento grade_item_created
     *
     * @param array $data
     * @return array
     */
    private function ResourceGradeItemCreated(array $data) : array
    {
        $arraySend = [];
        try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento grade_item_created');
                return $arraySend;
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

            $arraySend = [
                'data' => $dataToSave,
                'typeEvent' => 'grade_item_created',
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }
}