<?php
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
class HandleInitUplannerTask extends \core\task\scheduled_task
{
    /**
     * @inerhitdoc
     */
    public function get_name()
    {
        return get_string('syncinituplannertask', 'local_uplannerconnect');
    }

    /**
     * @inerhitdoc
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/../infrastructure/api/HandleSendUplannerTask.php');
        try {
            $handleUplannerTask = new HandleSendUplannerTask();
            $handleUplannerTask->procces(0); // 0 is state default
        } catch (Exception $e) {
            //TODO: current log
        }
    }
}