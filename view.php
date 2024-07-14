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

// Parameters.
$id = required_param('id', PARAM_INT);

// Get module data.
[$course, $cm] = get_course_and_cm_from_cmid($id, 'classrooms');
$instance = $DB->get_record('classrooms', ['id'=> $cm->instance], '*', MUST_EXIST);

// Page setup.
$PAGE->set_url('/mod/classrooms/view.php', ['id' => $id]);
$PAGE->set_cm($cm);
$PAGE->set_context(context_module::instance($cm->id));
$PAGE->set_title('Title Placeholder');
$PAGE->set_heading('Heading Placeholder');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_pagelayout('incourse');

// Security checks.

// Print page.
echo $OUTPUT->header();

echo $OUTPUT->footer();
