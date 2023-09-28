<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/



defined('MOODLE_INTERNAL') || die();

//Variables globales
require_once(__DIR__ . '/../../domain/ManagementFactory.php');
require_once(__DIR__ . '/../../application/service/EventAccesValidator.php');


/** 
  *  Instancia el factory   
  *
  * @package local_uplannerconnect 
  * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
  * @return void
  *
*/
function instantiateManagementFactory(array $data) {
   try {

       // Verificar si se proporcionan datos válidos
       if (empty($data['dataEvent']) || empty($data['typeEvent']) || 
           empty($data['dispatch'])  || empty($data['EnumEtities'])) 
       {
           error_log("Error en los datos proporcionados: algunos campos están vacíos.");
           return;
       }

       // Instanciar la clase ManagementFactory
       $ManageEntity = new ManagementFactory();

       // Verificar si existe el método
       if (method_exists($ManageEntity, 'create')) {
           // Llamar al método create
           $ManageEntity->create([
               "dataEvent" => $data['dataEvent'],
               "typeEvent" => $data['typeEvent'],
               "dispatch" => $data['dispatch'],
               "EnumEtities" => $data['EnumEtities']
           ]);
       } else {
           error_log("El método 'create' no existe en la clase ManagementFactory.");
       }

   } catch (Exception $e) {
       error_log('Excepción capturada: ' . $e->getMessage() . "\n");
   }

}


/** 
 *  Instancia el factory
 * 
  * @package local_uplannerconnect
  * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
  * @return bool 
  *
*/
function validateAccessTypeEvent(array $data) : bool {
   try {
      $eventAccesValidator = new EventAccesValidator();
      return $eventAccesValidator->validateTypeEvent($data);
   }
   catch (Exception $e) {
       error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}


/**
 * Lanza un handle cuando se actualiza un item de calificación
 * 
 * @package local_uplannerconnect
 * @author  Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @return  void
 * 
*/
function user_graded($event) {
  
   try {

      //Validar el tipo de evento
      if (validateAccessTypeEvent([
         "dataEvent" => $event,
         "typeEvent" => "\\core\\event\\user_graded",
         "key" => "eventname",
         "methodName" => "get_data"
      ])) {

         $grade = $event->get_grade();
         $agregationState =  $grade->get_aggregationstatus();

         if (!($agregationState === 'unknown')) {
               //Instanciar la clase ManagementFactory
               instantiateManagementFactory([
                  "dataEvent" => $event,
                  "typeEvent" => "user_graded",
                  "dispatch" => "update",
                  "EnumEtities" => 'course_notes'
               ]);
         }

      }

   } 
   catch (Exception $e) {
       error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }

}


/**
 * Lanza un handle cuando se borra una calificación
 * 
 * @package uPlannerConnect
 * @return void
 *  
*/
function grade_deleted($event) {
   
   try {
      //Instanciar la clase ManagementFactory
      // instantiateManagementFactory([
      //    "dataEvent" => $event,
      //    "typeEvent" => "grade_deleted",
      //    "dispatch" => "delete",
      //    "EnumEtities" => 'course_notes'
      // ]);

   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }

}


/**
 * Lanza un handle cuando se crea un item de calificación
 * 
 * @package uPlannerConnect
 * @return void
 * 
*/
function grade_item_created($event) {
         
   try {
     
      //Validar el tipo de evento
      if (validateAccessTypeEvent([
         "dataEvent" => $event,
         "typeEvent" => "\\core\\event\\grade_item_created",
         "key" => "eventname",
         "methodName" => "get_data"
      ])) {
      
      //Instanciar la clase ManagementFactory
      instantiateManagementFactory([
         "dataEvent" => $event,
         "typeEvent" => "grade_item_created",
         "dispatch" => "create",
         "EnumEtities" => 'evaluation_structure'
      ]);

      }
      
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}


/**
 * @package uPlannerConnect
 * @description Lanza un handle cuando se borra un item de calificación 
*/
function grade_item_deleted($event) {
      
   try {
      //  //Instanciar la clase ManagementFactory
      //  instantiateManagementFactory([
      //    "dataEvent" => $event,
      //    "typeEvent" => "grade_item_deleted",
      //    "dispatch" => "delete",
      //    "EnumEtities" => 'course_notes'
      //  ]);
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }

}