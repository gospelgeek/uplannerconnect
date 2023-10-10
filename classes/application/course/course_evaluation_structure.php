<?php
/**
 * @package  local_uplannerconnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co> 
 */

namespace local_uplannerconnect\application\course;

/**
 *  Encargada orquestar las estructuras de los cursos
 */
class course_evaluation_structure
{
    private $managerData;

    // Constructor.
    public function __construct()
    {
        $this->managerData = new data_manager('evaluation_structure');
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