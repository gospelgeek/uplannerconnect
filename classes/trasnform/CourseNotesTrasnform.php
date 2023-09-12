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
class CourseNotesTrasnform {

    //Atributos
    private $typeTransform;

    //Constructor
    public function __construct() {

        //Inicializar la variable typeTransform
        $this->typeTransform = [
            'user_graded' => 'convertDataUserGrade',
        ];

    }

    /**
     * @package uPlannerConnect
     * @description Convierte la data acorde al evento que se requiera
     * @return array
    */
    public function converDataJsonUplanner(array $data) {
        $typeTransform = $this->typeTransform[$data['typeEvent']];
        return $this->$typeTransform($data);
    }
    

    /**
     * @package uPlannerConnect
     * @description Transforma la data del evento en el formato que requiere uPlanner
     * @return array
    */
    private function convertDataUserGrade(array $data) {

        //Traer la información
        $getData = $data['get_data'];
        $grade = $data['get_grade'];
        $gradeRecordData = $data['get_record_data'];
        $gradeLoadItem = $data['get_load_grade_item'];

        //sacar la información del evento
        return [
            'sectionId' => $grade->grade_item->courseid,
            'studentCode' => $grade->userid,
            'finalGrade' => ($getData['other'])['finalgrade'],
            'finalGradeMessage' => $grade->feedback,
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


}
