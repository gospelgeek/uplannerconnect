<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\announcements;

use moodle_exception;

class data_manager
{
    private $announcementsExtractionData;
    private $announcementsTranslationData;
    private $announcementsRepository;
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
        $typeManagerArray = $this->typeManager->getTypeManager($type);

        $this->announcementsExtractionData = new $typeManagerArray['announcementsExtractionData']();
        $this->announcementsTranslationData = new $typeManagerArray['announcementsTranslationData']();
        $this->announcementsRepository = new $typeManagerArray['announcementsRepository']();
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
            $dataRepository = $this->announcementsExtractionData->getResource($data);
            // Traducir los datos.
            $dataTrasnform = $this->announcementsTranslationData->converDataJsonUplanner($dataRepository);
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
        $this->announcementsRepository->saveDataBD($data);
    }
}