<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @author      Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\repository;

/**
 * debe coincidir con el de los tipos de cliente en UplannerClientType
*/
class repository_type
{
    const STATE_DEFAULT = 0;  //Estado por defecto
    const STATE_SEND = 1;     //Estado de envío
    const STATE_ERROR = 2;    //Estado de error
    const GRADE = 'grade';

    const ACTIVE_REPOSITORY_TYPES = [
        'grade' => course_notes_repository::class
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