
// get landmarks coords and names using lat n lon from above

$landmarks_key = 'mnVPO3LXrGQ2n6tkwqfkHJmo7-q1baNvxR_bAPhmXf4';
$url = 'https://reverse.geocoder.ls.hereapi.com/6.2/reversegeocode.json?apiKey=' . $landmarks_key . '&mode=retrieveLandmarks&prox=' . $outcome['latlng']['lat'] . ',' . $outcome['latlng']['lon'] . ',1000000';

$ch = curl_init();

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if (curl_errno($ch)) {
  $err_msg = curl_error($ch);
}

curl_close($ch);


if (isset($err_msg)) {
  $outcome['landmarks'] = 'No data';
} else {
  $decoded = json_decode($result, true);
  for ($i = 0; $i < 10; $i++) {
    $outcome['landmarks'][$i]['lat'] = $decoded['Response']['View'][0]['Result'][$i]['Location']['DisplayPosition']['Latitude'];
    $outcome['landmarks'][$i]['lon'] = $decoded['Response']['View'][0]['Result'][$i]['Location']['DisplayPosition']['Longitude'];
    $outcome['landmarks'][$i]['type'] = $decoded['Response']['View'][0]['Result'][$i]['Location']['LocationType'];
    $outcome['landmarks'][$i]['name'] = $decoded['Response']['View'][0]['Result'][$i]['Location']['Name'];
  }  
}