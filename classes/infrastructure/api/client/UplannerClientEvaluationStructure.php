<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/AbstractUplannerClient.php');

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Cliente para consumir los métodos expuestos de estructuras de evaluación en Uplanner
 */
class UplannerClientEvaluationStructure extends AbstractUplannerClient
{
    const ENDPOINT_MATERIAL = '/integration/evaluationStructure';

    /**
     * Get evaluation structure in Uplanner
     *
     * @param null $data
     * @return array|bool|mixed|string
     * @throws Exception
     */
    public function get($data = null)
    {
        $response = [];
        if (!$this->getToken()) {
            return $response;
        }
        $endpoint = $this->getEndpoint(self::ENDPOINT_MATERIAL);
        $response = $this->curlWrapper->get($endpoint);
        if ($this->curlWrapper->getCode() === 200) {
            $response = json_decode($response, true);
        } else {
            $response['error'] = $this->curlWrapper->getError();
        }

        return $response;
    }

    /**
     * Update evaluation structure in Uplanner
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function update($data): array
    {
        $response = [];
        if (!$this->getToken()) {
            return $response;
        }
        $endpoint = $this->getEndpoint(self::ENDPOINT_MATERIAL);
        $response = $this->curlWrapper->put($endpoint, $data);
        if ($this->curlWrapper->getCode() === 200) {
            $response = json_decode($response, true);
        } else {
            $response['error'] = $this->curlWrapper->getError();
        }

        return $response;
    }

    /**
     * Create evaluation structure in Uplanner
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function create($data): array
    {
        $response = [];
        if (!$this->getToken()) {
            return $response;
        }
        $endpoint = $this->getEndpoint(self::ENDPOINT_MATERIAL);
        $response = $this->curlWrapper->post($endpoint, $data);
        if ($this->curlWrapper->getCode() === 200) {
            $response = json_decode($response, true);
        } else {
            $response['error'] = $this->curlWrapper->getError();
        }

        return $response;
    }

    /**
     * Delete evaluation structure in Uplanner
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function delete($data = null): array
    {
        $response = [];
        if (!$this->getToken()) {
            return $response;
        }
        $endpoint = $this->getEndpoint(self::ENDPOINT_MATERIAL);
        $response = $this->curlWrapper->delete($endpoint);
        if ($this->curlWrapper->getCode() === 200) {
            $response = json_decode($response, true);
        } else {
            $response['error'] = $this->curlWrapper->getError();
        }

        return $response;
    }
}