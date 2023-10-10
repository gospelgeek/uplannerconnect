<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author      Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\service;
use moodle_exception;

/**
 * Encargada de tener todas las validaciones
*/
class data_validator
{
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

    /**
      * Ejecuta la verificación acorde al tipo de verificación
      *
      * @param mixed $data El dato a validar.
      * @return bool 
    */
    public function executeVerification(array $data): bool
    {
        $result = false;
        try {
            // Verifica si el tipo de verificación es válido
            if (isset(self::TYPE_VERIFICATION[$data['type_verification']])) {
                // Obtiene el nombre del método
                $method = self::TYPE_VERIFICATION[$data['type_verification']];
                // Llama al método de validación y devuelve el resultado
                $result = $this->$method($data['value']);
            }
        }
        catch (moodle_exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $result;
    }

    /**
      * Validad si un array tiene las keys que se requieren
      *
      * @param mixed $data El dato a validar.
      * @return array
    */
    public function verifyArrayKeyExist(array $data): array
    {
        $arraySend = [];
        try {
            // Verifica si tiene la key o asigna un valor por defecto.
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
       }
       catch (moodle_exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
       }
        return $arraySend;
    }

    /**
     * Valida si una consulta tiene resultados
     * Pero solo un resultado
     * 
     * @param array $data
     * @return array
     */
    public function verifyQueryResult(array $data) : array
    {
        $arraySend = [
            'result' => '',
        ];
        try {
            if (!empty($data['data']) && 
                 is_object($data['data'])) {
                $arraySend = [
                    'result' => $data['data']
                ];
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $arraySend;
    }

    /**
      *  Validad si un objecto tiene un metodo
      *
      * @param mixed $data El dato a validar.
      * @return method or string
    */
    public function propertyExists(array $data): string
    {
        $propertySend = '';
        try {
            $getData = $data['getData'];
            $property = $data['property'];
            $propertySend = $getData->$property ?? '';
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
        return $propertySend;
    }

    /**
      *  Valida si un dato esta seteado 
      *
      * @param mixed $data El dato a validar.
      * @return data or null
    */
    public function isIsset($data)
    {
        return $data ?? null;
    }

    /**
      *  Valida si es un array o devielve null
      *
      * @param mixed $data El dato a validar.
      * @return data or null
    */
    public function isArrayData($data)
    {
        return is_array($data) ? $data : null;
    }

    /**
      * Valida si un dato es un objecto o devuelve null
      *
      * @param mixed $data El dato a validar.
      * @return data or null
    */
    public function isObjectData($data)
    {
        return is_object($data) ? $data : null;
    }

    /**
      *  Valida si en un array o detiene la ejecución
      *
      * @param mixed $data El dato a validar.
      * @return void
    */
    public function verifyArrayKeyExistOrDie($data) : void
    {
        if (!$this->isArray($data)) {
            error_log('El parametro no es un array');
            return;
        }
    }

    /**
      * Valida si una estructura de datos tiene las keys que se requieren
      *
      * @param mixed $data El dato a validar.
      * @return boolean
    */
    public function verificateKeyArrayBoolean($data) : bool
    {
        $boolean = true;
        try {
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
       } catch (moodle_exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
       }
        return $boolean;
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
     * @param $data
     * @return boolean
     */
    public function isFloat($data) : bool
    {
        return is_float($data);
    }

    /**
     *  Valida si un dato es un booleano
     *
     * @param $data
     * @return boolean
     */
    public function isBool($data): bool
    {
        return is_bool($data);
    }

    /**
     * Valida si un dato es un array
     *
     * @param $data
     * @return boolean
     */
    public function isArray($data): bool
    {
        return is_array($data);
    }

    /**
     *  Valida si un dato es un objecto
     *
     * @param $data
     * @return boolean
     */
    public function isObject($data) : bool
    {
        return is_object($data);
    }

    /**
     *  Valida si un dato es null
     *
     * @param $data
     * @return boolean
     */
    public function isNull($data) : bool
    {
        return is_null($data);
    }

    /**
     *  Valida si un dato es un numerico
     *
     * @param $data
     * @return boolean
     */
    public function isNumeric($data) : bool
    {
        return is_numeric($data);
    }
}