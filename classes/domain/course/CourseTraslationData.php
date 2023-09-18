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
            'user_graded' => 'convertDataUserGrade',
            'grade_item_updated' => 'convertDataGradeItemUpdated',
            'grade_deleted' => 'convertDataUserGrade',
            'grade_item_created' => 'convertDataItemCreated',
            'grade_item_deleted' => 'convertDataItemDeleted',
        ];

        $this->typeDefStructure = plugin_config::UPLANNER_GRADES;
        $this->validator = new DataValidator();
        
    }

    /**
     * @package uPlannerConnect
     * @description Convierte los datos acorde al evento que se requiera
     * @return array
    */
    public function converDataJsonUplanner(array $data) {
        //Traer la información
        $typeTransform = $this->typeTransform[$data['typeEvent']];
        //verificar si existe el método
        if (method_exists($this, $typeTransform)) {
            return $this->$typeTransform($data);
        }
        return [];
    }
    

    /**
     * @package uPlannerConnect
     * @description Transforma los datos del evento en el formato que requiere uPlanner
     * @return array
    */
    private function convertDataUserGrade(array $data) {

        //Traer la información
        $getData = $data['get_data'];
        $grade = $data['get_grade'];
        $gradeRecordData = $data['get_record_data'];
        $gradeLoadItem = $data['get_load_grade_item'];


        //return data traslate
        return $this->createCommonDataArray([
            'sectionId' => $this->validator->vericateMethodExist(['getData' => $grade->grade_item, 'property' => 'courseid']),
            'studentCode' => $this->validator->vericateMethodExist(['getData' => $grade, 'property' => 'userid']),
            'finalGrade' => isset(($getData['other'])['finalgrade']) ? ($getData['other'])['finalgrade'] : '',
            'finalGradePercentage' => isset($grade->grade_item->grademax, $grade->rawgrade) ? (100 / $grade->grade_item->grademax * $grade->rawgrade) : '',
            "evaluationGroupCode" => $this->validator->vericateMethodExist(['getData' => $gradeLoadItem, 'property' => 'categoryid']),
            "evaluationId" => $this->validator->vericateMethodExist(['getData' => $gradeLoadItem, 'property' => 'itemtype']),
            "value" => isset(($getData['other'])['finalgrade']) ? ($getData['other'])['finalgrade'] : '',
            "evaluationName" => $this->validator->vericateMethodExist(['getData' => $gradeLoadItem, 'property' => 'itemname']),
            "date" => $this->validator->vericateMethodExist(['getData' => $gradeLoadItem, 'property' => 'timecreated']),
            "lastModifiedDate" => $this->validator->vericateMethodExist(['getData' => $gradeLoadItem, 'property' => 'timemodified']),
        ]);
        

    }


    /**
     * @package uPlannerConnect
     * @description Transforma los datos del evento en el formato que requiere uPlanner
     * @return array 
    */
    private function convertDataGradeItemUpdated(array $data) {

        //Traer la información
        $getData = $data['get_data'];

        return $this->createCommonDataArray([
            'sectionId' => $this->validator->vericateMethodExist(['getData' => $getData, 'property' => 'courseid']),
            'evaluationGroupCode' => $this->validator->vericateMethodExist(['getData' => $getData, 'property' => 'categoryid']),
            'date' => $this->validator->vericateMethodExist(['getData' => $getData, 'property' => 'timecreated']),
            'lastModifiedDate' => $this->validator->vericateMethodExist(['getData' => $getData, 'property' => 'timemodified']),
            'action' => 'update'
        ]);
        

    }


    /**
     * @package uPlannerConnect
     * @description Transforma los datos del evento en el formato que requiere uPlanner
     * @return array 
    */
    private function convertDataItemCreated(array $data) {

        $get_grade_item = $data['get_grade_item'];

        //Retorna la data verificada en el formato de uplanner
        return $this->createCommonDataArray([
            'sectionId' => $this->validator->vericateMethodExist(['getData' => $get_grade_item, 'property' => 'courseid']),
            'evaluationGroupCode' => $this->validator->vericateMethodExist(['getData' => $get_grade_item, 'property' => 'courseid']),
            'evaluationId' => $this->validator->vericateMethodExist(['getData' => $get_grade_item, 'property' => 'courseid']),
            'evaluationName' => $this->validator->vericateMethodExist(['getData' => $get_grade_item, 'property' => 'itemname']),
            'date' => $this->validator->vericateMethodExist(['getData' => $get_grade_item, 'property' => 'timecreated']),
            'lastModifiedDate' =>$this->validator->vericateMethodExist(['getData' => $get_grade_item, 'property' => 'timemodified']),
            'action' => 'delete'
        ]);
        

    }


    /**
     * @package uPlannerConnect
     * @description Transforma los datos del evento en el formato que requiere uPlanner
     * @return array
    */
    private function convertDataItemDeleted(array $data) {

        $event = $data['gradeItems'];

        //Retorna la dataTraslate
        return $this->createCommonDataArray([
            'sectionId' => $this->validator->vericateMethodExist(['getData' => $event, 'property' => 'courseid']),
            'evaluationName' => $this->validator->vericateMethodExist(['getData' => $event, 'property' => 'itemname']),
        ]);  

    }


    /**
     *  @package uPlannerConnect
     *  @description Crea un array con la estructura que requiere uPlanner
    */
    private function createCommonDataArray(array $data) {
        
        $dataSend = $this->validator->verificateArrayKeyExist([ 
            'array_verification' => $this->typeDefStructure,
            'get_data' => $data 
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

}
