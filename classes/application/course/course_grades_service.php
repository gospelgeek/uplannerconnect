<?php
/**
 * @package     local_uplannerconnect
 * @author      cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @author      daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\course;

use local_uplannerconnect\domain\course\course_translation_data;
use local_uplannerconnect\domain\course\course_extraction_data;
use local_uplannerconnect\application\repository\course_notes_repository;
use local_uplannerconnect\application\general\data_manager;

/**
   *  Orquest the process of course grades
*/
class course_grades_service extends data_manager
{
    /**
     * Extract the data
     * Transform the data
     * Save the data
     *
     * @param array $data
     * @return void
    */
    public function process(array $data) : void
    {
        parent::process([
            'data' => $data,
            'extractionData' => course_extraction_data::class,
            'translationData' => course_translation_data::class,
            'repository' => course_notes_repository::class,
        ]);
    }
}