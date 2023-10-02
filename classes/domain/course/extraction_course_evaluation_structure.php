<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\course;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

/**
   * Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class extraction_course_evaluation_structure
{
    private $typeEvent;
    private $validator;

    public function __construct() {
        $this->typeEvent = [
            'grade_item_created' => 'ResourceGradeItemCreated',
        ];
        //instancia la clase data_validator
        $this->validator = new data_validator();
    }

    /**
     * Retorna los datos acorde al evento que se requiera
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