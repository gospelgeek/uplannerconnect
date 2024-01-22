<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\service\page;

use context_system;
use Exception;
use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\plugin_config\plugin_config;
use coding_exception;
use moodle_exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Class page
 */
abstract class page
{
    /**
     * @var string
     */
    protected $entity = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $urls = [];

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var moodle_query_handler
     */
    protected $query_handler;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->query_handler = new moodle_query_handler();
    }

    /**
     * Get custom url
     *
     * @return mixed
     */
    abstract public function get_url();

    /**
     * Get data
     *
     * @return mixed
     */
    abstract public function get_data();

    /**
     * Get urls
     *
     * @return void
     * @throws moodle_exception
     */
    public function get_urls()
    {
        $dataUrls = [
            '/index.php' => 'Daily Summary',
            '/routes/error_grades_summary.php' => 'Grades Error Summary',
            '/routes/error_evaluation_summary.php' => 'Evaluation Error Summary',
            '/routes/error_materials_summary.php' => 'Materials Error Summary',
            '/routes/error_anouncements_summary.php' => 'Anouncements Error Summary'
        ];
        foreach ($dataUrls as $file => $label) {
            $urlData = [
                'url' => new moodle_url('/local/' . plugin_config::PLUGIN_NAME . $file),
                'label' => $label
            ];
            $this->urls[] = $urlData;
        }
        $this->get_url();
    }

    /**
     * Load page data
     *
     * @return void
     * @throws moodle_exception
     */
    public function load_data()
    {
        $this->get_urls();
        $this->get_data();
    }

    /**
     * Render page layout
     *
     * @return void
     * @throws coding_exception
     */
    public function render()
    {
        global $PAGE, $OUTPUT;
        $context = context_system::instance();
        require_capability('local/' . plugin_config::PLUGIN_NAME . ':index', $context);
        require_login();
        try {
            $this->load_data();
            $PAGE->set_context($context);
            $PAGE->set_url($this->url);
            $PAGE->requires->js_call_amd(plugin_config::PLUGIN_NAME_LOCAL . '/log', 'init', [
                'entity' => $this->entity
            ]);
            $PAGE->set_pagelayout('admin');
        } catch (Exception $e) {
            error_log('Render template: ' . $e->getMessage() . PHP_EOL);
        }
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('title', plugin_config::PLUGIN_NAME_LOCAL));
        echo $OUTPUT->render_from_template($this->template, $this->data);
        echo $OUTPUT->footer();
    }
}