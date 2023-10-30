<?php
/**
 * @package     local_uplannerconnec
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\repository;

use moodle_exception;

/**
 * Loaded class to manipulate data in uplanner_esb_messages_status table
 */
class messages_status_repository
{

    const TABLE = 'uplanner_esb_messages_status';
    const QUERY_SELECT = "SELECT * FROM %s WHERE id_transaction = %s LIMIT '%s' OFFSET '%s'";
    CONST QUERY_COUNT = "SELECT count(*) FROM %s";

    /**
     * @var general_repository
     */
    private $general_repository;

    /**
     * @var moodle_query_handler
     */
    private $moodle_query_handler;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->general_repository = new general_repository();
        $this->moodle_query_handler = new moodle_query_handler();
    }

    /**
     * Update data
     *
     * @param array $data
     * @return void
     */
    public function update(array $data) : void
    {
        $this->general_repository->updateDataBD([
            'data' => [
                'id_transaction' => $data['id_transaction'],
                'ds_topic' => $data['ds_topic'],
                'ds_mongoId' => $data['ds_mongoId'],
                'ds_error' => $data['ds_error'],
                'dt_processingDate' => $data['dt_processingDate'],
                'is_successful' => $data['is_successful'],
                'createdAt' => $data['createdAt'],
                'id_code' => $data['id_code'],
            ],
            'table' => self::TABLE
        ]);
    }

    /**
     * Save data
     *
     * @param array $data
     * @return void
     */
    public function save(array $data) : void
    {
        $this->general_repository->saveDataBD([
            'data' => [
                'id_transaction' => $data['id_transaction'],
                'ds_topic' => $data['ds_topic'],
                'ds_mongoId' => $data['ds_mongoId'],
                'ds_error' => $data['ds_error'],
                'dt_processingDate' => $data['dt_processingDate'],
                'is_successful' => $data['is_successful'],
                'createdAt' => $data['createdAt']
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
    public function get_data(array $data = null) : array
    {
        $dataQuery = [];
        try {
            $dataQuery = $this->moodle_query_handler->executeQuery(
                sprintf(
                    self::QUERY_SELECT,
                    'mdl_' . self::TABLE,
                    $data['id_transaction'],
                    $data['limit'],
                    $data['offset']
                )
            );
        }
        catch (moodle_exception $e) {
            error_log('get_data: ' . $e->getMessage() . "\n");
        }

        return $dataQuery;
    }

    /**
     * Delete register
     *
     * @param $id_code
     * @return bool
     */
    public function delete_row($id_code): bool
    {
        $result = false;
        try {
            $result = $this->general_repository->delete_row(
                self::TABLE,
                $id_code,
                'id_code'

            );
        } catch (moodle_exception $e) {
            error_log('delete_row: ' . $e->getMessage() . "\n");
        }
        return $result;
    }

    /**
     * Count all rows
     *
     * @return bool
     */
    public function count(): bool
    {
        $result = false;
        try {
            $query =  sprintf(
                self::QUERY_COUNT,
                self::TABLE,
            );
            $result = $this->general_repository->count($query);
        } catch (moodle_exception $e) {
            error_log('delete_row: ' . $e->getMessage() . "\n");
        }
        return $result;
    }
}