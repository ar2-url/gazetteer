<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$endPoint = "https://en.wikipedia.org/w/api.php";
$params = [
    "action" => "opensearch",
    "search" => $_REQUEST['country'],
    "limit" => "5",
    "namespace" => "0",
    "format" => "json"
];

$url = $endPoint . "?" . http_build_query( $params );

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$output = curl_exec( $ch );
curl_close( $ch );

$decoded = json_decode($output);

echo json_encode($decoded[3][0]);