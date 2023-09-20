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
 *  @author Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @description Encargada de tener todas las validaciones
*/
class DataValidator {

    //Atributos
    CONST TYPE_VERIFICATION = [
        'string' => 'isString',
        'int' => 'isInt',
        'float' => 'isFloat',
        'bool' => 'isBool',
        'array' => 'isArray',
        'object' => 'isObject',
        'null' => 'isNull',
        'numeric' => 'isNumeric'
    ];

    //constructor
    public function __construct() {
    }


    /**
     *  @package uPlannerConnect
     *  @dec Ejecuta la verificación acorde al tipo de verificación
    */
    public function executeVerification(array $data) {
        try {
            // Verifica si el tipo de verificación es válido
            if (isset(self::TYPE_VERIFICATION[$data['type_verification']])) {
                // Obtiene el nombre del método
                $method = self::TYPE_VERIFICATION[$data['type_verification']];
                // Llama al método de validación y devuelve el resultado
                return $this->$method($data['value']);
            } else {
                // Tipo de verificación no válido
                return false;
            }
        }
        catch (Exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
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
                      
                    //verifica si el dato cumple con el tipo especificado
                    if ($this->executeVerification([
                        'type_verification' => $item['type'],
                        'value' => $data['get_data'][$item['name']]
                    ])) {
                        $arraySend[$item['name']] = $data['get_data'][$item['name']];
                    } else {
                        $arraySend[$item['name']] = '';
                    }               

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

    /**
     * @package uPlannerConnect
     * @dec Valida si un dato esta seteado 
    */
    public function isIsset($data) {
        return isset($data) ? $data : null;
    }

    public function isArrayData($data) {
        return is_array($data) ? $data : null;
    }

    /**
     * @package uPlannerConnect
     * @dec Valida si un dato es un string o detiene la ejecución 
    */
    public function isObjectData($data) {
        return is_object($data) ? $data : null;
    }


    /**
     * @package uPlannerConnect
     * @dec Valida si en un array o detiene la ejecución 
    */
    public function verificateArrayKeyExistOrDie($data) {
        if (!$this->isArray($data)) {
            error_log('El parametro no es un array');
            return;
        }
    }
    
    /**
     * @package uPlannerConnect
     * @dec Valida si una estructura de datos tiene las keys que se requieren
     * @return boolean 
    */
    public function verificateKeyArrayBoolean($data) {
        try {
            $boolean = true;
            //Verifica si tiene la key o asigna un valor por defecto
            foreach ($data['array_verification'] as $item) {
                if (array_key_exists($item['name'], $data['get_data'])) {
                      
                    //verifica si el dato cumple con el tipo especificado
                    if (!($this->executeVerification([
                        'type_verification' => $item['type'],
                        'value' => $data['get_data'][$item['name']]
                    ]))) {
                        $boolean = false;
                    }               

                } else {
                    $boolean = false;
                }
            }
        
            return $boolean;
       }
       catch (Exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
       }

    }


    /**
     * @package uPlannerConnect
     * @dec Valida si una key existe en un array
    */
    public function isArrayKeyExist(array $data) {
        return array_key_exists($data['key'], $data['array']);
    }

    /**
     *  @package uPlannerConnect
     *  @dec Validad si un dato es un string
    */
    public function isString($data) {
        return is_string($data);
    }


    /**
     * @package uPlannerConnect
     * @dec Valida si un dato es un entero
    */
    public function isInt($data) {
        return is_int($data);
    }

    /**
     * @package uPlannerConnect
     * @dec Valida si un dato es un float 
    */
    public function isFloat($data) {
        return is_float($data);
    }

    /** 
      * @package uPlannerConnect
      * @dec Valida si un dato es un booleano
    */
    public function isBool($data) {
        return is_bool($data);
    }

    /**
     * @package uPlannerConnect
     * @dec Valida si un dato es un array 
    */
    public function isArray($data) {
        return is_array($data);
    }

    /**
     *  @package uPlannerConnect
     *  @dec Valida si un dato es un objecto
    */
    public function isObject($data) {
        return is_object($data);
    }

    public function isNull($data) {
        return is_null($data);
    }

    /**
     *  @package uPlannerConnect
     *  @dec Valida si un dato es un numerico
    */
    public function isNumeric($data) {
        return is_numeric($data);
    }

}