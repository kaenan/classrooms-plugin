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
 * Classroom class file.
 *
 * @package   mod_classrooms
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('sessions.php');

class classroom {

    public $id;

    public $name;

    public $description;

    public $activesessions;

    public $pastsessions;

    function __construct($id, $restrict = true)
    {
        $data = classroom::get_classroom($id);
        $this->id = $id;
        $this->name = $data->name;
        $this->description = $data->description;

        $this->activesessions = $this->get_sessions(!$restrict);
        $this->pastsessions = $this->get_sessions(!$restrict);
    }

    public static function new($data): int {
        global $DB;

        $record = new stdClass;
        $record = $data;
        $record->timecreated = time();
        $record->timemodified = time();

        return $DB->insert_record('classrooms', $record);
    }

    public function update($data): bool {
        global $DB;

        $record = new stdClass;
        $record = $data;
        $record->timemodified = time();

        $record->id = $this->id;
    
        return $DB->update_record('classrooms', $record);
    }

    public function delete(): bool {
        global $DB;

        return $DB->delete_records('classrooms', ['id' => $this->id]);
    }

    /**
     * Private functions.
     */

    /**
     * Get sessions of classroom record.
    */
    private function get_sessions($hidden) {
        global $DB;

        // Get sessions.
        return sessions::get_sessions($this->id);
    }

    /**
     * Static functions.
     */

    /**
     * Get classroom record.
    */
    public static function get_classroom($id) {
        global $DB;

        return $DB->get_record('classrooms', ['id' => $id]);
    }
}