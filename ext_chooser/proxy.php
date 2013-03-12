<?php

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/lib.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once('Zend/Http/Client.php');

$api_url        = urldecode(required_param('request', PARAM_RAW));

$ensembleUrl    = get_config('ensemble', 'ensembleURL');
$serviceUser    = get_config('ensemble', 'serviceUser');
$servicePass    = get_config('ensemble', 'servicePass');
$authDomain     = get_config('ensemble', 'authDomain');

// Only service requests for our configured ensemble url
if (preg_match('#^' . preg_quote($ensembleUrl) . '#i', $api_url) !== 1) {
    header('Bad Request', true, 400);
    print('URL mismatch');
    exit;
}

$client = new Zend_Http_Client($api_url);
// Construct basic auth header for configured service account
$client->setHeaders('Authorization', 'Basic ' . base64_encode($serviceUser . (!empty($authDomain) ? '@' . $authDomain : '') . ':' . $servicePass));

// Append user filter for currently logged in Moodle user
$userFilter = $USER->username . (!empty($authDomain) ? '@' . $authDomain : '');
$client->setParameterGet('User', $userFilter);
$response = $client->request();

foreach ($response->getHeaders() as $header => $value) {
  header($header . ': ' . $value);
}

// Set response status.
header($response->getMessage(), true, $response->getStatus());

// Print actual data.
print $response->getBody();

exit;

?>
