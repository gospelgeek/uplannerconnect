<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure\api\client;

/**
 * uPlanner client grades
 */
class uplanner_client_grade extends abstract_uplanner_client
{
    /**
     * @inerhitdoc
     */
    protected string $name_file = 'uplanner_client_grade_date.csv';

    /**
     * @inerhitdoc
     */
    protected string $email_subject = 'uplanner_email_subject_grades';

    /**
     * @inerhitdoc
     */
    protected string $config_topic = 'grades_endpoint';
}