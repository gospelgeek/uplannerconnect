<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\api\client;

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Cliente para consumir los métodos expuestos de notas en Uplanner
 */
class uplanner_client_grade extends abstract_uplanner_client
{
    const ENDPOINT_MATERIAL = '/integration/grades';

    /**
     * @inerhitdoc
     */
    protected string $name_file = 'uplanner_client_grade.csv';

    /**
     * @inerhitdoc
     */
    protected string $email_subject = 'upllaner_email_subject_grades';

    /**
     * Get grade in Uplanner
     *
     * @param null $data
     * @return array|bool|mixed|string
     * @throws Exception
     */
    public function get($data = null)
    {
        $response = [];
        if (!$this->get_token()) {
            return $response;
        }
        $endpoint = $this->get_endpoint(self::ENDPOINT_MATERIAL);
        $response = $this->curl_wrapper->get($endpoint);
        if ($this->curl_wrapper->get_code() === 200) {
            $response = json_decode($response, true);
        } else {
            $response['error'] = $this->curl_wrapper->get_error();
        }

        return $response;
    }

    /**
     * Update grade in Uplanner
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function update($data): array
    {
        $response = [];
        if (!$this->get_token()) {
            return $response;
        }
        $endpoint = $this->get_endpoint(self::ENDPOINT_MATERIAL);
        $response = $this->curl_wrapper->put($endpoint, $data);
        if ($this->curl_wrapper->get_code() === 200) {
            $response = json_decode($response, true);
        } else {
            $response['error'] = $this->curl_wrapper->get_error();
        }

        return $response;
    }

    /**
     * Create grade in Uplanner
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function create($data): array
    {
        $response = [];
        if (!$this->get_token()) {
            return $response;
        }
        $endpoint = $this->get_endpoint(self::ENDPOINT_MATERIAL);
        $response = $this->curl_wrapper->post($endpoint, $data);
        if ($this->curl_wrapper->get_code() === 200) {
            $response = json_decode($response, true);
        } else {
            $response['error'] = $this->curl_wrapper->get_error();
        }

        return $response;
    }

    /**
     * Delete grade in Uplanner
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function delete($data = null): array
    {
        $response = [];
        if (!$this->get_token()) {
            return $response;
        }
        $endpoint = $this->get_endpoint(self::ENDPOINT_MATERIAL);
        $response = $this->curl_wrapper->delete($endpoint);
        if ($this->curl_wrapper->get_code() === 200) {
            $response = json_decode($response, true);
        } else {
            $response['error'] = $this->curl_wrapper->get_error();
        }

        return $response;
    }
}