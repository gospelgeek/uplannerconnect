<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\materials\usecases;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

/**
 *  Extraer los datos
 */
class material_utils
{   
    private $validator;
    private $moodle_query_handler;

    /**
     *  Construct
     */
    public function __construct()
    {
        $this->validator = new data_validator();
        $this->moodle_query_handler = new moodle_query_handler();
    }

    /**
     * Retorna los datos del evento user_graded
     *
     * @return array
     */
    public function resourceCreatedMaterial(array $data) : array
    {
        $dataToSave = [];
        try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento user_graded');
                return $arraySend;
            }

            //Traer la información
            $event = $data['dataEvent'];
            $getData = $this->validator->isArrayData($event->get_data());

            //información a guardar
            $dataToSave = [
                'id' => $this->validator->isIsset(strval($getData['other']['instanceid'])),
                'name' => $this->validator->isIsset($getData['other']['name']),
                'type' => $this->validator->isIsset($getData['other']['modulename']),
                'lastUpdatedTime' => $this->validator->isIsset(strval($getData['timecreated'])),
                'action' => $data['dispatch'],
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $dataToSave;
    }
}