<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\external;

defined('MOODLE_INTERNAL') || die();

use core\external\persistent_exporter;

/**
 * Class item_exporter
 */
class item_exporter extends persistent_exporter {

    /**
     * Returns the specific class the persistent should be an instance of.
     *
     * @return string
     */
    protected static function define_class() {
        return item::class;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }
}
