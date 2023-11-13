<?php
/**
 * @package     local_uplannerconnec
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\messages;

/**
 * Class messages_resource
 */
class messages_resource
{
    const TABLE = 'esb_messages_status';
    const QUERY_SELECT = 'SELECT * FROM %s WHERE %s';

    /**
     * Get message
     *
     * @param $transaction_id
     * @return array|null
     */
    public function get_message($transaction_id)
    {
        $message = null;

        try {
            $connection = connection::getInstance()->getConnection();
            if ($connection) {
                $where_clause = sprintf(
                    "id_transaction = %d",
                    implode(',', $transaction_id)
                );
                $query = sprintf(self::QUERY_SELECT, self::TABLE, $where_clause);
                $get_results = sqlsrv_query($connection, $query);
                if ($get_results) {
                    while ($row = sqlsrv_fetch_array($get_results, SQLSRV_FETCH_ASSOC)) {
                        $message = $row;
                        break;
                    }
                    sqlsrv_free_stmt($get_results);
                }
            } else {
                error_log('get_message connection fail: ' .  print_r(sqlsrv_errors(), true) . "\n");
            }
        } catch (\Exception $e) {
            error_log('get_message: ' . $e->getMessage() . "\n");
        }

        return $message;
    }

    /**
     * Get messages
     *
     * @param $transactions
     * @return array
     */
    public function get_messages($transactions)
    {
        $messages = [];
        try {
            $connection = connection::getInstance()->getConnection();
            if ($connection) {
                $where_clause = sprintf(
                    "id_transaction IN (%s)",
                    $transactions
                );
                $query = sprintf(self::QUERY_SELECT, self::TABLE, $where_clause);
                $get_results = sqlsrv_query($connection, $query);
                if ($get_results) {
                    while ($row = sqlsrv_fetch_array($get_results, SQLSRV_FETCH_ASSOC)) {
                        $messages[] = $row;
                        break;
                    }
                    sqlsrv_free_stmt($get_results);
                }
            } else {
                error_log('get_messages connect fail: ' .  print_r(sqlsrv_errors(), true) . "\n");
            }
        } catch (\Exception $e) {
            error_log('get_messages: ' . $e->getMessage() . "\n");
        }


        return $messages;
    }
}