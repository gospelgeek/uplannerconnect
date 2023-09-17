<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


require_once(__DIR__ . '/../repository/CourseNotesRepository.php');

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseExtractionData {

    //Atributos
    private $CourseNotesResource;
    private $typeEvent;

    //Constructor
    public function __construct() {
 
        //Instancia de la clase CourseNotesResource
        $this->CourseNotesRepository = new CourseNotesRepository();

        //Inicializar la variable typeEvent
        $this->typeEvent = [
            'user_graded' => 'ResourceUserGraded',
            'grade_item_updated' => 'ResourceGradeItemUpdated',
            'grade_deleted' => 'ResourceGradeDeleted',
            'grade_item_created' => 'ResourceGradeItemCreated',
        ];

    }

    /**
     * @package uPlannerConnect
     * @description Retorna los datos acorde al evento que se requiera
     * @return array
    */
    public function getResource(array $data) {
        $typeEvent = $this->typeEvent[$data['typeEvent']];
        return $this->$typeEvent($data);
    }


    /**
     * @package uPlannerConnect
     * @description Guarda los datos en la base de datos
     * @return void 
    */
    public function saveResource(array $data) {
        $this->CourseNotesRepository->saveDataBD($data);
    }


    /**
     *  @package uPlannerConnect
     *  @description Retorna los datos del evento user_graded
     *  @return array
    */
    private function ResourceUserGraded(array $data) {
        
       try {

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

      } catch (Exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }

    }


    /**
     * @package uPlannerConnect
     * @description Retorna los datos del evento grade_item_updated
     * @return array
    */
    private function ResourceGradeItemUpdated(array $data) {

        try {
            $event = $data['dataEvent'];
            $GradeItem = $event->get_grade_item();
            

            return [
                'get_data' => $GradeItem,
                'typeEvent' => 'grade_item_updated',
            ];


        } catch (Exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }

    }


    /**
     * @package uPlannerConnect
     * @description Retorna los datos del evento grade_deleted
     * @return array 
    */
    private function ResourceGradeDeleted(array $data) {

        try {
            $event = $data['dataEvent'];
            $getData = $event->get_data();
            $grade = $event->get_grade();
            $gradeRecordData = $grade->get_record_data();
            $gradeLoadItem = $grade->get_record_data();

            return [
                'get_data' => $getData,
                'get_grade' => $grade,
                'get_record_data' => $gradeRecordData,
                'get_load_grade_item' => $gradeLoadItem,
                'typeEvent' => 'grade_deleted',
            ];

        } catch (Exception $e) {

            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }

    }

    /**
     *  @package uPlannerConnect
     *  @description Retorna los datos del evento grade_item_created
     *  @return array
    */
    private function ResourceGradeItemCreated(array $data) {

        try {
            $event = $data['dataEvent'];
            $get_grade_item = $event->get_grade_item();

            return [
                'get_grade_item' => $get_grade_item,
                'typeEvent' => 'grade_item_created',
            ];

        } catch (Exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }

    }

}
