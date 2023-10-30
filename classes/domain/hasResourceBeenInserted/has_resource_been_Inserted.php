<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\hasResourceBeenInserted;

use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\event\resource_file;
use moodle_exception;
use \stdClass;

class has_resource_been_Inserted
{
    private $moodle_query_handler;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->moodle_query_handler = new moodle_query_handler();
    }

    /**
     *  Verifiquited if the resource has been inserted
     * 
     */
    public function isInsertedResource(array $data)
    {
        try {
            $table = $data['table'];
            $idCourse = $data['idCourse'];
            $time = strtotime('-1 minutes',$data['time']);

            $dataResult = $this->moodle_query_handler->executeQuery(
            "SELECT * FROM {resource} WHERE course = $idCourse AND timemodified >= $time"
            );
            $count = count($dataResult);
            if ($count > 0) { $this->triggerEventResource(); }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ' . $e->getMessage() . "\n");
        }
    }

    /**
     *  Trigger event resource 
     */
    private function triggerEventResource()
    {
        // Trigger academic period updated event
        $context = \context_system::instance();
        $params = array(
            "objectid" => 128,
            "context" => $context,
            "other" => array(
                "userid" => 128
            )
        );
        $event = \local_uplannerconnect\event\resource_file::create($params);
        $event->trigger();
    }
}