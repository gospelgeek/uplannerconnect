<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\course\usecases;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\plugin_config\plugin_config;
use local_uplannerconnect\domain\service\transition_endpoint;
use local_uplannerconnect\domain\service\utils;
use moodle_exception;

/**
 *  Extraer los datos
 */
class course_utils
{
    const TABLE_CATEGORY = 'grade_categories';
    const TABLE_ITEMS = 'grade_items';
    const ITEM_TYPE_CATEGORY = 'category';
    const RECALCULATE_AGGREATIONS = [11,6];
    const IS_SIMPLE = 11;

    private $validator;
    private $moodle_query_handler;
    private $transition_endpoint;
    private $utils_service;

    /**
     *  Construct
     */
    public function __construct()
    {
        $this->validator = new data_validator();
        $this->moodle_query_handler = new moodle_query_handler();
        $this->transition_endpoint = new transition_endpoint();
        $this->utils_service = new utils();
    }

    /**
     * Retorna los datos del evento user_graded
     *
     * @return array
     */
    public function resourceUserGraded(array $data) : array
    {
        $dataToSave = [];
        try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento user_graded');
                return $dataToSave;
            }

            //Traer la información
            $event = $data['dataEvent'];
            $getData = $this->validator->isArrayData($event->get_data());
            $grade = $this->validator->isObjectData($event->get_grade());
            $gradeRecordData = $this->validator->isObjectData($grade->get_record_data());
            $gradeLoadItem = $this->validator->isObjectData($grade->load_grade_item());
            $categoryItem = $this->getInstanceCategoryName($gradeLoadItem);
            $categoryFullName = $this->shortCategoryName($categoryItem); 
            $approved = $this->getApprovedItem($gradeLoadItem, $grade);

            $queryStudent = $this->validator->verifyQueryResult([
                'data' => $this->moodle_query_handler->extract_data_db([
                    'table' => plugin_config::TABLE_USER_MOODLE,
                    'conditions' => [
                        'id' => $this->validator->isIsset($grade->userid)
                    ]
                ]) 
            ])['result'];
             
            $queryCourse = ($this->validator->verifyQueryResult([                        
                'data' => $this->moodle_query_handler->extract_data_db([
                    'table' => plugin_config::TABLE_COURSE,
                    'conditions' => [
                        'id' => $this->validator->isIsset($grade->grade_item->courseid)
                    ]
                ])
            ]))['result'];

            $aggregationCategory = $this->validator->isIsset($this->getAggreationCategory($grade->grade_item->courseid));

            $timestamp =  $this->validator->isIsset(($gradeLoadItem->timecreated));
            $formattedDateCreated = date('Y-m-d', $timestamp);
            $timestampMod =  $this->validator->isIsset(($gradeLoadItem->timemodified));
            $formattedDateModified = date('Y-m-d', $timestampMod);
            $weightGrade = $this->validator->isIsset($this->getWeightGrade([
                'gradeItem' => $gradeLoadItem,
                'aggregation' => $aggregationCategory,
                'idCourse' => $grade->grade_item->courseid,
                'student' => $grade->userid
            ]));

            //información a guardar
            $dataToSave = [
                'sectionId' => $this->validator->isIsset($this->utils_service->convertFormatUplanner($queryCourse->shortname)),
                'studentCode' => $this->validator->isIsset($queryStudent->username),
                'evaluationGroupCode' => $this->validator->isIsset($categoryFullName), //Bien
                'evaluationId' => $this->validator->isIsset(intval($gradeLoadItem->id)),
                'average' => $this->validator->isIsset(strval($weightGrade)),
                'isApproved' => $this->validator->isIsset($approved),
                'value' => $this->validator->isIsset(strval(($getData['other'])['finalgrade'])),
                'evaluationName' => $this->validator->isIsset($gradeLoadItem->itemname),
                'date' => $this->validator->isIsset($formattedDateCreated),
                'lastModifiedDate' => $this->validator->isIsset($formattedDateModified),
                'action' => strtoupper($data['dispatch']),
                'transactionId' => $this->validator->isIsset($this->transition_endpoint->getLastRowTransaction($grade->grade_item->courseid)),
                'aggregation' => $this->validator->isIsset($aggregationCategory),
                'courseid' => $this->validator->isIsset($grade->grade_item->courseid),
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $dataToSave;
    }

    /**
     * Retorna los datos del evento grade_item_created
     *
     * @return array
     */
    public function resourceGradeItemCreated(array $data) : array
    {
        $dataToSave = [];
        try {
            if (empty($data['dataEvent'])) {
                error_log('No le llego la información del evento user_graded');
                return $dataToSave;
            }

            //Traer la información
            $event = $data['dataEvent'];
            $get_grade_item = $this->validator->isObjectData($event->get_grade_item());
            $dataEvent = $this->validator->isIsset($event->get_data());
            $grade = null;

            if (key_exists('userid', $dataEvent)) {
                $grade = $this->validator->isObjectData($get_grade_item->get_grade($dataEvent['userid'], false));    
            }
            
            //category info
            $itemType = $this->validator->isIsset($get_grade_item->itemtype);
            $itemName = $get_grade_item->itemname;

            if ($itemType === self::ITEM_TYPE_CATEGORY) {
                $iteminstance = $this->validator->isIsset($get_grade_item->iteminstance);
                $dataCategory = $this->getDataCategories($iteminstance);
                $categoryItem = $this->getNameCategoryItem($dataCategory);
                $categoryFullName = $this->shortCategoryName($categoryItem);
                $itemName = $categoryItem.' total';
            } else {
                $categoryItem = $this->getInstanceCategoryName($get_grade_item);
                $categoryFullName = $this->shortCategoryName($categoryItem);
            }
            $weight = $this->validator->isIsset($this->getWeight($get_grade_item)) ?? 0;

            $queryCourse = ($this->validator->verifyQueryResult([                        
                'data' => $this->moodle_query_handler->extract_data_db([
                    'table' => plugin_config::TABLE_COURSE,
                    'conditions' => [
                        'id' => $this->validator->isIsset($get_grade_item->courseid)
                    ]
                ])
            ]))['result'];

            $dataToSave = [
                'sectionId' => $this->validator->isIsset($this->utils_service->convertFormatUplanner($queryCourse->shortname)),
                'evaluationGroupCode' => $this->validator->isIsset($categoryFullName),
                'evaluationGroupName' => $this->validator->isIsset(substr($categoryItem, 0, 50)),
                'evaluationId' => $this->validator->isIsset(intval($get_grade_item->id)),
                'evaluationName' => $this->validator->isIsset($itemName),
                'weight' => floatval($weight),
                'action' => strtoupper($data['dispatch']),
                "date" => $this->validator->isIsset(strval($dataEvent['timecreated'])),
                'transactionId' => $this->validator->isIsset($this->transition_endpoint->getLastRowTransaction($get_grade_item->courseid)),
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $dataToSave;
    }

    /**
     * Retorna el nombre de la categoria
     * 
     * @param object $gradeItem
     * @return bool
     */
    private function getApprovedItem($gradeItem , $gradesGrades) : bool
    {
        $boolean = false;
        if ($gradeItem->grademax) {
            $boolean = ($gradesGrades->finalgrade / $gradeItem->grademax) >= 0.6;
        }
        return $boolean;
    }

    /**
     * Return name of category
     * 
     * @param object $gradeItem
     * @return string
     */
    private function getInstanceCategoryName($gradeItem) : string
    {
        $categoryFullName = 'NIVEL000';
        // Validate if property exists
        if (property_exists($gradeItem, 'id')) {
            // Ejecutar la consulta.
            $queryResult = $this->moodle_query_handler->executeQuery(sprintf(
                plugin_config::QUERY_NAME_CATEGORY_GRADE, 
                '{'.self::TABLE_ITEMS.'}', 
                '{'.self::TABLE_CATEGORY.'}', 
                )
                , ['id' => $gradeItem->id]
            );
            // Get first result.
            $firstResult = reset($queryResult);
            if (isset($firstResult->fullname) && 
                strlen($firstResult->fullname) !== 0 && 
                $firstResult->fullname !== '?')
            {
              // get value of 'fullname'
              $categoryFullName = $firstResult->fullname;
            }
        }
        return $categoryFullName;
    }

    /**
     * Retorna 10 caracteres del nombre de la categoria
     * 
     * @param string $categoryFullName
     * @return string
     */
    private function shortCategoryName($categoryFullName) : string
    {
        $sinEspacios = str_replace(' ', '', $categoryFullName);
        $categoryShort = substr($sinEspacios, 0, 10);
        return $categoryShort;
    }

    /**
     * Return weight of category
     * 
     * @param object $gradeItem
     * @return float
     */
    private function getWeight($gradeItem)
    {
        $weight = 0;
        if (property_exists($gradeItem, 'aggregationcoef2')) {
            $weight = $gradeItem->aggregationcoef2;
            if ($gradeItem->aggregationcoef2 == 0 ||
                $gradeItem->aggregationcoef2 == 0.00000) {
                $weight = $gradeItem->aggregationcoef;
            }
        }
        return $weight;
    }

        /**
     * Return weight of category
     * 
     * @param object $gradeItem
     * @return float
     */
    private function getWeightGrade(array $data)
    {
        $weight = 0;
        $gradeItem = $data['gradeItem'];
        $aggration = $data['aggregation'];
        $idCourse = $data['idCourse'];
        $student = $data['student'];

        if (in_array($aggration, self::RECALCULATE_AGGREATIONS)) {
            // Execute query sql
            $maxItemsCourse =  $this->moodle_query_handler->executeQuery(
                plugin_config::MAX_ITEM_COURSE,
                [
                    'courseid' => $idCourse
                ]
            );

            // Get Max Item Course
            $firstMaxItemCourse = reset($maxItemsCourse);
            $maxItemsCourse  = $firstMaxItemCourse->count;

            $isAggreationSimple = $aggration == self::IS_SIMPLE;
            $query = ($isAggreationSimple)? plugin_config::SUM_TOTAL_GRADE : plugin_config::MAX_STUDENT_GRADE;
            // Get Sum Total Qualified
            $sumTotalQualified = $this->moodle_query_handler->executeQuery(
                $query,
                [
                    'courseid' => $idCourse,
                    'userid' => $student
                ]
            );

            $resulTotalGrades = reset($sumTotalQualified);
            if ($isAggreationSimple) {  
                $weight = ($resulTotalGrades->total / $maxItemsCourse) / 100;
            }
            else {
                $weight = ($resulTotalGrades->nota_maxima / $gradeItem->grademax);
            }
        } 
        else if (property_exists($gradeItem, 'aggregationcoef2')) {
            $weight = $gradeItem->aggregationcoef2;
            if ($gradeItem->aggregationcoef2 == 0 ||
                $gradeItem->aggregationcoef2 == 0.00000) {
                $weight = $gradeItem->aggregationcoef;
            }
        }
        return $weight;
    }

    /**
     * Retorna el nombre de la categoria
     * 
     * @param object $gradeItem
     * @return string
     */
    private function getNameCategoryItem($queryResult)
    {
        $nameCategory = 'ISCATEGORY001';
        try {
            if (!empty($queryResult)) {
                if (isset($queryResult->fullname) && 
                    strlen($queryResult->fullname) !== 0 && 
                    $queryResult->fullname !== '?')
                {
                  // Luego, obtén el valor de 'fullname'
                  $nameCategory = $queryResult->fullname;
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $nameCategory;
    }

    /**
     * Return all data of category
     * 
     */
    private function getDataCategories($idCategory)
    {
        $objectClass = new \stdClass();
        try {
            if (!empty($idCategory)) {
                $queryResult = $this->moodle_query_handler->extract_data_db([
                    'table' => self::TABLE_CATEGORY,
                    'conditions' => [
                        'id' => $idCategory
                    ]
                ]);
                $objectClass = $queryResult;
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $objectClass;
    }

    /**
     * Return aggregation category
     */
    private function getAggreationCategory($idCourse)
    {
        $aggregationCategory = 0;
        try {
            if (!empty($idCourse)) {
                // Ejecutar la consulta.
                $queryResult = $this->moodle_query_handler->executeQuery(
                    plugin_config::AGGREGATION_CATEGORY_FATHER,
                    [
                        'courseid' => $idCourse
                    ]
                );
                // Obtener el primer elemento del resultado utilizando reset()
                $firstResult = reset($queryResult);
                $aggregationCategory = $firstResult->aggregation ?? 0;
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $aggregationCategory;
    }
}