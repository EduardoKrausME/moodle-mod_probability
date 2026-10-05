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
 * Restore structure for the Probability activity.
 *
 * @package    mod_probability
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Restores Probability activity data.
 */
class restore_probability_activity_structure_step extends restore_activity_structure_step {
    /**
     * Defines the restore structure.
     *
     * @return array
     */
    protected function define_structure(): array {
        return $this->prepare_activity_structure([
            new restore_path_element("probability", "/activity/probability"),
        ]);
    }

    /**
     * Restores the activity instance.
     *
     * @param array $data Restored data.
     * @return void
     */
    protected function process_probability(array $data): void {
        global $DB;

        $record = (object)$data;
        $record->course = $this->get_courseid();
        $record->timecreated = $this->apply_date_offset($record->timecreated);
        $record->timemodified = $this->apply_date_offset($record->timemodified);

        $newitemid = $DB->insert_record("probability", $record);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restores related files after the activity record exists.
     *
     * @return void
     */
    protected function after_execute(): void {
        $this->add_related_files("mod_probability", "intro", null);
    }
}
