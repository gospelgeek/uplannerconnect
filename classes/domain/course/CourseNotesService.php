<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

//Variables globales
require_once(__DIR__ . '/CourseTraslationData.php');
require_once(__DIR__ . '/../../application/course/CourseExtractionData.php');

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @author Daniel Dorado <doradodaniel14@gmail.com>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class CourseNotesService {

    private $CourseNotesTrasnform;
    private $CourseNotesRepository;

    public function __construct() {
        //Instanciar la clase de transformacion
        $this->CourseTraslationData = new CourseTraslationData();
        //Instanciar la clase de repositorio
        $this->CourseExtractionData = new CourseExtractionData();
    }


    public function proccess(array $data) {
        try {
            //Trear la informacion necesaria con los datos del evento
            $dataRepository = $this->CourseExtractionData->getResource($data);
            //Transformar los datos en el formato que requiere uPlanner
            $dataTrasnform = $this->CourseTraslationData->converDataJsonUplanner($dataRepository);
            //Enviar los datos a uPlanner
            $this->CourseExtractionData->saveResource($dataTrasnform);
        }
        catch (Exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

}