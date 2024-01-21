<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\external;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context_user;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use local_uplannerconnect\infrastructure\api\handle_send_uplanner_edit;
use local_uplannerconnect\plugin_config\plugin_config;
use required_capability_exception;
use restricted_context_exception;
use stdClass;

/**
 * trait edit_log
 *
 * Trait implementing the external function local_uplannerconnect_edit_log.
 */
trait edit_log {

    /**
     * Describes the structure of parameters for the function.
     *
     * @return external_function_parameters
     */
    public static function edit_log_parameters() {
        return new external_function_parameters([
            'data' => new external_value(PARAM_RAW, 'Invalid data')
        ]);
    }

    /**
     * Main function
     *
     * @param $data
     * @return stdClass
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     * @throws coding_exception
     */
    public static function edit_log($data) {
        global $USER, $PAGE;

        $context = context_user::instance($USER->id);
        self::validate_context($context);
        require_capability('local/' . plugin_config::PLUGIN_NAME . ':index', $context);
        $data = self::validate_parameters(self::edit_log_parameters(), compact('data'));
        $data = $data['data'];
        $data = json_decode($data, true);
        $handle = new handle_send_uplanner_edit('resend');
        $row = $handle->process($data);

        return self::getItem($row);
    }

    /**
     * Get data response
     *
     * @param $log
     * @return stdClass
     * @throws coding_exception
     */
    public static function getItem($log)
    {
        $message = get_string('uplanner_log_dont_processing', 'local_uplannerconnect');;
        $done = false;
        if (intval($log->success) === 1 && intval($log->is_sucessful) === 1) {
            $message = get_string('uplanner_log_processing', 'local_uplannerconnect');;
            $done = true;
        }
        $time = time();
        $item = new stdClass();
        $item->data = json_encode($log);
        $item->done = $done;
        $item->message = $message;
        $item->id = intval($log->id);
        $item->timecreated =  $time;
        $item->timemodified = $time;
        $item->usermodified = $time;

        return $item;
    }

    /**
     * Get structure
     *
     * @return external_single_structure
     */
    public static function edit_log_returns()
    {
        return item_exporter::get_read_structure();
    }
}
