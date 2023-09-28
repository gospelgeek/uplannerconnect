<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain;

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @author Daniel Dorado <doradodaniel14@gmail.com>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class ManagementFactory {

    //Atributos
    private $EnumEtities;

    /**
     * @package uPlannerConnect
     * @description Constructor de la clase
     * @return void
    */
    public function __construct() {
        $this->EnumEtities = new EnumEtities();
    }

    /**
     * @package uPlannerConnect
     * @description Retorna una instancia de la entidad de acuerdo al 
     *              tipo de dato que se requiera
    */
    public function create(array $data) : void {
        try {
            if (method_exists($this->EnumEtities, 'process') && is_array($data)) {
                $this->EnumEtities->process($data);
            } 
            else {
                error_log("El método 'process' no existe en la clase EnumEtities.");
            }
        } catch (\Exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

}