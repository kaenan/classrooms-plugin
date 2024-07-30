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
require_once('classes/sessions.php');
require_once('session_form.php');

// Parameters.
$id = required_param('id', PARAM_INT);
$classroomid = required_param('classroomid', PARAM_INT);
$sessionid = optional_param('sessionid', 0, PARAM_INT);
$adddate = optional_param('adddate', false, PARAM_NOTAGS);
$delete_date = optional_param('deleted', 0, PARAM_INT);
$numdates = optional_param('numdates', 0, PARAM_INT);
$submitted = optional_param('submitbutton', false, PARAM_NOTAGS);

// Get module data.
[$course, $cm] = get_course_and_cm_from_cmid($id, 'classrooms');
$instance = $DB->get_record('classrooms', ['id'=> $cm->instance], '*', MUST_EXIST);

// Page setup.
$PAGE->set_url('/mod/classrooms/edit_session.php');
$PAGE->set_context(context_module::instance($cm->id));
$PAGE->set_cm($cm);
$PAGE->set_title('Title Placeholder');
$PAGE->set_heading('Heading Placeholder');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_pagelayout('incourse');
$PAGE->requires->js_call_amd('mod_classrooms/edit_sessions_form', 'init');

// Security checks.
$restrict = false;

if (!$submitted) {
    if ($sessionid && !$adddate && !$delete_date) {
        $session = new sessions($sessionid);
        $formdata = $session->form_data();
        $numdates = $formdata['numdates'];
    }
}

$classroom = new classroom($classroomid, $restrict);

$custom_data = [];
if ($adddate) {
    $numdates++;

}

for ($i = 0; $i < $numdates + 1; $i++) {
    $key = 'session_deleted_' . $i;
    if (isset($_GET[$key])) {
        $custom_data[$key] = $_GET[$key];
    }
}

$custom_data['numdates'] = $numdates;

// Custom session fields.
$custom_data['fields'] = classrooms_session_fields();

$form = new session_form(null, $custom_data);

// Set from data.
if ($adddate) {
    $setdata = $_GET;
    // echo var_dump($setdata); die;
    $setdata['numdates'] = $numdates;
    if (isset($setdata['details'])) {
        $setdata['details'] = ['text' => $setdata['details'], 'format' => 1];
    }
    $form->set_data(
        $setdata
    );
} else if ($delete_date) {
    $setdata = $_GET;
    $setdata['details'] = ['text' => $setdata['details'], 'format' => 1];
    $form->set_data(
        $setdata
    );
} else if ($sessionid) {
    $formdata['id'] = $id;
    if (isset($formdata['details'])) {
        $formdata['details'] = ['text' => $formdata['details'], 'format' => 1];
    }
    $form->set_data(
        $formdata
    );
} else {
    $form->set_data(
        [
            'id' => $id,
            'classroomid' => $classroomid,
            'sessionid' => $sessionid,
            'numdates' => 0
        ]
    );
}

if ($data = $form->get_data()) {
    if (isset($data->adddate)) {
        $data->details = $data->details['text'];
        redirect(new moodle_url($PAGE->url, (array) $data));
    }

    foreach ($_POST as $key => $val) {
        if (substr($key, 0, 6) == 'delete') {
            $data->deleted = 1;
            $delete = 'session_deleted_' . substr($key, 7);
            $data->$delete = 1;
            $data->details = $data->details['text'];
            redirect(new moodle_url($PAGE->url, (array) $data));
        }
    }

    if ($data->sessionid) {
        $session = new sessions($data->sessionid);
        $session->update($data);

    } else {
        $sessionid = sessions::new($data);
    }

    redirect(new moodle_url('/mod/classrooms/view.php', ['id' => $id]));

} else if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/classrooms/view.php', ['id' => $id]));
}
 
echo $OUTPUT->header();

echo $form->display();

echo $OUTPUT->footer();