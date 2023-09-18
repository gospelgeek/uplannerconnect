<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


//Variables globales
//require_once(__DIR__ . '/../../plugin_config/plugin_config.php');

/**
 *  @package  uPlannerConnect
 *  @author Cristian Machado <cristian.machado@correounivalle.edu.co>
  * @description Encargada de tener todas las validaciones
*/
class DataValidator {

    //Atributos
    private $typeDefStructure;

    //constructor
    public function __construct() {
    }


    /**
     *  @package uPlannerConnect
     *  @dec Validad si un array tiene las keys que se requieren
    */
    public function verificateArrayKeyExist(array $data) {

        try {
            //array a devolver
            $arraySend = [];
            
            //Verifica si tiene la key o asigna un valor por defecto
            foreach ($data['array_verification'] as $item) {
                if (array_key_exists($item['name'], $data['get_data'])) {
                    $arraySend[$item['name']] = $data['get_data'][$item['name']];
                } else {
                    $arraySend[$item['name']] = '';
                }
            }
        
            return $arraySend;
       }
       catch (Exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
       }

    }


    /**
     *  @package uPlannerConnect
     *  @dec Validad si un objecto tiene un metodo
    */
    public function vericateMethodExist(array $data) {

        try {
            $getData = $data['getData'];
            $property = $data['property'];
            return isset($getData->$property) ? $getData->$property : '';
        }
        catch (Exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }

    }


}