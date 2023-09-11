<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

//Variables globales
require_once(__DIR__ . '/entity/ManagementNotesEntiry.php');

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
            'course_notes' => 'ManagementNotesEntiry',
        ];

    }

    /**
     *  @package uPlannerConnect
     *  @description retorna una instancia de la entidad de acuerdo al
     *               tipo de dato que se requiera
    */
    public function process(array $data) {

        try {
            
            $class = $this->Types[$data['EnumEtities']];
            $newEntity = $this->ManagementNotesEntiry = new $class();
            $newEntity->procces($data);
            
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
        
    }

}