<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Daniel Eduardo Dorado P. <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';

use local_uplannerconnect\application\service\page\page_error_materials_summary;

$page_error_summary = new page_error_materials_summary();
$page_error_summary->render();