<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\api;

use coding_exception;
use local_uplannerconnect\application\repository\repository_type;
use local_uplannerconnect\infrastructure\api\client\abstract_uplanner_client;
use local_uplannerconnect\infrastructure\api\factory\uplanner_client_factory;
use local_uplannerconnect\infrastructure\email\email;
use local_uplannerconnect\infrastructure\file;

defined('MOODLE_INTERNAL') || die;


/**
 * Class handle_send_uplanner_task, send data to uPlanner
 */
class handle_send_uplanner_task
{
    /**
     * @var $uplanner_client_factory
     */
    private $uplanner_client_factory;

    /**
     * @var $file
     */
    private $file;

    /**
     * @var $email
     */
    private $email;

    /**
     * @var $task_di
     */
    private $task_id;

    /**
     * @var $current_date
     */
    private $current_date;

    /**
     * Construct
     *
     * @param $tasks_id
     */
    public function __construct(
        $tasks_id
    ) {
        $this->task_id = $tasks_id;
        $this->current_date = date("F j, Y, g:i:s a");
        $this->uplanner_client_factory = new uplanner_client_factory();
        $this->email = new email();
    }

    /**
     * Handle process send information to uPlanner
     *
     * @param int $state
     * @param int $num_request_by_endpoint
     * @param int $num_rows MAXVALUE: 100
     * @return void
     */
    public function process(
        $state = 0,
        $num_request_by_endpoint = 1,
        $num_rows = 100
    ) {
        error_log('------------------------------------------  PROCESS START - FOREACH REPOSITORIES ------------------------------------------' . PHP_EOL);
        foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
            error_log('------- CREATE REPOSITORY OBJECT: ' . $type . ' - ' . $repository_class  . PHP_EOL);
            $repository = new $repository_class();
            $uplanner_client = $this->uplanner_client_factory->create($type);
            $this->process_request(
                $uplanner_client,
                $repository,
                $state,
                $num_request_by_endpoint,
                $num_rows
            );
        }
        error_log('------------------------------------------            PROCESS FINISHED             ------------------------------------------' . PHP_EOL);
    }

    /**
     * @param $uplanner_client
     * @param $repository
     * @param $state
     * @param $num_request_by_endpoint
     * @param $num_rows
     * @return void
     */
    public function process_request(
        $uplanner_client,
        $repository,
        $state,
        $num_request_by_endpoint,
        $num_rows
    ) {
        if ($num_request_by_endpoint <= 0 || $num_rows <= 0 ) {
            return;
        }
        $index_row = 0;
        $offset = 0;
        error_log('********** PROCESS REQUEST - WHILE: ' . PHP_EOL);
        while ($index_row < $num_request_by_endpoint) {
            $dataQuery = [
                'state' => $state,
                'limit' => $num_rows,
                'offset' => $offset
            ];
            error_log('DATA QUERY: ' . json_encode($dataQuery)  . PHP_EOL);
            $rows = $repository->getDataBD($dataQuery);
            error_log('DATA ROWS: ' . json_encode($rows)  . PHP_EOL);
            if (!$rows) {
                break;
            }
            $response = $this->request($uplanner_client, $rows);
            error_log('RESPONSE: ' . json_encode($response)  . PHP_EOL);
            $status = (empty($response) || array_key_exists('error', $response))
                ? repository_type::STATE_ERROR : repository_type::STATE_SEND;
            $fileCreated = $this->create_file($uplanner_client->get_file_name(), $rows, $status);
            error_log('FILE WAS CREATED: ' . $fileCreated  . PHP_EOL);
            if ($fileCreated) {
                $this->send_email($uplanner_client->get_email_subject());
            }
            $numRows = 0;
            error_log('UPDATE REGISTER STATUS: ' . $status  . PHP_EOL);
            foreach ($rows as $row) {
                $dataQuery = [
                    'response' => $response,
                    'success' => $status,
                    'id' => $row->id
                ];
                $repository->updateDataBD($dataQuery);
                if ($status == $state) {
                    $numRows++;
                }
            }
            $index_row++;
            $offset += $numRows;
        }
    }

    /**
     * @param $uplanner_client
     * @param $rows
     * @return mixed
     */
    public function request($uplanner_client, $rows)
    {
        $response = $json = [];
        try {
            foreach ($rows as $row) {
                $json[] = json_decode($row->json, true);
            }
            $response = $uplanner_client->request($json);
        } catch (\Exception $e) {
            error_log('handle_send_uplanner_task - request: ' . $e->getMessage() . PHP_EOL);
        }

        return $response ?? [];
    }

    /**
     * Create and add rows in file
     *
     * @param $file_name
     * @param $rows
     * @param $status
     * @return bool
     */
    private function create_file($file_name, $rows, $status)
    {
        $this->file = new file($this->task_id, $file_name);
        $fileCreated = $this->file->create_csv(abstract_uplanner_client::FILE_HEADERS);
        if ($fileCreated) {
            foreach ($rows as $row) {
                $data = [
                    $row->json,
                    $status
                ];
                $this->file->add_row($data);
            }
        }

        return $fileCreated;
    }

    /**
     * Send email
     *
     * @param $subject
     * @return bool
     */
    private function send_email($subject)
    {
        $recipient_email = 'samuel.ramirez@correounivalle.edu.co';
        return $this->email->send(
            $recipient_email,
            $subject,
            $this->current_date,
            $this->file->get_path_file(),
            $this->file->get_virtual_name()
        );
    }
}