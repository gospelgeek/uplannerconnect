<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\course;

use local_uplannerconnect\domain\course\course_translation_data;
use local_uplannerconnect\domain\course\course_extraction_data;
use local_uplannerconnect\application\repository\course_notes_repository;
use local_uplannerconnect\application\repository\course_evaluation_structure_repository;
use moodle_exception;

class data_manager
{
    private $courseExtractionData;
    private $courseTranslationData;
    private $courseNotesRepository;
    private $typeManager;

    /**
     *  Construct
     */
    public function __construct(string $type)
    {
        if (empty($type)) {
            error_log('No le llego el tipo de evento');
            return;
        }

        $this->typeManager = new type_manager($type);

        $this->courseExtractionData = new $this->typeManager['courseExtractionData']();
        $this->courseTranslationData = new $this->typeManager['courseTranslationData']();
        $this->courseNotesRepository = new $this->typeManager['courseNotesRepository']();
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
            $dataRepository = $this->courseExtractionData->getResource($data);
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