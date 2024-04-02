<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\utils;

use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\domain\course\usecases\course_utils;
use local_uplannerconnect\event\course_grades;
use local_uplannerconnect\domain\course\course_translation_data;
use local_uplannerconnect\application\repository\course_notes_repository;
use local_uplannerconnect\event\course_structure;
use moodle_exception;
use \stdClass;

/**
 * Class has_active_grades
 */
class has_active_grades implements structure_interface
{
    private $courseUtils;
    private $courseTraslate;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->courseUtils = new course_utils();
        $this->courseTraslate =  new course_translation_data();
    }

    /**
     * @param array $data
     * @return void
     */
    public function generateHandler(array $data)
    {
        $dataEventGrade =  $data['dataGrades'];
        $courseData = $this->courseUtils->resourceUserGraded([
            'dataEvent' => $data['event'],
            'dispatch' => $dataEventGrade['action']
        ]);

        if (!empty($courseData)) {
            error_log("hylee5 " . print_r($courseData, true));
        }
    }

    /**
     *  Trigger event resource
     */
    public static function triggerEvent(array $data)
    {
        // Trigger academic period updated event
        $context = \context_system::instance();
        $params = [
            "objectid" => 129,
            "context" => $context,
            "other" => $data
        ];
        $event = course_grades::create($params);
        $event->trigger();
    }
}