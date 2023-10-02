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
 * @description Handle remove success uplanner task
 */
class handle_remove_success_uplanner_task
{
    /**
     * Handle process remove state success registers Uplanner
     *
     * @return void
     */
    public function process() {
        foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
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