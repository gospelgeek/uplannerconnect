<?php
/**
 * @package     local_uplannerconnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\service;


use local_uplannerconnect\application\repository\course_data_repository;

/**
 *  
 *  Validaciones de los eventos
 *
*/
class EventAccesValidator {

    //Constantes
    const EVENT_DATA = 'dataEvent';
    const EVENT_KEY = 'key';
    const EVENT_METHOD_NAME = 'methodName';
    const DATA_VERIFY_EVENT = 'typeEvent';

    // Facultade a evaluar
    const FACULTY_ACTIVE = '4';

    //Atributos
    private $courseDataRepository;

    //constructor
    public function __construct() {
        //Instanciar la clase course_data_repository
        $this->courseDataRepository = new course_data_repository();
    }


    /**
     *  Valida si no repite multiples veces el mismo evento 
     *  con el mismo dato
     * 
     *  @param data $data
     * 
     *  @return bool
    */
    public function validateTypeEvent(array $data) : bool {
        try {

            // Valida si la información llega vacía
            if (empty($data) || 
                !isset($data[self::EVENT_DATA]) ||
                !isset($data[self::EVENT_METHOD_NAME]) ||
                !isset($data[self::EVENT_KEY]) ||
                !isset($data[self::DATA_VERIFY_EVENT])
            ) { return false; }

            // Obtener el nombre del método
            $methodName = $data[self::EVENT_METHOD_NAME];
            // Obtener la data del evento
            $dataEvent = ($data[self::EVENT_DATA])->$methodName(); 
            // Obtener la llave del evento
            $eventExecute = $dataEvent[$data[self::EVENT_KEY]];


            return ($eventExecute === $data[self::DATA_VERIFY_EVENT]);
            
        }
        catch (\Exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
    }

    /**
     *  Valida si la facultad puede usar el evento
     * 
     *  @param data $data
     * 
     *  @return bool
    */
    public function validateAccessByFaculty($courseid) : bool {

        //Inicia por defecto
        $result = false;

        try {
            
            //valida si el id del curso llega vacío
            if (empty($courseid)) { return $result; }
            //Obtener el shortname del curso  
            $shortname = $this->courseDataRepository->getCourseShortname($courseid);
            //verifica si tiene algun dato en la posición 4
            if (!isset($shortname[3])) { return $result; }
          
            return ($shortname[3] === self::FACULTY_ACTIVE);

        }
        catch (\Exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $result;
    }
}