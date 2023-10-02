<?php
/**
 * @package     local_uplannerconnect
 * @author      cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @author      daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\course;

use local_uplannerconnect\domain\course\course_translation_data;
use local_uplannerconnect\domain\course\course_extraction_data;
use local_uplannerconnect\application\repository\course_notes_repository;
use moodle_exception;

/**
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class course_grades_service
{
    private $courseTranslationData;
    private $course_extraction_data;
    private $courseNotesRepository;

    public function __construct() {
        $this->courseTranslationData = new course_translation_data();
        $this->course_extraction_data = new course_extraction_data();
        $this->courseNotesRepository = new course_notes_repository();
    }

    /**
     * Encargada de Extraer los datos
     * Trastransformar los datos
     * Guardar los datos
     *
     * @param array $data
     * @return void
    */
    public function process(array $data) : void
    {
        try {
            // Extraer la informacion.
            $dataRepository = $this->course_extraction_data->getResource($data);
            // Traducir los datos.
            $dataTrasnform = $this->courseTranslationData->converDataJsonUplanner($dataRepository);
            // Enviar los datos a uPlanner.
            $this->saveResource($dataTrasnform);
            error_log('UplannerConnect: Successful event registration -'. $data['typeEvent']. "\n");
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Guarda los datos en la base de datos
     *
     * @param array $data
     * @return void
     */
    private function saveResource(array $data) : void
    {
        if (empty($data)) {
            error_log('No le llego la información del evento');
            return;
        }
        $this->courseNotesRepository->saveDataBD($data);
    }
}