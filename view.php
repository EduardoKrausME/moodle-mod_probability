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
 * Activity view page.
 *
 * @package    mod_probability
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

$id = required_param("id", PARAM_INT);
$cm = get_coursemodule_from_id("probability", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$probability = $DB->get_record("probability", ["id" => $cm->instance], "*", MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/probability:view", $context);

$PAGE->set_url("/mod/probability/view.php", ["id" => $cm->id]);
$PAGE->set_title(format_string($probability->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->css("/mod/probability/styles.css");

$event = \mod_probability\event\course_module_viewed::create([
    "objectid" => $probability->id,
    "context" => $context,
]);
$event->add_record_snapshot("course_modules", $cm);
$event->add_record_snapshot("course", $course);
$event->add_record_snapshot("probability", $probability);
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$view = new \mod_probability\view_data($probability);
$data = $view->export();

$PAGE->requires->js_call_amd("mod_probability/simulator", "init", [[
    "defaultTrials" => (int)$probability->defaulttrials,
]]);

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($probability->name));

if (trim((string)$probability->intro) !== "") {
    echo $OUTPUT->box(format_module_intro("probability", $probability, $cm->id), "generalbox mod_introbox");
}

echo $OUTPUT->render_from_template("mod_probability/main", $data);
echo $OUTPUT->footer();
