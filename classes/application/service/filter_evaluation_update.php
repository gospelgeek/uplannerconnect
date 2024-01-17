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
    const LAST_INSERT_EVALUATION = "SELECT date,json FROM {uplanner_evaluation} WHERE courseid = :courseid ORDER BY id DESC limit 1";
    const ITEMTYPE_UPDATE = 'course';
    const IS_ITEM_UPDATE = 'UPDATED';
    const POSTGRESQL_lAST_EVALUATION = "SELECT id FROM {uplanner_evaluation}
                                        WHERE json::json->'evaluationGroups'->0->'evaluations'->0->>'evaluationId' = '%s'
                                        AND json::json->>'action' = '%s'
                                        AND json::json->'evaluationGroups'->0->'evaluations'->0->>'evaluationName' = '%s'
                                        AND date = :date
                                        AND courseid = :courseid
                                        ORDER BY id DESC limit 1";
    const MARIADB_LAST_EVALUATION = "SELECT id FROM {uplanner_evaluation}
                                     WHERE JSON_EXTRACT(json, '$.evaluationGroups[0].evaluations[0].evaluationId') = '%s'
                                     AND JSON_EXTRACT(json, '$.action') = '%s'
                                     AND JSON_EXTRACT(json, '$.evaluationGroups[0].evaluations[0].evaluationName') = '%s'
                                     AND date = :date
                                     AND courseid = :courseid
                                     ORDER BY id DESC LIMIT 1";

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
            $dataEvent = $event->get_data();
            $dataOther = $dataEvent['other'];
            $grade_item_load =  $event->get_grade_item();
            $itemName = $grade_item_load->itemname;

            // If item type 
            if ($grade_item_load->itemtype == 'category') {
                $get_category = $grade_item_load->get_item_category();
                $itemName = $get_category->fullname . ' total';
            }

            // Get last evaluation
            $evaluation = $this->query->executeQuery(
                sprintf(
                    self::POSTGRESQL_lAST_EVALUATION,
                    strval($dataEvent['objectid']),
                    strtoupper(substr($dataEvent['action'], 0, -1)),
                    $itemName
                ),
                [
                    'date' => ($dataEvent['timecreated']),
                    'courseid' => $dataEvent['courseid']
                ]
            );

            // Is category Father
            $isCategoryFather = true;
            // data of item
            $get_data_category = $grade_item_load->get_item_category();

            // Verificate if is category father
            if (isset($get_data_category->depth)) {
                $depth_category = $get_data_category->depth;
                if ($depth_category == 1) {
                    $isCategoryFather = false;
                }
            }

            if (empty($evaluation) && $isCategoryFather) {
                $isTotalItem = $this->isTotalItem($event->get_grade_item());
                if ($isTotalItem) {$isFilter = true;}
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