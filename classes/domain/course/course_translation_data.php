<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\course;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\plugin_config\estruture_types;
use moodle_exception;

/**
   * Instantiate an entity according to the functionality required.
*/
class course_translation_data
{
    private $typeTransform;
    private $validator;

    public function __construct()
    {
        $this->typeTransform = [
            'user_graded' => 'createCommonDataArray',
            'grade_item_updated' => 'createCommonDataArray',
            'grade_deleted' => 'createCommonDataArray',
            'grade_item_deleted' => 'createCommonDataArray',
            'grade_item_created' => 'createCommonDataEvaluation'
        ];
        $this->validator = new data_validator();
    }

    /**
     * Convert the data according to the required event.
     *
     * @param array $data
     * @return array
     */
    public function converDataJsonUplanner(array $data): array
    {
        $arraySend = [];
        try {
            if (array_key_exists(
                  $data['typeEvent'],
                  $this->typeTransform
            )) {
                //Traer la información
                $typeTransform = $this->typeTransform[$data['typeEvent']];
                //verificar si existe el método
                if (method_exists($this, $typeTransform) && $data['data'] !== []) {
                    $arraySend = $this->$typeTransform($data['data']);
                }
            }
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: '.  $e->getMessage(). "\n");
        }
        return $arraySend;
    }

    /**
     * Create an array with the structure required by uPlanner
     *
     * @param array $data
     * @return array
     */
    public function createCommonDataEvaluation(array $data) : array
    {
        $arraySend = [];
        try {
            $dataSend = $this->validator->verifyArrayKeyExist([
                'array_verification' => estruture_types::UPLANNER_EVALUATION_ESTRUTURE,
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
            "action" => $dataSend['action'],
            "date" => $dataSend['date'],
            "transactionId" => $dataSend['transactionId'],
            "courseid" => $dataSend['courseid']
            ];
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: 888 ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

    /**
     * Create an array with the structure required by uPlanner.
     *
     * @param array $data
     * @return array
     */
    private function createCommonDataArray(array $data) : array
    {
        $arraySend = [];
        try {
            $dataSend = $this->validator->verifyArrayKeyExist([
                'array_verification' => estruture_types::UPLANNER_GRADES,
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
                "action" =>  $dataSend['action'],
                "transactionId" => $dataSend['transactionId']
            ];
      }
      catch (moodle_exception $e) {
           return [];
           error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
      return $arraySend;
    }
}