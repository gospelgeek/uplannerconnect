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
    const IS_HIGHEST = 6;
    const IS_SIMPLE = 11;
    const TABLE_CATEGORY = 'course_categories';
    const ITEM_TYPE_CATEGORY = 'category';
    const TOTAL_ITEMS = "SELECT
                            t1.id AS idGrade,
                            t2.id AS idGradeItem,
                            t2.timecreated AS timecreatedGradeItem,
                            t2.timemodified AS timemodifiedGradeItem,
                            t2.itemname AS itemnameGradeItem,
                            t2.grademax AS grademaxGradeItem,
                            t1.finalgrade AS finalgradeGrades,
                            t1.userid AS useridGrades,
                            t2.courseid AS courseidGradeItem,
                            t2.grademax AS grademaxGradeItem
                        FROM
                            {grade_grades} AS t1
                            INNER JOIN {grade_items} AS t2 ON t1.itemid = t2.id
                        WHERE
                            t2.courseid = :courseid
                            AND t2.itemtype NOT IN ('course', 'category')
                            AND t2.hidden = 0
                            AND EXISTS (
                                SELECT 1
                                FROM {grade_grades} AS t3
                                WHERE t3.userid = t1.userid
                            )
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
                $aggregationCategory =  $data['aggregationCategory'];
                $data = $event->get_data();
                $idCourse = $data['courseid'];
                
                $maxItemCourse = $this->execute_query_sql([
                    'sql' => plugin_config::MAX_ITEM_COURSE,
                    'params' => [
                        'courseid' => $idCourse
                    ]
                ]);

                // Get Sum Total Qualified
                $sumTotalQualified = $this->execute_query_sql([
                    'sql' => self::TOTAL_ITEMS,
                    'params' => [
                        'courseid' => $idCourse
                    ]
                ]);

                if (empty($sumTotalQualified) &&
                    !(is_array($sumTotalQualified)) &&
                    empty($maxItemCourse)
                ) { return; }
                
                $firstMaxItemCourse = reset($maxItemCourse);
                $maxItemCourse  = $firstMaxItemCourse->count;

                $isAggreationSimple = $aggregationCategory == self::IS_SIMPLE;
                $query = ($isAggreationSimple)? plugin_config::SUM_TOTAL_GRADE : plugin_config::MAX_STUDENT_GRADE;
                
                foreach ($sumTotalQualified as $value) {
                    
                    if (!isset($value->finalgradegrades)) { continue; }
                   
                    // Get Sum Total Qualified
                    $sumTotalResult = $this->execute_query_sql([
                        'sql' => $query,
                        'params' => [
                            'courseid' => $idCourse,
                            'userid' => $value->useridgrades
                        ]
                    ]);

                    $totalQualified = reset($sumTotalResult);
                    if ($isAggreationSimple) {
                        $newWeight = ($totalQualified->total / $maxItemCourse) / 100;
                    }
                    else {
                        $newWeight = $totalQualified->nota_maxima / $value->grademaxgradeitem;
                    }
                    
                    
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
    private function execute_query_sql(array $data)
    {
        $result = new \stdClass();
        try {
            if (!empty($data)) {
                $sql = $data['sql'];
                $params = $data['params'] ?? [];
                $queryResult = $this->moodle_query_handler->executeQuery($sql, $params);
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