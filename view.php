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

/**
 * Activity view page for the plugintype_pluginname plugin.
 *
 * @package   mod_classrooms
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('lib.php');
require_once('classes/classroom.php');

// Parameters.
$id = required_param('id', PARAM_INT);
$action = optional_param('action', null, PARAM_NOTAGS);
$confirm = optional_param('confirm', null, PARAM_NOTAGS); // MD5 hash of action.

// Get module data.
[$course, $cm] = get_course_and_cm_from_cmid($id, 'classrooms');
$instance = $DB->get_record('classrooms', ['id'=> $cm->instance], '*', MUST_EXIST);
$classroom = new classroom($instance->id);

// Page setup.
$PAGE->set_url('/mod/classrooms/view.php', ['id' => $id]);
$PAGE->set_cm($cm);
$PAGE->set_context(context_module::instance($cm->id));
$PAGE->set_title('Title Placeholder');
$PAGE->set_heading('Heading Placeholder');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_pagelayout('incourse');

// Security checks.

// Actions.


// Buttons.
$PAGE->set_button($OUTPUT->single_button(
    new moodle_url('/mod/classrooms/edit_session.php', ['id' => $id, 'classroomid' => $classroom->id]),
    'Add session'
));

// Print page.
echo $OUTPUT->header();

if ($table = classrooms_sessions_table($classroom->id, $classroom->activesessions, $id, 
    ['id', 'classroomid', 'details', 'hidden', 'overbooking', 'timecreated', 'timemodified'])) {
    echo $table;
} else {
    echo "No sessions";
}

echo $OUTPUT->footer();
