<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$url ='https://en.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&titles=poland&redirects=true';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);

curl_close($ch);
$decoded = json_decode($result, true);

echo json_encode($decoded);