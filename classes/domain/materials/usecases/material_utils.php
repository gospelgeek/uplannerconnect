<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\materials\usecases;

use local_uplannerconnect\application\service\data_validator;
use local_uplannerconnect\application\repository\moodle_query_handler;
use local_uplannerconnect\plugin_config\plugin_config;
use moodle_exception;

/**
 *  Extraer los datos
 */
class material_utils
{   
    const QUERY_FILE_MATERIAL = "SELECT * FROM mdl_files where contextid='%s' ORDER BY sortorder DESC LIMIT 1";
    const TABLE_TRANSACTION_UPLANNER = 'uplanner_transaction_seq';
    const LAST_COURSE_TRANSACTION = 'SELECT id FROM mdl_uplanner_transaction_seq WHERE courseid = %s ORDER BY id DESC LIMIT 1';

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
    public function resourceCreatedMaterial(array $data) : array
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
            // Sacar el id de la pagina
            $courseid = $event->courseid;

            $queryCourse = ($this->validator->verifyQueryResult([                        
                'data' => $this->moodle_query_handler->extract_data_db([
                    'table' => plugin_config::TABLE_COURSE,
                    'conditions' => [
                        'id' => $this->validator->isIsset($courseid)
                    ]
                ])
            ]))['result'];
            $fileData = $this->getDataResource($event->contextid);
            $sizeFile = $fileData->filesize ?? 0;
            $typeFile = $fileData->mimetype ?? $getData['other']['modulename'];
            $url = $this->getUrlResource($event,$fileData);
            $nameFile = $getData['other']['name'] ?? '';

            $timestamp =  $this->validator->isIsset($getData['timecreated']);
            $formattedDateCreated = date('Y-m-d', $timestamp);
            
            //información a guardar
            $dataToSave = [
                'id' => $this->validator->isIsset(strval($getData['other']['instanceid'])),
                'name' => $this->validator->isIsset($nameFile),
                'type' => $this->validator->isIsset($typeFile),
                'url' => $this->validator->isIsset($url),
                'blackboardSectionId' => $this->validator->isIsset($queryCourse->shortname),
                'size' => $sizeFile, 
                'lastUpdatedTime' => $this->validator->isIsset($formattedDateCreated),
                'action' => strtoupper($data['dispatch']),
                'transactionId' => $this->validator->isIsset($this->getLastRowTransaction($courseid)),
            ];
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $dataToSave;
    }

    /**
     * Return the data of the resource
     * 
     * @param int $idContext
     * @return object
     */
    private function getDataResource($idContext) : object
    {
        $query = new \stdClass();
        try {

            if (isset($idContext)) {
                $queryResult = $this->moodle_query_handler->executeQuery(
                    sprintf(
                            self::QUERY_FILE_MATERIAL,
                            $idContext
                ));

                if (!empty($queryResult)) {
                    $fileData = reset($queryResult);
                    if (!empty($fileData)) {
                        $query = $fileData;
                    }
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $query;
    }

    private function getUrlResource($data,$dataFile) : string
    {
        GLOBAL $CFG;
        $url = '';
        try {
            if (!empty($data)) {
                $typeUrl = [
                    'folder' => 'mod/folder/view.php?id=%s',
                    'resource' => 'pluginfile.php/%s/mod_resource/%s/1/%s',
                    'label' => 'mod/label/view.php?id=%s',
                    'lightboxgallery' => 'mod/lightboxgallery/view.php?id=%s',
                    'book' => 'mod/book/view.php?id=%s',
                    'page' => 'mod/page/view.php?id=%s',
                    'url' => 'mod/url/view.php?id=%s',
                    'imscp' => 'mod/imscp/view.php?id=%s',
                ];
                $getData = $this->validator->isArrayData($data->get_data());

                if (isset($getData['other']['modulename'])) {
                    if ($getData['other']['modulename'] === 'resource') {
                        //sacar la url actual
                        $url = $CFG->wwwroot.'/'.sprintf(
                            $typeUrl[$getData['other']['modulename']], 
                            $dataFile->contextid ?? '', 
                            $dataFile->filearea ?? '', 
                            $dataFile->filename ?? ''
                        );
                    }
                    else {
                        $url = $base_url = $CFG->wwwroot.'/'.sprintf(
                            $typeUrl[$getData['other']['modulename']],
                            $this->validator->isIsset($data->objectid)
                        );
                    }
                }
            }
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $url;
    }

    private function insertTransactionUplanner(array $data)
    {
        $dataToSave = [];
        try {
            $dataToSave = [
                'courseid' => $data['courseId'],
                'transaction' => $data['transaction']
            ];
            $this->moodle_query_handler->insert_record_db([
                'table' => self::TABLE_TRANSACTION_UPLANNER,
                'data' => $dataToSave
            ]);
        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
    }

    private function getLastRowTransaction($courseId)
    {
        $lastRow = 0;
        try {
            $queryResult = $this->moodle_query_handler->executeQuery(sprintf(
                self::LAST_COURSE_TRANSACTION, 
                $courseId
            ));

            if (!empty($queryResult)) {
                $firstResult = reset($queryResult);
                $lastRow = intval((($firstResult->id) + 1).''.$courseId);
            } else {
                $lastRow = intval('1' . $courseId);
            }

            $this->insertTransactionUplanner([
                'courseId' => $courseId,
                'transaction' => $lastRow
            ]);

        } catch (moodle_exception $e) {
            error_log('Excepción capturada: ',  $e->getMessage(), "\n");
        }
        return $lastRow;
    }
}