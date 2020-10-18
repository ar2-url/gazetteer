<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$api_key = '0b0539e3df194e74ab64cdca683e822f';
$url = 'https://openexchangerates.org/api/latest.json?app_id=' . $api_key;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

curl_close($ch);

$data = json_decode($result, true);

foreach ($data['rates'] as $currency=>$value) {
    if ($currency === $_REQUEST['code']) {
        $outcome = $value;
    } 
} 

echo $outcome;

