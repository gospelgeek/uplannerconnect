<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\external;

defined('MOODLE_INTERNAL') || die();

use external_api;

/**
 * Class api
 *
 * Provides an external API.
 * Each external function is implemented in its own trait. This class
 * aggregates them all.
 */
class api extends external_api {
    use edit_log;
}
