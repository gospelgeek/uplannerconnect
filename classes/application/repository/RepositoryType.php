<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\repository;

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Contiene los tipos de repositorios existentes, el key en ACTIVE_REPOSITORY_TYPES
 * debe coincidir con el de los tipos de cliente en UplannerClientType
*/
class RepositoryType
{
    const STATE_DEFAULT = 0;  //Estado por defecto
    const STATE_SEND = 1;     //Estado de envío
    const STATE_ERROR = 2;    //Estado de error
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