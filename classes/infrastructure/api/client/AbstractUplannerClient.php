<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../CurlWrapper.php');

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Cliente abstracto para consumir la api de Uplanner
 */
class AbstractUplannerClient
{
    const ENDPOINT_TOKEN = '/oauth2/token';

    /**
     * @var CurlWrapper
     */
    protected CurlWrapper $curlWrapper;

    /**
     * @var string
     */
    protected string $token = '';

    /**
     * @var string
     */
    protected string $basePath = '';

    /**
     * @var string
     */
    protected string $clientId = '';

    /**
     * @var string
     */
    protected string $clientSecret = '';

    /**
     * @var string
     */
    protected string $grandType = '';

    /**
     * Construct
     */
    public function __construct()
    {
        $this->curlWrapper = new CurlWrapper();
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param $basePath
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return void
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return void
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return string
     */
    public function getGrandType(): string
    {
        return $this->grandType;
    }

    /**
     * @param string $grandType
     * @return void
     */
    public function setGrandType(string $grandType): void
    {
        $this->grandType = $grandType;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function getToken()
    {
        if (!$this->token) {
            $data = [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => $this->grandType
            ];
            $endpoint = $this->getEndpoint(self::ENDPOINT_TOKEN);
            $response = $this->curlWrapper->post($endpoint, $data);
            if ($this->curlWrapper->getCode() === 200) {
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
    public function getEndpoint($endpoint)
    {
        return sprintf('%s%S', $this->basePath, $endpoint);
    }
}