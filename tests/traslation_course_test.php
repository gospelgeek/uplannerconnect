<?php 
/**
 * @package  local_uplanconnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @des prueba unitaria para verficar que se instancie la clase ManagementFactory
 *      and comportamiento con diferentes tipos de datos
 * @see 
 * @group local_uplanconnect
*/

//Variables globales
require_once(__DIR__ . '/../classes/domain/course/CourseNotesService.php');

class traslation_course_test   extends advanced_testcase {

    //atributos
    private $CourseNotesService;


    public function setUp(): void {
        parent::setUp();
        //Instanciar la clase CourseNotesService antes de cada prueba
        $this->CourseNotesService = new CourseNotesService();
    }
 
    /**
     *  @package  local_uplanconnect
    */
    public function test_converDataJsonUplannerWithUnknownEventType() {
        $data = [
            'typeEvent' => 'unknown_event_type',
        ];
    
        $result = $this->ManagementFactory->converDataJsonUplanner($data);
    
        $this->assertEquals([], $result);
    }

    /**
     * @package  local_uplanconnect 
    */
    public function test_converDataJsonUplannerWithValidEventType() {
        $data = [
            'typeEvent' => 'user_graded', o
        ];
    
        $result = $this->ManagementFactory->converDataJsonUplanner($data);
    
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        
    }


    /**
     * @package  local_uplanconnect 
    */
    public function test_convertDataGradeWithValidData() {
        $data = [
            'sectionId' => '',
            'studentCode' => '',
            'finalGrade' => '',
            'finalGradePercentage' => '',
            'evaluationGroupCode' => '',
            'evaluationId' => '',
            'value' => '',
            'evaluationName' => '',
            'date' => '',
            'lastModifiedDate' => '',
            'action' => '',
        ];
    
        $result = $this->ManagementFactory->convertDataGrade($data);
    
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        // Puedes agregar más aserciones para verificar los datos generados
    }

    
    /**
     * @package  local_uplanconnect 
    */
    public function test_createCommonDataArrayWithValidData() {
        $data = [
            // Datos válidos necesarios para la prueba
        ];
    
        $result = $this->ManagementFactory->createCommonDataArray($data);
    
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        // Puedes agregar más aserciones para verificar los datos generados
    }
    

}