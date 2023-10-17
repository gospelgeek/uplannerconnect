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
use moodle_exception;

/**
 *  Extraer los datos
 */
class course_utils
{
    const TABLE_CATEGORY = 'grade_categories';
    const TABLE_ITEMS = 'grade_items';
    
    private $validator;
    private $moodle_query_handler;

    /**
     *  Construct
     */
    public function __construct()
    {
        $this->validator = new data_validator();
        $this->moodle_query_handler = new moodle_query_handler();
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
                return $arraySend;
            }

            //Traer la información
            $event = $data['dataEvent'];
            $getData = $this->validator->isArrayData($event->get_data());
            $grade = $this->validator->isObjectData($event->get_grade());
            $gradeRecordData = $this->validator->isObjectData($grade->get_record_data());
            $gradeLoadItem = $this->validator->isObjectData($grade->load_grade_item());
            $categoryItem = $this->getInstanceCategoryName($gradeLoadItem);
            $categoryFullName = $this->shortCategoryName($categoryItem); 
            $aproved = $this->getAprovedItem($gradeLoadItem, $grade);

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

            //información a guardar
            $dataToSave = [
                'sectionId' => $this->validator->isIsset($queryCourse->shortname),
                'studentCode' => $this->validator->isIsset($queryStudent->username),
                'evaluationGroupCode' => $this->validator->isIsset($categoryFullName), //Bien
                'evaluationId' => $this->validator->isIsset($gradeLoadItem->id),
                'average' => $this->validator->isIsset($grade->aggregationweight),
                'isApproved' => $this->validator->isIsset($aproved),
                'value' => $this->validator->isIsset(($getData['other'])['finalgrade']),
                'evaluationName' => $this->validator->isIsset($gradeLoadItem->itemname),
                'date' => $this->validator->isIsset($gradeLoadItem->timecreated),
                'lastModifiedDate' => $this->validator->isIsset($gradeLoadItem->timemodified),
                'action' => $data['dispatch'],
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
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
                return $arraySend;
            }

            //Traer la información
            $event = $data['dataEvent'];
            $get_grade_item = $this->validator->isObjectData($event->get_grade_item());
            //category info
            $categoryItem = $this->getInstanceCategoryName($get_grade_item);
            $categoryFullName = $this->shortCategoryName($categoryItem); 

            $queryCourse = ($this->validator->verifyQueryResult([                        
                'data' => $this->moodle_query_handler->extract_data_db([
                    'table' => plugin_config::TABLE_COURSE,
                    'conditions' => [
                        'id' => $this->validator->isIsset($get_grade_item->courseid)
                    ]
                ])
            ]))['result'];
            
            $dataToSave = [
                'sectionId' => $this->validator->isIsset($queryCourse->shortname),
                'evaluationGroupCode' => $this->validator->isIsset($categoryFullName),
                'evaluationGroupName' => $this->validator->isIsset(substr($categoryItem, 0, 50)),
                'evaluationId' => $this->validator->isIsset($get_grade_item->id),
                'evaluationName' => $this->validator->isIsset($get_grade_item->itemname),
                'action' => $data['dispatch']
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $dataToSave;
    }

    /**
     * Retorna el nombre de la categoria
     * 
     * @param object $gradeItem
     * @return bool
     */
    private function getAprovedItem($gradeItem , $gradesGrades) : bool
    {
        $boolean = false;
        if ($gradeItem->grademax) {
            if ($gradeItem->grademax <= $gradesGrades->finalgrade) {
                $boolean = true;
            }
        }
        return $boolean;
    }

    /**
     * Retorna el nombre de la categoria
     * 
     * @param object $gradeItem
     * @return string
     */
    private function getInstanceCategoryName($gradeItem) : string
    {
        $categoryFullName = 'NIVEL000';
        //validar si existe el metodo
        if (property_exists($gradeItem, 'id')) {
            // Ejecutar la consulta.
            $queryResult = $this->moodle_query_handler->executeQuery(sprintf(
                plugin_config::QUERY_NAME_CATEGORY_GRADE, 
                'mdl_'.self::TABLE_ITEMS, 
                'mdl_'.self::TABLE_CATEGORY, 
                $gradeItem->id
            ));
            // Obtener el primer elemento del resultado utilizando reset()
            $firstResult = reset($queryResult);
            if (isset($firstResult->fullname) && 
                strlen($firstResult->fullname) !== 0)
            {
              // Luego, obtén el valor de 'fullname'
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
}