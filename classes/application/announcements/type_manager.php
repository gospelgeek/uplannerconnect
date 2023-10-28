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
        $announcements_extraction_data  = announcements_extraction_data::class;
        $announcements_translation_data = announcements_translation_data::class;
        $announcements_repository = announcements_repository::class;

        $this->typeManager = [
            'announcements' => [
                'announcementsExtractionData'  => $announcements_extraction_data,
                'announcementsTranslationData' => $announcements_translation_data,
                'announcementsRepository' => $announcements_repository,
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