<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

/**
 * Returns all the grading items of a course
*/
class course_structure extends base
{
    const LEVEL_TEACHING = 1;
    const TABLE_NAME = 'uplanner_dispatch_tmp';

    /**
     * Init method.
     *
     * @return void
     */
    protected function init()
    {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = self::TABLE_NAME;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "Returns all the grading items of a course - uplannerconnect";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('uplannerconnect:uplanner_dispatch_tmp', 'local_uplannerconnect');
    }
}