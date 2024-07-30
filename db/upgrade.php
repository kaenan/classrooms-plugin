<?php

function xmldb_classrooms_upgrade($oldversion): bool {
    global $CFG, $DB;

    $newversion = 2024071301;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2024071301) {
        
        $r = new stdClass;
        $r->shortname       = 'building';
        $r->name            = 'Building';
        $r->timecreated     = time();
        $r->timemodified    = time();
        $DB->insert_record('classroom_session_fields', $r);

        $r = new stdClass;
        $r->shortname       = 'floor';
        $r->name            = 'Floor';
        $r->timecreated     = time();
        $r->timemodified    = time();
        $DB->insert_record('classroom_session_fields', $r);
        
        $r = new stdClass;
        $r->shortname       = 'room';
        $r->name            = 'Room';
        $r->timecreated     = time();
        $r->timemodified    = time();
        $DB->insert_record('classroom_session_fields', $r);
    }

    upgrade_plugin_savepoint(true, $newversion, 'mod', 'classrooms');

    // Everything has succeeded to here. Return true.
    return true;
}