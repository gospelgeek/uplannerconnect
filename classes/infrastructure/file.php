<?php
/**
 * @package     uPlannerConnect
 * @copyright   Cristian Machado Mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Eduardo Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uplannerconnect\infrastructure;

use Exception;

/**
 * Class csv file manager
 */
class file
{
    const BASE_NAME = '%s_uplanner_template.csv';

    /**
     * Directory path files
     *
     * @var string $filename
     */
    private string $directory = __DIR__ . '/email/files/';

    /**
     * File name
     *
     * @var string $filename
     */
    private string $filename;

    /**
     * Virtual name
     *
     * @var string $virtual_name
     */
    private $virtual_name;

    /**
     * Construct
     *
     * @param $filename
     * @param $virtual_name
     */
    public function __construct(
        $filename,
        $virtual_name
    ) {
        $millis = round(microtime(true) * 1000);
        $this->filename = sprintf(self::BASE_NAME, $filename);
        $this->virtual_name = str_replace("date", $millis, $virtual_name);

    }

    /**
     * Get virtual name
     *
     * @return string
     */
    public function get_virtual_name()
    {
        return $this->virtual_name;
    }

    /**
     * Get path file
     *
     * @return string
     */
    public function get_path_file()
    {
        return $this->directory . $this->filename;
    }

    /**
     * Create CSV file
     *
     * @param $headers
     * @return bool
     */
    public function create_csv($headers)
    {
        $response = false;
        try {
            if (!file_exists($this->directory)) {
                mkdir($this->directory, 0755, true);
            }
            $csv_file = $this->get_path_file();
            $fp = fopen($csv_file, 'w');
            if ($fp) {
                fputcsv($fp, $headers);
                fclose($fp);
                $response = true;
            }
        } catch (Exception $e) {
            error_log('create_csv: '. $e->getMessage() . PHP_EOL);
        }

        return $response;
    }

    /**
     * Add row
     *
     * @param $data
     * @return bool
     */
    public function add_row($data)
    {
        try {
            $csv_file = $this->get_path_file();
            if (file_exists($csv_file)) {
                $fp = fopen($csv_file, 'a');
                fputcsv($fp, $data);
                fclose($fp);
                return true;
            }
        } catch (Exception $e) {
            error_log('add_row: '. $e->getMessage() . PHP_EOL);
        }

        return false;
    }

    /**
     * Reset CSV file
     *
     * @param $headers
     * @return bool
     */
    public function reset_csv($headers)
    {
        try {
            $csv_file = $this->get_path_file();
            if (file_exists($csv_file)) {
                $fp = fopen($csv_file, 'w');
                fputcsv($fp, $headers);
                fclose($fp);
            }
            return true;
        } catch (Exception $e) {
            error_log('reset_csv: '.  $e->getMessage() . PHP_EOL);
        }

        return false;
    }

    /**
     * Delete CSV file
     *
     * @return void
     */
    public function delete_csv()
    {
        try {
            $csv_file = $this->get_path_file();
            if (file_exists($csv_file)) {
                unlink($csv_file);
            }
        } catch (Exception $e) {
            error_log('delete_csv: '.  $e->getMessage() . PHP_EOL);
        }
    }
}
