<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$url ='https://api.opencagedata.com/geocode/v1/json?q=' . $_REQUEST['lat'] . '+' . $_REQUEST['lng'] . '&key=d742ee79b3224590be1c34e98a8a50a7';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);

curl_close($ch);
$decoded = json_decode($result, true);

if ($httpCode == 200) {
    $outcome['country_ISO3'] = $decoded['results'][0]['components']['ISO_3166-1_alpha-3'];
    $outcome['country_name'] = $decoded['results'][0]['components']['country'];
} else {
    $outcome = $httpCode;
}

echo json_encode($outcome);