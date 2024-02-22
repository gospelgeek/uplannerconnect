<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado<cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\event;

use local_uplannerconnect\domain\management_factory;
use local_uplannerconnect\infrastructure\utils\utils_events;
use local_uplannerconnect\infrastructure\utils\has_active_structure;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Manage Moodle events.
*/
class dispatch_event_handler
{
    const TEXT_ERROR = 'Exception caught: ';

   /**
    * Trigger a handler when a grade item is updated.
    *
    * @return  void
   */
   public static function user_graded($event)
   {
      try {
            //Validar el tipo de evento
            if (utils_events::validateAccessTypeEvent([
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
                  if (!utils_events::validateAccessFaculty($event)) { return; }
                     $stateCreated = [
                        'novalue',
                        'unknown'
                     ];
                     $stateDispatch = in_array($agregationState,$stateCreated);
                     $isTotalItem = utils_events::isTotalItem($grade-> load_grade_item());
                     if ($isTotalItem) {
                         //Instanciar la clase management_factory
                         utils_events::instanceFactory([
                            "dataEvent" => $event,
                            "typeEvent" => "user_graded",
                            "dispatch" => ($stateDispatch)? 'create': 'update',
                            "enum_etities" => 'course_notes'
                         ]);
                     }
               }
            }
      } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
      }
   }

   /**
    * Trigger a handler when a grade is deleted.
    *
    * @return void
   */
   public static function grade_deleted($event)
   {
      try {
            //Validar el tipo de evento
            if (utils_events::validateAccessTypeEvent([
               "dataEvent" => $event,
               "typeEvent" => ["\\core\\event\\grade_deleted"],
               "key" => "eventname",
               "methodName" => "get_data"
            ])) {
               $eventUser = ($event->get_data())['userid'];
   
               if ($eventUser !== -1) {
                  //valida si la facultad tiene acceso
                  if (!utils_events::validateAccessFaculty($event)) { return; }
                     //Instanciar la clase management_factory
                     utils_events::instanceFactory([
                        "dataEvent" => $event,
                        "typeEvent" => "user_graded",
                        "dispatch" => 'delete',
                        "enum_etities" => 'course_notes'
                     ]);
               }
            }
      } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
      }
   }

   /**
    * Trigger a handler when a grade item is created.
    *
    * @param object $event
    * @return void
   */
   public static function grade_item_created($event)
   {
      try {
            $IsValidEvent = [
               'delete' => utils_events::validateAccessTypeEvent([
                  "dataEvent" => $event,
                  "typeEvent" => ["\\core\\event\\grade_item_deleted"],
                  "key" => "eventname",
                  "methodName" => "get_data"
               ]),
               'create' => utils_events::validateAccessTypeEvent([
                  "dataEvent" => $event,
                  "typeEvent" => ["\\core\\event\\grade_item_created"],
                  "key" => "eventname",
                  "methodName" => "get_data"
               ])
            ];

            $isTotalItem = utils_events::isTotalItem($event->get_grade_item());
            //Validar el tipo de evento
            if ($IsValidEvent['create'] || $IsValidEvent['delete']) {
                //valida si la facultad tiene acceso
                if (!utils_events::validateAccessFaculty($event)) { return; }

                if ($isTotalItem && $IsValidEvent['create']) {
                    utils_events::isStructureCourse($event,'create');
                }
                else if ($isTotalItem) {
                    //Instanciar la clase management_factory
                    utils_events::instanceFactory([
                        "dataEvent" => $event,
                        "typeEvent" => "grade_item_created",
                        "dispatch" => "delete",
                        "enum_etities" => 'evaluation_structure'
                    ]);
                }
            }
      } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
      }
   }

   /**
    *  Triggers an event when an item is updated.
    */
   public static function grade_item_updated($event)
   {
     try {
         if (utils_events::validateAccessTypeEvent([
            "dataEvent" => $event,
            "typeEvent" => ["\\core\\event\\grade_item_updated"],
            "key" => "eventname",
            "methodName" => "get_data"
         ])) {
            if (utils_events::validateAccessFaculty($event)) {
               utils_events::filterRecentUpdate($event);
            }
         }
     } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR.  $e->getMessage(). "\n");
     }
   }

   /**
    * Triggers an event when a resource is created.
    */
   public static function course_structure($event)
   {
      try {
         $instanceUtils = new has_active_structure();
         $instanceUtils->generateHandler($event->other);
      } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
      }
   }

   /**
    * Resource created
    */
   public static function course_module_created($event)
   {
      try {
            if (utils_events::validateAccessTypeEvent([
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
                     if (!utils_events::validateAccessFaculty($event)) { return; }
                     //Instanciar la clase management_factory
                     utils_events::instanceFactory([
                        "dataEvent" => $event,
                        "typeEvent" => "resource_created",
                        "dispatch" => 'create',
                        "enum_etities" => 'material_created'
                     ]);
                  }
               }
            }
      } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
      }
   }

   /**
    * Resource updated
    */
   public static function course_module_updated($event)
   {
      try {
         if (utils_events::validateAccessTypeEvent([
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
                  if (!utils_events::validateAccessFaculty($event)) { return; }
                  //Instanciar la clase management_factory
                  utils_events::instanceFactory([
                     "dataEvent" => $event,
                     "typeEvent" => "resource_created",
                     "dispatch" => 'update',
                     "enum_etities" => 'material_created'
                  ]);
               }
            }
         }
      } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
      }
   }

   /**
    * Resource deleted
    */
   public static function course_module_deleted($event)
   {
      try {
         if (utils_events::validateAccessTypeEvent([
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
                  if (!utils_events::validateAccessFaculty($event)) { return; }
                  //Instanciar la clase management_factory
                  utils_events::instanceFactory([
                     "dataEvent" => $event,
                     "typeEvent" => "resource_created",
                     "dispatch" => 'delete',
                     "enum_etities" => 'material_created'
                  ]);
               }
            }
         }
      } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
      }
   }

   /**
    * Triggers an event when a discussion is created.
    */
   public static function discussion_created($event)
   {
      try {
            if (utils_events::validateAccessTypeEvent([
               "dataEvent" => $event,
               "typeEvent" => ["\\mod_forum\\event\\discussion_created"],
               "key" => "eventname",
               "methodName" => "get_data"
            ])) {
               if (!utils_events::validateAccessFaculty($event) || !utils_events::isRolTeacher($event)) { return; }
               //Instanciar la clase management_factory
               utils_events::instanceFactory([
                  "dataEvent" => $event,
                  "typeEvent" => "created_announcements",
                  "dispatch" => 'create',
                  "enum_etities" => 'announcements'
               ]);
            }
      } catch (moodle_exception $e) {
         error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
      }
   }

   /**
    * Triggers an event when a discussion is deleted.
    */
    public static function discussion_deleted($event)
    {
       try {
             if (utils_events::validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\discussion_deleted"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!utils_events::validateAccessFaculty($event) || !utils_events::isRolTeacher($event)) { return; }
                //Instanciar la clase management_factory
                utils_events::instanceFactory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'delete',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
       }
    }

   /**
    * Triggers an event when a discussion is deleted.
    */
    public static function discussion_updated($event)
    {
       try {
             if (utils_events::validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\discussion_updated"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!utils_events::validateAccessFaculty($event) || !utils_events::isRolTeacher($event)) { return; }
                //Instanciar la clase management_factory
                utils_events::instanceFactory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'update',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
       }
    }

   /**
    * Triggers an event when a post_created.
    */
    public static function post_created($event)
    {
       try {
             if (utils_events::validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\post_created"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!utils_events::validateAccessFaculty($event)) { return; }
                //Instanciar la clase management_factory
                utils_events::instanceFactory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'create',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
       }
     }

   /**
    * Triggers an event when a post_updated.
    */
    public static function post_updated($event)
    {
       try {
             if (utils_events::validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\post_updated"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!utils_events::validateAccessFaculty($event) || !utils_events::isRolTeacher($event)) { return; }
                  $dataEvent = $event->get_data();
                  $post = $dataEvent['objectid'];
        
                  if (utils_events::isForumPostParent($post)) {
                     //Instanciar la clase management_factory
                     utils_events::instanceFactory([
                        "dataEvent" => $event,
                        "typeEvent" => "created_announcements",
                        "dispatch" => 'update',
                        "enum_etities" => 'announcements'
                     ]);
                  }
             }
       } catch (moodle_exception $e) {
          error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
       }
    }

   /**
    * Triggers an event when a post_deleted.
    */
    public static function post_deleted($event)
    {
       try {
             if (utils_events::validateAccessTypeEvent([
                "dataEvent" => $event,
                "typeEvent" => ["\\mod_forum\\event\\post_deleted"],
                "key" => "eventname",
                "methodName" => "get_data"
             ])) {
                if (!utils_events::validateAccessFaculty($event)) { return; }
                //Instanciar la clase management_factory
                utils_events::instanceFactory([
                   "dataEvent" => $event,
                   "typeEvent" => "created_announcements",
                   "dispatch" => 'delete',
                   "enum_etities" => 'announcements'
                ]);
             }
       } catch (moodle_exception $e) {
          error_log(self::TEXT_ERROR. $e->getMessage(). "\n");
       }
    }
}