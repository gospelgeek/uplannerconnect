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
    const QUERY_SELECT = 'SELECT * FROM (SELECT *, ROW_NUMBER() OVER (PARTITION BY id_transaction ORDER BY createdAt DESC) AS RowNum FROM %s WHERE id_transaction IN (%s)) AS RankedRows WHERE RowNum = 1';

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
                $query = sprintf(self::QUERY_SELECT, self::TABLE, $transactions);
                $get_results = sqlsrv_query($connection, $query);
                if ($get_results) {
                    while ($row = sqlsrv_fetch_array($get_results, SQLSRV_FETCH_ASSOC)) {
                        $messages[] = $row;
                    }
                    sqlsrv_free_stmt($get_results);
                }
            } else {
                error_log('get_messages connect fail: ' .  print_r(sqlsrv_errors(), true) . PHP_EOL);
            }
        } catch (\Exception $e) {
            error_log('get_messages: ' . $e->getMessage() . PHP_EOL);
        }

        return $messages;
    }
}