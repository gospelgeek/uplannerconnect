<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


namespace local_uplannerconnect\domain\service; 

use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\domain\management_factory;
use moodle_exception;

/**
 * Recalculate Item Weight
 */
class recalculate_item_weight
{    
    const IS_NATURAL = 13;
    const IS_SIMPLE = 11;
    const TABLE_CATEGORY = 'course_categories';
    const ITEM_TYPE_CATEGORY = 'category';
    const ALL_CATEGORY = "SELECT * FROM mdl_grade_categories WHERE courseid='%s' AND hidden = 0";
    const ITEMS_CATEGORY = "SELECT * FROM mdl_grade_items WHERE courseid='%s' AND categoryid = '%s' AND hidden = 0";
    const TOTAL_ITEMS = "SELECT SUM(t1.finalgrade) as total FROM mdl_grade_grades as t1 INNER JOIN mdl_grade_items as t2 ON t1.itemid = t2.id WHERE t2.courseid='%s' AND t2.itemtype NOT IN ('course', 'category') AND t2.hidden = 0 AND t1.userid = '%s'";
    const ALL_STUNDET_COURSE_QUALIFIED = "SELECT DISTINCT (t1.userid) FROM mdl_grade_grades as t1 INNER JOIN mdl_grade_items as t2 ON t1.itemid = t2.id WHERE t2.courseid='%s'";
    const MAX_ITEM_COURSE = "SELECT DISTINCT COUNT(t2.id) as count FROM mdl_grade_items as t2 WHERE t2.courseid='%s' AND t2.itemtype NOT IN ('course', 'category') AND t2.hidden = 0";

    private $moodle_query_handler;
    private $manageEntity;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->moodle_query_handler = new moodle_query_handler();
        $this->manageEntity = new management_factory();
    }

    /**
     * Recalculate Item Weight
     * 
     * @param array $data
     * @return void
     */
    public function recalculate_weight_evaluation(array $data) : void
    {
        try {
            if (!empty($data)) {
                // Get data.
                $event = $data['event'];
                $data = $event->get_data();
                $idCourse = $data['courseid'];
                
                $allStudentsCourse = $this->execute_query_sql(sprintf(
                    self::ALL_STUNDET_COURSE_QUALIFIED,
                    $idCourse
                ));

                $maxItemCourse = $this->execute_query_sql(sprintf(
                    self::MAX_ITEM_COURSE,
                    $idCourse
                ));
               
                if (!empty($allStudentsCourse) &&
                    is_array($allStudentsCourse) &&
                    !empty($maxItemCourse)
                ) {
                    $firstMaxItemCourse = reset($maxItemCourse);
                    $maxItemCourse  = $firstMaxItemCourse->count;
                   
                    foreach ($allStudentsCourse as $student) {
                       // Get Sum Total Qualified
                       $sumTotalQualified = $this->execute_query_sql(sprintf(
                            self::TOTAL_ITEMS,
                            $idCourse,
                            $student->userid
                        ));

                        if (!empty($sumTotalQualified)
                        ) {
                            $firstTotalQualified = reset($sumTotalQualified);
                            $sumTotalQualified  = $firstTotalQualified->total;
                            $newWeight = ($sumTotalQualified / $maxItemCourse);
                            error_log('sumTotalQualified: '. print_r($sumTotalQualified, true). "\n");
                            error_log('newWeight: '. print_r($newWeight, true). "\n");
                        } 
                    }
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
    }

    private function contruct_event(array $data)
    {
        $event = new \stdClass();
        try {
            if (!empty($data)) {
                $event->get_data =[
                    'courseid' => $data['courseid'],
                    'categoryid' => $data['categoryid']
                ];
                $event->get_grade_item = $data['get_grade_item'];
                $event->get_grade_item->aggregationcoef2 = $data['aggregationcoef2'];
                //get_grade
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $event;
    }

    /**
     * Verify if the item is a category
     * 
     * @param string $typeItem
     * @return bool
     */
    private function isItemCategory($typeItem) : bool
    {
        if (empty($typeItem)) {
            error_log('isItemCategory: There is no type item' . "\n");
            return false;
        }
        return $typeItem === self::ITEM_TYPE_CATEGORY;
    }

    private function get_grade_item(array $data)
    {
        $gradeItem = new \stdClass();
        try {
            $courseId = $data['courseid'];
            $idcategory = $data['categoryid'];
            $result = $this->execute_query_sql(sprintf(
                self::ITEMS_CATEGORY,
                $courseId,
                $idcategory
            ));

            if (!empty($result)) {
                $gradeItem = $result;
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $gradeItem;
    }

    private function get_categories($courseId)
    {
        $categories =  new \stdClass();
        try {
            $result = $this->execute_query_sql(sprintf(
                self::ALL_CATEGORY,
                $courseId
            ));
            if (!empty($result)) {
                $categories = $result;
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $categories;
    }

    private function execute_query_sql($sql)
    {
        $result = new \stdClass();
        try {
            if (!empty($sql)) {
                $queryResult = $this->moodle_query_handler->executeQuery($sql);
                if (!empty($queryResult)) {
                    $result = ($queryResult);
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $result;
    }

    private function instantiatemanagement()
    {
        try {
            // Verificar si existe el método
            if (method_exists($ManageEntity, 'create')) {
            // Llamar al método create
            $ManageEntity->create([
                    "dataEvent" => $data['dataEvent'],
                    "typeEvent" => $data['typeEvent'],
                    "dispatch" => $data['dispatch'],
                    "enum_etities" => $data['enum_etities']
            ]);
            } else {
            error_log("El método 'create' no existe en la clase management_factory.");
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
    }
}