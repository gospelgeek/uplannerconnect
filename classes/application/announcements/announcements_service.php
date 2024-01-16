<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\announcements;

use local_uplannerconnect\domain\announcements\announcements_extraction_data;
use local_uplannerconnect\domain\announcements\announcements_translation_data;
use local_uplannerconnect\application\repository\announcements_repository;
use local_uplannerconnect\application\general\data_manager;

/**
 * Orquest the process of announcements
 */
class announcements_service extends data_manager
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
            'extractionData' => announcements_extraction_data::class,
            'translationData' => announcements_translation_data::class,
            'repository' => announcements_repository::class,
        ]);
    }
}