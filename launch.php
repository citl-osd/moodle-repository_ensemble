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
 * @copyright  2013 Symphony Video, Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use moodle\mod\lti as lti;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once($CFG->dirroot . '/mod/lti/OAuth.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

// TODO - access needs testing
$repo_id = required_param('repo_id', PARAM_INT);
$repo = repository::get_instance($repo_id);
if (!$repo) {
    error("Invalid repository id");
}
require_login($repo->context);
require_capability('repository/ensemble:view', $repo->context);

$launchUrl = $repo->get_option('ensembleURL') . '/app/lti/launch.ashx';
$consumerKey = $repo->get_option('consumerKey');
$sharedSecret = $repo->get_option('sharedSecret');
$additionalParams = $repo->get_option('additionalParams');

$consumer = new lti\OAuthConsumer($consumerKey, $sharedSecret);
$request = lti\OAuthRequest::from_consumer_and_token($consumer, false, 'POST', $launchUrl);
$url = new moodle_url('/repository/ensemble/return.php', array(
                'repo_id' => $repo_id
));
$returnUrl = $url->out(false);

// Add our LTI params
$request->set_parameter('oauth_callback', 'about:blank');
$request->set_parameter('lis_person_contact_email_primary', $USER->email);
$request->set_parameter('lti_message_type', 'basic-lti-launch-request');
$request->set_parameter('lti_version', 'LTI-1p0');
$request->set_parameter('resource_link_id', 'TODO');
$request->set_parameter('tool_consumer_info_product_family_code', 'moodle');
$request->set_parameter('user_id', $USER->id);
$request->set_parameter('launch_presentation_return_url', $returnUrl);
$request->set_parameter('custom_moodle_user_login_id', $USER->username);
$params = explode("\n", $additionalParams);
foreach ($params as $param) {
    $param = trim($param);
    if ($param === '') {
        continue;
    }
    $parts = explode('=', $param);
    if (count($parts) !== 2) {
        continue;
    }
    $parts[0] = trim($parts[0]);
    $parts[1] = trim($parts[1]);
    $request->set_parameter($parts[0], $parts[1]);
}

$request->sign_request(new lti\OAuthSignatureMethod_HMAC_SHA1(), $consumer, false);

$api_url = $request->get_normalized_http_url();

echo lti_post_launch_html($request->get_parameters(), $api_url);
