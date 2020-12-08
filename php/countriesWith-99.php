<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// get feature

$content = file_get_contents('../vendors/countries/countries_small.geo.json');

$decoded = json_decode($content, true);

$info = '';

$countryName = $_REQUEST['countryName'];

foreach ($decoded['features'] as $feature) {
    if ($feature['id'] == $countryName) {
        $info = $feature;
    break;
    } else {
        $info = 'Not supported';
    }
}

$outcome['feature'] = $info;


//get capital, population, currency code, symbol

$url = 'https://restcountries.eu/rest/v2/alpha?codes=' . $feature['id'];

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
    $outcome['name'] = $feature['properties']['name'];

// get lat n lon of capital

$token = '5aa3fa0354022f';
$url = 'https://eu1.locationiq.com/v1/search.php?key=' . $token . '&q=' . $outcome['capital'] . '&format=json';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($httpCode != 200) {
  $outcome['capital']['lat'] = 'No data';
} else {
  $decoded = json_decode($result, true);
  $outcome['capitalLat'] = $decoded[0]['lat'];
  $outcome['capitalLon'] = $decoded[0]['lon'];
}

// get major cities

$content = file_get_contents('../vendors/world-cities_zip/world_cities.json');

$decoded = json_decode($content, true);

$j = 0;
for ($i = 0; $i < count($decoded); $i++) {
  if ($decoded[$i]['iso3'] == $feature['id'] && $decoded[$i]['population'] > 200000) {
    $outcome['cities'][$j]['city'] = $decoded[$i]['city_ascii'];
    $outcome['cities'][$j]['lat'] = $decoded[$i]['lat'];
    $outcome['cities'][$j]['lng'] = $decoded[$i]['lng'];
    $outcome['cities'][$j]['population'] = $decoded[$i]['population'];
    $j++;
  } 
}

if ($j == 0) {
  for ($i = 0; $i < count($decoded); $i++) {
    if ($decoded[$i]['iso3'] == $feature['id'] && $decoded[$i]['population'] > 150000) {
      $outcome['cities'][$j]['city'] = $decoded[$i]['city_ascii'];
      $outcome['cities'][$j]['lat'] = $decoded[$i]['lat'];
      $outcome['cities'][$j]['lng'] = $decoded[$i]['lng'];
      $outcome['cities'][$j]['population'] = $decoded[$i]['population'];
      $j++;
    } 
  }
}

if ($j == 0) {
  for ($i = 0; $i < count($decoded); $i++) {
    if ($decoded[$i]['iso3'] == $feature['id'] && $decoded[$i]['population'] > 100000) {
      $outcome['cities'][$j]['city'] = $decoded[$i]['city_ascii'];
      $outcome['cities'][$j]['lat'] = $decoded[$i]['lat'];
      $outcome['cities'][$j]['lng'] = $decoded[$i]['lng'];
      $outcome['cities'][$j]['population'] = $decoded[$i]['population'];
      $j++;
    } 
  }
}

if ($j == 0) {
  for ($i = 0; $i < count($decoded); $i++) {
    if ($decoded[$i]['iso3'] == $feature['id'] && $decoded[$i]['population'] > 50000) {
      $outcome['cities'][$j]['city'] = $decoded[$i]['city_ascii'];
      $outcome['cities'][$j]['lat'] = $decoded[$i]['lat'];
      $outcome['cities'][$j]['lng'] = $decoded[$i]['lng'];
      $outcome['cities'][$j]['population'] = $decoded[$i]['population'];
      $j++;
    } 
  }
}
// get currency rate against USD

$rate_api_key = '0b0539e3df194e74ab64cdca683e822f';
$url = 'https://openexchangerates.org/api/latest.json?app_id=' . $rate_api_key;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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

// get weather forecast

$key = 'ad6e24a64254b73ff9e9cc4c08e43823';
$url = 'https://api.openweathermap.org/data/2.5/onecall?lat=50&lon=-3&exclude=minutely,hourly,alerts&appid=' . $key;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

$decoded = json_decode($result, true);

if ($httpCode == 200) {
  for ($i = 0; $i < count($decoded['daily']); $i++) {
    $outcome['weather']['forecast'][$i]['date'] = date('d F', $decoded['daily'][$i]['dt']);
    $outcome['weather']['forecast'][$i]['day'] = date('l', $decoded['daily'][$i]['dt']);
    $outcome['weather']['forecast'][$i]['temp_day'] = round($decoded['daily'][$i]['temp']['day'] - 273);
    $outcome['weather']['forecast'][$i]['temp_night'] = round($decoded['daily'][$i]['temp']['night'] - 273);
    $outcome['weather']['forecast'][$i]['description'] = $decoded['daily'][$i]['weather'][0]['description'];
    $outcome['weather']['forecast'][$i]['icon'] = $decoded['daily'][$i]['weather'][0]['icon'];
  }
} else {
  $outcome['weather'] = $httpCode;
}
// **************************************
// get wiki paragraph

$url = 'https://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&titles=' . $feature['properties']['name'];
$url = str_replace(' ', '%20', $url);

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$output = curl_exec( $ch );

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);;
curl_close( $ch );

$decoded = json_decode($output, true);

if ($httpCode != 200) {
    $outcome['wiki'] = 'No data';
} else {
    foreach ($decoded['query']['pages'] as $page) {
      $outcome['wiki'][] = $page['extract'];
    }
    
}
// get images 

$access_key = '563492ad6f91700001000001300c4ca6a4e548b9b395bfd0848e7c44';
$name = str_replace(' ', '%20', $feature['properties']['name']);
$queryFields = [
  "query" => $name,
  "page" => 1,
  "per_page" => 10
];

$options = [
  CURLOPT_URL => "https://api.shutterstock.com/v2/images/search?" . http_build_query($queryFields),
  CURLOPT_USERAGENT => "php/curl",
  CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
  CURLOPT_USERPWD => "QHfJIG7lmQIFvM1sDH0z9kyjUUESAP4m:KXGvkMBUMBt7LfVL",
  CURLOPT_RETURNTRANSFER => 1
];

$handle = curl_init();
curl_setopt_array($handle, $options);
$response = curl_exec($handle);
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
curl_close($handle);

$decoded = json_decode($response, true);

if ($httpCode == 200) {
  foreach ($decoded['data'] as $photos) {
    $outcome['photos'][] = $photos['assets']['preview']['url'];
  }
} else {
  $outcome['photos'] = $httpCode;
}

}
echo json_encode($outcome);