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


    return true;
}