<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\announcements;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\plugin_config\estruture_types;
use local_uplannerconnect\domain\announcements\usecases\announcements_utils;
use moodle_exception;

/**
   * Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class announcements_extraction_data
{
    private $typeEvent;
    private $validator;
    private $announcementsUtils;
    
    public function __construct()
    {
        $this->typeEvent = [
            'created_announcements' => 'resourceAnnouncements'
        ];
        $this->validator = new data_validator();
        $this->announcementsUtils = new announcements_utils();
    }

    /**
     * Retorna los datos acorde al evento que se requiera
     *
     * @param array $data
     * @return array
     */
    public function getResource(array $data) : array
    {
      $arraySend = [];  
      try {
            if ($this->validator->verificateKeyArrayBoolean([
               'array_verification' => estruture_types::CREATE_EVENT_DATA,
               'get_data' => $data,
            ])) {
                $typeEvent = $this->typeEvent[$data['typeEvent']];
                $arraySend = $this->$typeEvent($data);
            }
      }
      catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
      return $arraySend;
    }

    /**
     * Return resource created
     * 
     * @param array $data
     * @return array
     */
    private function resourceAnnouncements(array $data) : array
    {
        return $this->send_data_uplanner([
            'data' => $this->announcementsUtils->createdAnnouncementResources($data),
            'typeEvent' => $data['typeEvent'],
        ]);
    }

    /**
     * Data Format send to uPlanner
     * 
     * @param array $data
     * @return array
     */
    private function send_data_uplanner(array $data) : array
    {
        $arraySend = [
            'data' => [],
            'typeEvent' => '',
        ]; 

        try {
            if (!empty($data)) {
                if (is_array($data['data'])) {               
                    $arraySend = [
                        'data' => $data['data'],
                        'typeEvent' => $data['typeEvent'],
                    ];
                }
            }
        }
        catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $arraySend;
    }
}