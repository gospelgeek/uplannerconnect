<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


namespace local_uplannerconnect\domain\service; 

class custom_event
{
    private $data;

    /**
     * Construct
     */
    public function __construct($data)
    {
        error_log('custom_event');
        error_log(print_r($data, true));
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function get_data()
    {
        return [
            'other' => [
                'finalgrade' => $this->data->finalgradeGrades ?? 0,
            ]
        ];
    }

    /**
     * @return mixed
     */
    public function get_grade()
    {
       $grade = new grade_custom($this->data);
       return $grade;
    }
}