<?php

require_once(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])))) . '/config.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once('Zend/Http/Client.php');

$api_url = !empty($_GET['request']) ? urldecode($_GET['request']) : '';

// TODO - whitelist support?


$client = new Zend_Http_Client($api_url);
// Construct basic auth header for configured service account
$client->setHeaders('Authorization', 'Basic ' . base64_encode('hasp:hasp'));

// TODO - Append user filter for currently logged in Moodle user

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
