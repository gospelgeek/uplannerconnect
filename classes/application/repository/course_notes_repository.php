<?php
/**
 * @package     local_uplannerconnect
 * @athor       Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\repository;

use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

/**
 * Class course_notes_repository
 */
class course_notes_repository
{
    const TABLE = 'uplanner_grades';
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
     * Update data in custom table
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
     * Save data in custom table
     *
     * @TODO: Validate data type
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
     * Get data db
     *
     * @param array|null $data
     * @return array
     */
    public function getDataBD(array $data = null) : array
    {
        return $this->general_repository->getDataBD([
            'data' => $data,
            'query' => plugin_config::QUERY_SELECT_COURSE_GRADES,
            'table' => 'mdl_' . self::TABLE
        ]);
    }
}