<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\course;

use local_uplannerconnect\domain\course\course_translation_data;
use local_uplannerconnect\domain\course\course_extraction_data;
use local_uplannerconnect\application\repository\course_notes_repository;
use local_uplannerconnect\application\repository\course_evaluation_structure_repository;

/**
 * Gestiona los tipos de manager
 */
class type_manager
{
    private $typeManager;

    /**
     *  Construct
     */
    public function __construct()
    {
        $course_extraction_data  = course_extraction_data::class;
        $course_translation_data = course_translation_data::class;
        $course_notes_repository = course_notes_repository::class;
        $course_evaluation_structure = course_evaluation_structure_repository::class;

        $this->typeManager = [
            'grades' => [
                'courseExtractionData'  => $course_extraction_data,
                'courseTranslationData' => $course_translation_data,
                'courseNotesRepository' => $course_notes_repository,
            ],
            'evaluation_structure' => [
                'courseExtractionData'  => $course_extraction_data,
                'courseTranslationData' => $course_translation_data,
                'courseNotesRepository' => $course_evaluation_structure,
            ],
        ];
    }

    /**
     * Obtiene el tipo de manager
     *
     * @param string $type
     */
    public function getTypeManager(string $type) 
    {
        return $this->typeManager[$type];
    }
}