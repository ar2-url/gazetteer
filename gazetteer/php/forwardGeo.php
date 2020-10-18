<?php 

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$token = '5aa3fa0354022f';
$url = 'https://eu1.locationiq.com/v1/search.php?key=' . $token . '&q=' . $_REQUEST['countryName'] . '&format=json';

$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

curl_close($ch);

$decoded = json_decode($result, true);

$latln[] = $decoded[0]['lat'];
$latln[] = $decoded[0]['lon'];
$latln[] = $decoded[0]['boundingbox'];

echo json_encode($latln);