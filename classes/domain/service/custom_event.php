<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


namespace local_uplannerconnect\domain\service; 

class custom_event
{
    protected $data;
    public $finalgradeGrades;
    /**
     * Construct
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->finalgradeGrades = $data['finalgradeGrades'] ?? 0;
    }

    /**
     * @return mixed
     */
    public function get_data()
    {
        return [
            'other' => [
                'finalgrade' => $this->finalgradeGrades ?? 0,
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