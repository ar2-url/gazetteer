<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$access_key = '563492ad6f91700001000001300c4ca6a4e548b9b395bfd0848e7c44';

$url ='https://api.pexels.com/v1/search?page=1&per_page=5&query=united%20kingdom';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: ' . $access_key
));
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);

curl_close($ch);

$decoded = json_decode($result, true);

if ($httpCode == 200 || $err) {
    foreach ($decoded['photos'] as $value) {
        $outcome['photos'][] = $value['url'];
     }
} else {
    $outcome['photos'] = 'No data';
}

echo json_encode($outcome);