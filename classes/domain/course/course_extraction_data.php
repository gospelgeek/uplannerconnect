<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\course;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\plugin_config\plugin_config;
use local_uplannerconnect\domain\course\usecases\course_utils;
use moodle_exception;

/**
   * Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class course_extraction_data
{
    private $typeEvent;
    private $validator;
    private $courseUtils;
    
    public function __construct()
    {
        $this->typeEvent = [
            'user_graded' => 'resourceUserGraded',
            'grade_deleted' => 'resourceUserGraded',
            'grade_item_created' => 'resourceGradeItemCreated'
        ];
        $this->validator = new data_validator();
        $this->courseUtils = new course_utils();
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
    private function resourceUserGraded(array $data) : array
    {
        return $this->send_data_uplanner([
            'data' => $this->courseUtils->resourceUserGraded($data),
            'typeEvent' => $data['typeEvent'],
        ]);
    }

    /**
     * Retorna los datos del evento grade_item_created
     *
     * @param array $data
     * @return array
     */
    private function resourceGradeItemCreated(array $data) : array
    {
        return $this->send_data_uplanner([
            'data' => $this->courseUtils->resourceGradeItemCreated($data),
            'typeEvent' => $data['typeEvent'],
        ]);
    }

    /**
     * Formato de datos para enviar a uPlanner
     * 
     * @param array $data
     * @return array
     */
    private function send_data_uplanner(array $data) : array
    {
        $arraySend = [
            'data' => [],
            'typeEvent' => '',
        ]; 
        try {
            if (!empty($data)) {
                if (is_array($data['data'])) {               
                    $arraySend = [
                        'data' => $data['data'],
                        'typeEvent' => $data['typeEvent'],
                    ];
                }
            }
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $arraySend;
    }
}