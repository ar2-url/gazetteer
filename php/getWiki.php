<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);
$api_key = 'Kv9rI1tWulq7TDPY-sZK3rWXGzasTgR_B3A3QzE_1w5lSA8aVa9lXTqxTtIFk-h3jbuKV4HqHaHylHeiNjlgoA';

$url = 'https://reverse.geocoder.ls.hereapi.com/6.2/reversegeocode.json
?apiKey=' . $api_key . '&mode=retrieveLandmarks&prox=37.7442,-119.5931,1000';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$output = curl_exec( $ch );

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close( $ch );



echo json_encode($output);

