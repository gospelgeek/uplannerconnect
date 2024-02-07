<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado<cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\utils;

/**
 * Interface Dispatch Event
*/
interface dispatch_structure_interface
{
    public function executeEventHandler(array $data, $event) : void;
}