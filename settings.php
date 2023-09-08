<?php
defined('MOODLE_INTERNAL') || die();

$pluginName = 'local_uplannerconnect';

if ($ADMIN->fulltree) {
    // Página de configuración principal
    $settings = new admin_settingpage(''.$pluginName.'_settings', get_string('pluginname', $pluginName));

    // Configuración para el endpoint de cursos
    $settings->add(new admin_setting_heading(''.$pluginName.'_courses', get_string('courses_endpoint_heading', $pluginName), ''));
    $settings->add(new admin_setting_configtext(
        ''.$pluginName.'/courses_endpoint',
        get_string('courses_endpoint', $pluginName),
        get_string('courses_endpoint_desc', $pluginName),
        'Bus de servicios de uPlanner',
        PARAM_URL
    ));

    // Configuración para el endpoint de notificaciones
    $settings->add(new admin_setting_heading(''.$pluginName.'_notifications', get_string('notifications_endpoint_heading', $pluginName), ''));
    $settings->add(new admin_setting_configtext(
        ''.$pluginName.'/notifications_endpoint',
        get_string('notifications_endpoint', $pluginName),
        get_string('notifications_endpoint_desc', $pluginName),
        'Bus de servicios de uPlanner',
        PARAM_URL
    ));

    // Configuración para el endpoint de materiales
    $settings->add(new admin_setting_heading(''.$pluginName.'_materials', get_string('materials_endpoint_heading', $pluginName), ''));
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

    // Agrega más configuraciones si es necesario

    // Agregar la página de configuración al árbol
    $ADMIN->add('localplugins', $settings);
}
