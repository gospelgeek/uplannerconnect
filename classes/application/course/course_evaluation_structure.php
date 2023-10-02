<?php
/**
 * @package  local_uplannerconnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co> 
 */

namespace local_uplannerconnect\application\course;

use local_uplannerconnect\domain\course\translation_evaluation_structure;
use local_uplannerconnect\domain\course\extraction_course_evaluation_structure;
use local_uplannerconnect\application\repository\course_evaluation_structure_repository;
use moodle_exception;

/**
 *  Encargada orquestar las estructuras de los cursos
 */
class course_evaluation_structure
{
    private $courseTranslationData;
    private $course_extraction_data;
    private $courseRepository;

    // Constructor.
    public function __construct() {
        $this->courseTranslationData = new translation_evaluation_structure();
        $this->course_extraction_data = new extraction_course_evaluation_structure();
        $this->courseRepository = new course_evaluation_structure_repository();
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
        $this->courseRepository->saveDataBD($data);
    }
}