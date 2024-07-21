<?php

class sessions {

    public function __construct($id)
    {
        
    }

    public static function new($data) {
        global $DB;

        $record = new stdClass;
        $record->classroomid = $data->classroomid;
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record('classroom_sessions', $record);
    }

    public function update() {

    }

    public function delete() {

    }

    private function add_date() {

    }

    private function update_date() {
        
    }

    private function delete_date() {

    }

    /**
     * Static functions.
     */

    /**
     * Get session record.
     */
    public static function get_session($id) {

    }

    public static function get_sessions($classroomid = null, $conditions = [], $fields = '*') {
        global $DB;

        if (!is_null($classroomid)) {
            $conditions['classroomid'] = $classroomid;
        }

        return $DB->get_records('classroom_sessions', $conditions, '', $fields);
    }
}