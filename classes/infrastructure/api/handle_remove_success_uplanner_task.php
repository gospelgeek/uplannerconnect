<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\api;

use local_uplannerconnect\application\repository\repository_type;
use moodle_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @description Handle remove success uPlanner task
 */
class handle_remove_success_uplanner_task
{
    /**
     * Handle process remove state success registers uPlanner
     *
     * @return void
     */
    public function process() {
        foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
            /*
             * TODO:
             * 1. Get logs Uplanner api
             * 2. Compare and update X day
             * 3. Get logs in custom db and send email
             * 4. Register logs X day
             * 5. Clean tables
             */
            try {
                $repository = new $repository_class($type);
                $repository->add_log_data();
                $repository->delete_data_bd(repository_type::STATE_SEND);
                $repository->delete_data_bd(repository_type::STATE_ERROR);
                $repository->delete_data_bd(repository_type::STATE_DEFAULT);
            } catch (moodle_exception $e) {
                error_log('handle_remove_success_uplanner_task - process: ' . $e->getMessage() . "\n");
            }
        }
    }
}