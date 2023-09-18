<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/factory/UplannerClientFactory.php');
require_once(__DIR__ . '/../../application/repository/RepositoryType.php');

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @author Daniel Dorado <doradodaniel14@gmail.com>
 * @description Clase que controla la lógica que enváa datos a Uplanner
 */
class HandleSendUplannerTask
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->uplannerClientFactory = new UplannerClientFactory();
    }

    /**
     * Handle process send information to Uplanner
     *
     * @return void
     */
    public function process($state = 0, $numRequestByEndpoint = 1, $numRows = 100)
    {
        foreach (RepositoryType::ACTIVE_REPOSITORY_TYPES as $type => $repositoryClass) {
            $repository = new $repositoryClass();
            $uplannerClient = $this->uplannerClientFactory->create($type);
            $this->processEndpoint(
                $uplannerClient,
                $repository,
                $state,
                $numRequestByEndpoint,
                $numRows
            );
        }
    }

    /**
     * @param $uplannerClient
     * @param $repository
     * @param $state
     * @param $numRequestByEndpoint
     * @param $numRows
     * @return void
     */
    public function processEndpoint($uplannerClient, $repository, $state, $numRequestByEndpoint, $numRows)
    {
        if ($numRequestByEndpoint <= 0 || $numRows <= 0 ) {
            return;
        }
        $indexRow = 0;
        while ($indexRow < $numRequestByEndpoint) {
            $rows = $repository->getDataDB($state);
            if (!$rows) {
                break;
            }
            foreach ($rows as $row) {
                $response = $this->request($uplannerClient, $row['type'], $row['json']);
                // 2 -> state error, 1 -> state send
                $status = in_array($response, 'error') ? 2 : 1;
                //$repository->saveData($status, $response);
            }
        }
    }

    /**
     * @param $uplannerClient
     * @param $type
     * @param $json
     * @return mixed
     */
    public function request($uplannerClient, $type, $json)
    {
        $response = [];
        try {
            switch ($type) {
                case 'delete':
                    $response = $uplannerClient->delete($json);
                    break;
                case 'update':
                    $response = $uplannerClient->update($json);
                    break;
                default:
                    $response = $uplannerClient->create($json);
                    break;
            }
        } catch (Exception $e) {
            //TODO: add logs
        }

        return $response ?? [];
    }
}