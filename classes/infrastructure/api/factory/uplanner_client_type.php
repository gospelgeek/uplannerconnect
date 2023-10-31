<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\api\factory;

use local_uplannerconnect\infrastructure\api\client\uplanner_client_announcement;
use local_uplannerconnect\infrastructure\api\client\uplanner_client_evaluation_structure;
use local_uplannerconnect\infrastructure\api\client\uplanner_client_grade;
use local_uplannerconnect\infrastructure\api\client\uplanner_client_material;

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Contiene los tipos de clientes en Uplanner
 */
class uplanner_client_type
{
    const MATERIAL = 'material';
    const GRADE = 'grade';
    const ANNOUNCEMENT = 'announcement';
    const EVALUATION_STRUCTURE = 'evaluation_structure';

    const CLIENT_TYPES = [
        'grade' => uplanner_client_grade::class,
        'evaluation_structure' => uplanner_client_evaluation_structure::class,
        'material' => uplanner_client_material::class,
        'announcement' => uplanner_client_announcement::class
    ];

    /**
     * Get client by type
     *
     * @param $type
     * @return string
     */
    public static function get_class($type): string
    {
        return self::CLIENT_TYPES[$type];
    }
}