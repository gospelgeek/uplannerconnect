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


    return true;
}