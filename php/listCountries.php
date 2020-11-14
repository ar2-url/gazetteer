<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$countries = file_get_contents('../vendors/countries/countries_small.geo.json');

$decoded = json_decode($countries, true);

$countriesList = [];

if ($decoded['features']) {
    for ($i = 0; $i < count($decoded['features']); $i++) {
        $countriesList[$i]['name'] = $decoded['features'][$i]['properties']['name'];
        $countriesList[$i]['code'] = $decoded['features'][$i]['id'];
    }
}

function compare($a, $b) {
    return strcmp($a['name'], $b['name']);
}

usort($countriesList, 'compare');

echo json_encode($countriesList);

?>