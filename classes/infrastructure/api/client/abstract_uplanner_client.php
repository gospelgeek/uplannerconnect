<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\api\client;

use Exception;
use local_uplannerconnect\infrastructure\api\curl_wrapper;
use local_uplannerconnect\plugin_config\plugin_config;

/**
 * uPlanner abstract client
 */
class abstract_uplanner_client
{
    const FILE_HEADERS = [
        'json',
        'request_type',
        'state'
    ];

    /**
     * @var string
     */
    protected string $name_file = '';

    /**
     * @var string
     */
    protected string $email_subject = '';

    /**
     * @var curl_wrapper
     */
    protected curl_wrapper $curl_wrapper;

    /**
     * @var string
     */
    protected string $base_url = '';

    /**
     * @var string
     */
    protected string $key = '';

    /**
     * @var string
     */
    protected string $config_topic = '';

    /**
     * @var string
     */
    protected string $topic = '';

    /**
     * @var string
     */
    protected string $token_url = '';

    /**
     * @var string
     */
    protected string $token = '';

    /**
     * Construct
     */
    public function __construct()
    {
        $this->curl_wrapper = new curl_wrapper();
        $this->get_config();
    }

    /**
     * Get default config
     *
     * @return void
     */
    protected function get_config()
    {
        try {
            $this->key = get_config(plugin_config::PLUGIN_NAME_LOCAL, 'key') ?? '';
            $this->base_url = get_config(plugin_config::PLUGIN_NAME_LOCAL, 'base_url') ?? '';
            $this->token_url = get_config(plugin_config::PLUGIN_NAME_LOCAL, 'token_endpoint') ?? '';
            $this->topic = get_config(plugin_config::PLUGIN_NAME_LOCAL, $this->config_topic) ?? '';
        } catch (\dml_exception $e) {
            error_log('abstract_uplanner_client - get_config: ' . $e->getMessage() . "\n");
        }
    }

    /**
     * Get name file
     *
     * @return string
     */
    public function get_file_name()
    {
        return $this->name_file;
    }

    /**
     * Get email subject
     *
     * @return string
     */
    public function get_email_subject()
    {
        return $this->email_subject;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function get_token()
    {
        if (!$this->token) {
            $data = [
                'topicName' => $this->topic,
                'sharedAccessKey' => $this->key
            ];
            $headers = [
                'Content-Type: application/json'
            ];
            $this->curl_wrapper->set_header($headers);
            $response = $this->curl_wrapper->post($this->token_url, $data);
            if ($this->curl_wrapper->get_code() === 201) {
                $response = json_decode($response, true);
                $this->token = $response['signature'];
            } else {
                $this->token = '';
            }
        }

        return $this->token;
    }

    /**
     * Send data uPlanner
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function request($data): array
    {
        if (!$this->get_token()) {
            return [
                'error' => 'Not authorized'
            ];
        }
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->get_token(),
            'BrokerProperties: {"Label":"M1","State":"Active"}',
            'Priority: High',
            'Customer: AllMessages'
        ];
        $endpoint = $this->get_endpoint();
        $this->curl_wrapper->set_header($headers);
        $response = $this->curl_wrapper->post($endpoint, $data);
        if ($this->curl_wrapper->get_code() === 201) {
            $result = json_decode($response, true);
            $result = $result ?? ['code' => 201];
        } else {
            $result = [
                'error' => json_encode($response)
            ];
        }

        return $result;
    }

    /**
     * Get url request
     *
     * @return string
     */
    public function get_endpoint()
    {
        $url = '';
        if ($this->base_url && $this->topic) {
            $url = str_replace('topic', $this->topic, $this->base_url);
        }

        return $url;
    }
}