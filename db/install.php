<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

function xmldb_classrooms_install() {
    global $DB;

    // Default custom fields.
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
