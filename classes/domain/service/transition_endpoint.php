<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\service; 

use local_uplannerconnect\application\repository\moodle_query_handler;
use moodle_exception;

/**
 * Get TransitionId
 */
class transition_endpoint
{
    const LAST_COURSE_TRANSACTION = "SELECT id FROM mdl_uplanner_transaction_seq WHERE courseid = '%s' ORDER BY id DESC LIMIT 1";
    const TABLE_TRANSACTION_UPLANNER = 'uplanner_transaction_seq';
    
    private $moodle_query_handler;

    /**
     *  Construct
     */
    public function __construct()
    {
        $this->moodle_query_handler = new moodle_query_handler();
    }
    /**
     * Insert new register Transition
     *
     * @param array $data
     * @return void
     */
    private function insertTransactionUplanner(array $data)
    {
        $dataToSave = [];
        try {
            $dataToSave = [
                'courseid' => $data['courseId'],
                'transaction' => $data['transaction']
            ];
            $this->moodle_query_handler->insert_record_db([
                'table' => self::TABLE_TRANSACTION_UPLANNER,
                'data' => $dataToSave
            ]);
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
    }

    /**
     * Get last row of transition
     *
     * @param array $data
     * @return array
     */
    public function getLastRowTransaction($courseId)
    {
        $lastRow = 0;
        try {
            $queryResult = $this->moodle_query_handler->executeQuery(sprintf(
                self::LAST_COURSE_TRANSACTION, 
                strval($courseId)
            ));

            if (!empty($queryResult)) {
                $firstResult = reset($queryResult);
                $lastRow = intval((($firstResult->id) + 1).''.$courseId);
            } else {
                $lastRow = intval('1' . $courseId);
            }

            $this->insertTransactionUplanner([
                'courseId' => $courseId,
                'transaction' => $lastRow
            ]);

        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $lastRow;
    }
}