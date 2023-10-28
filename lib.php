<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

use local_uplannerconnect\domain\hasResourceBeenInserted\has_resource_been_Inserted;

/**
 * Intercepts the navigation rendering.
 */
function local_uplannerconnect_extend_navigation(global_navigation $navigation)
{
    // global $PAGE;
    
    // //necesito saber si esta en modo edicion
    // $is_editing = $PAGE->user_is_editing();
    // $current_url = $PAGE->url;
    // $path = $current_url->get_path();
    // $path = explode('/', $path);
    // $isCourseView = isset($path[3]) ? $path[3] : '';
    // $path = $path[2];

    // if ($is_editing) {
    //     if (isset($path) && 
    //         $path === 'course' &&
    //         $isCourseView === 'view.php') {
    //         $hasResourceBeenInserted = new has_resource_been_Inserted();
    //         $hasResourceBeenInserted->isInsertedResource([
    //             'table' => 'resource',
    //             'idCourse' =>  $PAGE->course->id,
    //             'time' => time()
    //         ]);
    //     }
    // }
}