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
     * @throws Exception
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
            $this->add_log_before_request('POST', $this->token_url, $headers, $data);
            $this->curl_wrapper->set_header($headers);
            $response = $this->curl_wrapper->post($this->token_url, $data);
            $code = $this->curl_wrapper->get_code();
            if ($code === 201) {
                $response = json_decode($response, true);
                $this->token = $response['signature'];
            } else {
                $this->token = '';
            }
            $this->add_log_after_request(
                'POST',
                $this->token_url,
                $headers,
                $data,
                $code,
                $response
            );
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
        $this->add_log_before_request('POST', $endpoint, $headers, $data);
        $this->curl_wrapper->set_header($headers);
        $response = $this->curl_wrapper->post($endpoint, $data);
        $code = $this->curl_wrapper->get_code();
        if ($code === 201) {
            $result = json_decode($response, true);
            $result = $result ?? ['code' => 201];
        } else {
            $result = [
                'error' => json_encode($response)
            ];
        }
        $this->add_log_after_request(
            'POST',
            $endpoint,
            $headers,
            $data,
            $code,
            $result
        );

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

    /**
     * Add log before request
     *
     * @param $method
     * @param $url
     * @param $headers
     * @param $data
     * @return void
     */
    public function add_log_before_request($method, $url, $headers, $data)
    {
        try {
            $data_request = [
                'topic' => $this->topic,
                'url' => $url,
                'method' => $method,
                //'data' => $data,
                'headers' => $headers
            ];
            $this->add_log('***** uPlanner - before request data: ', $data_request);
        } catch (Exception $e) {
            error_log('add_log_before_request: ' . $e->getMessage());
        }
    }

    /**
     * Add log after request
     *
     * @param $method
     * @param $url
     * @param $headers
     * @param $data
     * @param $code
     * @param $response
     * @return void
     */
    public function add_log_after_request(
        $method,
        $url,
        $headers,
        $data,
        $code,
        $response
    ) {
        try {
            $data_request = [
                'url' => $url,
                'method' => $method,
                //'data' => $data,
                'headers' => $headers,
                'code' => $code,
                'response' => $response
            ];
            $this->add_log('***** uPlanner - after request data: ', $data_request);
        } catch (Exception $e) {
            error_log('add_log_after_request: ' . $e->getMessage());
        }
    }

    /**
     * Add log request
     *
     * @param $firsts_message
     * @param $data
     * @return void
     */
    public function add_log($firsts_message, $data)
    {
        $log_data = PHP_EOL . $firsts_message . json_encode($data);
        error_log($log_data);
    }
}