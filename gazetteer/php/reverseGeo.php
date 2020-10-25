<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$token = '5aa3fa0354022f';
$url = 'https://us1.locationiq.com/v1/reverse.php?key=' . $token . '&format=json&lat=' . $_REQUEST['lat'] . '&lon=' . $_REQUEST['lng'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

curl_close($ch);

echo $result;