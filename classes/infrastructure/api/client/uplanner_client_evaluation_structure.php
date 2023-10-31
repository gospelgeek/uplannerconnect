<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\api\client;

/**
 * uPlanner evaluation structure client
 */
class uplanner_client_evaluation_structure extends abstract_uplanner_client
{
    /**
     * @inerhitdoc
     */
    protected string $name_file = 'uplanner_client_evaluation_structure_date.csv';

    /**
     * @inerhitdoc
     */
    protected string $email_subject = 'upllaner_email_subject_evaluations';

    /**
     * @inerhitdoc
     */
    protected string $config_topic = 'evaluation_structure_endpoint';
}