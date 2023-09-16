<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

//Variables globales
require_once(__DIR__ . '/../trasnform/CourseNotesTrasnform.php');
require_once(__DIR__ . '/../repository/CourseNotesRepository.php');

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @author Daniel Dorado <doradodaniel14@gmail.com>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class ManagementNotesEntiry {

    private $CourseNotesTrasnform;
    private $CourseNotesRepository;

    public function __construct() {
        //Instanciar la clase de transformacion
        $this->CourseNotesTrasnform = new CourseNotesTrasnform();
        //Instanciar la clase de repositorio
        $this->CourseNotesRepository = new CourseNotesRepository();
    }


    public function procces(array $data) {
        //Trear la informacion necesaria con los datos del evento
        $dataRepository = $this->CourseNotesRepository->getResource($data);
        //Transformar los datos en el formato que requiere uPlanner
        $dataTrasnform = $this->CourseNotesTrasnform->converDataJsonUplanner($dataRepository);
        //Enviar los datos a uPlanner
        $this->CourseNotesRepository->saveResource($dataTrasnform);

    }

}