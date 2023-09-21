<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../client/UplannerClientAnnouncement.php');
require_once(__DIR__ . '/../client/UplannerClientEvaluationStructure.php');
require_once(__DIR__ . '/../client/UplannerClientGrade.php');
require_once(__DIR__ . '/../client/UplannerClientMaterial.php');

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Contiene los tipos de clientes en Uplanner
 */
class UplannerClientType
{
    const MATERIAL = 'material';
    const GRADE = 'grade';
    const ANNOUNCEMENT = 'announcement';
    const EVALUATION_STRUCTURE = 'evaluation_structure';

    const CLIENT_TYPES = [
        'material' => UplannerClientMaterial::class,
        'grade' => UplannerClientGrade::class,
        'announcement' => UplannerClientAnnouncement::class,
        'evaluation_structure' => UplannerClientEvaluationStructure::class
    ];

    /**
     * Get client by type
     *
     * @param $type
     * @return string
     */
    public static function getClass($type): string
    {
        return self::CLIENT_TYPES[$type];
    }
}