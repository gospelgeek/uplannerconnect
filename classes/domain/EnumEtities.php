<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

//Variables globales
require_once(__DIR__ . '/course/CourseNotesService.php');

/**
   * @package uPlannerConnect
   * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
   * @author Daniel Dorado <doradodaniel14@gmail.com>
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class EnumEtities {

    private $Types;
    private $ManagementNotesEntiry;

    public function __construct() {

        $this->Types = [
            'course_notes' => 'CourseNotesService',
        ];

    }

    /**
     *  @package uPlannerConnect
     *  @description retorna una instancia de la entidad de acuerdo al
     *               tipo de dato que se requiera
    */
    public function process(array $data) {

        try {
            //Saca el nombre de la clase
            $class = $this->Types[$data['EnumEtities']];
        
            if (array_key_exists($data['EnumEtities'], $this->Types)) {

                $newEntity = $this->ManagementNotesEntiry = new $class();
                if (!method_exists($newEntity, 'proccess')) {
                    error_log('La clase ' . $class . ' no tiene el método process.');
                    return;
                }

                $newEntity->proccess($data);

            } else {
               error_log('La clase ' . $class . ' no existe o no tiene el método process.');
            }

        } catch (Exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
        
    }

}