<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/



defined('MOODLE_INTERNAL') || die();

//Variables globales
require_once(__DIR__ . '/../../domain/ManagementFactory.php');



/** 
  * @package uPlannerConnect 
  * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
  * @description Instancia el factory
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
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description Lanza un handle cuando se actualiza un item de calificación
*/
function grade_item_updated($event) {

   try {
      //Instanciar la clase ManagementEntityFactory
      instantiateManagementFactory([
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
     //Instanciar la clase ManagementFactory
     instantiateManagementFactory([
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
      //Instanciar la clase ManagementFactory
      instantiateManagementFactory([
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
 * @description Lanza un handle cuando se crea un item de calificación
 * 
*/
function grade_item_created($event) {
         
   try {
      //Instanciar la clase ManagementFactory
      instantiateManagementFactory([
         "dataEvent" => $event,
         "typeEvent" => "grade_item_created",
         "dispatch" => "create",
         "EnumEtities" => 'course_notes'
      ]);
      
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
       //Instanciar la clase ManagementFactory
       instantiateManagementFactory([
         "dataEvent" => $event,
         "typeEvent" => "grade_item_deleted",
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
 * @description Lanza un handle cuando se crea una letra de calificación 
*/
function grade_letter_created($event) {
            
   try {
      error_log("grade_letter_created");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}


/**
 *  @package uPlannerConnect
 * @description Lanza un handle cuando se borra una letra de calificación
*/
function grade_letter_deleted($event) {
               
   try {
      error_log("grade_letter_deleted");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }
}

/**
 * @package uPlannerConnect
 * @description Lanza un handle cuando se actualiza una letra de calificación
 * 
*/
function grade_letter_updated($event) {
                  
   try {
      error_log("grade_letter_updated");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }

}


/**
 *  @package uPlannerConnect
 *  @description Lanza un handle cuando se crea una escala de calificación
*/
function scale_created($event) {         

   try {
      error_log("scale_created");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }  

}  


/**
 *  @package uPlannerConnect
 * @description Lanza un handle cuando se borra una escala de calificación
*/
function scale_deleted($event) {         

   try {
      error_log("scale_deleted");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }  

}


/**
 *  @package uPlannerConnect
 *  @description Lanza un handle cuando se actualiza una escala de calificación
*/
function scale_updated($event) {         

   try {
      error_log("scale_updated");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }  

}

/**
 *  @package uPlannerConnect
 *  @description Lanza un handle cuando se crea una competencia
*/
function competency_created($event) {
   try {
      error_log("competency_created");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }  
}


/**
 *  @package uPlannerConnect
 *  @description Lanza un handle cuando se borra una competencia
*/
function competency_deleted($event) {
   try {
      error_log("competency_deleted");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }  
}


/**
 *  @package uPlannerConnect
 *  @description Lanza un handle cuando se actualiza una competencia
*/
function competency_updated($event) {
   try {
      error_log("competency_updated");
   } 
   catch (Exception $e) {
      error_log('Excepción capturada: ',  $e->getMessage(), "\n");
   }  
}