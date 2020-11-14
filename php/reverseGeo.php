<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$url ='https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=' . $_REQUEST['lat'] . '&longitude=' . $_REQUEST['lng'] . '&localityLanguage=en';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json'
));
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);

curl_close($ch);

if ($httpCode == 200 && !$err) {
    $decoded = json_decode($result, true);
    $outcome = $decoded['countryName'];
} else {
    $outcome = $httpCode;
}
echo json_encode($outcome);