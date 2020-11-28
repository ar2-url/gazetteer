<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$key = 'ad6e24a64254b73ff9e9cc4c08e43823';
$url ='https://api.openweathermap.org/data/2.5/onecall?lat=50&lon=-3&exclude={part}&appid=' . $key;

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
	$outcome['weather']['date'] = date('l d-m', $decoded['current']['dt']);
	$outcome['weather']['temp'] = round($decoded['current']['temp'] - 273);
	$outcome['weather']['feels_like'] = round($decoded['current']['feels_like'] - 273);
	$outcome['weather']['sunrise'] = date('H:i', $decoded['current']['sunrise']);
	$outcome['weather']['sunset'] = date('H:i', $decoded['current']['sunset']);
	for ($i = 0; $i < count($decoded['hourly']); $i++ ) {
		$outcome['weather']['forecast'][$i]['hour'] = date('H:i', $decoded['hourly'][$i]['dt']);
		$outcome['weather']['forecast'][$i]['description'] = $decoded['hourly'][$i]['weather'][0]['description'];
		$outcome['weather']['forecast'][$i]['icon'] = $decoded['hourly'][$i]['weather'][0]['icon'];
	}
	echo json_encode($outcome);
} else {
	echo $err;
}
