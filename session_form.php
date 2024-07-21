<?php

require_once("$CFG->libdir/formslib.php");

class session_form extends moodleform {

    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'classroomid');
        $mform->setType('classroomid', PARAM_INT);

        $mform->addElement('hidden', 'sessionid');
        $mform->setType('sessionid', PARAM_INT);

        $mform->addElement('html', html_writer::link('#', 'Add date'));

        // Default value.
        $this->add_action_buttons();
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }
}