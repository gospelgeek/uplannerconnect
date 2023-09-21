<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/UplannerClientType.php');

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Fábrica para crear los diferentes tipos de clientes en Uplanner
 */
class UplannerClientFactory
{
    /**
     * Create a client type
     *
     * @param string $type
     * @return void
     */
    public function create(string $type) {
        $client = null;
        try {
            $class = UplannerClientType::getClass($type);
            $client = new $class();

        } catch (Exception $e) {
           //TODO: management structure
        }

        return $client;
    }
}