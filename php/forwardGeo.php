<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$url = 'https://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&exsentences=10&exlimit=1&titles=london&explaintext=1&formatversion=2';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode != 200) {
	echo $httpCode;
} else {
	$decoded = json_decode($result, true);
	echo json_encode($decoded);
}


