$(window).on('load', function () {    
  if ($('#preloader').length) {      
      $('#preloader').delay(100).fadeOut('slow', function () {        
          $(this).remove();      
      });    
  }
  $('#element1').slideUp()
});


// initialize leaflet map
let mymap = L.map('mapid').setView([50, 50], 3);
const token = 'pk.eyJ1IjoiY3plc2xhdzE4NyIsImEiOiJja2Z4OGUzbXAwMmVrMndzMTd6ajgzd2RjIn0.OMQ-3vAZjK9CAisL9N15Sg';
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
maxZoom: 18,
id: 'mapbox/streets-v11',
tileSize: 512,
zoomOffset: -1,
accessToken: token,
}).addTo(mymap);

//list countries in menu
$.get('php/listCountries.php', data => {
  let countries = JSON.parse(data);
  let str = ''
  for (let i = 0; i < countries.length; i++) {
      str +=`<a id="country${i}" class="dropdown-item" href="#">` + countries[i] + '</a>'
  }

  
  $('#countries').html(str)

  for (let i = 0; i < countries.length; i++) {
      $(`#country${i}`).bind('click', getCountrySpec)
  }
})

navigator.geolocation.getCurrentPosition(position => {
    let lat = position.coords.latitude;
    let lng = position.coords.longitude
    L.marker([lat, lng]).addTo(mymap);
    $.ajax({
        url: 'php/reverseGeo.php',
        type: 'POST',
        dataTpe: 'json',
        data: { lat: lat, lng: lng},
        success: response => {
            let name = JSON.parse(response)
            console.log(name)
            let country = name['address']['country']
            $.ajax({
                url: 'php/countryList.php',
                type: 'POST',
                dataTpe: 'json',
                data: {countryName: country}
            })
        }
    })
})    

const getCountrySpec = () => {

}