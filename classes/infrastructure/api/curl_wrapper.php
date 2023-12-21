<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\api;

use Exception;

/**
 * Class curl_wrapper, implementation of curl wrapper
 */
class curl_wrapper
{
    /**
     * @var false|resource
     */
    private $ch;

    /**
     * @var int
     */
    private $code = 404;

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
    public function set_header($header)
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 4);

        return $this;
    }

    /**
     * Add option in curl
     *
     * @param $option
     * @param $value
     * @return $this
     */
    public function add_option($option, $value)
    {
        curl_setopt($this->ch, $option, $value);

        return $this;
    }

    /**
     * Get code response
     *
     * @return int
     */
    public function get_code()
    {
        return $this->code;
    }

    /**
     * Get curl error
     *
     * @return string
     */
    public function get_error()
    {
        return curl_error($this->ch);
    }


    /**
     * Close curl connection
     *
     * @return void
     */
    public function close()
    {
        if ($this->ch) {
            curl_close($this->ch);
        }
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
            $response = curl_error($this->ch);
            $this->code = 404;
        } else {
            $this->code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        }
        $this->close();

        return $response;
    }
}