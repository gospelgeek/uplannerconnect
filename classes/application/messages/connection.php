<?php
/**
 * @package     local_uplannerconnec
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\messages;

use local_uplannerconnect\plugin_config\plugin_config;

/**
 * Class Connection AzureSql DB
 */
class connection
{
    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Connection
     *
     * @var false|resource
     */
    private $connection;

    /**
     * Construct
     */
    private function __construct() {
        $this->connection = sqlsrv_connect(
            $this->getServerName(),
            $this->getOptions()
        );
        if ($this->connection ) {
            error_log('********** CONNECTION ESTABLISHED : ' . PHP_EOL);
        } else{
            error_log('********** CONNECTION NOT BE ESTABLISHED : ' . json_encode(sqlsrv_errors())  . PHP_EOL);
        }
    }

    /**
     * @return connection
     */

    public static function getInstance(): ?connection
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get connection db
     *
     * @return mixed
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Singletons should not be cloneable.
     */
    private function __clone() { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * Server host
     *
     * @return false|mixed|object|string
     * @throws \dml_exception
     */
    private function getServerName()
    {
        $port = get_config(plugin_config::PLUGIN_NAME_LOCAL, 'messages_port') ?? '';
        $host = get_config(plugin_config::PLUGIN_NAME_LOCAL, 'messages_host') ?? '';;
        if ($port && $host) {
            $host .= ',' . $port;
        }

        return $host;
    }

    /**
     * Server options
     *
     * @return array
     * @throws \dml_exception
     */
    private function getOptions()
    {
        return [
            "Database" => get_config(plugin_config::PLUGIN_NAME_LOCAL, 'messages_database') ?? '',
            "Uid" => get_config(plugin_config::PLUGIN_NAME_LOCAL, 'messages_user') ?? '',
            "PWD" => get_config(plugin_config::PLUGIN_NAME_LOCAL, 'messages_password') ?? ''
        ];
    }
}