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
    const QUERY_DISPATCH_COURSE = "SELECT courseid FROM {uplanner_dispatch_tmp} WHERE courseid = :courseid AND action = :action AND itemid = :itemid";
    const DELETE_DISPATCH_COURSE = "DELETE FROM {uplanner_dispatch_tmp} WHERE courseid = :courseid AND action = :action AND itemid = :itemid";
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

    public function executeEventHandler(array $data , $event)
    {
        if ($this->validator->validateKeysArrays([
            'keys' => ['courseid','itemid', 'action'],
            'data' => $data,
        ]))  {
            $isCourseActive = $this->isCourseActive($data);
            if ($isCourseActive) {
                // Insert New Record
                $this->insertCourseStruture($data);
                has_active_structure::triggerEvent([
                    'event' => $event,
                    'dataCourse' => $data
                ]);
            }
        }
    }

    /**
     * Validate if the course no is in the database.
     * 
     * @return bool
    */
    private function isCourseActive(array $data) : bool
    {
        $haveCourseActive =  false;
        try {
            $isCreate = $data['action'] === self::ACTION;
            $haveCourseActive = empty($this->query->executeQuery(
                self::QUERY_DISPATCH_COURSE,
                [
                    'courseid' => strval($data['courseid']),
                    'action' => strval($data['action']),
                    'itemid' => strval( $isCreate ? $data['itemid'] : '0')
                ]
            ));
        } catch (e) {
            error_log('Caught exception: '.  $e->getMessage(). "\n");
        }

        return $haveCourseActive;
    }

    /**
     *  Insert New Record
    */
    private function insertCourseStruture(array $data)
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
        } catch(e) {
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
                    'action' => strval($data['action']),
                    'itemid' => strval($isCreate ? $data['itemid'] : '0')
                ]
            );
        } catch (e) {
            error_log('Caught exception: '.  $e->getMessage(). "\n");
        }
    }
}