<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\materials;

use local_uplannerconnect\domain\materials\material_extraction_data;
use local_uplannerconnect\domain\materials\material_translation_data;
use local_uplannerconnect\application\repository\material_repository;

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
        $material_extraction_data  = material_extraction_data::class;
        $material_translation_data = material_translation_data::class;
        $material_repository = material_repository::class;

        $this->typeManager = [
            'materials' => [
                'materialExtractionData'  => $material_extraction_data,
                'materialTranslationData' => $material_translation_data,
                'materialRepository' => $material_repository,
            ]
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