<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

/**
 * @package uPlannerConnect
 * @author Cristian Machado <cristian.machado@correounivalle.edu.co>
 * @description variables globales para el plugin
*/
class plugin_config {

    public $pluginNameLocal_;
    public $pluginName_ ;
    public $tableCourseGrade_;

    public function __construct() {
        $this->pluginNameLocal_ = 'local_uplannerconnect';
        $this->pluginName_ = 'uplannerconnect';
        $this->tableCourseGrade_ = 'mdl_uplanner_notes';
    }


    /**
     * @package uPlannerConnect
     * @description retorna el nombre del plugin
     * @return string
    */
    public function getPluginName() {
        return $this->pluginName_;
    }


    /**
     * @package uPlannerConnect
     * @description retorna el nombre del plugin
     * @return string
    */
    public function getPluginNameLocal() {
        return $this->pluginNameLocal_;
    }


    /**
     * @package uPlannerConnect
     * @description retorna el nombre de la tabla de notas
     * @return string
    */
    public function getTableCourseGrade() {
        return $this->tableCourseGrade_;
    }
   
}
