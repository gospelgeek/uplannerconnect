<?php
/**
 * Moodle CLI script - clean_uplanner_logs.php
 *
 * @package     local_uplannerconnect
 * @copyright   Daniel Eduardo Dorado Pérez <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

use local_uplannerconnect\application\repository\general_repository;
use local_uplannerconnect\application\repository\repository_type;
use local_uplannerconnect\infrastructure\api\factory\uplanner_client_factory;

$usage = "Clean registers in uPlanner tables. If no exists param state remove all data in tables

Usage:
    # php clean_uplanner_logs.php
    # php clean_uplanner_logs.php [--help|-h]

Options:
    -h --help               Print this help.
";

list($options, $unrecognised) = cli_get_params([
    'help' => false
], [
    'h' => 'help'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL . '  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}

try {
    $uplanner_client_factory = new uplanner_client_factory();
    $general_repository = new general_repository();
    $list_states = [0, 1, 2, 3];
    foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
        $repository = new $repository_class($type);
        $uplanner_client = $uplanner_client_factory->create($type);
        foreach ($list_states as $state) {
            $condition = [
                'success' => $state
            ];
            $general_repository->delete_rows($repository::TABLE, $condition);
        }
    }
} catch (\Exception $e) {
    error_log('clean_uplanner_logs_cli: ' . $e->getMessage() . PHP_EOL);
}