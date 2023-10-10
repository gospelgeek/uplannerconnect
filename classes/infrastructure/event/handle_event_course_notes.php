<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


namespace local_uplannerconnect\infrastructure\event;

use local_uplannerconnect\domain\management_factory;
use local_uplannerconnect\application\service\event_access_validator;

defined('MOODLE_INTERNAL') || die();


/**
 * 
 *  Maneja los eventos de moodle
 * 
 *  @package local_uplannerconnect 
 *  
*/
class handle_event_course_notes {

   //constructor
   public function __construct() {
   }


   /**
    * Lanza un handle cuando se actualiza un item de calificación
    * 
    * @package local_uplannerconnect
    * @author  Cristian Machado <cristian.machado@correounivalle.edu.co>
    * @return  void
    * 
   */
   public static function user_graded($event) {
   
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

               //valida si la facultad tiene acceso
               if (!validateAccesFaculty($event)) { return; }

                  //Instanciar la clase management_factory
                  instantiatemanagement_factory([
                     "dataEvent" => $event,
                     "typeEvent" => "user_graded",
                     "dispatch" => "update",
                     "enum_etities" => 'course_notes'
                  ]);
            }

         }

      } 
      catch (\Exception $e) {
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
   public static function grade_deleted($event) {
      
      try {
         //Instanciar la clase management_factory
         // instantiatemanagement_factory([
         //    "dataEvent" => $event,
         //    "typeEvent" => "grade_deleted",
         //    "dispatch" => "delete",
         //    "enum_etities" => 'course_notes'
         // ]);

      } 
      catch (\Exception $e) {
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
   public static function grade_item_created($event) {
            
      try {
      
         $IsValidEvent = [
            'delete' => validateAccessTypeEvent([
               "dataEvent" => $event,
               "typeEvent" => "\\core\\event\\grade_item_deleted",
               "key" => "eventname",
               "methodName" => "get_data"
            ]),
            'create' => validateAccessTypeEvent([
               "dataEvent" => $event,
               "typeEvent" => "\\core\\event\\grade_item_created",
               "key" => "eventname",
               "methodName" => "get_data"
             ])
         ];

         //Validar el tipo de evento
         if ($IsValidEvent['create']  || $IsValidEvent['delete']) {

         //valida si la facultad tiene acceso
         if (!validateAccesFaculty($event)) { return; }

         //Instanciar la clase management_factory
         instantiatemanagement_factory([
            "dataEvent" => $event,
            "typeEvent" => "grade_item_created",
            "dispatch" => $IsValidEvent['create']? "create" : "delete",
            "enum_etities" => 'evaluation_structure'
         ]);

         }
      } 
      catch (\Exception $e) {
         error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }
   }


   /**
    * @package uPlannerConnect
    * @description Lanza un handle cuando se borra un item de calificación 
   */
   public static function grade_item_deleted($event) {
         
      try {
         //  //Instanciar la clase management_factory
         //  instantiatemanagement_factory([
         //    "dataEvent" => $event,
         //    "typeEvent" => "grade_item_deleted",
         //    "dispatch" => "delete",
         //    "enum_etities" => 'course_notes'
         //  ]);
      } 
      catch (\Exception $e) {
         error_log('Excepción capturada: ',  $e->getMessage(), "\n");
      }

   }


}


/** 
 * Instancia el factory   
 *
 * @package local_uplannerconnect 
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @return void
 *
*/
function instantiatemanagement_factory(array $data) {
   try {
      
      // Verificar si se proporcionan datos válidos
      if (empty($data['dataEvent']) || empty($data['typeEvent']) || 
          empty($data['dispatch'])  || empty($data['enum_etities'])) 
      {
         error_log("Error en los datos proporcionados: algunos campos están vacíos.");
         return;
      }

      // Instanciar la clase management_factory
      $ManageEntity = new management_factory();

      // Verificar si existe el método
      if (method_exists($ManageEntity, 'create')) {
         // Llamar al método create
         $ManageEntity->create([
               "dataEvent" => $data['dataEvent'],
               "typeEvent" => $data['typeEvent'],
               "dispatch" => $data['dispatch'],
               "enum_etities" => $data['enum_etities']
         ]);
      } else {
         error_log("El método 'create' no existe en la clase management_factory.");
      }

   } catch (\Exception $e) {
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
      $event_access_validator = new event_access_validator();
      return $event_access_validator->validateTypeEvent($data);
   }
   catch (\Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}


/**
 *  Valida si la facultad tiene acceso
 * 
 *  @package local_uplannerconnect
 *  @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 *  @return bool 
*/
function validateAccesFaculty($data) : bool {
   try {
      //Instanciar la clase event_access_validator
      $event_access_validator = new event_access_validator();
      //Obtener los datos del evento
      $eventData = $data->get_data();
      
      //validar si el evento tiene el campo courseid
      if (!array_key_exists('courseid', $eventData)) { return false; }
     
      //validar si la facultad tiene acceso
      return $event_access_validator->validateAccessByFaculty($eventData['courseid']);

   }
   catch (\Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
   return false;
}