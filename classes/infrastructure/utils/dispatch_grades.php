<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado<cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\utils;

use moodle_exception;

/**
 * Dispatch ALL GRADES OF COURSE
 */
class dispatch_grades implements dispatch_structure_interface
{
    /**
     * Construct
     */
    public function __construct()
    {}

    /**
     * @param array $data
     * @param $event
     * @return void
     */
    public function executeEventHandler(array $data , $event): void
    {
       try {
           $this->executeTrigger($data,$event);
       } catch (moodle_exception $e) {
           error_log('Caught exception: '.  $e->getMessage(). "\n");
       }
    }

    /**
     * @param $data
     * @param $event
     * @return void
     */
    private function executeTrigger($data,$event): void
    {
        has_active_grades::triggerEvent([
            'event' => $event,
            'dataGrades' => $data
        ]);
    }
}