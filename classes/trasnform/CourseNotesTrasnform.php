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

    public function __construct() {

    }


    public function converDataJsonUplanner(array $data) {

        // $gradeItem = $data['gradeItem'];
        // $grade = $data['grade'];

        // //sacar la información del evento
        // $dataSend = [
        //     'sectionId' => $gradeItem->get_courseid(),
        //     'studentCode' => $grade->get_userid(),
        //     'finalGrade' => $grade->get_finalgrade(),
        //     'finalGradeMessage' => $grade->get_feedback(),
        //     'finalGradePercentage' => $grade->get_percentage(),
        //     'evaluationGroups' => [
        //         [
        //             "evaluationGroupCode" => $gradeItem->get_itemmodule(),
        //             "average" => $gradeItem->get_finalgrade(),
        //             "grades" => [
        //                 [
        //                     "evaluationId" => $gradeItem->get_iteminstance(),
        //                     "value" => $gradeItem->get_finalgrade(),
        //                     "evaluationName" => $gradeItem->get_itemname(),
        //                     "date" => $gradeItem->get_timemodified(),
        //                     "isApproved" => $gradeItem->get_finalgrade() >= 3.0,
        //                 ]
        //             ]
        //         ]
        //     ],
        //     "lastModifiedDate" => $gradeItem->get_timemodified()
        // ];

        return [
            'dataSend' => '',
            'gradeItem' => '',
            'grade' => '',
        ];
    }

}
