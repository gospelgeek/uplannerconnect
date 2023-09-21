<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Implementation of curl wrapper
 */
class CurlWrapper
{
    /**
     * @var false|resource
     */
    private $ch;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Send get request
     *
     * @param $url
     * @return bool|string
     * @throws Exception
     */
    public function get($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
        return $this->execute();
    }

    /**
     * Send post request
     *
     * @param $url
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function post($url, $data)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        return $this->execute();
    }

    /**
     * Send put request
     *
     * @param $url
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function put($url, $data)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        return $this->execute();
    }

    /**
     * Send delete request
     *
     * @param $url
     * @return bool|string
     * @throws Exception
     */
    public function delete($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->execute();
    }

    /**
     * Set header in request
     *
     * @param $header
     * @return $this
     */
    public function setHeader($header)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        return $this;
    }


    /**
     * Set basic auth in request
     *
     * @param $username
     * @param $password
     * @return $this
     */
    public function setBasicAuth($username, $password)
    {
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->ch, CURLOPT_USERPWD, "$username:$password");
        return $this;
    }

    /**
     * Add option in curl
     *
     * @param $option
     * @param $value
     * @return $this
     */
    public function addOption($option, $value)
    {
        curl_setopt($this->ch, $option, $value);
        return $this;
    }

    /**
     * Get code response
     *
     * @return mixed
     */
    public function getCode()
    {
        return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    /**
     * Get curl error
     *
     * @return string
     */
    public function getError()
    {
        return curl_error($this->ch);
    }

    /**
     * Send request
     *
     * @return bool|string
     * @throws Exception
     */
    private function execute()
    {
        $response = curl_exec($this->ch);
        if ($response === false) {
            throw new Exception(curl_error($this->ch));
        }
        return $response;
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        curl_close($this->ch);
    }
}