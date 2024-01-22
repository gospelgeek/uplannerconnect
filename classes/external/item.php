<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\external;

defined('MOODLE_INTERNAL') || die();

use core\persistent;

/**
 * Class item
 *
 * Persistent model representing a single log.
 */
class item extends persistent
{
    const TABLE = '';

    /** @var array The model data. */
    private $data = array();

    /**
     * Set data
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'data' => [
                'type' => PARAM_RAW,
            ],
            'message' => [
                'type' => PARAM_TEXT,
            ],
            'done' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ]
        ];
    }
}