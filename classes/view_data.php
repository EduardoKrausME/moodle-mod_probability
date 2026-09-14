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

namespace mod_probability;

/**
 * Prepares Mustache data for the simulator page.
 *
 * @package    mod_probability
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_data {
    /** @var stdClass Activity instance. */
    private \stdClass $instance;

    /**
     * Constructor.
     *
     * @param \stdClass $instance Activity instance.
     */
    public function __construct(\stdClass $instance) {
        $this->instance = $instance;
    }

    /**
     * Exports template data.
     *
     * @return array
     */
    public function export(): array {
        $firstmode = "";
        foreach (["coin" => $this->instance->coinenabled, "die" => $this->instance->dieenabled,
                     "urn" => $this->instance->urnenabled] as $mode => $enabled) {
            if ($enabled) {
                $firstmode = $mode;
                break;
            }
        }

        return [
            "coin" => (bool)$this->instance->coinenabled,
            "die" => (bool)$this->instance->dieenabled,
            "urn" => (bool)$this->instance->urnenabled,
            "coinactive" => $firstmode === "coin",
            "dieactive" => $firstmode === "die",
            "urnactive" => $firstmode === "urn",
            "trial10" => (int)$this->instance->defaulttrials === 10,
            "trial100" => (int)$this->instance->defaulttrials === 100,
            "trial1000" => (int)$this->instance->defaulttrials === 1000,
        ];
    }
}
