<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @author      Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\repository;

/**
 * Register the active repository types, they must match the client types in uplanner_client_type
*/
class repository_type
{
    /**
     * Log is created
     */
    const STATE_DEFAULT = 0;

    /**
     * Log is send to uPlanner
     */
    const STATE_SEND = 1;

    /**
     * Log don't send to uPlanner
     */
    const STATE_ERROR = 2;

    /**
     * Log with error in uPlanner
     */
    const STATE_UP_ERROR = 3;

    const LIST_STATES = [
        'send' => 1
    ];

    const ACTIVE_REPOSITORY_TYPES = [
        'evaluation_structure' => course_evaluation_structure_repository::class,
        'grade' => course_notes_repository::class,
        'material' => material_repository::class,
        'announcement' => announcements_repository::class
    ];

    /**
     * Get repository by type
     *
     * @param $type
     * @return string
     */
    public static function getClass($type): string
    {
        return self::ACTIVE_REPOSITORY_TYPES[$type];
    }
}