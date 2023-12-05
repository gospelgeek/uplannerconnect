<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\application\service;

use local_uplannerconnect\application\repository\moodle_query_handler;
use moodle_exception;

/**
 * Class filter_evaluation_update
 */
class filter_evaluation_update
{
    const LAST_INSERT_EVALUATION = "SELECT date,json FROM {uplanner_evaluation} ORDER BY id DESC limit 1";
    const ITEMTYPE_UPDATE = 'course';
    const IS_ITEM_UPDATE = 'UPDATED';

    private $query;

    /** 
      * Construct
    */
    public function __construct()
    {
        $this->query = new moodle_query_handler();
    }

    /**
      * Filter recent update
    */
    public function filterRecentUpdate($event)
    {
        $isFilter = false;
        try {
                $evaluation = $this->query->executeQuery(self::LAST_INSERT_EVALUATION);
                // Is category Father
                $isCategoryFather = true;
                // data of item
                $grade_item_load =  $event->get_grade_item();
                $get_data_category = $grade_item_load->get_item_category();

                // Verificate if is category father
                if (isset($get_data_category->depth)) {
                    $depth_category = $get_data_category->depth;
                    if ($depth_category == 1) {
                        $isCategoryFather = false;
                    }
                }
 
                if (!empty($evaluation) && $isCategoryFather) { 
                    //obeter el primer resultado
                    $firstResult = reset($evaluation);
                    //obtener el json
                    $evaluationLast = (json_decode($firstResult->json));
                    $date = intval($firstResult->date);
                    $dataEvent = $event->get_data();
                    //obtener el primer grupo de evaluaciones
                    $evaluationGroups = ($evaluationLast->evaluationGroups)[0];
                    //obtener la primera evaluacion
                    $evaluationsData  = ($evaluationGroups->evaluations)[0];
                    $validateUpdateNew = $this->isUpdateItem([
                    "dataEvent" => $dataEvent,
                    "evaluationLast" => $evaluationLast,
                    "evaluationsData" => $evaluationsData,
                    "date" => $date
                    ]);

                    $isTotalItem = $this->isTotalItem($event->get_grade_item());
                    $itemActions = strtolower($evaluationLast->action.'d');

                    // Validar si la evaluacion es diferente
                    if (($itemActions !== $dataEvent['action'] &&
                        $evaluationsData->evaluationId === $dataEvent['objectid']) ||
                        $validateUpdateNew
                    ) {
                        if ($isTotalItem) {$isFilter = true;}
                    }
                }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: '. $e->getMessage(). "\n");
        }
        return $isFilter;
    }

    /**
     * Validate if the grade item is total
     */
    public function isTotalItem($grade_item)
    {
        $isTotalItem = true;
        // grade item
        $grade_item_load = $grade_item ?? null; 
        // Verificate if the grade item is not null
        if ($grade_item_load !== null) {
            $categoryId = $grade_item_load->categoryid ?? 0;
            $itemType = $grade_item_load->itemtype ?? '';
            // Verificate if the grade item is total
            $isTotalItem = !($categoryId === 0 && $itemType === self::ITEMTYPE_UPDATE);
        }

        return $isTotalItem;
    }

    /**
     * Validate if the item is update
     */
    private function isUpdateItem(array $data)
    {
        $dataEvent = $data['dataEvent'];
        $evaluationLast = $data['evaluationLast'];
        $evaluationsData = $data['evaluationsData'];
        $date = $data['date'];
        $validateUpdateNew = false;

        if (key_exists('objectid', $dataEvent) &&
            key_exists('timecreated', $dataEvent))
        {
            if ($evaluationsData->evaluationId !== 
                $dataEvent['objectid']) {
                $validateUpdateNew = (
                    $evaluationLast->action.'D' === self::IS_ITEM_UPDATE
                );
            } else {
                $validateUpdateNew = (
                    $evaluationLast->action.'D' === self::IS_ITEM_UPDATE &&
                    $date !== $dataEvent['timecreated']
                );
            }
        } 
        return $validateUpdateNew;
    }
}