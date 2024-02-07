<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado<cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\utils;

use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\application\service\data_validator;
use moodle_exception;

/**
 * Dispatch ALL ITEMS STRUTURE COURSE
*/
class dispatch_structure implements dispatch_structure_interface
{
    const TABLE_NAME = 'uplanner_dispatch_tmp';
    const ACTION = 'create';
    const QUERY_DISPATCH_COURSE = "SELECT action  FROM {uplanner_dispatch_tmp} WHERE courseid = :courseid ORDER BY id ASC LIMIT 1";
    const DELETE_DISPATCH_COURSE = "DELETE FROM {uplanner_dispatch_tmp} WHERE courseid = :courseid AND action = :action";
    const LAST_ITEMS_COURSE = "SELECT id FROM {grade_items} WHERE courseid = :courseid AND itemtype NOT IN ('course', 'category') ORDER BY id DESC LIMIT 2";
    const NOT_AVAILABLE_ITEMS = ['course','category'];
    private $query;
    private $validator;

    /**
     * Construct
    */
    public function __construct()
    {
        $this->query = new moodle_query_handler();
        $this->validator= new data_validator();
    }

    /**
     * @param array $data
     * @param $event
     * @return void
     */
    public function executeEventHandler(array $data , $event): void
    {
        $get_grade_item = $event->get_grade_item();
        $itemtype = $get_grade_item->itemtype;

        if ($this->validator->validateKeysArrays([
            'keys' => ['courseid','itemid', 'action'],
            'data' => $data
            ]) && !in_array($itemtype, self::NOT_AVAILABLE_ITEMS))
        {
            $lastItems  = $this->lastItemsCourse($data['courseid']);
            if (!empty($lastItems)) {
                // Action Dispatch
                $isActionCreated = $this->isActionCreated($data);
                $actionCurrent = array_values(array_slice($isActionCreated, -1))[0] ?? [];
                $valueAction = $actionCurrent->action ?? $data['action'];
                $isCreate = $valueAction == self::ACTION;

                // Create Action if is first
                if (empty($isActionCreated)) {
                    $this->insertCourseStructure($data);
                }

                if ($isCreate) {
                    // Last Item
                    $lastItem = array_values(array_slice($lastItems, -1))[0] ?? [];
                    $valueLasItem = $lastItem->id ?? 0;

                    if ($data['itemid'] == $valueLasItem) {
                        $data['action'] = self::ACTION;
                        $this->executeTrigger($data,$event);
                    }
                }

                if (!$isCreate) {
                    $this->executeTrigger($data,$event);
                }
            }
        }
    }

    /**
     * Validate if the course now is in the database.
     *
     * @param array $data
     * @return array
     */
    private function isActionCreated(array $data) : array
    {
        return $this->getQueryArray([
            'sql' => self::QUERY_DISPATCH_COURSE,
            'params' => [
                'courseid' => $data['courseid']
            ]
        ]);
    }

    /**
     *  Insert New Record
    */
    private function insertCourseStructure(array $data): void
    {
        try {
            $isCreate = $data['action'] === self::ACTION;
            $this->query->insert_record_db([
                'table' => self::TABLE_NAME,
                'data' => [
                    'courseid' => strval($data['courseid']),
                    'action' => strval($data['action']),
                    'itemid' => strval($isCreate ? $data['itemid'] : '0'),
                    'updated_item' => date("Y/m/d")
                ]
            ]);
        } catch(moodle_exception $e) {
            error_log('Caught exception: '.  $e->getMessage(). "\n");
        }
    }

    /**
     * Delete Drop 
    */
    public function deleteRecord(array $data)
    {
        try {
            $isCreate = $data['action'] === self::ACTION;;
            $this->query->executeQuery(
                self::DELETE_DISPATCH_COURSE,
                [
                    'courseid' => strval($data['courseid']),
                    'action' => strval($data['action'])
                ]
            );
        } catch (moodle_exception $e) {
            error_log('Caught exception: '.  $e->getMessage(). "\n");
        }
    }

    /**
     * @param $courseid
     * @return array
     */
    private function lastItemsCourse($courseid) : array
    {
        return $this->getQueryArray([
            'sql' => self::LAST_ITEMS_COURSE,
            'params' => [
                'courseid' => $courseid
            ]
        ]);
    }

    /**
     * @param array $data
     * @return array
     */
    private function getQueryArray(array $data): array
    {
        $rest = [];
        try {
            if ($this->validator->validateKeysArrays([
                'keys' => ['sql','params'],
                'data' => $data,
            ])) {
                $response = $this->query->executeQuery(
                    $data['sql'],
                    $data['params']
                );

                if (!empty($response)) {
                    $rest = $response;
                }
            }
        } catch (moodle_exception $e) {
            error_log('Caught exception: '.  $e->getMessage(). "\n");
        }

        return $rest;
    }

    /**
     * @param $data
     * @param $event
     * @return void
     */
    private function executeTrigger($data,$event): void
    {
        has_active_structure::triggerEvent([
            'event' => $event,
            'dataCourse' => $data
        ]);
    }
}