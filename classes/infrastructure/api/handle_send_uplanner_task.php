<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\api;

use local_uplannerconnect\application\repository\repository_type;
use local_uplannerconnect\infrastructure\api\client\abstract_uplanner_client;
use local_uplannerconnect\infrastructure\api\factory\uplanner_client_factory;
use local_uplannerconnect\infrastructure\email\email;
use local_uplannerconnect\infrastructure\file;

defined('MOODLE_INTERNAL') || die;


/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Clase que controla la lógica que enváa datos a Uplanner
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
     * Handle process send information to Uplanner
     *
     * @return void
     * @throws \coding_exception
     */
    public function process(
        $state = 0,
        $num_request_by_endpoint = 1,
        $num_rows = 100,
        $is_email = false
    ) {
        foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
            $repository = new $repository_class();
            $uplanner_client = $this->uplanner_client_factory->create($type);
            if ($is_email) {
                $this->process_email(
                    $uplanner_client,
                    $repository,
                    $state,
                    $num_request_by_endpoint,
                    $num_rows
                );
            } else {
                $this->process_endpoint(
                    $uplanner_client,
                    $repository,
                    $state,
                    $num_request_by_endpoint,
                    $num_rows
                );
            }
        }
    }

    /**
     * @param $uplanner_client
     * @param $repository
     * @param $state
     * @param $num_request_by_endpoint
     * @param $num_rows
     * @return void
     * @throws \coding_exception
     */
    public function process_email(
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
                'limit' => 100,
                'offset' => 0,
            ];
            $rows = $repository->getDataBD($dataQuery);
            if (!$rows) {
                break;
            }
            $this->file = new file($uplanner_client->get_file_name());
            $this->file->create_csv(abstract_uplanner_client::FILE_HEADERS);
            foreach ($rows as $row) {
                $data = [
                    $row->json,
                    $row->request_type
                ];
                $this->file->add_row($data);
            }
            //send email;
            $recipient_email = 'samuel.ramirez@correounivalle.edu.co';
            $response = $this->email->send($recipient_email, $this->file->get_path_file());
            $status = $response ? repository_type::STATE_SEND : repository_type::STATE_ERROR;
            foreach ($rows as $row) {
                $dataQuery = [
                    'json' => $row->json,
                    'response' => $response,
                    'success' => $status,
                    'id' => $row->id
                ];
                $repository->updateDataBD($dataQuery);
            }
            $this->file->reset_csv(abstract_uplanner_client::FILE_HEADERS);
        }
    }

    /**
     * @param $uplanner_client
     * @param $repository
     * @param $state
     * @param $num_request_by_endpoint
     * @param $num_rows
     * @return void
     */
    public function process_endpoint(
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
            $rows = $repository->getDataBD($state);
            if (!$rows) {
                break;
            }
            foreach ($rows as $row) {
                $response = $this->request($uplanner_client, $row['type'], $row['json']);
                // 2 -> state error, 1 -> state send
                $status = in_array($response, 'error') ? 2 : 1;
                //$repository->saveData($status, $response);
            }
        }
    }

    /**
     * @param $uplanner_client
     * @param $type
     * @param $json
     * @return mixed
     */
    public function request($uplanner_client, $type, $json)
    {
        $response = [];
        try {
            switch ($type) {
                case 'delete':
                    $response = $uplanner_client->delete($json);
                    break;
                case 'update':
                    $response = $uplanner_client->update($json);
                    break;
                default:
                    $response = $uplanner_client->create($json);
                    break;
            }
        } catch (\Exception $e) {
            error_log('handle_send_uplanner_task - request: ' . $e->getMessage() . "\n");
        }

        return $response ?? [];
    }
}