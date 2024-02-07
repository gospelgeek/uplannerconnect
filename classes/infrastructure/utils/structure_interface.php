<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado<cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\utils;

/**
 * Structure_interface
*/
interface structure_interface
{
    public function generateHandler(array $data);
    public static function triggerEvent(array $data);
}