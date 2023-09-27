<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


/**
 *  @package local_uplannerconnect
 *  @author  Cristian Machado <cristian.machado@correounivalle.edu.co>
 *  @author  Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @description Encargada de tener todas las validaciones
*/
class DataValidator {

    //Constantes
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
      * Ejecuta la verificación acorde al tipo de verificación
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
      * @return bool 
    */
    public function executeVerification(array $data) : bool {
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
      * Validad si un array tiene las keys que se requieren
      *
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
      * @return array
    */
    public function verifyArrayKeyExist(array $data) : array {

        try {
            //array a devolver
            $arraySend = [];
            
            //Verifica si tiene la key o asigna un valor por defecto
            foreach ($data['array_verification'] as $item) {
                if (array_key_exists($item['name'], $data['data'])) {
                      
                    //verifica si el dato cumple con el tipo especificado
                    if ($this->executeVerification([
                        'type_verification' => $item['type'],
                        'value' => $data['data'][$item['name']]
                    ])) {
                        $arraySend[$item['name']] = $data['data'][$item['name']];
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
      *  Validad si un objecto tiene un metodo
      *
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
      * @return method or string
    */
    public function propertyExists(array $data) {
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
      *  Valida si un dato esta seteado 
      *
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
      * @return data or null
    */
    public function isIsset($data)  {
        return isset($data) ? $data : null;
    }

    /**
      *  Valida si es un array o devielve null
      *
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
      * @return data or null
    */
    public function isArrayData($data) {
        return is_array($data) ? $data : null;
    }


    /**
      *  Valida si un dato es un objecto o devuelve null 
      *
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
      * @return data or null
    */
    public function isObjectData($data) {
        return is_object($data) ? $data : null;
    }


    /**
      *  Valida si en un array o detiene la ejecución  
      *
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
      * @return void
    */
    public function verifyArrayKeyExistOrDie($data) {
        if (!$this->isArray($data)) {
            error_log('El parametro no es un array');
            return;
        }
    }
    

    /**
      *  Valida si una estructura de datos tiene las keys que se requieren  
      *
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
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
      *  Valida si una key existe en un array
      *
      * @package uPlannerConnect
      * @param mixed $data El dato a validar.
      *
      * @return boolean
    */
    public function isArrayKeyExist(array $data) {
        return array_key_exists($data['key'], $data['array']);
    }

    /**
     *  Validad si un dato es un string
     * 
     *  @package uPlannerConnect
     *  
     *  @return boolean
    */
    public function isString($data) {
        return is_string($data);
    }


    /**
     * Valida si un dato es un entero
     * 
     * @package uPlannerConnect
     * 
     * @return boolean
    */
    public function isInt($data) {
        return is_int($data);
    }

    /**
     * Valida si un dato es un float 
     * 
     * @package uPlannerConnect
     * 
     * @return boolean
    */
    public function isFloat($data) {
        return is_float($data);
    }

    /** 
      *  Valida si un dato es un booleano
      * 
      * @package uPlannerConnect
      * 
      * @return boolean
    */
    public function isBool($data) {
        return is_bool($data);
    }

    /**
     * Valida si un dato es un array
     * 
     * @package uPlannerConnect
     * 
     * @return boolean
    */
    public function isArray($data) {
        return is_array($data);
    }

    /**
     *  Valida si un dato es un objecto
     *  @package uPlannerConnect
     *  
     *  @return boolean
    */
    public function isObject($data) {
        return is_object($data);
    }

    /**
     *  Valida si un dato es null
     * 
     *  @package uPlannerConnect
     *  
     *  @return boolean
    */
    public function isNull($data) {
        return is_null($data);
    }

    /**
     *  Valida si un dato es un numerico
     * 
     *  @package uPlannerConnect
     *  
     *  @return boolean
    */
    public function isNumeric($data) {
        return is_numeric($data);
    }

}