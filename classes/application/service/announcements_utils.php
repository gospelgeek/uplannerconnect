<?php
/**
 * @package     local_uplannerconnect
 * @author      Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author      Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\service;

use local_uplannerconnect\application\repository\moodle_query_handler;
use moodle_exception;

/**
 * Filter only for rol of teacher
 */
class announcements_utils
{
    private $query;

    const ROL_TEACHER = "SELECT id FROM {role_assignments} WHERE userid = :userid AND roleid IN (2,3,30,4)";
    const PARENT_FORM_POST = "SELECT id FROM {forum_posts} WHERE id = :id AND parent = 0";

    /**
      * Construct
    */
    public function __construct()
    {
        $this->query = new moodle_query_handler();
    }

    /**
     * Filter only for rol of teacher
     */
    public function isRolTeacher($idUser)
    {
        $isTeacher = false;
        try {
            $params = ['userid' => $idUser];
            $result = $this->query->executeQuery(self::ROL_TEACHER, $params);
            if (!empty($result)) {
                $isTeacher = true;
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $isTeacher;
    }


    /**
     * Is Parent Form Post
     */
    public function isParentFormPost($idPost)
    {
        $isParent = false;
        try {
            $params = ['id' => $idPost];
            $result = $this->query->executeQuery(self::PARENT_FORM_POST, $params);
            if (!empty($result)) {
                $isParent = true;
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $isParent;
    }
}