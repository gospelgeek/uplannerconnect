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
 * Loaded class to manipulate data in upplanner_material table
*/
class material_repository
{
    const TABLE_COURSE_MATERIALS = 'uplanner_materials';

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
        $this->general_repository->updateDataBD([
            'data' => [
                'json' => json_encode($data['json']),
                'response' => json_encode($data['response']),
                'success' => $data['success'],
                'id' => $data['id'],
            ],
            'table' => self::TABLE_COURSE_MATERIALS
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
            'table' => self::TABLE_COURSE_MATERIALS
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
            'table' => 'mdl_' . self::TABLE_COURSE_MATERIALS
        ]);
    }

    /**
     * Delete registers by field state
     *
     * @param $state
     * @return bool
     */
    public function delete_data_bd($state): bool
    {
        return $this->general_repository->delete_data_bd($state, self::TABLE_COURSE_MATERIALS);
    }

    /**
     * Delete registers by field state
     *
     * @return void
     */
    public function add_log_data() : void
    {
        // $this->general_repository->add_log_data([
        //     'query_insert' => plugin_config::QUERY_COUNT_LOGS,
        //     'table_insert' => 'mdl_'.plugin_config::TABLE_COURSE_MATERIALS,
        //     'query_log' => plugin_config::QUERY_INSERT_LOGS,
        //     'table_log' => plugin_config::TABLE_LOG
        // ]);
    }

    /**
     * Delete register
     *
     * @param $id
     * @return bool
     */
    public function delete_row($id): bool
    {
        $result = false;
        try {
            $result = $this->general_repository->delete_row(
                self::TABLE_COURSE_MATERIALS,
                $id

            );
        } catch (moodle_exception $e) {
            error_log('delete_row: ' . $e->getMessage() . "\n");
        }
        return $result;
    }
}