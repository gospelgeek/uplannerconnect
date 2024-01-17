<?php
/**
 * @package  local_uplannerconnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co> 
 */

namespace local_uplannerconnect\application\course;

use local_uplannerconnect\domain\course\course_translation_data;
use local_uplannerconnect\domain\course\course_extraction_data;
use local_uplannerconnect\application\repository\course_evaluation_structure_repository;
use local_uplannerconnect\application\general\data_manager;

/**
 *  Orquest the process of course evaluation structure
 */
class course_evaluation_structure extends data_manager
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
            'repository' => course_evaluation_structure_repository::class,
        ]);
    }
}