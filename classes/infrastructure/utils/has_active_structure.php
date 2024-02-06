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
    const QUERY_COURSE_ITEMS = "SELECT t2.id,t2.itemname FROM {grade_items} AS t2 WHERE t2.courseid = :courseid AND t2.itemtype NOT IN ('course', 'category') AND hidden = 0";

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
        $allItems =  $this->getAllItems([
            'courseid' =>  $dataCourse['courseid'],
            'gradeItem' => $event->get_grade_item()
        ]);
        $dataTranslate['evaluationGroups'][0]['evaluations'] = $allItems;
        $this->repository->saveDataBD($dataTranslate);
        $this->dispatch_structure->deleteRecord($dataCourse);
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
     * Get all items of course
    */
    private function getAllItems(array $data) : array
    {
        $response = [];
        try {
            $courseid = $data['courseid'];
            $gradeItem = $data['gradeItem'];
            $allItems = $this->_query->executeQuery(
                self::QUERY_COURSE_ITEMS,
                ['courseid' => $courseid]
            );

            if (!empty($allItems)) {
                foreach ($allItems as $item) {
                    $dataItem = [
                        "evaluationId" => $item->id,
                        "evaluationName" => strval($item->itemname ?? ''),
                        "weight" => floatval($this->course_utils->getWeight($gradeItem) ?? 0)
                    ];
                    array_push($response, $dataItem);
                }
            }
        } catch (moodle_exception $e) {
            error_log('Caught exception: '.  $e->getMessage(). "\n");
        }

        return $response;
    }
}