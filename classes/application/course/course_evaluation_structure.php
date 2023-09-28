<?php
/**
 * @package  local_uplannerconnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 *  
*/

namespace local_uplannerconnect\application\course;

use local_uplannerconnect\domain\course\traslation_evaluation_structure;
use local_uplannerconnect\domain\course\extraction_course_evaluation_structure;
use local_uplannerconnect\application\repository\course_evaluation_structure_repository;

/**
 *  Encargada orquestar las estructuras de los cursos
 * 
 *  @package local_uplannerconnect
 *  @author Cristian Machado <cristian.machado@correounivalle.edu.co>
*/
class course_evaluation_structure {

    //Atributos
    private $courseTraslationData;
    private $courseExtractionData;
    private $courseRepository;

    // Constructor.
    public function __construct() {
        //Instanciar la clase de transformacion
        $this->courseTraslationData = new traslation_evaluation_structure();
        //Instanciar la clase de repositorio
        $this->courseExtractionData = new extraction_course_evaluation_structure();
        //Instancia de la clase CourseNotesResource
        $this->courseRepository = new course_evaluation_structure_repository();
    }

       /**
     * Encargada de Extraer los datos
     * Trastransformar los datos
     * Guardar los datos
     * 
     * @package uPlannerConnect
     * 
     * @param array $data
     * 
     * @return void
    */
    public function proccess(array $data) : void {
        try {
            //Trear la informacion necesaria con los datos del evento
            $dataRepository = $this->courseExtractionData->getResource($data);
            //Transformar los datos en el formato que requiere uPlanner
            $dataTrasnform = $this->courseTraslationData->converDataJsonUplanner($dataRepository);
            //Enviar los datos a uPlanner
            $this->saveResource($dataTrasnform);
            error_log('UplannerConnect: Successful event registration -'. $data['typeEvent']. "\n");
        }
        catch (\Exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }


    /**
     * @package uPlannerConnect
     * @description Guarda los datos en la base de datos
     * @return void 
    */
    private function saveResource(array $data) : void {
        //matar el proceso si no llega la información
        if (empty($data)) {
            error_log('No le llego la información del evento');
            return;
        }

        $this->courseRepository->saveDataBD($data);
    }



    

}