<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Evento que se lanza cuando se inserta un registro
 * en la tabla de mdl_files 
*/
class resource_file extends \core\event\base
{
    const LEVEL_TEACHING = 1;
    const TABLE_NAME = 'files';

    /**
     * Init method.
     *
     * @return void
     */
    protected function init()
    {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = self::TABLE_NAME;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' entered the search course module of the plugin uplannerConnect.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('uplannerconnect:mod_resource_file', 'local_uplannerconnect');
    }
}