<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\general;

use moodle_exception;

class data_manager
{
    private $extractionData;
    private $translationData;
    private $repository;

    /**
     * Extract the data
     * Transform the data
     * Save the data
     *
     * @param array $data
     * @return void
    */
    public function process(array $params) : void
    {
        try {
            $this->_initClass($params);
            $data = $params['data'];
            // Extraer la informacion.
            $dataRepository = $this->extractionData->getResource($data);
            // Traducir los datos.
            $dataTrasnform = $this->translationData->converDataJsonUplanner($dataRepository);
            if ($dataTrasnform !== []) {
              // Enviar los datos a uPlanner.
              $this->saveResource($dataTrasnform);
              error_log('UplannerConnect: Successful event registration -'. $data['typeEvent']. "\n");
            }
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Init class
     */
    private function _initClass(array $type) : void
    {
        if (empty($type)) {
            error_log('No have type event');
            return;
        }
      
        $this->extractionData = new $type['extractionData']();
        $this->translationData = new $type['translationData']();
        $this->repository = new $type['repository']();
    }

    /**
     * 
     * Save the data
     *
     * @param array $data
     * @return void
     */
    private function saveResource(array $data) : void
    {
        if (empty($data)) {
            error_log('No have data for save resource');
            return;
        }
        $this->repository->saveDataBD($data);
    }

}