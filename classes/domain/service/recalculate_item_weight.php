<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


namespace local_uplannerconnect\domain\service; 

use local_uplannerconnect\application\repository\moodle_query_handler;
use moodle_exception;

/**
 * Recalculate Item Weight
 */
class recalculate_item_weight
{    
    const IS_NATURAL = 13;
    const TABLE_CATEGORY = 'course_categories';

    /**
     * Recalculate Item Weight
     * 
     * @param array $data
     * @return void
     */
    public function recalculate_weight_evaluation(array $data) : void
    {
        try {
            if (!empty($data)) {
                error_log('No le llego el id del curso');
                return;
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
    }
}