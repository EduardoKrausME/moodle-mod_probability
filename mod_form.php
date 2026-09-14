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
 * Activity configuration form.
 *
 * @package    mod_probability
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("{$CFG->dirroot}/course/moodleform_mod.php");

/**
 * Activity form.
 */
class mod_probability_mod_form extends moodleform_mod {
    /**
     * Defines the form fields.
     *
     * @return void
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement("header", "general", get_string("general", "form"));
        $mform->addElement("text", "name", get_string("probabilityname", "mod_probability"), ["size" => 64]);
        $mform->setType("name", PARAM_TEXT);
        $mform->addRule("name", null, "required", null, "client");

        $this->standard_intro_elements();

        $mform->addElement("html", html_writer::tag("h3", get_string("simulators", "mod_probability")));
        $mform->addElement("advcheckbox", "coinenabled", get_string("enablecoin", "mod_probability"));
        $mform->setDefault("coinenabled", 1);

        $mform->addElement("advcheckbox", "dieenabled", get_string("enabledie", "mod_probability"));
        $mform->setDefault("dieenabled", 1);

        $mform->addElement("advcheckbox", "urnenabled", get_string("enableurn", "mod_probability"));
        $mform->setDefault("urnenabled", 1);

        $trialoptions = [10 => "10", 100 => "100", 1000 => "1.000"];
        $mform->addElement("select", "defaulttrials", get_string("defaulttrials", "mod_probability"), $trialoptions);
        $mform->setDefault("defaulttrials", 100);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Validates form data.
     *
     * @param array $data Submitted values.
     * @param array $files Submitted files.
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (empty($data["coinenabled"]) && empty($data["dieenabled"]) && empty($data["urnenabled"])) {
            $errors["coinenabled"] = get_string("erroratleastonemode", "mod_probability");
        }

        return $errors;
    }
}
