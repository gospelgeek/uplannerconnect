<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\infrastructure\api;

use coding_exception;
use local_uplannerconnect\application\repository\general_repository;
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
class handle_clean_uplanner_task
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
     * @var general_repository
     */
    private $general_repository;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->uplanner_client_factory = new uplanner_client_factory();
        $this->email = new email();
        $this->message_repository = new messages_status_repository();
        $this->general_repository = new general_repository();
    }

    /**
     * Handle process remove state success registers uPlanner
     *
     * @param int $page_size
     * @return void
     */
    public function process($page_size = 1000) {
        error_log("------------------------------------------  PROCESS START - FOREACH REPOSITORIES ------------------------------------------ \n");
        $current_date = date("F j, Y, g:i:s a");
        $log_id = $this->general_repository->add_log_data();
        foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
            $repository = new $repository_class($type);
            $uplanner_client = $this->uplanner_client_factory->create($type);
            $this->start_process_per_repository(
                $repository,
                $uplanner_client,
                $page_size,
                $current_date
            );
        }
        error_log("------------------------------------------            ADD LOGS (COUNT LOGS)     ------------------------------------------ \n");
        $this->general_repository->add_log_errors_data($log_id);
        error_log("--------------------------------------DELETE LOGS (success and is_sucessful = 1)------------------------------------------ \n");
        foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
            $repository = new $repository_class($type);
            // Remove registers with operation complete
            $condition = [
                'success' => 1,
                'is_sucessful' => 1
            ];
            $this->general_repository->delete_rows($repository::TABLE, $condition);
            // Remove registers with operation error
            /*$condition = [
                'success' => 1,
                'is_sucessful' => 0
            ];
            $this->general_repository->delete_rows($repository::TABLE, $condition);*/
        }
        error_log("------------------------------------------            PROCESS FINISHED             ------------------------------------------ \n");
    }

    /**
     * @param $repository
     * @param $uplanner_client
     * @param $page_size
     * @param $current_date
     * @return void
     */
    private function start_process_per_repository(
        $repository,
        $uplanner_client,
        $page_size,
        $current_date
    ) {
        try {
            if ($page_size <= 0) {
                return;
            }
            //$fileCreated = $this->create_file(self::PREFIX . $uplanner_client->get_file_name());
            $offset = 0;
            while (true) {
                $data = [
                    'state' => repository_type::STATE_SEND,
                    'limit' => $page_size,
                    'offset' => $offset,
                ];
                $rows = $repository->getDataBD($data);
                if (!$rows) {
                    break;
                }
                $this->message_repository->process($repository, $rows);
                /*$data = [
                    'state' => repository_type::STATE_SEND,
                    'limit' => $page_size,
                    'offset' => $offset,
                ];
                $rows = $repository->getDataBD($data);
                if ($fileCreated) {
                    $this->add_rows_in_file($rows);
                    $this->send_email(
                        self::PREFIX . $uplanner_client->get_email_subject(),
                        $current_date
                    );
                    $this->file->delete_csv();
                }*/
                $offset += count($rows);
            }
        } catch (moodle_exception $e) {
            error_log('handle_remove_success_uplanner_task - process: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Create and add rows in file
     *
     * @param $file_name
     * @return bool
     */
    private function create_file($file_name)
    {
        $headers = abstract_uplanner_client::FILE_HEADERS;
        $headers[] = 'is_sucessful';
        $headers[] = 'ds_error';
        $this->file = new file($file_name);
        return $this->file->create_csv($headers);
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
                $row->success,
                $row->is_sucessful,
                $row->ds_error
            ];
            $this->file->add_row($data);
        }
    }

    /**
     * Send email
     *
     * @param $subject
     * @param $current_date
     * @return bool
     */
    private function send_email($subject, $current_date): bool
    {
        $recipient_email = 'samuel.ramirez@correounivalle.edu.co';
        return $this->email->send(
            $recipient_email,
            $subject,
            $current_date,
            $this->file->get_path_file()
        );
    }
}