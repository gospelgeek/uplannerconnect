<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain;

use local_uplannerconnect\application\course\course_grades_service;
use local_uplannerconnect\application\course\course_evaluation_structure;
use local_uplannerconnect\application\materials\material_resource;
use moodle_exception;

/**
   * @description Instancia una entidad de acorde a la funcionalidad que se requiera
*/
class enum_etities
{
    private $Types;

    public function __construct() {
        $this->Types = [
            'course_notes' => course_grades_service::class,
            'evaluation_structure' => course_evaluation_structure::class,
            'material_created' => material_resource::class,
        ];
    }

    /**
      * Retorna una instancia de la entidad de acuerdo al
     *  tipo de dato que se requiera
    */
    public function process(array $data) : void
    {
        try {
            //Saca el nombre de la clase
            $class = $this->Types[$data['enum_etities']];
            
            if (array_key_exists($data['enum_etities'], $this->Types)) {
                $newEntity = new $class();
                if (!method_exists($newEntity, 'process')) {
                    error_log('La clase ' . $class . ' no tiene el método process.');
                    return;
                }
                $newEntity->process($data);
            } else {
               error_log('La clase ' . $class . ' no existe o no tiene el método process.');
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }
}