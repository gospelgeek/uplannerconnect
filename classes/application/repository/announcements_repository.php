<?php
/**
 * @package     local_uplannerconnec
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

/**
 * Class announcements_repository, Loaded class to manipulate data in upplanner_material table
 */
class announcements_repository
{
    const TABLE = 'uplanner_notification';
    const FIELDS = [
        'json' => 'json',
        'response' => 'json',
        'success' => 'int',
        'ds_error' => 'string',
        'is_sucessful' => 'int',
        'id' => 'int',
    ];

    /**
     * @var general_repository
     */
    private $general_repository;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->general_repository = new general_repository(); 
    }

    /**
     * Update data
     *
     * @param array $data
     * @return void
     */
    public function updateDataBD(array $data) : void
    {
        $update_data = $this->general_repository->get_transfor_data(self::FIELDS, $data);
        $this->general_repository->updateDataBD([
            'data' => $update_data,
            'table' => self::TABLE
        ]);
    }

    /**
     * Save data
     *
     * @param array $data
     * @return void
     */
    public function saveDataBD(array $data) : void
    {
        $this->general_repository->saveDataBD([
            'data' => [
                'json' => json_encode($data),
                'response' => '{"status": "Default response"}',
                'success' => repository_type::STATE_DEFAULT,
                'request_type' => $data['action'],
            ],
            'table' => self::TABLE
        ]);
    }

    /**
     * Get data
     *
     * @param array|null $data
     * @return array
     */
    public function getDataBD(array $data = null) : array
    {
        return $this->general_repository->getDataBD([
            'data' => $data,
            'query' => plugin_config::QUERY_SELECT_COURSE_GRADES,
            'table' => self::TABLE
        ]);
    }
}