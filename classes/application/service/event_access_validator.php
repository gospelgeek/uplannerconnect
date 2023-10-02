<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\service;

use local_uplannerconnect\application\repository\course_data_repository;
use moodle_exception;

/**
 *  Validaciones de los eventos
*/
class event_access_validator
{
    const EVENT_DATA = 'dataEvent';
    const EVENT_KEY = 'key';
    const EVENT_METHOD_NAME = 'methodName';
    const DATA_VERIFY_EVENT = 'typeEvent';
    // Facultade a evaluar
    const FACULTY_ACTIVE = '4';
    private $courseDataRepository;

    public function __construct() {
        $this->courseDataRepository = new course_data_repository();
    }

    /**
     *  Valida si no repite multiples veces el mismo evento 
     *  con el mismo dato
     * 
     *  @param data $data
     *  @return bool
    */
    public function validateTypeEvent(array $data) : bool
    {
        $boolean = false;
        try {
            if (empty($data) ||
                !isset($data[self::EVENT_DATA]) ||
                !isset($data[self::EVENT_METHOD_NAME]) ||
                !isset($data[self::EVENT_KEY]) ||
                !isset($data[self::DATA_VERIFY_EVENT])
            ) { return false; }

            // Obtener el nombre del método.
            $methodName = $data[self::EVENT_METHOD_NAME];
            // Obtener la data del evento.
            $dataEvent = ($data[self::EVENT_DATA])->$methodName();
            // Obtener la llave del evento.
            $eventExecute = $dataEvent[$data[self::EVENT_KEY]];
            // valida.
            $boolean = ($eventExecute === $data[self::DATA_VERIFY_EVENT]);
        }
        catch (moodle_exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $boolean;
    }

    /**
     *  Valida si la facultad puede usar el evento
     *
    */
    public function validateAccessByFaculty($courseid) : bool
    {
        $result = false;
        try {
            if (!empty($courseid)) {
                //Obtener el shortname del curso
                $shortname = $this->courseDataRepository->getCourseShortname($courseid);
                //verifica si tiene algun dato en la posición 4
                if (isset($shortname[3])) {
                    $result = ($shortname[3] === self::FACULTY_ACTIVE);
                }
            }
        }
        catch (moodle_exception $e) {
          error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $result;
    }
}