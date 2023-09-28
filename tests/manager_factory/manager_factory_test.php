<?php 
/**
 * 
 * prueba unitaria para verficar que se instancie la clase ManagementFactory
 * y comportamiento con diferentes tipos de datos
 * 
 * @package  local_uplanconnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @group local_uplanconnect
 * 
*/


//Variables globales
use local_uplannerconnect\domain\ManagementFactory;


/**
 * Test case para la clase ManagementFactory 
*/
class manager_factory_test extends advanced_testcase {

    //Atributos
    private $ManagementFactory;

    public function setUp(): void {
        parent::setUp();
        //Instanciar la clase ManagementFactory antes de cada prueba
        $this->ManagementFactory = new ManagementFactory();
    }

    /**
     * @covers instantiateManagementFactory
    */
    public function test_instantiateManagementFactory() {

        //Resetear el entorno
        $this->resetAfterTest(true);
        
        //Verificar si existe el método
        $this->assertTrue(method_exists($this->ManagementFactory, 'create'));


        //Verificar si se proporcionan datos no validos
        //no deberia instanciar la clase EnumEtities
        $result = $this->ManagementFactory->create([
            "dataEvent" => "dataEvent",
            "typeEvent" => "typeEvent",
            "dispatch" => "dispatch",
            "EnumEtities" => "EnumEtities"
        ]);

        $this->assertTrue(!(isset($result)));


        //Verificar si se proporcionan datos no validos
        //no deberia instanciar la clase EnumEtities
        $result = $this->ManagementFactory->create([
            "dataEvent" => [],
            "typeEvent" => [],
            "dispatch" => [],
            "EnumEtities" => []
        ]);

        $this->assertTrue(!(isset($result)));


        //Verificar si se proporcionan datos no validos
        //no deberia instanciar la clase EnumEtities
        $result = $this->ManagementFactory->create([
            "dataEvent" => "aaaaaaaaaaaaaaaaaaa",
            "typeEvent" => true,
            "dispatch" => null,
            "EnumEtities" => 1
        ]);

        $this->assertTrue(!(isset($result)));


        //verificar en caso de pasarle un array vacio
        //no deberia instanciar la clase EnumEtities
        $result = $this->ManagementFactory->create([]);

        $this->assertTrue(!(isset($result)));

       
    }


}
