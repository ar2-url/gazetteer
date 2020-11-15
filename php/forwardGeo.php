<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$key = '148801d4057a0ce93529c9abbeb45c10';
$url ='http://data.fixer.io/api/latest?access_key=' . $key . '&base=USD&symbols=GBP';

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
echo $err;