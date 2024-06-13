<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\utils;

use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\domain\course\usecases\course_utils;
use local_uplannerconnect\domain\course\course_translation_data;
use local_uplannerconnect\application\repository\course_notes_repository;
use local_uplannerconnect\event\course_grades;
use moodle_exception;
use \stdClass;

/**
 * Class has_active_grades
 */
class has_active_grades implements structure_interface
{
    const CATEGORY_FATHER_DEFAULT = "NOTAS";
    const GET_ALL_GRADES_USER = "SELECT t1.id, t2.fullname , t1.itemname , t3.finalgrade, t1.timecreated, t2.aggregation, t2.depth
                                 FROM {grade_items} AS t1 
                                 INNER JOIN {grade_categories} AS t2 ON t2.id = t1.categoryid 
                                 INNER JOIN  {grade_grades} AS t3 ON t3.itemid = t1.id 
                                 WHERE t1.courseid = :courseid AND 
                                 t1.itemtype NOT IN ('course', 'category') 
                                 AND t3.aggregationstatus IN ('used','unknown') AND
                                 t3.finalgrade is not NULL AND 
                                 t3.userid = :userid ORDER BY t1.id DESC";
    private $courseUtils;
    private $courseTraslate;
    private $_query;
    private $has_structure;
    private $repository;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->courseUtils = new course_utils();
        $this->courseTraslate =  new course_translation_data();
        $this->_query =  new moodle_query_handler();
        $this->has_structure = new has_active_structure();
        $this->repository = new course_notes_repository();
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

            if ($dataEventGrade['action'] == 'create') {
                $nameCategoryCreated = $courseData['evaluationGroupCode'] !== self::CATEGORY_FATHER_DEFAULT;
                if ($allItems) {
                    $firstItem = reset($allItems);
                    if ($firstItem && (int)$firstItem->id !== (int)$courseData['evaluationId']) {
                        $allItems[] = (object) [
                            'id' => $courseData['evaluationId'],
                            'itemname' => $courseData['evaluationName'],
                            'finalgrade' => $courseData['value'],
                            'fullname' => $nameCategoryCreated? $courseData['evaluationGroupCode'] : '?'
                        ];
                    }
                }
            }

            $allCategory = $this->generateCategoryGroups([
                'allitems' => $allItems
            ]);
            $allGrades = $this->gradesByCategorys([
                'allItems' => $allItems,
                'uniqueCategory' => $allCategory
            ]);
            // traslated data
            $dataTraslated['evaluationGroups'] = $allGrades;
            // save json
            $this->repository->saveDataBD($dataTraslated);
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
     * @param array $data
     * @return int
     */
    private function getAggregationFather(array $data): int
    {
        $allItems = $data['allitems'];
        $categoryDepth = 1;
        $aggregation = 11;

        $filterByName = function($item) use ($categoryDepth) {
            return isset($item->depth) && $item->depth == $categoryDepth;
        };
        $filter = array_filter($allItems, $filterByName);

        if (count($filter) > 0) {
            $firstItem = reset($filter);
            $aggregation = $firstItem->aggregation ?? 11;
        }

        return intval($aggregation);
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

    /**
     * @param array $data
     * @return array
     */
    private function gradesByCategorys(array $data) : array
    {
        $allItems = $data['allItems'];
        $uniqueCategory = $data['uniqueCategory'];
        $categoryArray = [];
        $allGrades = [];

        foreach ($uniqueCategory as $categoty) {
            $categoryArray[$categoty] = [];
        }

        foreach ($allItems as $item) {
            $categoryArray[$item->fullname][] = [
                "evaluationId" => intval($item->id),
                "value" => $item->finalgrade,
                "evaluationName" => $item->itemname,
                "date" => date('Y-m-d' , ($item->timecreated ?? time())),
                "isApproved" => $item->finalgrade >= 3,
            ];
        }

        $aggregation = $this->getAggregationFather([
            'allitems' => $allItems
        ]);

        foreach ($uniqueCategory as $category) {
            $name_ = $this->has_structure->transformNameCategory($category);
            $nameCode = $this->has_structure->shortCategoryName($name_);
            $allGrades[] = [
                "evaluationGroupCode" => strtoupper($nameCode),
                "average" => $this->getWeight($aggregation,count($uniqueCategory)),
                "grades" => $categoryArray[$category]
            ];
        }

        return $allGrades;
    }

    /**
     * 
     * Return weight of category based on aggregation type
     * 
     * @param object $gradeItem
     * @return string
     */
    private function getWeight($category,$sizeItems)
    {
        $weight = "0";
        
        if (!isset($category) || 
            $category <= 0
        ) {
            return $weight;
        }

        if ($sizeItems > 0 && 
            $category == 11
        ) {
            $weight = (string)(1 / $sizeItems);
        }

        return (string)$weight;
    }
}