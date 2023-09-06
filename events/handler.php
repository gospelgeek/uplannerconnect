<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Lanza la tarea correspondiente al evento 
*/
function event_handler_grade_changes_in_course(\core\event\base $eventdata) 
{

    try {
       
       //Instancia del extractor de datos
       $extractor = new \local_uplannerconnect\CourseDataExtractor();

       //insertar el registro
       $extractor->insertCourseRegistry($eventdata);


    } catch (\Exception $e) {
        // Capturar cualquier excepción que pueda ocurrir durante la ejecución de la tarea
        // Puedes registrar la excepción o tomar medidas adicionales según tus necesidades
        error_log('Error al ejecutar la tarea programada: ' . $e->getMessage());
    }

}
