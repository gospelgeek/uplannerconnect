<?php
/**
 * @package     uPlannerConnect
 * @copyright   cristian machado mosquera <cristian.machado@correounivalle.edu.co>
 * @copyright   Daniel Dorado <doradodaniel14@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


defined('MOODLE_INTERNAL') || die();


//Variables globales
require_once(__DIR__ .'/classes/plugin_config/plugin_config.php');

$pluginName = plugin_config::PLUGIN_NAME_LOCAL;

if ($hassiteconfig) {

    $ADMIN->add('localplugins',
                new admin_category('local_uplannerconnect_settings',
                new lang_string('pluginname', 'local_uplannerconnect')));
    $settings = new admin_settingpage('managelocaluplannerconnect', new lang_string('manage', 'local_uplannerconnect'));


    if ($ADMIN->fulltree) {
        // Página de configuración principal
        //$settings = new admin_settingpage(''.$pluginName.'_settings', get_string('pluginname', $pluginName));

        // Configuración para el endpoint de cursos
        // $settings->add(new admin_setting_heading(
        //     ''.$pluginName.'_courses', 
        //     get_string('courses_endpoint_heading', 
        //     $pluginName), 
        //     ''
        // ));

        $settings->add(new admin_setting_heading(
            'generalsettingsheading',
            new lang_string('generalsettingsheading', 'local_uplannerconnect'),
            new lang_string('generalsettingsheading_desc', 'local_uplannerconnect')));

        $settings->add(new admin_setting_configtext(
            ''.$pluginName.'/courses_endpoint',
            get_string('courses_endpoint', $pluginName),
            get_string('courses_endpoint_desc', $pluginName),
            'Bus de servicios de uPlanner',
            PARAM_URL
        ));

        // Configuración para el endpoint de notificaciones
        $settings->add(new admin_setting_heading(
            ''.$pluginName.'_notifications', 
            get_string('notifications_endpoint_heading', 
            $pluginName), 
            ''
        ));
        
        $settings->add(new admin_setting_configtext(
            ''.$pluginName.'/notifications_endpoint',
            get_string('notifications_endpoint', $pluginName),
            get_string('notifications_endpoint_desc', $pluginName),
            'Bus de servicios de uPlanner',
            PARAM_URL
        ));

        // Configuración para el endpoint de materiales
        $settings->add(new admin_setting_heading(
            ''.$pluginName.'_materials', 
            get_string('materials_endpoint_heading',
            $pluginName), 
            ''
        ));
        
        $settings->add(new admin_setting_configtext(
            ''.$pluginName.'/materials_endpoint',
            get_string('materials_endpoint', $pluginName),
            get_string('materials_endpoint_desc', $pluginName),
            'Bus de servicios de uPlanner',
            PARAM_URL
        ));

        $settings->add(new admin_setting_configtext(
            ''.$pluginName.'/task_runtime_primary',
            get_string('task_rutine_primary', $pluginName),
            get_string('task_rutine_primary_desc', $pluginName),
            5,
            PARAM_INT
        ));
        
    }

    // Agrega la página de configuración al árbol
    $ADMIN->add('localplugins', $settings);

    $ADMIN->add('reports', 
      new admin_category('uplannerconnect', 
      new lang_string('pluginname', 'local_uplannerconnect')));

    // $ADMIN->add('uplannerconnect',
    //     new admin_externalpage('index', new lang_string('reports', 'local_uplannerconnect'),
    //         new moodle_url('/local/uplannerconnect/index.php'), 'moodle/site:configview'
    //     )
    // );
    
    
}