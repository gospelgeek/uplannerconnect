<?php
/**
 * @package  local_uplanconnect
 * @group local_uplanconnect
*/

// Incluir la clase a probar
use local_uplannerconnect\domain\EnumEtities;

/**
 * Test case para la clase EnumEntities
 * 
 * @package  local_uplanconnect
 * @author   Cristian Machado <cristian.machado@correounivalle.edu.co> 
*/
class enum_entities_test extends advanced_testcase {

    private $EnumEntities;

    public function setUp(): void {
        parent::setUp();
        // Instanciar la clase EnumEntities antes de cada prueba
        $this->EnumEntities = new EnumEtities();
    }

    /**
     *  @package uPlannerConnect
     *  @des Verificar que se instancie la entidad correcta y se llame al método process
    */
    public function test_successful_processing() {
        $data = [
            'EnumEntities' => 'course_notes',
        ];

        $this->expectOutputString('course_grades_service::proccess called');
        $this->EnumEntities->process($data);
    }

    /**
     *  @package uPlannerConnect
     *  @des Verificar que se registra un mensaje de error en el log
    */
    public function test_failed_processing_entity_not_exist() {
        $data = [
            'EnumEntities' => 'nonexistent_entity',
        ];

        $this->expectOutputString("La clase nonexistent_entity no existe o no tiene el método process.\n");
        $this->EnumEntities->process($data);
    }

    /**
     *  @package uPlannerConnect
     *  @des Verificar que se registra un mensaje de error en el log
    */
    public function test_failed_processing_method_not_exist() {
        $data = [
            'EnumEntities' => 'course_notes_invalid',
        ];

        $this->expectOutputString("La clase course_grades_serviceInvalid no tiene el método process.\n");
        $this->EnumEntities->process($data);
    }

    /**
     *  @package uPlannerConnect
     *  @des erificar que se pasa el arreglo de datos correctamente al método process
    */
    public function test_processing_with_parameters() {
        $data = [
            'EnumEntities' => 'course_notes',
            'param1' => 'value1',
            'param2' => 'value2',
        ];

        // V
        $this->expectOutputString('course_grades_service::proccess called with parameters: param1=value1, param2=value2');
        $this->EnumEntities->process($data);
    }

    /**
     *  @package uPlannerConnect
     *  @des Verificar que se registra un mensaje de error en el log 
    */
    public function test_failed_processing_no_enum_entities_key() {
        $data = [
            'param1' => 'value1',
        ];

        $this->expectOutputString("La clave 'EnumEntities' no está presente en el arreglo de datos.\n");
        $this->EnumEntities->process($data);
    }

    /**
     * @package uPlannerConnect
     * `@des test falla cuando se pasa un valor no valido en el parametro EnumEntities
    */
    public function test_failed_processing_invalid_enum_entity() {
        $data = [
            'EnumEntities' => 'invalid_entity',
        ];

        // Verificar que se registra un mensaje de error en el log
        $this->expectOutputString("La clase invalid_entity no existe o no tiene el método process.\n");
        $this->EnumEntities->process($data);
    }

    
    /**
     *  @package uPlannerConnect
     *  @desc test falla cuando se pasa un valor valido y parametros no validos
    */
    public function test_failed_processing_invalid_parameters() {
        $data = [
            'EnumEtities' => 'course_notes',
            'param1' => [],
            'param2' => null,
        ];

        // Verificar que se registra un mensaje de error en el log
        $this->expectOutputString("Excepción capturada: Argument 1 passed to course_grades_service::proccess() must be of the type string, array given\n");
        $this->EnumEntities->process($data);
    }

}
