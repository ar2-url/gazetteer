<?php

$content = file_get_contents('../vendors/countries/countries_large.geo.json');

$decoded = json_decode($content, true);

$info = '';

$name = $_REQUEST['countryName'];

foreach ($decoded['features'] as $feature) {
    if ($feature['properties']['ADMIN'] == $name) {
        $info = $feature;
    break;
    } else {
        $info = 'Not supported';
    }
}

echo json_encode($info);
