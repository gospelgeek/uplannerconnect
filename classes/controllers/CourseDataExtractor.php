<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
namespace local_uplannerconnect;
global $DB;

$tableName = 'uplannerconnect_course';

class CourseDataExtractor {

    public function __construct() {
    }

    /**
     * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
     * @description Inserta un registro en la tabla miplugin_tabla 
    */
    public function insertCourseRegistry($data) {
        
        //Parámetros de ejemplo
        $id_curso = 1;
        $id_student = 123;
        $item_note = 2;
        $note = 'Nota de ejemplo';
       
         //Instancia de la base de datos
         $record = new \stdClass();
         $record->id_curso = $id_curso;
         $record->id_student = $id_student;
         $record->item_note = $item_note;
         $record->note = $note;
       
          //Insertar el registro
          $result = $DB->insert_record($tableName, $record);
       
          if ($result) {
             $this->log('Registro insertado correctamente en miplugin_tabla.', \core\log\level::INFO);
          } else {
             $this->log('Error al insertar el registro en miplugin_tabla.', \core\log\level::ERROR);
          }

    }

}