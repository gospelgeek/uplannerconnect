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
 * Class Validator
*/
class data_validator
{
    const TYPE_VERIFICATION = [
        'string' => 'isString',
        'int' => 'isInt',
        'float' => 'isFloat',
        'bool' => 'isBool',
        'array' => 'isArray',
        'object' => 'isObject',
        'null' => 'isNull',
        'numeric' => 'isNumeric'
    ];

    const DATA_NATIVE = [
        'string' => '',
        'int' => 0,
        'float' => 0.0,
        'bool' => false,
        'array' => [],
        'null' => null,
        'numeric' => 0
    ];

    /**
      * Execute the verification according to the type of validation.
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
          error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $result;
    }

    /**
      * Validate if an array has the required keys.
      *
      * @param mixed $data.
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
                        $arraySend[$item['name']] = self::DATA_NATIVE[$item['type']] ?? '';
                    }
                } else {
                    $arraySend[$item['name']] = self::DATA_NATIVE[$item['type']] ?? '';
                }
            }
       }
       catch (moodle_exception $e) {
          error_log('Excepción capturada: '. $e->getMessage(). "\n");
       }
        return $arraySend;
    }

    /**
     * Validates if a query has results, but only one result.
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
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $arraySend;
    }

    /**
      *  Validate if an object has a method
      *
      * @param mixed $data.
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
      * Validate if a data is set. 
      *
      * @param mixed $data El dato a validar.
      * @return data or null
    */
    public function isIsset($data)
    {
        return $data ?? null;
    }

    /**
      * Validate if a data is an array or return null.
      *
      * @param mixed $data
      * @return data or null
    */
    public function isArrayData($data)
    {
        return is_array($data) ? $data : null;
    }

    /**
      * Validate if a data is an object or return null.
      *
      * @param mixed $data
      * @return data or null
    */
    public function isObjectData($data)
    {
        return is_object($data) ? $data : null;
    }

    /**
      *  Validates if an array exists or halts execution.
      *
      * @param mixed $data.
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
      * Validate if a data structure has the required keys.
      *
      * @param mixed $data
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
          error_log('Excepción capturada: '. $e->getMessage(). "\n");
       }

        return $boolean;
    }
    
    /**
      *  Validate if a key exists in an array.
      *
      * @package uPlannerConnect
      * @param mixed $data.
      *
      * @return boolean
    */
    public function isArrayKeyExist(array $data) {
        return array_key_exists($data['key'], $data['array']);
    }

    /**
     *  Validate if a data is an string
     * 
     *  @package uPlannerConnect
     *  
     *  @return boolean
    */
    public function isString($data) {
        return is_string($data);
    }
    
    /**
     * Validate if a data is an integer.
     * 
     * @package uPlannerConnect
     * 
     * @return boolean
    */
    public function isInt($data) {
        return is_int($data);
    }

    /**
     * Validate if a data is a float.
     *
     * @param $data
     * @return boolean
     */
    public function isFloat($data) : bool
    {
        return is_float($data);
    }

    /**
     *  Validate if a data is a boolean.
     *
     * @param $data
     * @return boolean
     */
    public function isBool($data): bool
    {
        return is_bool($data);
    }

    /**
     * Validate if a data is an array.
     *
     * @param $data
     * @return boolean
     */
    public function isArray($data): bool
    {
        return is_array($data);
    }

    /**
     *  Validate if a data is an object.
     *
     * @param $data
     * @return boolean
     */
    public function isObject($data) : bool
    {
        return is_object($data);
    }

    /**
     * Validate if a data is null.
     *
     * @param $data
     * @return boolean
     */
    public function isNull($data) : bool
    {
        return is_null($data);
    }

    /**
     *  Output if a data is numeric.
     *
     * @param $data
     * @return boolean
     */
    public function isNumeric($data) : bool
    {
        return is_numeric($data);
    }

    /**
     * Validate if arraya have all keys
     * 
     * @return bool 
    */
    public function validateKeysArrays(array $data) : bool
    {
        $result = false;
        if (!empty($data))  {
            $keys = $data['keys'];
            $array =  $data['data'];
            $result = true;
            foreach ($keys as $key) {
                if (!array_key_exists($key, $array)) {
                    $result = false;
                }
            }
        }

        return $result;
    }
}