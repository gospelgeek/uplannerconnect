<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


//Variables globales
require_once(__DIR__ . '/../../plugin_config/plugin_config.php');
require_once(__DIR__ . '/../../application/service/DataValidator.php');


/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseTraslationData {

    //Atributos
    private $typeTransform;
    private $typeDefStructure;
    private $validator;

    //Constructor
    public function __construct() {

        //Inicializar la variable typeTransform
        $this->typeTransform = [
            'user_graded' => 'convertDataGrade',
            'grade_item_updated' => 'convertDataGrade',
            'grade_deleted' => 'convertDataGrade',
            'grade_item_created' => 'convertDataGrade',
            'grade_item_deleted' => 'convertDataGrade',
        ];

        //Inicializar la variable typeDefStructure
        $this->typeDefStructure = plugin_config::UPLANNER_GRADES;
        //instancia la clase DataValidator
        $this->validator = new DataValidator();
        
    }

    /**
     * @package uPlannerConnect
     * @description Convierte los datos acorde al evento que se requiera
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
     * @package uPlannerConnect
     * @description Transforma los datos del evento en el formato que requiere uPlanner
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
     *  @package uPlannerConnect
     *  @description Crea un array con la estructura que requiere uPlanner
    */
    private function createCommonDataArray(array $data) : array {

        try {
            
            $dataSend = $this->validator->verifyArrayKeyExist([ 
                'array_verification' => $this->typeDefStructure,
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
           error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }

    }

}
