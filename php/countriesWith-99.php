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

// get lat and lon of country

$token = '5aa3fa0354022f';
$url = 'https://eu1.locationiq.com/v1/search.php?key=' . $token . '&q=' . $_REQUEST['countryName'] . '&format=json';

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
    $outcome['name'] = $countryName;

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

// get country cities 

$content = file_get_contents('../vendors/world-cities_zip/world_cities.json');

$decoded = json_decode($content, true);

$j = 0;
for ($i = 0; $i < count($decoded); $i++) {
  if ($decoded[$i]['country'] == $_REQUEST['countryName'] && $decoded[$i]['population'] > 200000) {
    $outcome['cities'][$j]['city'] = $decoded[$i]['city_ascii'];
    $outcome['cities'][$j]['lat'] = $decoded[$i]['lat'];
    $outcome['cities'][$j]['lng'] = $decoded[$i]['lng'];
    $outcome['cities'][$j]['population'] = $decoded[$i]['population'];
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

// get 5 day weather forecast

$key = 'ad6e24a64254b73ff9e9cc4c08e43823';
$url ='https://api.openweathermap.org/data/2.5/onecall?lat=' . $outcome['latlng']['lat'] . '&lon=' . $outcome['latlng']['lon'] . '&appid=' . $key;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);

curl_close($ch);
$decoded = json_decode($result, true);

if ($httpCode == 200) {
	$outcome['weather']['day'] = date('l', $decoded['current']['dt']);
	$outcome['weather']['date'] = date('d F', $decoded['current']['dt']);
	$outcome['weather']['temp'] = round($decoded['current']['temp'] - 273);
	$outcome['weather']['feels_like'] = round($decoded['current']['feels_like'] - 273);
	$outcome['weather']['sunrise'] = date('H:i', $decoded['current']['sunrise']);
	$outcome['weather']['sunset'] = date('H:i', $decoded['current']['sunset']);
	for ($i = 0; $i < count($decoded['hourly']); $i++ ) {
		$outcome['weather']['forecast'][$i]['hour'] = date('H:i', $decoded['hourly'][$i]['dt']);
		$outcome['weather']['forecast'][$i]['description'] = $decoded['hourly'][$i]['weather'][0]['description'];
		$outcome['weather']['forecast'][$i]['icon'] = $decoded['hourly'][$i]['weather'][0]['icon'];
	}
} else {
	$outcome['weather'] = 'No data';
}
// **************************************
// get wiki paragraph

$url = 'https://en.wikipedia.org/w/api.php?action=query&format=json&prop=extracts&exsentences=10&exlimit=1&titles=' . $_REQUEST['countryName'] . '&explaintext=1&formatversion=2';

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

$url ='https://api.pexels.com/v1/search?page=1&per_page=5&query=' . $_REQUEST['countryName'];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: ' . $access_key,
    'Content-Type:image/jpeg',
    'Access-Control-Allow-Origin: *'
));
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);

curl_close($ch);

$decoded = json_decode($result, true);

if ($httpCode == 200 && !$err) {
    for ($i = 0; $i < count($decoded['photos']); $i++) {
        $outcome['photos'][] = $decoded['photos'][$i]['src']['small'];
    }
} else {
    $outcome['photos'] = 'No data';
}

}
echo json_encode($outcome);