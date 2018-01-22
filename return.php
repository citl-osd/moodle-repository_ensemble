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
 * Ensemble Video repository plugin.
 *
 * @package    repository_ensemble
 * @copyright  2012 Liam Moran, Nathan Baxley, University of Illinois
 *             2013 Symphony Video, Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

$defaultthumb = new moodle_url('repository/ensemble/ext_chooser/css/images/playlist.png');

$content   = required_param('content', PARAM_RAW);
$title     = required_param('title', PARAM_TEXT);
$repoid    = required_param('repo_id', PARAM_INT);
$contextid = required_param('context_id', PARAM_INT);

$repo = repository::get_instance($repoid);
if (!$repo) {
    error("Invalid repository id");
}

require_login();
require_sesskey();

$context = context::instance_by_id($contextid, true);
require_capability('repository/ensemble:view', $context);

$PAGE->set_pagelayout('embedded');
$PAGE->set_url(new moodle_url('/repository/ensemble/return.php'));
$PAGE->set_context($context);

echo $OUTPUT->header();

$sourceurl = new moodle_url($repo->get_option('ensembleURL'), array(
    'content' => urlencode($content)
));
$source = $sourceurl->out(true);
$sourcekey = sha1($source . $repo::get_secret_key() . sesskey());

$escapedTitle = str_replace("'", "\'", $title);

$js = <<<EOD
var filepicker = window.parent.M.core_filepicker.active_filepicker;

filepicker.select_file({
    title: '{$escapedTitle}.mp4',
    source: '{$source}',
    sourcekey: '{$sourcekey}',
    thumbnail: ''
});
EOD;

$PAGE->requires->js_amd_inline($js);

echo $OUTPUT->footer();
