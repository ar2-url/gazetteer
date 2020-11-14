<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$key = 'ad6e24a64254b73ff9e9cc4c08e43823';
$url ='https://api.openweathermap.org/data/2.5/forecast?q=london&appid=' . $key;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);

curl_close($ch);
$decoded = json_decode($result, true);

for ($i = 0; $i < count($decoded['list']); $i++) {
    $outcome['weather'][$i]['date'] = $decoded['list'][$i]['dt_txt'];
    $outcome['weather'][$i]['temp'] = round($decoded['list'][$i]['main']['temp'] - 273);
    $outcome['weather'][$i]['feels'] = round($decoded['list'][$i]['main']['feels_like'] - 273);
    $outcome['weather'][$i]['pressure'] = $decoded['list'][$i]['main']['pressure'];
    $outcome['weather'][$i]['decription'] = $decoded['list'][$i]['weather'][0]['description'];
    $outcome['weather'][$i]['icon'] = $decoded['list'][$i]['weather'][0]['icon'];
if (isset($decoded['list'][$i]['rain'])) {
    $outcome['weather'][$i]['rain'] = $decoded['list'][$i]['rain']['3h'];
    
} else {
    $outcome['weather'][$i]['rain'] = 0;
}    
}


echo json_encode($outcome);
echo $err;