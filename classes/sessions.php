<?php

class sessions {

    public $classroomid;

    public $id;

    public function __construct($id)
    {
        $data = sessions::get_session($id);
        $this->id = $data->id;
        $this->classroomid = $data->classroomid;
    }

    public static function new($data) {
        global $DB;

        $record = new stdClass;
        $record->classroomid = $data->classroomid;
        $record->timecreated = time();
        $record->timemodified = time();

        $sessionid = $DB->insert_record('classroom_sessions', $record);

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
        $record->timemodified = time();

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
}