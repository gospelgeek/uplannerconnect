<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseTraslationData {

    //Atributos
    private $typeTransform;

    //Constructor
    public function __construct() {

        //Inicializar la variable typeTransform
        $this->typeTransform = [
            'user_graded' => 'convertDataUserGrade',
            'grade_item_updated' => 'convertDataGradeItemUpdated',
            'grade_deleted' => 'convertDataUserGrade',
            'grade_item_created' => 'convertDataItemCreated',
        ];

    }

    /**
     * @package uPlannerConnect
     * @description Convierte los datos acorde al evento que se requiera
     * @return array
    */
    public function converDataJsonUplanner(array $data) {
        $typeTransform = $this->typeTransform[$data['typeEvent']];
        return $this->$typeTransform($data);
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

        // Sacar la información del evento
        return [
            'sectionId' => isset($grade->grade_item->courseid) ? $grade->grade_item->courseid : '',
            'studentCode' => isset($grade->userid) ? $grade->userid : '',
            'finalGrade' => isset(($getData['other'])['finalgrade']) ? ($getData['other'])['finalgrade'] : '',
            'finalGradeMessage' => '',
            'finalGradePercentage' => isset($grade->grade_item->grademax, $grade->rawgrade) ? (100 / $grade->grade_item->grademax * $grade->rawgrade) : '',
            'evaluationGroups' => [
                [
                    "evaluationGroupCode" => isset($gradeLoadItem->categoryid) ? $gradeLoadItem->categoryid : '',
                    "average" => '',
                    "grades" => [
                        [
                            "evaluationId" => isset($gradeLoadItem->itemtype) ? $gradeLoadItem->itemtype : '',
                            "value" => isset(($getData['other'])['finalgrade']) ? ($getData['other'])['finalgrade'] : '',
                            "evaluationName" => isset($gradeLoadItem->itemname) ? $gradeLoadItem->itemname : '',
                            "date" => isset($gradeLoadItem->timecreated) ? $gradeLoadItem->timecreated : '',
                            "isApproved" => '',
                        ]
                    ]
                ]
            ],
            "lastModifiedDate" => isset($gradeLoadItem->timemodified) ? $gradeLoadItem->timemodified : '',
            "action" => 'create'
        ];

    }


    /**
     * @package uPlannerConnect
     * @description Transforma los datos del evento en el formato que requiere uPlanner
     * @return array 
    */
    private function convertDataGradeItemUpdated(array $data) {


        //Traer la información
        $getData = $data['get_data'];

        // Sacar la información del evento
        return [
            'sectionId' => isset($getData->courseid) ? $getData->courseid : '',
            'studentCode' => '',
            'finalGrade' => '',
            'finalGradeMessage' => '',
            'finalGradePercentage' => '',
            'evaluationGroups' => [
                [
                    "evaluationGroupCode" => isset($getData->categoryid) ? $getData->categoryid : '',
                    "average" => '',
                    "grades" => [
                        [
                            "evaluationId" => '',
                            "value" => '',
                            "evaluationName" => '',
                            "date" => isset($getData->timecreated) ? $getData->timecreated : '',
                            "isApproved" => '',
                        ]
                    ]
                ]
            ],
            "lastModifiedDate" => isset($getData->timemodified) ? $getData->timemodified : '',
            "action" => 'update'
        ];

    }


    /**
     * @package uPlannerConnect
     * @description Transforma los datos del evento en el formato que requiere uPlanner
     * @return array 
    */
    private function convertDataItemCreated(array $data) {

        $get_grade_item = $data['get_grade_item'];
        
        // Sacar la información del evento
        return [
            'sectionId' => isset($get_grade_item->courseid) ? $get_grade_item->courseid : '',
            'studentCode' => '',
            'finalGrade' => '',
            'finalGradeMessage' => '',
            'finalGradePercentage' => '',
            'evaluationGroups' => [
                [
                    "evaluationGroupCode" => isset($get_grade_item->courseid) ? $get_grade_item->courseid : '',
                    "average" => '',
                    "grades" => [
                        [
                            "evaluationId" => isset($get_grade_item->courseid) ? $get_grade_item->courseid : '',
                            "value" => '',
                            "evaluationName" => isset($get_grade_item->itemname) ? $get_grade_item->itemname : '',
                            "date" => isset($get_grade_item->timecreated) ? $get_grade_item->timecreated : '',
                            "isApproved" => '',
                        ]
                    ]
                ]
            ],
            "lastModifiedDate" => isset($get_grade_item->timemodified) ? $get_grade_item->timemodified : '',
            "action" => 'delete'
        ];

    }


}
