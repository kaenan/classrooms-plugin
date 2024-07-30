<?php

require_once(dirname(__FILE__) . '/../lib.php');

class sessions {

    public $classroomid;

    public $id;

    public $details;

    public $duration;

    public $hidden;

    public $capacity;

    public $overbooking;

    public $cost;

    public function __construct($id)
    {
        $data = sessions::get_session($id);
        $this->id           = $data->id;
        $this->classroomid  = $data->classroomid;
        $this->details      = $data->details;
        $this->duration     = $data->duration;
        $this->hidden       = $data->hidden;
        $this->capacity     = $data->capacity;
        $this->overbooking  = $data->overbooking;
        $this->cost         = $data->cost;
    }

    public static function new($data) {
        global $DB;

        $record = new stdClass;
        $record->classroomid = $data->classroomid;
        $record->details = $data->details['text'];
        $record->capacity = $data->capacity;
        $record->overbooking = $data->overbooking;
        $record->hidden = $data->hidden;
        $record->cost = $data->cost;
        $record->timecreated = time();
        $record->timemodified = time();

        $sessionid = $DB->insert_record('classroom_sessions', $record);

        // Add custom fields.
        if ($fields = classrooms_session_fields()) {
            foreach ($fields as $f) {
                $shortname = $f->shortname;
                sessions::update_custom_field($sessionid, $f->id, $data->$shortname);
            }
        }

        // Add dates.
        if ($data->numdates > 0) {
            for ($i = 1; $i < $data->numdates + 1; $i++) {
                $start = "session_timestart_" . $i;
                $finish = "session_timefinish_" . $i;
                sessions::add_date($sessionid, $data->$start, $data->$finish);
            }
        }
    }

    public function update($data) {
        global $DB;

        $record = new stdClass;
        $record->id = $this->id;
        $record->classroomid = $this->classroomid;
        $record->details = $data->details['text'];
        $record->capacity = $data->capacity;
        $record->overbooking = $data->overbooking;
        $record->hidden = $data->hidden;
        $record->cost = $data->cost;
        $record->timemodified = time();

        $DB->update_record('classroom_sessions', $record);

        // Add custom fields.
        if ($fields = classrooms_session_fields()) {
            foreach ($fields as $f) {
                $shortname = $f->shortname;
                sessions::update_custom_field($this->id, $f->id, $data->$shortname);
            }
        }

        if ($data->numdates > 0) {
            for ($i = 1; $i < $data->numdates + 1; $i++) {
                $dateid = "session_dateid_" . $i;
                $deleted = 'session_deleted_' . $i;

                if (isset($data->$dateid) && $data->$deleted) {
                    $this->delete_date($data->$dateid);
                    continue;
                } else if ($data->$deleted) {
                    continue;
                }

                $start = "session_timestart_" . $i;
                $finish = "session_timefinish_" . $i;

                if ($data->$dateid) {
                    $this->update_date($data->$dateid, $data->$start, $data->$finish);
                } else {
                    $this->add_date($this->id, $data->$start, $data->$finish);
                }
            }
        }
    }

    public function delete() {

    }

    private static function add_date($sessionid, $timestart, $timefinish) {
        global $DB;

        $rec = new stdClass;
        $rec->sessionid = $sessionid;
        $rec->canceled = 0;
        $rec->timestart = $timestart;
        $rec->timefinish = $timefinish;
        $rec->timecreated = time();
        $rec->timemodified = time();

        return $DB->insert_record('classroom_session_dates', $rec);
    }

    private function update_date($dateid, $timestart, $timefinish) {
        global $DB;

        $rec = new stdClass;
        $rec->id = $dateid;
        $rec->timestart = $timestart;
        $rec->timefinish = $timefinish;
        $rec->timemodified = time();

        return $DB->update_record('classroom_session_dates', $rec);
    }

    private function delete_date($dateid) {
        global $DB;

        return $DB->delete_records('classroom_session_dates', ['id' => $dateid]);
    }

    public function form_data() {
        global $DB;

        $formdata = [];
        $formdata['sessionid'] = $this->id;
        $formdata['classroomid'] = $this->classroomid;
        $formdata['details'] = $this->details;
        $formdata['duration'] = $this->duration;
        $formdata['hidden'] = $this->hidden;
        $formdata['capacity'] = $this->capacity;
        $formdata['overbooking'] = $this->overbooking;
        $formdata['cost'] = $this->cost;

        // Get custom fields.
        $sql =
        "SELECT fd.id, fd.value, f.shortname
          FROM {classroom_session_field_data} fd
          JOIN {classroom_session_fields} f ON fd.fieldid = f.id
         WHERE fd.sessionid = ?";
        if ($fields = $DB->get_records_sql($sql, [$this->id])) { 
            foreach ($fields as $f) {
                $formdata[$f->shortname] = $f->value;
            }
        }

        // Get dates.
        if ($dates = $DB->get_records('classroom_session_dates', ['sessionid' => $this->id])) {
            $formdata['numdates'] = count($dates);

            $i = 1;
            foreach ($dates as $date) {
                $formdata['session_dateid_' . $i] = $date->id;
                $formdata['session_timestart_' . $i] = $date->timestart;
                $formdata['session_timefinish_' . $i] = $date->timefinish; 
                $i++;
            }
        }

        return $formdata;
    }

    public function custom_fields() {
        global $DB;

        $sql =
        "SELECT f.id, d.value, f.name
           FROM {classroom_session_fields} f
      LEFT JOIN {classroom_session_field_data} d ON f.id = d.fieldid AND d.sessionid = ?";

        return $DB->get_records_sql($sql, [$this->id]);
    }

    /**
     * Static functions.
     */

    /**
     * Get session record.
     */
    public static function get_session($id) {
        global $DB;

        return $DB->get_record('classroom_sessions', ['id' => $id]);
    }

    public static function get_sessions_sql($sql, $params = []) {
        global $DB;

        return $DB->get_record_sql($sql, $params);
    }

    public static function get_sessions($classroomid = null, $conditions = [], $fields = '*') {
        global $DB;

        if (!is_null($classroomid)) {
            $conditions['classroomid'] = $classroomid;
        }

        return $DB->get_records('classroom_sessions', $conditions, '', $fields);
    }

    public static function get_dates($sessionid) {
        global $DB;

        return $DB->get_records('classroom_session_dates', ['sessionid' => $sessionid]);
    }

    private static function update_custom_field($sessionid, $fieldid, $value) {
        global $DB;

        $rec                = new stdClass;
        $rec->sessionid     = $sessionid;
        $rec->fieldid       = $fieldid;
        $rec->value         = $value;
        $rec->timemodified  = time();

        if ($data = $DB->get_record('classroom_session_field_data', ['sessionid' => $sessionid, 'fieldid' => $fieldid])) {
            $rec->id = $data->id;
            return $DB->update_record('classroom_session_field_data', $rec);
        }

        return $DB->insert_record('classroom_session_field_data', $rec);
    } 
}