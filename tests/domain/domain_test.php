<?php
/**
 * 
 * prueba unitaria para verificar la lógica de dominio del plugin
 * 
 * @package  local_uplanconnect 
 * @author Isabela Rosero <isabela.rosero@correounivalle.edu.co>
 * @group local_uplanconnect
*/


use local_uplannerconnect\domain\course\CourseTraslationData;

/**
 *  Test case para la clase CourseTraslationData
 * 
 *  @package  local_uplanconnect
 *  @author Isabela Rosero <isabela.rosero@correounivalle.edu.co>
 *  
*/
class domain_test extends advanced_testcase{

    //atributos
    private $CourseTraslationData;

    public function setUp():void{
        parent::setUp();
         //Instanciar la clase CourseTraslationData antes de cada prueba
         $this->CourseTraslationData = new CourseTraslationData();
    }

    /**
     *  @package  local_uplanconnect
    */
    public function test_converDataJsonUplanner(){
        $data = [
            'typeEvent' => 'user_graded', 'data' => [],
        ];

        $result = $this->CourseTraslationData->converDataJsonUplanner($data);

        $this->assertIsArray($result);

        if (empty($data)){
            $this->assertEmpty($result);
        } else {
            foreach($result as $value) {
                if (is_array($value)){
                    foreach($value as $subItem){
                        if(is_array($result)){
                            $this->assertIsArray($subItem);
                        }else{
                            $this->assertSame('',$subItem);
                        }
                    }
                }else {
                    $this->assertSame('',$value);
                }
            }
        }
    }
}