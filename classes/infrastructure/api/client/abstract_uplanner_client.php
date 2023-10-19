<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\api\client;

use local_uplannerconnect\infrastructure\api\curl_wrapper;

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Cliente abstracto para consumir la api de Uplanner
 */
class abstract_uplanner_client
{
    const ENDPOINT_TOKEN = '/oauth2/token';
    const FILE_HEADERS = [
        'json',
        'request_type'
    ];

    /**
     * @var curl_wrapper
     */
    protected curl_wrapper $curl_wrapper;

    /**
     * @var string
     */
    protected string $token = '';

    /**
     * @var string
     */
    protected string $base_path = '';

    /**
     * @var string
     */
    protected string $client_id = '';

    /**
     * @var string
     */
    protected string $client_secret = '';

    /**
     * @var string
     */
    protected string $grand_type = '';

    /**
     * @var string
     */
    protected string $name_file = '';

    /**
     * @var string
     */
    protected string $email_subject = '';

    /**
     * Construct
     */
    public function __construct()
    {
        $this->curl_wrapper = new curl_wrapper();
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
     * @return string
     */
    public function get_base_path()
    {
        return $this->base_path;
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
     * @param $base_path
     * @return void
     */
    public function set_base_path($base_path)
    {
        $this->base_path = $base_path;
    }

    /**
     * @return string
     */
    public function get_client_id(): string
    {
        return $this->client_id;
    }

    /**
     * @param string $client_id
     * @return void
     */
    public function set_client_id(string $client_id): void
    {
        $this->client_id = $client_id;
    }

    /**
     * @return string
     */
    public function get_client_secret(): string
    {
        return $this->client_secret;
    }

    /**
     * @param string $client_secret
     * @return void
     */
    public function set_client_secret(string $client_secret): void
    {
        $this->client_secret = $client_secret;
    }

    /**
     * @return string
     */
    public function get_grand_tType(): string
    {
        return $this->grand_type;
    }

    /**
     * @param string $grand_type
     * @return void
     */
    public function set_grand_tType(string $grand_type): void
    {
        $this->grand_type = $grand_type;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function get_token()
    {
        if (!$this->token) {
            $data = [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type' => $this->grand_type
            ];
            $endpoint = $this->get_endpoint(self::ENDPOINT_TOKEN);
            $response = $this->curl_wrapper->post($endpoint, $data);
            if ($this->curl_wrapper->get_code() === 200) {
                $response = json_decode($response, true);
                $this->token = $response['access_token'];
            } else {
                $this->token = '';
            }
        }

        return $this->token;
    }

    /**
     * @param $endpoint
     * @return string
     */
    public function get_endpoint($endpoint)
    {
        return sprintf('%s%S', $this->base_path, $endpoint);
    }
}