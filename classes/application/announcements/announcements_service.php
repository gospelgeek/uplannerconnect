<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\announcements;

/**
 * 
 */
class announcements_service
{
    private $managerData;

    public function __construct() {
        $this->managerData = new data_manager('announcements');
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