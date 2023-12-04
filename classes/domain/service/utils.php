<?php
/**
 * @package     local_uplannerconnect
 * @copyright   Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @copyright   daniel eduardo dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_uplannerconnect\domain\service; 

/**
 *  Utils class
 */
class utils
{
    /**
     * Convert format Uplanner
     *
     * @param string $originalString
     * @return string
     */
    public function convertFormatUplanner($originalString) 
    {
        $pattern = '/^(\d{2})-(\d{6}[A-Za-z])-(\d{2})-(\d{9})$/';
        $newString = $originalString;
        
        if (preg_match($pattern, $originalString, $matches)) {
            
            $part1 = $matches[1];
            $part2 = $matches[2];
            $part3 = $matches[3];
            $part4 = $matches[4];

            // Build the new string in the desired format
            $newString = "$part1-$part4-$part2-$part3";
        }

        return $newString;
    }
}