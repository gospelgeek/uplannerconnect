<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\task;

defined('MOODLE_INTERNAL') || die();

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description TODO:
 */
class handle_error_uplanner_task extends \core\task\scheduled_task
{
    /**
     * @inerhitdoc
     */
    public function get_name()
    {
        return get_string('syncerroruplannertask', 'local_uplannerconnect');
    }

    /**
     * @inerhitdoc
     */
    public function execute() {
        global $CFG;
        require_once(__DIR__ . '/../infrastructure/api/HandleSendUplannerTask.php');
        try {
            mtrace("Test");
            //$handleUplannerTask = new HandleSendUplannerTask();
            //$handleUplannerTask->procces(2, 5); // 2 is state error
        } catch (Exception $e) {
            //TODO: current log
        }
    }
}