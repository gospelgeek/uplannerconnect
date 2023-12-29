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
use local_uplannerconnect\application\general\data_manager;

/**
 * Orquest the process of materials 
 */
class material_resource extends data_manager
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
            'extractionData' => material_extraction_data::class,
            'translationData' => material_translation_data::class,
            'repository' => material_repository::class,
        ]);
    }
}