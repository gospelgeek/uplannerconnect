<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\plugin_config;

/**
 * Event observers
 */
class events_observers
{
    // Rutas de los eventos.
    CONST ROUTE_HANDLER_EVENT_1 = 'local_uplannerconnect\infrastructure\event\dispatch_event_handler';
    
    const EVENTS_OBSERVERS = [
        [
            'eventname' => '\core\event\user_graded',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_deleted',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_item_created',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\grade_item_updated',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\local_uplannerconnect\event\resource_file',
            'isLocal' => true,
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\course_module_created',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\course_module_updated',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\core\event\course_module_deleted',
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\mod_forum\event\discussion_created',
            'isForum' => true,
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        [
            'eventname' => '\mod_forum\event\discussion_deleted',
            'isForum' => true,
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        // [
        //     'eventname' => '\mod_forum\event\post_created',
        //     'isForum' => true,
        //     'includefile' => self::ROUTE_HANDLER_EVENT_1,
        // ],
        [
            'eventname' => '\mod_forum\event\post_updated',
            'isForum' => true,
            'includefile' => self::ROUTE_HANDLER_EVENT_1,
        ],
        // [
        //     'eventname' => '\mod_forum\event\post_deleted',
        //     'isForum' => true,
        //     'includefile' => self::ROUTE_HANDLER_EVENT_1,
        // ]
    ];
}