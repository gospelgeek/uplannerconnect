<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\api;

use local_uplannerconnect\application\repository\RepositoryType;

defined('MOODLE_INTERNAL') || die;

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
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
        foreach (RepositoryType::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
            try {
                $repository = new $repository_class($type);
                //$repository->remove(RepositoryType::STATE_SEND);
            } catch (\Exception $e) {
                error_log('handle_remove_success_uplanner_task - process: ' . $e->getMessage() . "\n");
            }
        }
    }
}