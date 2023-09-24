<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

//Variables globales
require_once(__DIR__ . '/../../domain/course/CourseTraslationData.php');
require_once(__DIR__ . '/../../domain/course/CourseExtractionData.php');
require_once(__DIR__ . '/../repository/CourseNotesRepository.php');


/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @author Daniel Dorado <doradodaniel14@gmail.com>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseNotesService {

    //Atributos
    private $courseNotesTrasnform;
    private $courseExtractionData;
    private $courseNotesRepository;

    public function __construct() {
        //Instanciar la clase de transformacion
        $this->courseTraslationData = new CourseTraslationData();
        //Instanciar la clase de repositorio
        $this->courseExtractionData = new CourseExtractionData();
        //Instancia de la clase CourseNotesResource
        $this->courseNotesRepository = new CourseNotesRepository();
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
        catch (Exception $e) {
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

        $this->courseNotesRepository->saveDataBD($data);
    }

}