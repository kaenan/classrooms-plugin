<?php

require_once("$CFG->libdir/formslib.php");

class session_form extends moodleform {

    public function definition() {

        $mform = $this->_form;

        $numdates = $this->_customdata['numdates'];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'classroomid');
        $mform->setType('classroomid', PARAM_INT);

        $mform->addElement('hidden', 'sessionid');
        $mform->setType('sessionid', PARAM_INT);

        $mform->addElement('hidden', 'numdates', $numdates);
        $mform->settype('numdates', PARAM_INT);

        // Details.
        $mform->addElement('editor', 'details', 'Details');
        $mform->settype('details', PARAM_RAW);

        // Custom session fields.
        foreach ($this->_customdata['fields'] as $f) {

            $mform->addElement('text', $f->shortname, $f->name);
            $mform->settype($f->shortname, PARAM_NOTAGS);

        }

        // Capacity.
        $mform->addElement('text', 'capacity', 'Capacity');
        $mform->setDefault('capacity', 10);
        $mform->settype('capacity', PARAM_INT);

        // Over booking.
        $mform->addElement('advcheckbox', 'overbooking', 'Allow overbooking?');

        // Capacity.
        $mform->addElement('text', 'cost', 'Cost (£)');
        $mform->setDefault('cost', 0);
        $mform->settype('cost', PARAM_INT);

        // Hidden.
        $mform->addElement('advcheckbox', 'hidden', 'Hide session from list and lock signups?');

        $mform->addElement('submit', 'adddate', 'Add date');

        if ($numdates > 0) {

            $mform->addElement('header', 'dates_header', 'Session dates');

            $arr = [
                'startyear' => date('Y', time()),
                'stopyear' => date('Y', strtotime('+5 years'))
            ];

            for ($i = 1; $i < $numdates + 1; $i++) {

                $check = (isset($this->_customdata['session_deleted_' . $i]) && $this->_customdata['session_deleted_' . $i] == 0)
                || !isset($this->_customdata['session_deleted_' . $i]);

                if ($check) {
                    $mform->addElement('html', html_writer::start_div('session_date'));
                }

                $mform->addElement('hidden', 'session_dateid_' . $i, 0);
                $mform->settype('session_dateid_' . $i, PARAM_INT);

                $mform->addElement('hidden', 'session_deleted_' . $i, 0);
                $mform->settype('session_deleted_' . $i, PARAM_INT);

                $mform->addElement('date_time_selector', 'session_timestart_' . $i, 'Start time', $arr);
                $mform->hideIf('session_timestart_' . $i, 'session_deleted_' . $i, 'eq', 1);

                $mform->addElement('date_time_selector', 'session_timefinish_' . $i, 'Finish time', $arr);
                $mform->hideIf('session_timefinish_' . $i, 'session_deleted_' . $i, 'eq', 1);

                if ($check) {
                    $mform->addElement(
                        'html',
                        '<input type="submit" value="Delete" name="delete_'. $i .'" class="btn btn-secondary">' 
                    );

                    $mform->addElement('html', html_writer::end_div());
                }
            }

            $mform->closeHeaderBefore('buttonar');
        }

        // Default value.
        $this->add_action_buttons();
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }
}
