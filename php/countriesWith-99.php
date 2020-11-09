<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// get feature

$content = file_get_contents('../vendors/countries/countries_small.geo.json');

$decoded = json_decode($content, true);

$info = '';

$countryName = $_REQUEST['countryName'];

foreach ($decoded['features'] as $feature) {
    if ($feature['properties']['name'] == $countryName) {
        $info = $feature;
    break;
    } else {
        $info = 'Not supported';
    }
}

$outcome['feature'] = $info;

// get lat and lon

$token = '5aa3fa0354022f';
$url = 'https://eu1.locationiq.com/v1/search.php?key=' . $token . '&q=' . $countryName . '&format=json';

$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode != 200) {
  $outcome['latlng'] = 'No data';
} else {
  $decoded = json_decode($result, true);
  $outcome['latlng']['lat'] = $decoded[0]['lat'];
  $outcome['latlng']['lon'] = $decoded[0]['lon'];
}

//get capital, population, currency code, symbol

$url = 'https://restcountries.eu/rest/v2/name/' . $countryName;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

if(curl_errno($ch)){
    echo 'Request Error:' . curl_error($ch);
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

$data = json_decode($result, true); 
if ($httpCode != 200) {
    $outcome['status'] = $httpCode;
    $outcome['message'] = 'No data';
    $outcome['weather'] = 'No data';
} else {
   
    $outcome['status'] = $httpCode;
    $outcome['code'] = $data[0]['alpha2Code'];
    $outcome['capital'] = $data[0]['capital'];
    $outcome['population'] = $data[0]['population'];
    $outcome['curr_Code'] = $data[0]['currencies'][0]['code'];
    $outcome['curr_Name'] = $data[0]['currencies'][0]['name'];
    $outcome['curr_Symbol'] = $data[0]['currencies'][0]['symbol'];
    $outcome['flag'] = $data[0]['flag'];
    $outcome['name'] = $data[0]['name'];
   
// get country cities 

$content = file_get_contents('../vendors/world-cities_zip/world_cities.json');

$decoded = json_decode($content, true);

$j = 0;
$list[] = '';
for ($i = 0; $i < count($decoded); $i++) {
  if ($decoded[$i]['country'] == $countryName && $decoded[$i]['population'] > 200000) {
    $outcome['cities'][$j]['city'] = $decoded[$i]['city_ascii'];
    $outcome['cities'][$j]['lat'] = $decoded[$i]['lat'];
    $outcome['cities'][$j]['lng'] = $decoded[$i]['lng'];
    $j++;
  } elseif (!$countryName) {
    $outcome['cities'] = 'No data';
  break;
  }
}

// get currency rate against USD

$rate_api_key = '0b0539e3df194e74ab64cdca683e822f';
$url = 'https://openexchangerates.org/api/latest.json?app_id=' . $rate_api_key;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

curl_close($ch);

$data = json_decode($result, true);

foreach ($data['rates'] as $currency => $value) {
  if ($currency == $outcome['curr_Code']) {
    $outcome['exRate'] = $value;
  break;
  } else {
    $outcome['exRate'] = 'No data';
  }
}

// get 5 day weather forecast

$api_key = 'ad6e24a64254b73ff9e9cc4c08e43823';
$url = 'api.openweathermap.org/data/2.5/weather?q=' . $outcome['capital'] . '&appid=' . $api_key;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

$decoded = json_decode($result, true);

if ($httpCode != 200) {
  $outcome['weather'] = 'No data';
} else {
    $outcome['weather']['description'] = $decoded['weather'][0]['description'];
    $outcome['weather']['icon'] = $decoded['weather'][0]['icon'];
    $outcome['weather']['temp'] = round($decoded['main']['temp']) - 273;
    $outcome['weather']['feels'] = round($decoded['main']['feels_like']) - 273;
    $outcome['weather']['sunrise'] = date('H:i:sa', $decoded['sys']['sunrise']);
    $outcome['weather']['sunset'] = date('H:i:sa', $decoded['sys']['sunset']);
}
// **************************************
// get wiki paragraph

$url = 'https://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&exsentences=10&exlimit=1&titles=' . $outcome['name'] . '&explaintext=1&formatversion=2';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$output = curl_exec( $ch );

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);;
curl_close( $ch );

$decoded = json_decode($output);

if ($httpCode != 200 || !$decoded->query->pages[0]->extract) {
    $outcome['wiki'] = 'No data';
} else {
    $outcome['wiki'] = $decoded->query->pages[0]->extract;
}
// get images 

$access_key = '563492ad6f91700001000001300c4ca6a4e548b9b395bfd0848e7c44';

$url ='https://api.pexels.com/v1/search?page=1&per_page=5&query=' . $countryName;

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

}
echo json_encode($outcome);