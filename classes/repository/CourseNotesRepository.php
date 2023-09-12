<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


require_once(__DIR__ . '/../resource/CourseNotesResource.php');

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseNotesRepository {

    private $CourseNotesResource;
    private $typeEvent;

    public function __construct() {

        //Instancia de la clase CourseNotesResource
        $this->CourseNotesResource = new CourseNotesResource();

        //Inicializar la variable typeEvent
        $this->typeEvent = [
            'user_graded' => 'ResourceUserGraded',
        ];

    }

    /**
     * 
    */
    public function getResource(array $data) {
        $typeEvent = $this->typeEvent[$data['typeEvent']];
        return $this->$typeEvent($data);
    }

    public function saveResource(array $data) {
        $this->CourseNotesResource->saveDataBD($data);
    }

    private function ResourceUserGraded(array $data) {

        $event = $data['dataEvent'];
        $getData = $event->get_data();
        $grade = $event->get_grade();
        $gradeRecordData = $grade->get_record_data();
        $gradeLoadItem = $grade->load_grade_item();

        
        return [
            'get_data' => $getData,
            'get_grade' => $grade,
            'get_record_data' => $gradeRecordData,
            'get_load_grade_item' => $gradeLoadItem,
            'typeEvent' => 'user_graded',
        ];

    }

}
