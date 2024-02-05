<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado<cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\utils;

/**
  * Class Utils Events
*/
class utils_events
{
    public static function isStructureCourse($event,$action)
    {
        $get_grade_item = $event->get_grade_item();
        $dispatchStructure = new dispatch_structure();
        $isAvailableCoure = $dispatchStructure->executeEventHandler(
            [
                'courseid' => $get_grade_item->courseid,
                'itemid' => $get_grade_item->id,
                'action' => $action
            ],
            $event
        );
    }
}