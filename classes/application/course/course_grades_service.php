<?php
/**
 * @package     local_uplannerconnect
 * @author      cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @author      daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\course;

/**
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class course_grades_service
{
    private $managerData;

    public function __construct() {
        $this->managerData = new data_manager('grades');
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
        $this->managerData->process($data);
    }
}