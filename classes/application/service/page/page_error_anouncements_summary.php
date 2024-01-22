<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\service\page;

use local_uplannerconnect\plugin_config\plugin_config;
use local_uplannerconnect\application\repository\announcements_repository;
use local_uplannerconnect\application\repository\repository_type;
use moodle_exception;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Data Page Index
 */
class page_error_anouncements_summary extends page
{
    /**
     * @var string
     */
    protected $entity = 'announcement';

    /**
     * @var string
     */
    protected $title = 'Anouncements Error Summary';

    /**
     * @var string
     */
    protected $template = 'local_uplannerconnect/error_summary';

    /**
     * @inerhitdoc
     */
    public function get_url()
    {
        $this->url = new moodle_url('/local/' . plugin_config::PLUGIN_NAME . '/routes/error_anouncements_summary.php');
    }

    /**
     * @inerhitdoc
     */
    public function get_data()
    {
        $data = new stdClass();
        try {
            $data->row = [];
            $repository = new announcements_repository();
            $dataQuery = [
                'state' => repository_type::STATE_UP_ERROR,
                'limit' => 1000,
                'offset' => 0
            ];
            $rows = $repository->getDataBD($dataQuery);
            foreach ($rows as $row) {
                $data->row[] = $row;
            }
            $data->ulItems = $this->urls;
            $data->title = [
                ['title' => $this->title]
            ];
        } catch (moodle_exception $e) {
            error_log('get_summary_data_uplanner_log: ' . $e->getMessage() . PHP_EOL);
        }
        $this->data = $data;
    }
}