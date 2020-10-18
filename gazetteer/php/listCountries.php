<?php

$countries = file_get_contents('../vendors/countries/countries_small.geo.json');

$decoded = json_decode($countries, true);

$countriesList = [];

foreach ($decoded['features'] as $code) {

    $countriesList[] = $code['properties']['name'];
} 

echo json_encode($countriesList);