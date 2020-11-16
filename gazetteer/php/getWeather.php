<?php

ini_set('diplay_errors', 'On');
error_reporting(E_ALL);

$api_key = 'ad6e24a64254b73ff9e9cc4c08e43823';
$url = 'api.openweathermap.org/data/2.5/weather?q=' . $_REQUEST['capital'] . '&appid=' . $api_key;
$url2 = 'api.openweathermap.org/data/2.5/forecast?q=' . $_REQUEST['capital'] . '&appid=' . $api_key;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

$decoded = json_decode($result, true);

if ($httpCode != 200) {
    $outcome['status'] = $httpCode;
    $outcome['message'] = 'No data';
} else {

    $outcome['description'] = $decoded['weather'][0]['description'];
    $outcome['temperature'] = round($decoded['main']['temp'] - 273);
    $outcome['icon'] = $decoded['weather'][0]['icon'];

    $ch = curl_init($url2);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $url2);

    $result = curl_exec($ch);

    curl_close($ch);

    $decoded = json_decode($result, true);

    $outcome['tomorrow']['description'] = $decoded['list'][7]['weather'][0]['description'];
    $outcome['tomorrow']['temperature'] = round($decoded['list'][7]['main']['temp'] - 273);
    $outcome['tomorrow']['icon'] = $decoded['list'][7]['weather'][0]['icon'];
}
echo json_encode($outcome);


