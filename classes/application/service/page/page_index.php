<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\service\page;

use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;
use moodle_url;
use stdClass;

/**
 * Data Page Index
 */
class page_index extends page
{
    const DATA_SEND_UPLANNER = "SELECT * FROM {uplanner_log}";

    /**
     * @var string
     */
    protected $template = 'local_uplannerconnect/index';

    /**
     * @inerhitdoc
     */
    public function get_url()
    {
        $this->url = new moodle_url('/local/' . plugin_config::PLUGIN_NAME . '/index.php');
    }

    /**
     * @inerhitdoc
     */
    public function get_data()
    {
        $data = new stdClass();
        try {
            $data->row = [];
            $rows = $this->query_handler->executeQuery(self::DATA_SEND_UPLANNER);
            foreach ($rows as $row) {
                $data->row[] = $row;
            }
            $data->ulItems = $this->urls;
        } catch (moodle_exception $e) {
            error_log('get_summary_data_uplanner_log: ' . $e->getMessage() . PHP_EOL);
        }
        $this->data = $data;
    }
}