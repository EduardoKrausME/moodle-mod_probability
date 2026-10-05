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
 * Backup structure for the Probability activity.
 *
 * @package    mod_probability
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the data included in a Probability activity backup.
 */
class backup_probability_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines the backup structure.
     *
     * @return backup_nested_element
     */
    protected function define_structure(): backup_nested_element {
        $probability = new backup_nested_element("probability", ["id"], [
            "name",
            "intro",
            "introformat",
            "coinenabled",
            "dieenabled",
            "urnenabled",
            "defaulttrials",
            "timecreated",
            "timemodified",
        ]);

        $probability->set_source_table("probability", ["id" => backup::VAR_ACTIVITYID]);
        $probability->annotate_files("mod_probability", "intro", null);

        return $this->prepare_activity_structure($probability);
    }
}
