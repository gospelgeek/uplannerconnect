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
    public function __construct(string $type)
    {
        $this->typeManager = [
            'grades' => [
                'courseExtractionData' =>  course_extraction_data::class,
                'courseTranslationData' => course_translation_data::class,
                'courseNotesRepository' => course_notes_repository::class,
            ],
            'evaluation_structure' => [
                'courseExtractionData' =>  course_extraction_data::class,
                'courseTranslationData' => course_translation_data::class,
                'courseNotesRepository' => course_evaluation_structure_repository::class,
            ],
        ];
    }

    /**
     * Obtiene el tipo de manager
     *
     * @param string $type
     * @return array
     */
    public function getTypeManager(string $type) : array
    {
        return $this->typeManager[$type];
    }
}