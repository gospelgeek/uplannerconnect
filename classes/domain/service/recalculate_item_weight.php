<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\service; 

use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\domain\management_factory;
use local_uplannerconnect\plugin_config\plugin_config;
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
    const ALL_STUNDET_COURSE_QUALIFIED = "SELECT DISTINCT (t1.userid) FROM mdl_grade_grades as t1 INNER JOIN mdl_grade_items as t2 ON t1.itemid = t2.id WHERE t2.courseid='%s'";
    const TOTAL_ITEMS = "SELECT
                            SUM(t1.finalgrade) OVER (ORDER BY t1.finalgrade DESC) AS total,
                            t2.id AS idGradeItem,
                            t2.timecreated AS timecreatedGradeItem,
                            t2.timemodified AS timemodifiedGradeItem,
                            t2.itemname AS itemnameGradeItem,
                            t2.grademax AS grademaxGradeItem,
                            t1.finalgrade AS finalgradeGrades,
                            t1.userid AS useridGrades,
                            t2.courseid AS courseidGradeItem
                        FROM
                            mdl_grade_grades AS t1
                            INNER JOIN mdl_grade_items AS t2 ON t1.itemid = t2.id
                        WHERE
                            t2.courseid = '%s'
                            AND t2.itemtype NOT IN ('course', 'category')
                            AND t2.hidden = 0
                            AND t1.userid = '%s'
                            AND t1.finalgrade IS NOT NULL";

    private $moodle_query_handler;
    private $manageEntity;
    private $custom_event;

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
                    plugin_config::MAX_ITEM_COURSE,
                    $idCourse
                ));
               
                if (empty($allStudentsCourse) &&
                    !(is_array($allStudentsCourse)) &&
                    empty($maxItemCourse)
                ) { return; }

                $firstMaxItemCourse = reset($maxItemCourse);
                $maxItemCourse  = $firstMaxItemCourse->count;
                
                foreach ($allStudentsCourse as $student) {
                    // Get Sum Total Qualified
                    $sumTotalQualified = $this->execute_query_sql(sprintf(
                        self::TOTAL_ITEMS,
                        $idCourse,
                        $student->userid
                    ));
                    
                    if (empty($sumTotalQualified)) { continue; }

                    foreach ($sumTotalQualified as $value) {
                     
                        if (!isset($value->finalgradegrades)) { continue; }

                        $totalQualified = end($sumTotalQualified);
                        $sumTotal  = $totalQualified->total;
                        $newWeight = ($sumTotal / $maxItemCourse) / 100;
                        
                        $event_ = new custom_event([
                            'finalgradeGrades' => $value->finalgradegrades,
                            'idGradeItem' => $value->idgradeitem,
                            'timecreatedGradeItem' => $value->timecreatedgradeitem,
                            'timemodifiedGradeItem' => $value->timemodifiedgradeitem,
                            'itemnameGradeItem' => $value->itemnamegradeitem,
                            'grademaxGradeItem' => $value->grademaxgradeitem,
                            'useridGrades' => $value->useridgrades,
                            'courseidGradeItem' => $value->courseidgradeitem,
                            'newWeightGradeItem' => $newWeight
                        ]);

                        $this->manageEntity->create([
                                "dataEvent" => $event_,
                                "typeEvent" => "user_graded",
                                "dispatch" => 'update',
                                "enum_etities" => 'course_notes'
                        ]);
                    } 
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
    }

    /**
     * Execute query sql
     * 
     * @param string $sql
     * @return object
     */
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

    /**
     * Instantiatemanagement
     * 
     * @return void
     */
    private function instantiatemanagement() : void
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