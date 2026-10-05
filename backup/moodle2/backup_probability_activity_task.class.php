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
 * Backup task for the Probability activity.
 *
 * @package    mod_probability
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . "/backup_probability_stepslib.php");

/**
 * Backup task for mod_probability.
 */
class backup_probability_activity_task extends backup_activity_task {
    /**
     * Defines activity-specific backup settings.
     *
     * @return void
     */
    protected function define_my_settings(): void {
    }

    /**
     * Defines activity-specific backup steps.
     *
     * @return void
     */
    protected function define_my_steps(): void {
        $this->add_step(new backup_probability_activity_structure_step("probability_structure", "probability.xml"));
    }

    /**
     * Encodes links in backed-up content.
     *
     * @param string $content Content to encode.
     * @return string
     */
    public static function encode_content_links($content): string {
        return $content;
    }
}
