<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\materials;

use moodle_exception;

class data_manager
{
    private $materialExtractionData;
    private $materialTranslationData;
    private $materialRepository;
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

        $this->materialExtractionData = new $typeManagerArray['materialExtractionData']();
        $this->materialTranslationData = new $typeManagerArray['materialTranslationData']();
        $this->materialRepository = new $typeManagerArray['materialRepository']();
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
            $dataRepository = $this->materialExtractionData->getResource($data);
            // Traducir los datos.
            $dataTrasnform = $this->materialTranslationData->converDataJsonUplanner($dataRepository);
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
        $this->materialRepository->saveDataBD($data);
    }
}