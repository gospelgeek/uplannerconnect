<?php
/**
 * @package     local_uplannerconnec
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\application\repository;

use local_uplannerconnect\application\messages\messages_resource;

/**
 * Loaded class to manipulate data in uplanner_esb_messages_status table
 */
class messages_status_repository
{
    /**
     * @var messages_resource
     */
    private $messages_resource;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->messages_resource = new messages_resource();
    }

    /**
     * get message by transaction id
     *
     * @param $transaction_id
     * @return array|null
     */
    public function get_by_transaction_id($transaction_id)
    {
        return $this->messages_resource->get_message($transaction_id);
    }
}