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
class course_translation_data
{
    private $typeTransform;
    private $validator;

    public function __construct() {
        $this->typeTransform = [
            'user_graded' => 'convertDataGrade',
            'grade_item_updated' => 'convertDataGrade',
            'grade_deleted' => 'convertDataGrade',
            'grade_item_deleted' => 'convertDataGrade',
            'grade_item_created' => 'convertDataEvaluation'
        ];
        $this->validator = new data_validator();
    }

    /**
     * Convierte los datos acorde al evento que se requiera
     *
     * @param array $data
     * @return array
     */
    public function converDataJsonUplanner(array $data): array
    {
        $arraySend = [];
        try {
            //Traer la información
            $typeTransform = $this->typeTransform[$data['typeEvent']];
            //verificar si existe el método
            if (method_exists($this, $typeTransform)) {
                $arraySend = $this->$typeTransform($data);
            }
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

    /**
     * Transforma los datos del evento en el formato que requiere uPlanner
     *
     * @param array $data
     * @return array
     */
    private function convertDataGrade(array $data) : array
    {
        $arraySend = [];
        try {
            //Traer la información
            $getData = $data['data'];
            //return data traslate
            $arraySend = $this->createCommonDataArray($getData);
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

        /**
     * Transforma los datos del evento en el formato que requiere uPlanner
     *
     * @param array $data
     * @return array
     * @todo Aqui es evidente que se puede optimizar el código
     */
    private function convertDataEvaluation(array $data) : array
    {
        $arraySend = [];
        try {
            //Traer la información
            $getData = $data['data'];
            //return data traslate
            $arraySend = $this->createCommonDataEvaluation($getData);
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

    /**
     * Crea un array con la estructura que requiere uPlanner
     *
     * @param array $data
     * @return array
     */
    private function createCommonDataEvaluation(array $data) : array
    {
        $arraySend = [];
        try {
            $dataSend = $this->validator->verifyArrayKeyExist([
                'array_verification' => plugin_config::UPLANNER_EVALUATION_ESTRUTURE,
                'data' => $data
            ]);
            
            //Estructura de la evaluación
            $arraySend = [
            "sectionId" => $dataSend['sectionId'],
            "evaluationGroups" => [
                [
                    "evaluationGroupCode" => $dataSend['evaluationGroupCode'],
                    "evaluationGroupName" => $dataSend['evaluationGroupName'],
                    "evaluations" => [
                        [
                            "evaluationId" => $dataSend['evaluationId'],
                            "evaluationName" => $dataSend['evaluationName'],
                            "weight" => $dataSend['weight']
                        ]
                    ]
                ]
            ],
            "action" => $dataSend['action']
            ];
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

    /**
     *  Crea un array con la estructura que requiere uPlanner
     *
     * @param array $data
     * @return array
     */
    private function createCommonDataArray(array $data) : array
    {
        $arraySend = [];
        try {
            $dataSend = $this->validator->verifyArrayKeyExist([ 
                'array_verification' => plugin_config::UPLANNER_GRADES,
                'data' => $data 
            ]);
            
            //Sacar la información del evento
            $arraySend = [
                'sectionId' => $dataSend['sectionId'],
                'studentCode' => $dataSend['studentCode'],
                'finalGrade' =>  $dataSend['finalGrade'],
                'finalGradeMessage' => $dataSend['finalGradeMessage'],
                'finalGradePercentage' => $dataSend['finalGradePercentage'],
                'evaluationGroups' => [
                    [
                        "evaluationGroupCode" => $dataSend['evaluationGroupCode'],
                        "average" => $dataSend['average'],
                        "grades" => [
                            [
                                "evaluationId" => $dataSend['evaluationId'],
                                "value" => $dataSend['value'],
                                "evaluationName" => $dataSend['evaluationName'],
                                "date" => $dataSend['date'],
                                "isApproved" => $dataSend['isApproved'],
                            ]
                        ]
                    ]
                ],
                "lastModifiedDate" => $dataSend['lastModifiedDate'],
                "action" =>  $dataSend['action']
            ];
      }
      catch (moodle_exception $e) {
           return [];
           error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }
      return $arraySend;
    }
}