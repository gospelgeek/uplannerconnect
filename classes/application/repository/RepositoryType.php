<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/CourseNotesRepository.php');

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Contiene los tipos de repositorios existentes, el key en ACTIVE_REPOSITORY_TYPES
 * debe coincidir con el de los tipos de cliente en UplannerClientType
*/
class RepositoryType
{
    const GRADE = 'grade';

    const ACTIVE_REPOSITORY_TYPES = [
        'grade' => CourseNotesRepository::class
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