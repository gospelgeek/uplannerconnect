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
 * Dispatch ALL GRADES OF COURSE
 */
class dispatch_grades implements dispatch_structure_interface
{
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