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

    if ($oldversion < 2023101802) {

        // Define field num_evaluation to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('num_evaluation', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'num_anouncements');

        // Conditionally launch add field num_evaluation.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023101802, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023102502) {

        // Define field request_type to be added to uplanner_materials.
        $table = new xmldb_table('uplanner_materials');
        $field = new xmldb_field('request_type', XMLDB_TYPE_TEXT, null, null, null, null, null, 'success');

        // Conditionally launch add field request_type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023102502, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023102800) {

        // Define field request_type to be added to uplanner_materials.
        $table = new xmldb_table('uplanner_notification');
        $field = new xmldb_field('request_type', XMLDB_TYPE_TEXT, null, null, null, null, null, 'success');

        // Conditionally launch add field request_type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023102800, 'local', 'uplannerconnect');
    }
  
    if ($oldversion < 2023104000) {

        // Define table uplanner_esb_messages_status to be created.
        $table = new xmldb_table('uplanner_esb_messages_status');

        // Adding fields to table uplanner_esb_messages_status.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('id_code', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('id_transaction', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('ds_topic', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('ds_mongo_id', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('ds_error', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('dt_processing_date', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('is_success_ful', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('created_at', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table uplanner_esb_messages_status.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for uplanner_esb_messages_status.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023104000, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023110201) {

        // Define field date to be added to uplanner_evaluation.
        $table = new xmldb_table('uplanner_evaluation');
        $field = new xmldb_field('date', XMLDB_TYPE_TEXT, null, null,null, null, null, 'success');

        // Conditionally launch add field date.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023110201, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023110700) {

        // Define table uplanner_transaction_seq to be created.
        $table = new xmldb_table('uplanner_transaction_seq');

        // Adding fields to table uplanner_transaction_seq.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table uplanner_transaction_seq.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for uplanner_transaction_seq.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023110700, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023110701) {

        // Define field transaction to be added to uplanner_transaction_seq.
        $table = new xmldb_table('uplanner_transaction_seq');
        $field = new xmldb_field('transaction', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null, 'courseid');

        // Conditionally launch add field transaction.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023110701, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111301) {

        // Define table uplanner_esb_messages_status to be dropped.
        $table = new xmldb_table('uplanner_esb_messages_status');
        // Conditionally launch drop table for uplanner_esb_messages_status.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        upgrade_plugin_savepoint(true,2023111301, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111302) {
        // Define field ds_error to be added to uplanner_grades.
        $table = new xmldb_table('uplanner_grades');
        $field = new xmldb_field('ds_error', XMLDB_TYPE_TEXT, null, null, null, null, null, 'success');
        // Conditionally launch add field ds_error.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true,2023111302, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111303) {
        // Define field is_sucessful to be added to uplanner_grades.
        $table = new xmldb_table('uplanner_grades');
        $field = new xmldb_field('is_sucessful', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'ds_error');
        // Conditionally launch add field is_sucessful.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2023111303, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111304) {
        // Define field ds_error to be added to uplanner_evaluation.
        $table = new xmldb_table('uplanner_evaluation');
        $field = new xmldb_field('ds_error', XMLDB_TYPE_TEXT, null, null, null, null, null, 'success');
        // Conditionally launch add field ds_error.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2023111304, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111305) {
        // Define field is_sucessful to be added to uplanner_evaluation.
        $table = new xmldb_table('uplanner_evaluation');
        $field = new xmldb_field('is_sucessful', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'ds_error');
        // Conditionally launch add field is_sucessful.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2023111305, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111306) {
        // Define field ds_error to be added to uplanner_notification.
        $table = new xmldb_table('uplanner_notification');
        $field = new xmldb_field('ds_error', XMLDB_TYPE_TEXT, null, null, null, null, null, 'success');
        // Conditionally launch add field ds_error.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2023111306, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111307) {
        // Define field ds_error to be added to uplanner_notification.
        $table = new xmldb_table('uplanner_notification');
        $field = new xmldb_field('ds_error', XMLDB_TYPE_TEXT, null, null, null, null, null, 'success');
        // Conditionally launch add field ds_error.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111307, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111308) {
        // Define field is_sucessful to be added to uplanner_notification.
        $table = new xmldb_table('uplanner_notification');
        $field = new xmldb_field('is_sucessful', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'ds_error');
        // Conditionally launch add field is_sucessful.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111308, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111309) {
        // Define field ds_error to be added to uplanner_materials.
        $table = new xmldb_table('uplanner_materials');
        $field = new xmldb_field('ds_error', XMLDB_TYPE_TEXT, null, null, null, null, null, 'success');
        // Conditionally launch add field ds_error.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111309, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111310) {
        // Define field is_sucessful to be added to uplanner_materials.
        $table = new xmldb_table('uplanner_materials');
        $field = new xmldb_field('is_sucessful', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'ds_error');
        // Conditionally launch add field is_sucessful.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111310, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111311) {
        // Define field num_grades_err to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('num_grades_err', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'num_evaluation');
        // Conditionally launch add field num_grades_err.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111311, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111312) {
        // Define field num_materials_err to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('num_materials_err', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'num_grades_err');
        // Conditionally launch add field num_materials_err.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111312, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111313) {
        // Define field num_anouncements_err to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('num_anouncements_err', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'num_materials_err');
        // Conditionally launch add field num_anouncements_err.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111313, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111314) {
        // Define field num_evaluation_err to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('num_evaluation_err', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'num_anouncements_err');
        // Conditionally launch add field num_evaluation_err.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111314, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023111315) {
        // Define field updated_at to be added to uplanner_log.
        $table = new xmldb_table('uplanner_log');
        $field = new xmldb_field('updated_at', XMLDB_TYPE_TEXT, null, null, null, null, null, 'date');
        // Conditionally launch add field updated_at.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true,2023111315, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023112000) {

        // Define field aggregation to be added to uplanner_grades.
        $table = new xmldb_table('uplanner_grades');
        $field = new xmldb_field('aggregation', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'is_sucessful');

        // Conditionally launch add field aggregation.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023112000, 'local', 'uplannerconnect');
    }
    
    if ($oldversion < 2023112600) {

        // Define field aggregation to be added to uplanner_grades.
        $table = new xmldb_table('uplanner_grades');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'is_sucessful');

        // Conditionally launch add field aggregation.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023112600, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023120500) {

        // Define field aggregation to be dropped from uplanner_grades.
        $table = new xmldb_table('uplanner_grades');
        $field = new xmldb_field('aggregation');

        // Conditionally launch drop field aggregation.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023120500, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023120502) {

        // Define field courseid to be dropped from uplanner_grades.
        $table = new xmldb_table('uplanner_grades');
        $field = new xmldb_field('courseid');

        // Conditionally launch drop field courseid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023120502, 'local', 'uplannerconnect');
    }

    if ($oldversion < 2023120800) {

        // Define field courseid to be added to uplanner_evaluation.
        $table = new xmldb_table('uplanner_evaluation');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'is_sucessful');

        // Conditionally launch add field courseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Uplannerconnect savepoint reached.
        upgrade_plugin_savepoint(true, 2023120800, 'local', 'uplannerconnect');
    }

    return true;
}