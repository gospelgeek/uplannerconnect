<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\service; 

class grade_custom
{
    private $data;
    public $id;
    public $finalgrade;
    public $userid;
    public $grade_item;

    /**
     * Construct
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->id = $data['idGradeGrades'] ?? 0;
        $this->finalgrade = $data['finalgradeGrades'] ?? 0;
        $this->userid = $data['useridGrades'];
        $this->grade_item = new \stdClass();
        $this->grade_item->courseid = $data['courseidGradeItem'];
    }

    /**
     * @return mixed
     */
    public function get_record_data()
    {
        return new \stdClass();
    }

    /**
     * @return mixed
    */
    public function load_grade_item()
    {
        $gradeItem = new \stdClass();
        $gradeItem->id = $this->data['idGradeItem'] ?? 0;
        $gradeItem->timecreated = $this->data['timecreatedGradeItem'] ?? 0;
        $gradeItem->timemodified = $this->data['timemodifiedGradeItem'] ?? 0;
        $gradeItem->itemname = $this->data['itemnameGradeItem'] ?? '';
        $gradeItem->aggregationcoef2 = $this->data['newWeightGradeItem'] ?? 0;
        $gradeItem->aggregationcoef = 0;
        $gradeItem->grademax = $this->data['grademaxGradeItem'];
        return $gradeItem;
    }
}