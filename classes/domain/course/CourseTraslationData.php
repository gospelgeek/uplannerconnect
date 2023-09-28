<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\course;

use local_uplannerconnect\application\service\DataValidator;
use local_uplannerconnect\plugin_config\plugin_config;

/**
   * 
   * Instancia una entidad de acorde a la funcionalidad que se requiera
   * 
   * @package local_uplannerconnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   *  
*/
class CourseTraslationData {

    //Atributos
    private $typeTransform;
    private $validator;

    //Constructor
    public function __construct() {

        //Inicializar la variable typeTransform
        $this->typeTransform = [
            'user_graded' => 'convertDataGrade',
            'grade_item_updated' => 'convertDataGrade',
            'grade_deleted' => 'convertDataGrade',
            'grade_item_created' => 'convertDataEvaluation',
            'grade_item_deleted' => 'convertDataGrade',
        ];

        //instancia la clase DataValidator
        $this->validator = new DataValidator();
        
    }

    /**
     * Convierte los datos acorde al evento que se requiera
     * 
     * @package uPlannerConnect
     * @return array
    */
    public function converDataJsonUplanner(array $data) {

        try {

            //verifica el parámetro es un array

            //Traer la información
            $typeTransform = $this->typeTransform[$data['typeEvent']];
            //verificar si existe el método
            if (method_exists($this, $typeTransform)) {
                return $this->$typeTransform($data);
            }
            return [];

        }
        catch (Exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }

    }
    

    /**
     * Transforma los datos del evento en el formato que requiere uPlanner
     * 
     * @package local_uplannerconnect
     * @return array
    */
    private function convertDataGrade(array $data) : array {

        try {

            //Traer la información
            $getData = $data['data'];

            //return data traslate
            return $this->createCommonDataArray($getData);

       }
       catch (Exception $e) {
                error_log('Excepción capturada: ',  $e->getMessage(), "\n");
       }

    }


    /**
     * Transforma los datos del evento en el formato que requiere uPlanner
     * 
     * @package local_uplannerconnect
     * @todo Aqui es evidente que se puede optimizar el código
     * @return array
    */
    private function convertDataEvaluation(array $data) : array {

        try {

            //Traer la información
            $getData = $data['data'];

            //return data traslate
            return $this->createCommonDataEvaluation($getData);

       }
       catch (Exception $e) {
                error_log('Excepción capturada: ',  $e->getMessage(), "\n");
       }

    }


    /**
     *  Crea un array con la estructura que requiere uPlanner
     * 
     *  @package local_uplannerconnect
     *  @return array
    */
    private function createCommonDataArray(array $data) : array {

        try {
            
            $dataSend = $this->validator->verifyArrayKeyExist([ 
                'array_verification' => plugin_config::UPLANNER_GRADES,
                'data' => $data 
            ]);
            
            //Sacar la información del evento
            return [
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
      catch (Exception $e) {
           return [];
           error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }

    }


    /**
     * Crea un array con la estructura que requiere uPlanner
     *  
     * @package local_uplannerconnect
     * @return array
    */
    private function createCommonDataEvaluation(array $data) : array {

        try {

            //extraer la información
            $dataSend = $this->validator->verifyArrayKeyExist([ 
                'array_verification' => plugin_config::UPLANNER_EVALUATION_ESTRUTURE,
                'data' => $data 
            ]);

            //Estructura de la evaluación
            return [        
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
        catch (Exception $e) {
           return [];
           error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }       

    }

}
