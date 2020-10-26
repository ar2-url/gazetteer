<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

if (!$_REQUEST) {
    $outcome['message'] = 'No data';
} else {

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

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close( $ch );
    $decoded = json_decode($output);

    if ($httpCode != 200) {
        $outcome['status'] = $httpCode;
        $outcome['message'] = 'No data';
    } else {
        $outcome = $decoded[3][0];
    }
}
echo json_encode($outcome);