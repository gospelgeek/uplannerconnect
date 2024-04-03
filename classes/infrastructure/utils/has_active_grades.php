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
use moodle_exception;
use \stdClass;

/**
 * Class has_active_grades
 */
class has_active_grades implements structure_interface
{
    const CATEGORY_FATHER_DEFAULT = "NOTAS";
    const GET_ALL_GRADES_USER = "SELECT t1.id, t2.fullname , t1.itemname , t3.finalgrade 
                                 FROM {grade_items} AS t1 
                                 INNER JOIN {grade_categories} AS t2 ON t2.id = t1.categoryid 
                                 INNER JOIN  {grade_grades} AS t3 ON t3.itemid = t1.id 
                                 WHERE t1.courseid = :courseid AND 
                                 t1.itemtype NOT IN ('course', 'category') 
                                 AND t3.aggregationstatus IN ('used') AND 
                                 t3.userid = :userid ORDER BY t1.id DESC";
    private $courseUtils;
    private $courseTraslate;
    private $_query;
    private $has_structure;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->courseUtils = new course_utils();
        $this->courseTraslate =  new course_translation_data();
        $this->_query =  new moodle_query_handler();
        $this->has_structure = new has_active_structure();
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
        $allItems = $this->_query->executeQuery(
            self::GET_ALL_GRADES_USER,
            [
                'courseid' => $dataEventGrade['courseid'],
                'userid' => $dataEventGrade['iduser']
            ]
        );

        if (!empty($courseData) && !empty($allItems)) {
            $dataTraslated = $this->courseTraslate->converDataJsonUplanner([
                'data' => $courseData,
                'typeEvent' => 'user_graded'
            ]);
            $allItems[] = [
                'id' => $courseData['evaluationId'],
                'itemname' => $courseData['evaluationName'],
                'finalgrade' => $courseData['value'],
                'fullname' => $courseData['evaluationGroupCode']
            ];
            $allCategory = $this->generateCategoryGroups([
                'allitems' => $allItems
            ]);
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function generateCategoryGroups(array $data): array
    {
        $uniqueCategory = [];
        $allItems = $data['allitems'];
        foreach ($allItems as $item) {
            $uniqueCategory[] = strval($item->fullname ?? self::CATEGORY_FATHER_DEFAULT);
        }
        $uniqueCategory = array_unique($uniqueCategory);
        return $uniqueCategory;
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