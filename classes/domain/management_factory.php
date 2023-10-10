<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain;
use moodle_exception;

/**
   * Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class management_factory
{
    private $enum_etities;

    public function __construct() {
        $this->enum_etities = new enum_etities();
    }

    /**
     * Retorna una instancia de la entidad de acuerdo al 
     *  tipo de dato que se requiera
    */
    public function create(array $data) : void 
    {
        try {
            if (method_exists($this->enum_etities, 'process') && is_array($data)) {
                $this->enum_etities->process($data);
            } 
            else {
                error_log("El método 'process' no existe en la clase enum_etities.");
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }
}