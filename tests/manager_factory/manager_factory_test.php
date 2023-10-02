<?php 
/**
 * 
 * prueba unitaria para verficar que se instancie la clase management_factory
 * y comportamiento con diferentes tipos de datos
 * 
 * @package  local_uplanconnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @group local_uplanconnect
 * 
*/


//Variables globales
use local_uplannerconnect\domain\management_factory;


/**
 * Test case para la clase management_factory 
*/
class manager_factory_test extends advanced_testcase {

    //Atributos
    private $management_factory;

    public function setUp(): void {
        parent::setUp();
        //Instanciar la clase management_factory antes de cada prueba
        $this->management_factory = new management_factory();
    }

    /**
     * @covers instantiatemanagement_factory
    */
    public function test_instantiatemanagement_factory() {

        //Resetear el entorno
        $this->resetAfterTest(true);
        
        //Verificar si existe el método
        $this->assertTrue(method_exists($this->management_factory, 'create'));


        //Verificar si se proporcionan datos no validos
        //no deberia instanciar la clase enum_etities
        $result = $this->management_factory->create([
            "dataEvent" => "dataEvent",
            "typeEvent" => "typeEvent",
            "dispatch" => "dispatch",
            "enum_etities" => "enum_etities"
        ]);

        $this->assertTrue(!(isset($result)));


        //Verificar si se proporcionan datos no validos
        //no deberia instanciar la clase enum_etities
        $result = $this->management_factory->create([
            "dataEvent" => [],
            "typeEvent" => [],
            "dispatch" => [],
            "enum_etities" => []
        ]);

        $this->assertTrue(!(isset($result)));


        //Verificar si se proporcionan datos no validos
        //no deberia instanciar la clase enum_etities
        $result = $this->management_factory->create([
            "dataEvent" => "aaaaaaaaaaaaaaaaaaa",
            "typeEvent" => true,
            "dispatch" => null,
            "enum_etities" => 1
        ]);

        $this->assertTrue(!(isset($result)));


        //verificar en caso de pasarle un array vacio
        //no deberia instanciar la clase enum_etities
        $result = $this->management_factory->create([]);

        $this->assertTrue(!(isset($result)));

       
    }


}
