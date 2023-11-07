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
    const QUERY_SELECT = "SELECT * FROM %s WHERE id_transaction = %s";

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
                $query = sprintf(self::QUERY_SELECT, $transaction_id);
                $get_results = sqlsrv_query($connection, $query);
                if ($get_results) {
                    while ($row = sqlsrv_fetch_array($get_results, SQLSRV_FETCH_ASSOC)) {
                        $message = $row;
                        break;
                    }
                    sqlsrv_free_stmt($get_results);
                }
            } else {
                error_log('get_message: ' .  print_r(sqlsrv_errors(), true) . "\n");
            }
        } catch (\Exception $e) {
            error_log('get_message: ' . $e->getMessage() . "\n");
        }

        return $message;
    }
}