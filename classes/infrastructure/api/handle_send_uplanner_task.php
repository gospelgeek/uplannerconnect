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
     * Construct
     */
    public function __construct()
    {
        $this->uplanner_client_factory = new uplanner_client_factory();
        $this->email = new email();
    }

    /**
     * Handle process send information to uPlanner
     *
     * @param $state
     * @param int $num_request_by_endpoint
     * @param $num_rows MAXVALUE: 100
     * @return void
     * @throws coding_exception
     */
    public function process(
        $state = 0,
        $num_request_by_endpoint = 1,
        $num_rows = 100
    ) {
        foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
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
    }

    /**
     * @param $uplanner_client
     * @param $repository
     * @param $state
     * @param $num_request_by_endpoint
     * @param $num_rows
     * @return void
     * @throws coding_exception
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
        while ($index_row < $num_request_by_endpoint) {
            $dataQuery = [
                'state' => $state,
                'limit' => $num_rows,
                'offset' => 0,
            ];
            $rows = $repository->getDataBD($dataQuery);
            if (!$rows) {
                break;
            }
            $response = $this->request($uplanner_client, $rows);
            $status = (empty($response) || in_array('error', $response))
                ? repository_type::STATE_ERROR : repository_type::STATE_SEND;
            $this->create_file($uplanner_client->get_file_name(), $rows, $status);
            $response = $this->send_email($uplanner_client->get_email_subject());
            foreach ($rows as $row) {
                $dataQuery = [
                    'json' => json_decode($row->json, true),
                    'response' => $response,
                    'success' => $status,
                    'id' => $row->id
                ];
                $repository->updateDataBD($dataQuery);
            }
            $this->file->delete_csv();
            $index_row++;
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
                $json[] = $row->json;
            }
            $response = $uplanner_client->request($json);
        } catch (\Exception $e) {
            error_log('handle_send_uplanner_task - request: ' . $e->getMessage() . "\n");
        }

        return $response ?? [];
    }

    /**
     * Create and add rows in file
     *
     * @param $file_name
     * @param $rows
     * @param $status
     * @return void
     */
    private function create_file($file_name, $rows, $status)
    {
        $this->file = new file($file_name);
        $this->file->create_csv(abstract_uplanner_client::FILE_HEADERS);
        foreach ($rows as $row) {
            $data = [
                $row->json,
                $row->request_type,
                $status
            ];
            $this->file->add_row($data);
        }
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
            $this->file->get_path_file()
        );
    }
}