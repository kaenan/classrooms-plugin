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
 * Activity creation/editing form for the mod_classroom plugin.
 *
 * @package   mod_classrooms
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_classrooms_mod_form extends moodleform_mod {

    function definition() {

        $mform =& $this->_form;

        $mform->addElement('header', 'general', 'General');

        $mform->addElement('text', 'name', 'Name', ['size' => '255']);
        $mform->setType('name', PARAM_NOTAGS);

        $mform->addElement('textarea', 'description', 'Description', 'rows="10" cols="30"');
        $mform->setType('description', PARAM_NOTAGS);

        // Standard Moodle course module elements (course, category, etc.).
        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

    function validation($data, $files) {
        $errors = [];

        return $errors;
    }
}