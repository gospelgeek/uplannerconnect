<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\api;

use coding_exception;
use local_uplannerconnect\application\repository\messages_status_repository;
use local_uplannerconnect\application\repository\repository_type;
use local_uplannerconnect\infrastructure\api\client\abstract_uplanner_client;
use local_uplannerconnect\infrastructure\api\factory\uplanner_client_factory;
use local_uplannerconnect\infrastructure\email\email;
use local_uplannerconnect\infrastructure\file;
use moodle_exception;

defined('MOODLE_INTERNAL') || die;

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @description Handle remove success uPlanner task
 */
class handle_remove_success_uplanner_task
{
    const PREFIX = 'delete_';

    /**
     * @var $uplanner_client_factory
     */
    private $uplanner_client_factory;


    /**
     * @var $file
     */
    private $file;

    /**
     * @var email
     */
    private $email;

    /**
     * @var messages_status_repository
     */
    private $message_repository;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->uplanner_client_factory = new uplanner_client_factory();
        $this->email = new email();
        $this->message_repository = new messages_status_repository();
    }

    /**
     * Handle process remove state success registers uPlanner
     *
     * @return void
     */
    public function process() {
        foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
            $repository = new $repository_class($type);
            $uplanner_client = $this->uplanner_client_factory->create($type);
            $this->start_process_per_repository($repository, $uplanner_client);
        }
    }

    /**
     * @param $repository
     * @param $uplanner_client
     * @return void
     */
    private function start_process_per_repository($repository, $uplanner_client)
    {
        try {
            $repository->add_log_data();
            $this->create_file(self::PREFIX . $uplanner_client->get_file_name());
            foreach (repository_type::LIST_STATES as $state) {
                $dataQuery = [
                    'state' => $state,
                    'limit' => 100,
                    'offset' => 0,
                ];
                $rows = $repository->getDataBD($dataQuery);
                if (!$rows) {
                    continue;
                }
                $this->add_rows_in_file($rows);
                foreach ($rows as $row) {
                    $messages = $this->message_repository->get_data([
                        'id_transaction' => $row->id,
                        'limit' => 1,
                        'offset' => 0,
                    ]);
                    $message = reset($messages);
                    if ($message->is_successful === 1) {
                        $repository->delete_row($row->id);
                        $this->message_repository->delete_row($message->id_code);
                    }
                }
            }
            $this->send_email(self::PREFIX . $uplanner_client->get_email_subject());
            $this->reset_file();
        } catch (moodle_exception $e) {
            error_log('handle_remove_success_uplanner_task - process: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Create and add rows in file
     *
     * @param $file_name
     * @return void
     */
    private function create_file($file_name)
    {
        $headers = abstract_uplanner_client::FILE_HEADERS;
        $headers[] = 'state';
        $this->file = new file($file_name);
        $this->file->create_csv($headers);
    }

    /**
     * @return void
     */
    private function reset_file()
    {
        $headers = abstract_uplanner_client::FILE_HEADERS;
        $headers[] = 'state';
        $this->file->reset_csv($headers);
    }

    /**
     * Add rows in file
     *
     * @param $rows
     * @return void
     */
    private function add_rows_in_file($rows)
    {
        foreach ($rows as $row) {
            $data = [
                $row->json,
                $row->request_type,
                $row->success
            ];
            $this->file->add_row($data);
        }
    }

    /**
     * Send email
     *
     * @param $subject
     * @return bool
     * @throws coding_exception
     */
    private function send_email($subject): bool
    {
        $recipient_email = 'samuel.ramirez@correounivalle.edu.co';
        return $this->email->send(
            $recipient_email,
            $subject,
            $this->file->get_path_file()
        );
    }
}