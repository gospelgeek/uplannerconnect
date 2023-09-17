/**
<?php
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseNotesTrasnform {

    //Atributos
    private $typeTransform;

    //Constructor
    public function __construct() {

        //Inicializar la variable typeTransform
        $this->typeTransform = [
            'user_graded' => 'convertDataUserGrade',
            'grade_item_updated' => 'convertDataGradeItemUpdated',
            'grade_deleted' => 'convertDataUserGrade',
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

        //Sacar la información del evento
        return [
            'sectionId' => $grade->grade_item->courseid,
            'studentCode' => $grade->userid,
            'finalGrade' => ($getData['other'])['finalgrade'],
            'finalGradeMessage' => '',
            'finalGradePercentage' => (100 / $grade->grade_item->grademax * $grade->rawgrade),
            'evaluationGroups' => [
                [
                    "evaluationGroupCode" => $gradeLoadItem->categoryid,
                    "average" => '',
                    "grades" => [
                        [
                            "evaluationId" => $gradeLoadItem->itemtype,
                            "value" => ($getData['other'])['finalgrade'],
                            "evaluationName" => $gradeLoadItem->itemname,
                            "date" => $gradeLoadItem->timecreated,
                            "isApproved" => '',
                        ]
                    ]
                ]
            ],
            "lastModifiedDate" => $gradeLoadItem->timemodified,
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

        //Sacar la información del evento
        return [
            'sectionId' => $getData->courseid,
            'studentCode' => '',
            'finalGrade' => '',
            'finalGradeMessage' => '',
            'finalGradePercentage' => '',
            'evaluationGroups' => [
                [
                    "evaluationGroupCode" => $getData->categoryid,
                    "average" => '',
                    "grades" => [
                        [
                            "evaluationId" => '',
                            "value" => '',
                            "evaluationName" => '',
                            "date" => $getData->timecreated,
                            "isApproved" => '',
                        ]
                    ]
                ]
            ],
            "lastModifiedDate" => $getData->timemodified,
            "action" => 'update'
        ];

    }


    /**
     * @package uPlannerConnect
     * @description Transforma los datos del evento en el formato que requiere uPlanner
     * @return array 
    */
    private function convertDataGradeDeleted(array $data) {

        //Traer la información
        $getData = $data['get_data'];

        //Sacar la información del evento
        return [
            'sectionId' => '',
            'studentCode' => '',
            'finalGrade' => '',
            'finalGradeMessage' => '',
            'finalGradePercentage' => '',
            'evaluationGroups' => [
                [
                    "evaluationGroupCode" => '',
                    "average" => '',
                    "grades" => [
                        [
                            "evaluationId" => '',
                            "value" => '',
                            "evaluationName" => '',
                            "date" => '',
                            "isApproved" => '',
                        ]
                    ]
                ]
            ],
            "lastModifiedDate" => '',
            "action" => 'delete'
        ];

    }


}
