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
 * Returns all the grades of course
 */
class course_grades extends base
{
    const LEVEL_TEACHING = 1;
    const TABLE_NAME = 'uplanner_evaluation';

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
        return "Returns all the grades of a course - uplannerconnect";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('uplannerconnect:uplanner_grades', 'local_uplannerconnect');
    }
}