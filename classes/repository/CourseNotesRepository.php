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

    public function __construct() {

        //Instancia de la clase CourseNotesResource
        $this->CourseNotesResource = new CourseNotesResource();

    }


    public function getResource(array $data) {
       
        $event = $data['dataEvent'];
        // $grade = $event->get_grade();
        //sprint_r($grade . "\n" . $grade->get_grade_item() . "a");
        //$gradeItem = $grade->get_grade_item();  
        
        return [
            'grade' => '',
            'gradeItem' => '',
        ];
    }

    public function saveResource(array $data) {
        $this->CourseNotesResource->saveDataBD($data);
    }

}
