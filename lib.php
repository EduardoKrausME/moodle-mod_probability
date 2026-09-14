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
 * Callbacks for mod_probability.
 *
 * @package    mod_probability
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the features supported by the activity.
 *
 * @param string $feature Feature identifier.
 * @return mixed
 */
function probability_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_OTHER;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return false;
    }

    if (defined("FEATURE_MOD_PURPOSE") && $feature === constant("FEATURE_MOD_PURPOSE")) {
        return defined("MOD_PURPOSE_CONTENT") ? constant("MOD_PURPOSE_CONTENT") : null;
    }

    return null;
}

/**
 * Creates an activity instance.
 *
 * @param stdClass $data Activity data.
 * @param mod_probability_mod_form|null $mform Form instance.
 * @return int
 */
function probability_add_instance($data, $mform = null): int {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;

    return $DB->insert_record("probability", $data);
}

/**
 * Updates an activity instance.
 *
 * @param stdClass $data Activity data.
 * @param mod_probability_mod_form|null $mform Form instance.
 * @return bool
 */
function probability_update_instance($data, $mform = null): bool {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();

    return $DB->update_record("probability", $data);
}

/**
 * Deletes an activity instance.
 *
 * @param int $id Instance id.
 * @return bool
 */
function probability_delete_instance($id): bool {
    global $DB;

    if (!$DB->record_exists("probability", ["id" => $id])) {
        return false;
    }

    $DB->delete_records("probability", ["id" => $id]);
    return true;
}
