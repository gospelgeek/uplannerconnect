<?php
/**
 * @package     local_uplannerconnec
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\repository;

use local_uplannerconnect\application\messages\messages_resource;

/**
 * Loaded class to manipulate data in uplanner_esb_messages_status table
 */
class messages_status_repository
{
    /**
     * @var messages_resource
     */
    private $messages_resource;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->messages_resource = new messages_resource();
    }

    /**
     * get message by transaction id
     *
     * @param $transaction_id
     * @return array|null
     */
    public function get_by_transaction_id($transaction_id)
    {
        return $this->messages_resource->get_message($transaction_id);
    }

    /**
     * get list by transactions
     *
     * @param $transactions_ids
     * @return array
     */
    public function get_list_by_transactions($transactions_ids)
    {
        return $this->messages_resource->get_messages(
            implode(',', $transactions_ids)
        );
    }

    /**
     * Compare message log in uPlanner
     *
     * @return void
     */
    public function process($repository, $rows)
    {
        try {
            $up_messages = $this->get_list_by_transactions(
                $this->get_uv_transactions($rows)
            );
            foreach ($rows as $row) {
                $json = json_decode($row->json, true);
                $id_transaction = intval($json['transactionId']);
                $filtered_messages = array_filter($up_messages, function ($message) use ($id_transaction) {
                    return $message['id_transaction'] == $id_transaction;
                });
                $message = reset($filtered_messages);
                $is_successful = 0;
                $ds_error = 'Error invalid data';
                if ($message) {
                    if (($message['is_successful'] === 1 || $message['is_successful'] === '1')) {
                        $ds_error = '';
                        $is_successful = 1;
                    } else {
                        $ds_error = $message['ds_error'];
                    }
                }
                $data = [
                    'is_sucessful' => $is_successful,
                    'ds_error' => $ds_error,
                    'id' => $row->id
                ];
                $repository->updateDataBD($data);
            }
        } catch (\Exception $e) {
            error_log('messages_status_repository->process: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Return list transactions
     *
     * @param $rows
     * @return array
     */
    private function get_uv_transactions($rows)
    {
        return array_map(function ($row) {
            $json = json_decode($row->json, true);
            return intval($json['transactionId']);
        }, $rows);
    }
}