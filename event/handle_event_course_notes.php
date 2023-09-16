<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


// namespace local\uplannerconnect\events;

defined('MOODLE_INTERNAL') || die();

//Variables globales
require_once(__DIR__ . '/../classes/ManagementEntityFactory.php');



/**
 * @package uPlannerConnect
 * @todo Se deja comentado por que esta por definirse si se va a utilizar
 *       Mi idea del evento global
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Lanza un handle cuando se actualiza un item de calificación
*/
function grade_item_updated($event) {

   try {
      //instaciar la clase ManagementEntityFactory
      $ManageEntity = new ManagementEntityFactory();

      $ManageEntity->create([
         "dataEvent" => $event,
         "typeEvent" => "grade_item_updated",
         "dispatch" => "update",
         "EnumEtities" => 'course_notes'
      ]);
     
   }
   catch (Exception $e) {
       error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}


/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Lanza un handle cuando se actualiza un item de calificación
*/
function user_graded($event) {

   try {
     //instaciar la clase ManagementEntityFactory 
     $ManageEntity = new ManagementEntityFactory();

     $ManageEntity->create([
        "dataEvent" => $event,
        "typeEvent" => "user_graded",
        "dispatch" => "update",
        "EnumEtities" => 'course_notes'
     ]);

   } 
   catch (Exception $e) {
       error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }

}


/**
 * @package uPlannerConnect
 * @description Lanza un handle cuando se borra una calificación
*/
function grade_deleted($event) {
   
   try {
      
      //instaciar la clase ManagementEntityFactory
      $ManageEntity = new ManagementEntityFactory();

      $ManageEntity->create([
         "dataEvent" => $event,
         "typeEvent" => "grade_deleted",
         "dispatch" => "delete",
         "EnumEtities" => 'course_notes'
      ]);

   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }

}


/**
 * @package uPlannerConnect
 * @todo Se deja comentado por que esta por definirse si se va a utilizar
 *     Mi idea del evento global
 * @description Lanza un handle cuando se crea un item de calificación
 * 
*/
function grade_item_created($event) {
         
   try {
      print_r("grade_item_created");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}


/**
 * @package uPlannerConnect
 * @todo Se deja comentado por que esta por definirse si se va a utilizar
 *    Mi idea del evento global
 * @description Lanza un handle cuando se borra un item de calificación 
*/
function grade_item_deleted($event) {
      
   try {
      print_r("grade_item_deleted");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }

}


/**
 * @package uPlannerConnect
 * @todo Se deja comentado por que esta por definirse si se va a utilizar
 *   Mi idea del evento global
 * @description Lanza un handle cuando se crea una letra de calificación 
*/
function grade_letter_created($event) {
            
   try {
      print_r("grade_letter_created");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}


/**
 *  @package uPlannerConnect
 *  @todo Se deja comentado por que esta por definirse si se va a utilizar
 *  Mi idea del evento global
 * @description Lanza un handle cuando se borra una letra de calificación
*/
function grade_letter_deleted($event) {
               
   try {
      print_r("grade_letter_deleted");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}

/**
 * @package uPlannerConnect
 * @todo Se deja comentado por que esta por definirse si se va a utilizar
 *  Mi idea del evento global
 * @description Lanza un handle cuando se actualiza una letra de calificación
 * 
*/
function grade_letter_updated($event) {
                  
   try {
      print_r("grade_letter_updated");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }

}