<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle CLI script - clean_uplanner_logs.php
 *
 * @package     local_uplannerconnect
 * @copyright   2023 Daniel Eduardo Dorado Pérez <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
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

$uplanner_client_factory = new uplanner_client_factory();

foreach (repository_type::ACTIVE_REPOSITORY_TYPES as $type => $repository_class) {
    $repository = new $repository_class($type);
    $uplanner_client = $uplanner_client_factory->create($type);
    foreach (repository_type::LIST_STATES as $state) {
        while (true) {
            $dataQuery = [
                'state' => $state,
                'limit' => 500,
                'offset' => 0,
            ];
            $rows = $repository->getDataBD($dataQuery);
            if (!$rows) {
                break;
            }
            foreach ($rows as $row) {
                $repository->delete_row($row->id);
            }
        }
    }
}