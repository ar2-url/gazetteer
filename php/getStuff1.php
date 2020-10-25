<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$url = 'https://restcountries.eu/rest/v2/name/' . $_REQUEST['countryName'];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

if(curl_errno($ch)){
    echo 'Request Error:' . curl_error($ch);
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

$data = json_decode($result, true); 

    $outcome['status'] = $httpCode;
    $outcome['capital'] = $data[0]['capital'];
    $outcome['population'] = $data[0]['population'];
    $outcome['curr_Code'] = $data[0]['currencies'][0]['code'];
    $outcome['curr_Name'] = $data[0]['currencies'][0]['name'];
    $outcome['curr_Symbol'] = $data[0]['currencies'][0]['symbol'];
    $outcome['flag'] = $data[0]['flag'];
    $outcome['name'] = $data[0]['name'];
   
    echo json_encode($outcome);


