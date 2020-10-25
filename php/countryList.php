<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$content = file_get_contents('../vendors/countries/countries_small.geo.json');
$content = utf8_decode($content);

$decoded = json_decode($content, true);

$info = '';

$name = $_REQUEST['countryName'];

foreach ($decoded['features'] as $feature) {
    if ($feature['properties']['name'] == $name) {
        $info = $feature;
    break;
    } else {
        $info = 'Not supported';
    }
}

echo json_encode($info);
