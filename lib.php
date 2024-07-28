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
 * Lib file for the mod_classrooms plugin.
 *
 * @package   mod_classrooms
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('classes/classroom.php');

/**
 * Created a new classroom instance.
 */
function classrooms_add_instance($instancedata, $mform = null): int {
    return classroom::new($instancedata);
}

/**
 * Updated an existing classroom instance.
 */
function classrooms_update_instance($instancedata, $mform): bool {
    $classroom = new classroom($instancedata->id);
    return $classroom->update($instancedata);
}

/**
 * Delete a classroom instance.
 */
function classrooms_delete_instance($id): bool {
    $classroom = new classroom($id);
    return $classroom->delete();
}

function classrooms_sessions_table($classroomid, $data, $cmid, $hiddencolumns = ['id']) {
    global $OUTPUT;

    if (!isset($data)) {
        return false;
    }

    $table = new html_table;

    foreach (reset($data) as $key => $val) {
        if (!in_array(strtolower($key), $hiddencolumns)) {
            $table->head[] = $key;
        }
    }
    $table->head[] = 'Dates';
    $table->head[] = 'Actions';

    foreach ($data as $d) {
        $cells = [];
        foreach ($d as $key => $val) {
            if (in_array(strtolower($key), $hiddencolumns)) {
                continue;
            }

            if (str_contains($key, 'time')) {
                $cells[] = new html_table_cell(date("d-m-Y g:iA", $val));
                continue;
            }

            $cells[] = new html_table_cell($val);
        }

        // Dates column.
        // Get dates.
        if ($dates = sessions::get_dates($d->id)) {
            $datecell = "";
            foreach ($dates as $date) {
                $datecell .= date("Y/m/d", $date->timestart) . " - " . date("Y/m/d", $date->timefinish) . '<br>';
            }
            $cells[] = new html_table_cell($datecell);
        } else {
            $cells[] = new html_table_cell('No dates defined');
        }


        // Actions column.
        $icons = [];

        // Edit icon.
        $icons[] = $OUTPUT->action_icon(
            new moodle_url('/mod/classrooms/edit_session.php', ['id' => $cmid, 'classroomid' => $classroomid, 'sessionid' => $d->id]),
            new pix_icon('b/document-edit', get_string('edit')),
        );

        $cells[] = implode("", $icons);

        $table->data[] = new html_table_row($cells);
    }

    return html_writer::table($table);
}