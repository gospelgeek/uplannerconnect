<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\utils;

use local_uplannerconnect\application\repository\moodle_query_handler;
USE local_uplannerconnect\domain\course\usecases\course_utils;
use local_uplannerconnect\event\course_structure;
use local_uplannerconnect\domain\course\course_translation_data;
use local_uplannerconnect\application\repository\course_evaluation_structure_repository;
use moodle_exception;
use \stdClass;

class has_active_structure implements structure_interface
{
    private $_query;
    private $course_utils;
    private $translationData;
    private $repository;
    private $dispatch_structure;
    const CATEGORY_FATHER_DEFAULT = "NOTAS";
    const QUERY_CATEGORYS_ITEMS = "SELECT t1.id, t2.fullname , t1.itemname, t1.itemtype, t1.categoryid FROM {grade_items} AS t1 INNER JOIN {grade_categories} AS t2 ON t2.id = t1.categoryid WHERE t1.courseid = :courseid AND t1.itemtype NOT IN ('course', 'category') ORDER BY t1.id DESC";
    const QUERY_IS_JSON_REPEAT = "SELECT id FROM {uplanner_evaluation} WHERE  json::jsonb->'evaluationGroups' @> :json::jsonb ORDER BY id DESC LIMIT 1";
    const QUERY_WEIGHT_GRADES = "SELECT t1.id AS category_id, t1.fullname AS category_name, COUNT(t2.id) AS item_count, SUM(t3.finalgrade) AS total_grade, MAX(t3.finalgrade) AS max_grade, t1.aggregation FROM {grade_categories} AS t1 INNER JOIN {grade_items} AS t2 ON t2.categoryid = t1.id INNER JOIN  {grade_grades} AS t3 ON t3.itemid = t2.id WHERE t1.courseid = :courseid AND t2.itemtype NOT IN ('course', 'category') AND t3.userid = :userid GROUP BY t1.id, t1.fullname";

    /**
     * Construct
    */
    public function __construct()
    {
        $this->_query = new moodle_query_handler();
        $this->course_utils = new course_utils();
        $this->translationData = new course_translation_data();
        $this->repository = new course_evaluation_structure_repository();
        $this->dispatch_structure = new dispatch_structure();
    }

    public function generateHandler(array $data)
    {
        $dataCourse = $data['dataCourse'];
        $event = $data['event'];
        $dataStructure = $this->course_utils->resourceGradeItemCreated([
            'dataEvent' => $event,
            'dispatch' => $dataCourse['action']
        ]);
        $dataTranslate = $this->translationData->createCommonDataEvaluation($dataStructure);
        $allItems = $this->generateCategoryItems([
            'courseid' =>  $dataCourse['courseid'],
            'gradeItem' => $event->get_grade_item(),
            'event' => $event
        ]);
        $dataTranslate['evaluationGroups'] = $allItems;

        // Validate if no repeat json
        $isRepeatJson = $this->_query->executeQuery(
            self::QUERY_IS_JSON_REPEAT,
            [
                "json" => json_encode($allItems)
            ]
        );
        
        if (empty($isRepeatJson)) $this->repository->saveDataBD($dataTranslate);
    }
    
    /**
     *  Trigger event resource
    */
    public static function triggerEvent(array $data)
    {
        // Trigger academic period updated event
        $context = \context_system::instance();
        $params = array(
            "objectid" => 128,
            "context" => $context,
            "other" => $data
        );
        $event = course_structure::create($params);
        $event->trigger();
    }

    /**
     * @param array $data
     * @return array
     */
    private function generateCategoryItems(array $data) : array
    {
        $response = [];
        try {
            $courseid = $data['courseid'];
            $gradeItem = $data['gradeItem'];
            $event =  $data['event'];
            $allCategorys = [];

            $allItems = $this->_query->executeQuery(
                self::QUERY_CATEGORYS_ITEMS,
                ['courseid' => $courseid]
            );

            if (!empty($allItems)) {
                foreach ($allItems as $item) {
                    $allCategorys[] = strval($item->fullname ?? self::CATEGORY_FATHER_DEFAULT);
                }
                $allCategorys = array_unique($allCategorys);
                foreach ($allCategorys as $category) {
                    $name_ = $this->transformNameCategory($category);
                    $nameCode = $this->shortCategoryName($name_);
                    $dataCategory = [
                        "evaluationGroupCode" => strtoupper($nameCode),
                        "evaluationGroupName" => strtoupper(substr($nameCode, 0, 30)),
                        "evaluations" => $this->generateAllItems([
                            'allItems' => $allItems,
                            'gradeItem' => $gradeItem,
                            'category' => $category,
                            'event' => $event
                        ])
                    ];
                    array_push($response, $dataCategory);
                }
            }
        } catch (moodle_exception $e) {
            error_log('Caught exception: '.  $e->getMessage(). "\n");
        }

        return $response;
    }

    /**
     * @param string $categoryFullName
     * @return string
     */
    public function transformNameCategory(string $categoryFullName) : string
    {
        $short = $categoryFullName;
        if ($categoryFullName == '?' ||
            strlen($categoryFullName) == 0) {
            $short = self::CATEGORY_FATHER_DEFAULT;
        }

        return $short;
    }

    /**
     * @param string $categoryFullName
     * @return string
     */
    public function shortCategoryName(string $categoryFullName) : string
    {
        $noSpaces = str_replace(' ', '', $categoryFullName);
        return substr($noSpaces, 0, 10);
    }

    /**
     * @param array $data
     * @return array
     */
    private function generateAllItems(array $data) : array
    {
        $response = [];
        try {
            $allItems = $data['allItems'];
            $gradeItem = $data['gradeItem'];
            $categoryName = $data['category'];
            $event = $data['event'];

            if (!empty($allItems)) {
                foreach ($allItems as $item) {
                    if ($item->fullname == $categoryName) {
                        $dataItem = [
                            "evaluationId" => intval($item->id),
                            "evaluationName" => strval($item->itemname ?? ''),
                            "weight" => $this->getWeight($event,$item)
                        ];
                        array_push($response, $dataItem);
                    }
                }
            }
        } catch (moodle_exception $e) {
            error_log('Caught exception: '.  $e->getMessage(). "\n");
        }

        return $response;
    }

    /**
     * 
     * Return weight of category based on aggregation type
     * 
     * @param object $gradeItem
     * @return float
     */
    private function getWeight($event,$item)
    {
        $weight = 0.0;
        // $gradeItem = $event->get_grade_item();
        // $courseId = $gradeItem->courseid;
        // $categoryId = $gradeItem->categoryid;
        // $userid = $item->userid;
        // $categoryName = $item->fullname ?? '?';

        // if (!isset($userid)) {
        //     return $weight;
        // }

        // $allWeight = $this->_query->executeQuery(
        //     self::QUERY_WEIGHT_GRADES,
        //     [
        //         'courseid' => $courseId,
        //         'userid' => $userid
        //     ]
        // );

        // if (!empty($allWeight)) {
        //     $filterByName = function($weight) use ($categoryName) {
        //         return $weight->category_name == $categoryName;
        //     };
        
        //     $filteredWeights = array_filter($allWeight, $filterByName);
        //     $firstWeight = reset($filteredWeights);

        //     if ($firstWeight !== false) {
           
        //         if ($firstWeight->item_count <= 0 ||
        //             $firstWeight->total_grade <= 0) {
        //             return $weight;
        //         }

        //         if ($firstWeight->aggregation == 11) {
        //             $weight = $firstWeight->total_grade / $firstWeight->item_count;
        //         }

        //         if ($firstWeight->aggregation == 10) {
        //             if (property_exists($gradeItem, 'aggregationcoef2') && $gradeItem->aggregationcoef2 != 0) {
        //                 $weight = $gradeItem->aggregationcoef2;
        //             } elseif (property_exists($gradeItem, 'aggregationcoef')) {
        //                 $weight = $gradeItem->aggregationcoef;
        //             }
        //         }
        //     }
        // }
                
        return (float)$weight;
    }
}