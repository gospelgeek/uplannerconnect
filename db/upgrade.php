<?php

function xmldb_local_uplannerconnect_upgrade($oldversion): bool {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    //No se mdifica la versión
    if ($oldversion < 2023092301) {

        // Define field id to be added to uplanner_grades.
        $table = new xmldb_table('uplanner_grades');
        $field = new xmldb_field(
        'request_type', 
        XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'success');

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true,  2023092301, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023092703) {
        // Define table uplanner_evaluation to be created.
        $table = new xmldb_table('uplanner_evaluation');

        // Adding fields to table uplanner_evaluation.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('json', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('response', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('request_type', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('success', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
       
        // Adding keys to table uplanner_evaluation.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
       
        // Conditionally launch create table for uplanner_evaluation.
        if (!$dbman->table_exists($table)) {
              $dbman->create_table($table);
        }
       
        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true,2023092703 , 'local', 'uplannerconnect');
    }


    if ($oldversion < 2023100200) {

        // Define field response to be dropped from uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('response');

        // Conditionally launch drop field response.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023100200, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023100201) {

        // Define field success to be dropped from uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('success');

        // Conditionally launch drop field success.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023100201, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023100202) {

        // Define field date to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('date', XMLDB_TYPE_TEXT, null, null, null, null, null, 'success');

        // Conditionally launch add field date.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023100202, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023100203) {

        // Define field num_grades to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('num_grades', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'date');

        // Conditionally launch add field num_grades.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023100203, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023100204) {

        // Define field num_materials to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('num_materials', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'num_grades');

        // Conditionally launch add field num_materials.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023100204, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023100205) {

        // Define field num_anouncements to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('num_anouncements', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'num_materials');

        // Conditionally launch add field num_anouncements.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023100205, 'local', 'uplannerconnect');
    }

    return true;
}