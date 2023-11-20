<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado<cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\event;

use local_uplannerconnect\domain\management_factory;
use local_uplannerconnect\application\service\event_access_validator;
use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\domain\service\recalculate_item_weight; 
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

const LAST_INSERT_EVALUATION = "SELECT date,json FROM mdl_uplanner_evaluation ORDER BY id DESC limit 1";
const ITEMTYPE_UPDATE = 'course';
const IS_ITEM_UPDATE = 'UPDATED';

/**
 *  Maneja los eventos de moodle
*/
class handle_event_course_notes
{
   /**
    * Lanza un handle cuando se actualiza un item de calificación
    * 
    * @return  void
   */
   public static function user_graded($event)
   {
      try {
            //Validar el tipo de evento
            if (validateAccessTypeEvent([
               "dataEvent" => $event,
               "typeEvent" => ["\\core\\event\\user_graded"],
               "key" => "eventname",
               "methodName" => "get_data"
            ])) {
               $eventUser = ($event->get_data())['userid'];
               $grade = $event->get_grade();
               $agregationState =  $grade->get_aggregationstatus();
       
               if ($eventUser !== -1) {
                  //valida si la facultad tiene acceso
                  if (!validateAccesFaculty($event)) { return; }
                     $stateCreated = [
                        'novalue',
                        'unknown'
                     ];
                     $stateDispatch = in_array($agregationState,$stateCreated);
                     $isTotalItem = isTotalItem($grade-> load_grade_item());
                     if ($isTotalItem) {
                         //Instanciar la clase management_factory
                         instantiatemanagement_factory([
                            "dataEvent" => $event,
                            "typeEvent" => "user_graded",
                            "dispatch" => ($stateDispatch)? 'create': 'update',
                            "enum_etities" => 'course_notes'
                         ]);
                     }
               }
            }
      } catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
   }

   /**
    * Lanza un handle cuando se borra una calificación
    * 
    * @return void 
   */
   public static function grade_deleted($event)
   {   
      try {
            //Validar el tipo de evento
            if (validateAccessTypeEvent([
               "dataEvent" => $event,
               "typeEvent" => ["\\core\\event\\grade_deleted"],
               "key" => "eventname",
               "methodName" => "get_data"
            ])) {
               $eventUser = ($event->get_data())['userid'];
   
               if ($eventUser !== -1) {
                  //valida si la facultad tiene acceso
                  if (!validateAccesFaculty($event)) { return; }
                     //Instanciar la clase management_factory
                     instantiatemanagement_factory([
                        "dataEvent" => $event,
                        "typeEvent" => "user_graded",
                        "dispatch" => 'delete',
                        "enum_etities" => 'course_notes'
                     ]);
               }
            }
      } catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
   }

   /**
    * Lanza un handle cuando se crea un item de calificación
    * 
    * @param object $event
    * @return void
   */
   public static function grade_item_created($event)
   {        
      try {
            $IsValidEvent = [
               'delete' => validateAccessTypeEvent([
                  "dataEvent" => $event,
                  "typeEvent" => ["\\core\\event\\grade_item_deleted"],
                  "key" => "eventname",
                  "methodName" => "get_data"
               ]),
               'create' => validateAccessTypeEvent([
                  "dataEvent" => $event,
                  "typeEvent" => ["\\core\\event\\grade_item_created"],
                  "key" => "eventname",
                  "methodName" => "get_data"
               ])
            ];

            $isTotalItem = isTotalItem($event->get_grade_item());
            //Validar el tipo de evento
            if ($IsValidEvent['create'] || $IsValidEvent['delete']) {
            //valida si la facultad tiene acceso
            if (!validateAccesFaculty($event)) { return; }
               if ($isTotalItem) {
                  //Instanciar la clase management_factory
                  instantiatemanagement_factory([
                     "dataEvent" => $event,
                     "typeEvent" => "grade_item_created",
                     "dispatch" => $IsValidEvent['create']? "create" : "delete",
                     "enum_etities" => 'evaluation_structure'
                  ]);
               }
            }
      } catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
   }

   /**
    *  Triggers an event when an item is updated.
    */
   public static function grade_item_updated($event)
   {
     try {
         if (validateAccessTypeEvent([
            "dataEvent" => $event,
            "typeEvent" => ["\\core\\event\\grade_item_updated"],
            "key" => "eventname",
            "methodName" => "get_data"
         ])) {
            if (validateAccesFaculty($event)) {
               filterRecentUpdate($event);
            }
         }
     } catch (moodle_exception $e) {
         error_log('Excepción capturada: '.  $e->getMessage(). "\n");
     }  
   }

   /**
    * Triggers an event when a resource is created.
    */
   public static function resource_file($events)
   {
      try {
         error_log('*******************************************************');
         error_log('resource_file');
         error_log('*******************************************************');
      } catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
   }

   /**
    * Resource created
    */
   public static function course_module_created($event)
   {  
      try {
            if (validateAccessTypeEvent([
               "dataEvent" => $event,
               "typeEvent" => ["\\core\\event\\course_module_created"],
               "key" => "eventname",
               "methodName" => "get_data"
            ])) {
               $dataEvent = $event->get_data();
               if(isset($dataEvent['other']['modulename']) && 
                  $dataEvent['objecttable'] === 'course_modules') {
                  $moduleType = $dataEvent['other']['modulename'];
                  
                  //course_modules
                  $availableModules = [
                     'folder',
                     'resource',
                     'label',
                     'lightboxgallery',
                     'book',
                     'page',
                     'url',
                     'imscp'
                  ];

                  if (in_array($moduleType, $availableModules)) {
                     if (!validateAccesFaculty($event)) { return; }
                     //Instanciar la clase management_factory
                     instantiatemanagement_factory([
                        "dataEvent" => $event,
                        "typeEvent" => "resource_created",
                        "dispatch" => 'create',
                        "enum_etities" => 'material_created'
                     ]);
                  }
               }
            }
      } catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
   }

   /**
    * Resource updated
    */
   public static function course_module_updated($event)
   {
      try {
         if (validateAccessTypeEvent([
            "dataEvent" => $event,
            "typeEvent" => ["\\core\\event\\course_module_updated"],
            "key" => "eventname",
            "methodName" => "get_data"
         ])) {
            $dataEvent = $event->get_data();
            if(isset($dataEvent['other']['modulename']) && 
               $dataEvent['objecttable'] === 'course_modules') {
               $moduleType = $dataEvent['other']['modulename'];
               
               //course_modules
               $availableModules = [
                  'folder',
                  'resource',
                  'label',
                  'lightboxgallery',
                  'book',
                  'page',
                  'url',
                  'imscp'
               ];

               if (in_array($moduleType, $availableModules)) {
                  if (!validateAccesFaculty($event)) { return; }
                  //Instanciar la clase management_factory
                  instantiatemanagement_factory([
                     "dataEvent" => $event,
                     "typeEvent" => "resource_created",
                     "dispatch" => 'update',
                     "enum_etities" => 'material_created'
                  ]);
               }
            }
         }
      } catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
   }

   /**
    * Resource deleted
    */
   public static function course_module_deleted($event)
   {
      try {
         if (validateAccessTypeEvent([
            "dataEvent" => $event,
            "typeEvent" => ["\\core\\event\\course_module_deleted"],
            "key" => "eventname",
            "methodName" => "get_data"
         ])) {
            $dataEvent = $event->get_data();
            if(isset($dataEvent['other']['modulename']) && 
               $dataEvent['objecttable'] === 'course_modules') {
               $moduleType = $dataEvent['other']['modulename'];
               
               //course_modules
               $availableModules = [
                  'folder',
                  'resource',
                  'label',
                  'lightboxgallery',
                  'book',
                  'page',
                  'url',
                  'imscp'
               ];

               if (in_array($moduleType, $availableModules)) {
                  if (!validateAccesFaculty($event)) { return; }
                  //Instanciar la clase management_factory
                  instantiatemanagement_factory([
                     "dataEvent" => $event,
                     "typeEvent" => "resource_created",
                     "dispatch" => 'delete',
                     "enum_etities" => 'material_created'
                  ]);
               }
            }
         }
      } catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
   }

   /**
    * Triggers an event when a discussion is created.
    */
   public static function discussion_created($event)
   {
      try {
            if (validateAccessTypeEvent([
               "dataEvent" => $event,
               "typeEvent" => ["\\mod_forum\\event\\discussion_created"],
               "key" => "eventname",
               "methodName" => "get_data"
            ])) {
               if (!validateAccesFaculty($event)) { return; }
               //Instanciar la clase management_factory
               instantiatemanagement_factory([
                  "dataEvent" => $event,
                  "typeEvent" => "created_announcements",
                  "dispatch" => 'create',
                  "enum_etities" => 'announcements'
               ]);
            }
      } catch (moodle_exception $e) {
         error_log('Excepción capturada: '. $e->getMessage(). "\n");
      }
   }

   /**
    * Triggers an event when a discussion is deleted.
    */
    public static function discussion_deleted($event)
    {
       try {
             if (validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\discussion_deleted"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!validateAccesFaculty($event)) { return; }
                //Instanciar la clase management_factory
                instantiatemanagement_factory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'delete',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log('Excepción capturada: '. $e->getMessage(). "\n");
       }
    }

   /**
    * Triggers an event when a discussion is deleted.
    */
    public static function discussion_updated($event)
    {
       try {
             if (validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\discussion_updated"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!validateAccesFaculty($event)) { return; }
                //Instanciar la clase management_factory
                instantiatemanagement_factory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'update',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log('Excepción capturada: '. $e->getMessage(). "\n");
       }
    }

   /**
    * Triggers an event when a post_created.
    */
    public static function post_created($event)
    {
       try {
             if (validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\post_created"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!validateAccesFaculty($event)) { return; }
                //Instanciar la clase management_factory
                instantiatemanagement_factory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'create',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log('Excepción capturada: '. $e->getMessage(). "\n");
       }
     }

   /**
    * Triggers an event when a post_updated.
    */
    public static function post_updated($event)
    {
       try {
             if (validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\post_updated"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!validateAccesFaculty($event)) { return; }
                //Instanciar la clase management_factory
                instantiatemanagement_factory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'update',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log('Excepción capturada: '. $e->getMessage(). "\n");
       }
    }

   /**
    * Triggers an event when a post_deleted.
    */
    public static function post_deleted($event)
    {
       try {
             if (validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\post_deleted"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!validateAccesFaculty($event)) { return; }
                //Instanciar la clase management_factory
                instantiatemanagement_factory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'delete',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log('Excepción capturada: '. $e->getMessage(). "\n");
       }
    }
}

/**
  * Verifica si el evento no es un duplicado
  */
function filterRecentUpdate($event)
{
   try {
         // Get last register
         $query = new moodle_query_handler();
         $evaluation = $query->executeQuery(LAST_INSERT_EVALUATION);
         // Is category Father
         $isCategoryFather = true;
         // data of item
         $grade_item_load =   $event->get_grade_item();
         $get_data_category = $grade_item_load->get_item_category();

         // Verificate if is category father
         if (isset($get_data_category->depth)) {
            $depth_category = $get_data_category->depth;
            if ($depth_category == 1) {
               $isCategoryFather = false;
            }
         }

         if (!empty($evaluation) && $isCategoryFather) { 
            //obeter el primer resultado
            $firstResult = reset($evaluation);
            //obtener el json
            $evaluationLast = (json_decode($firstResult->json));
            $date = intval($firstResult->date);
            $dataEvent = $event->get_data();
            //obtener el primer grupo de evaluaciones
            $evaluationGroups = ($evaluationLast->evaluationGroups)[0];
            //obtener la primera evaluacion
            $evaluationsData  = ($evaluationGroups->evaluations)[0];
            $validateUpdateNew = isUpdateItem([
               "dataEvent" => $dataEvent,
               "evaluationLast" => $evaluationLast,
               "evaluationsData" => $evaluationsData,
               "date" => $date
            ]);
            $isTotalItem = isTotalItem($event->get_grade_item());
            $itemActions = strtolower($evaluationLast->action.'d');

            // Validar si la evaluacion es diferente
            if (($itemActions !== $dataEvent['action'] &&
                 $evaluationsData->evaluationId === $dataEvent['objectid']) ||
                 $validateUpdateNew
            ) {
                 if ($isTotalItem) {
                     //Instanciar la clase management_factory
                     instantiatemanagement_factory([
                        "dataEvent" => $event,
                        "typeEvent" => "grade_item_created",
                        "dispatch" => "update",
                        "enum_etities" => 'evaluation_structure'
                     ]);
                  }
            }
         }
   } catch (moodle_exception $e) {
      error_log('Excepción capturada: '. $e->getMessage(). "\n");
   }
}

function isTotalItem($grade_item)
{
   $isTotalItem = true;
   // grade item
   $grade_item_load = $grade_item ?? null; 
   // Verificate if the grade item is not null
   if ($grade_item_load !== null) {
      $categoryId = $grade_item_load->categoryid ?? 0;
      $itemType = $grade_item_load->itemtype ?? '';
      // Verificate if the grade item is total
      $isTotalItem = !($categoryId === 0 && $itemType === ITEMTYPE_UPDATE);
   }

   return $isTotalItem;
}

function isUpdateItem(array $data)
{
   $dataEvent = $data['dataEvent'];
   $evaluationLast = $data['evaluationLast'];
   $evaluationsData = $data['evaluationsData'];
   $date = $data['date'];
   $validateUpdateNew = false;

   if (key_exists('objectid', $dataEvent) &&
       key_exists('timecreated', $dataEvent))
   {
      if ($evaluationsData->evaluationId !== 
         $dataEvent['objectid']) {
         $validateUpdateNew = (
            $evaluationLast->action.'D' === IS_ITEM_UPDATE
         );
      } else {
         $validateUpdateNew = (
            $evaluationLast->action.'D' === IS_ITEM_UPDATE &&
            $date !== $dataEvent['timecreated']
         );
      }
   } 
   return $validateUpdateNew;
}

/** 
 * Instancia el factory   
 *
 * @return void
*/
function instantiatemanagement_factory(array $data)
{
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
   } catch (moodle_exception $e) {
      error_log('Excepción capturada: ' . $e->getMessage() . "\n");
   }
}

/** 
 *  Instancia el factory
 * 
 * @return bool
*/
function validateAccessTypeEvent(array $data) : bool
{
   try {
        $event_access_validator = new event_access_validator();
        return $event_access_validator->validateTypeEvent($data);
   }
   catch (moodle_exception $e) {
      error_log('Excepción capturada: '. $e->getMessage(). "\n");
   }
}

/**
 *  Valida si la facultad tiene acceso
 * 
 *  @return bool 
*/
function validateAccesFaculty($data) : bool
{
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
   catch (moodle_exception $e) {
      error_log('Excepción capturada: '. $e->getMessage(). "\n");
   }
   return false;
}

/**
 * Recalculate weight
 */
function recalculatesWeight($data) : void
{
   try {
         $recalculate_item_weight = new recalculate_item_weight();
         $recalculate_item_weight->recalculate_weight_evaluation([
            "event" => $data
         ]);
   }
   catch (moodle_exception $e) {
      error_log('Excepción capturada: '. $e->getMessage(). "\n");
   }
}