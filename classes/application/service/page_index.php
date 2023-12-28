<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\service;

use local_uplannerconnect\application\repository\moodle_query_handler;
use moodle_exception;

/**
 * Data Page Index
 */
class page_index
{
    private $query;

    const DATA_SEND_UPLANNER = "SELECT * FROM {uplanner_log}";

    /** 
      * Construct
    */
    public function __construct()
    {
        $this->query = new moodle_query_handler();
    }

    public function getDataSendJsonUplanner(array $params)
    {
        $data = new \stdClass();
        try {
            $urlSummary = $params['urlSummary'];
            $rawData = $this->query->executeQuery(self::DATA_SEND_UPLANNER);
            $data->row = [];

            foreach ($rawData as $key => $value) {
                    $value->date = date('Y-m-d H:i:s', $value->date);
                    array_push($data->row, $value);
            }

            $data->ulItems = [];
            array_push($data->ulItems, [
                    "url" => $urlSummary 
            ]);
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
        return $data;
    }
}