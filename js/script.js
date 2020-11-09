$(window).on('load', function () {    
  if ($('#preloader').length) {      
      $('#preloader').delay(100).fadeOut('slow', function () {        
          $(this).remove();      
      });    
  }
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
let menuCont = L.control.slideMenu().addTo(mymap);

//list countries in menu
$.get('php/listCountries.php', data => {
  let countries = JSON.parse(data);
  let str = ''
  for (let i = 0; i < countries.length; i++) {
      str +=`<a id="country${i}" class="dropdown-item" href="#">` + countries[i] + '</a>'
  }

  $('#countries').html(str)

  for (let i = 0; i < countries.length; i++) {
    $(`#country${i}`).bind('click', renderInfo)
  }
  
})

navigator.geolocation.getCurrentPosition(position => {
    let lat = position.coords.latitude;
    let lng = position.coords.longitude
    $.ajax({
        url: 'php/reverseGeo.php',
        type: 'POST',
        dataTpe: 'json',
        data: { lat: lat, lng: lng},
        success: response => {
            let name = JSON.parse(response)
            let country = name
            getCountrySpec(country)
        }
    })
})    

// get country name for specs

const renderInfo = e => {
    getCountrySpec(e.target.innerHTML)
}

//get country specs

const getCountrySpec = (myCountry) => {
    $('.leaflet-interactive').remove();
    $.ajax({
        url: 'php/countriesWith-99.php',
        type: 'POST',
        dataTpe: 'json',
        data: {countryName: myCountry},
        success: result => {
            let resultDec = JSON.parse(result)
            console.log(resultDec)
            let border = L.geoJSON(resultDec['feature']).addTo(mymap)
            mymap.fitBounds(border.getBounds())
            if (resultDec['status'] == 200) {
                for (let i = 0; i < resultDec['cities'].length; i++) {
                   let marker = L.marker([resultDec['cities'][i]['lat'], resultDec['cities'][i]['lng']]).addTo(mymap)
                }
            }
          
            //************************************
            menuCont.setContents(`
            <div class="card text-center w-90 mr-5">
                <img class="card-img-top" src="${resultDec['flag']}" alt="Card image">
                <div class="card-body">
                    <h4 class="card-title">${myCountry}</h4>
                    <p class="card-text">Capital: ${resultDec['capital']}</p>
                    <p class="card-text">Population: ${resultDec['population']}</p>
                    <p class="card-text">Currency: ${resultDec['curr_Name']}/${resultDec['curr_Code']}/${resultDec['curr_Symbol']}</p>
                    <p class="card-text">Exchange rate: ${resultDec['exRate']}/USD</p>   
                </div>
            </div><br>
            <div class="card text-center w-90 mr-2">
               <div class="card-body">
                    <h4 class="card-title">Wikipedia</h4>
                    <p class="card-text">${resultDec['wiki']}</p><br>
               </div>
            </div>
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                <img class="d-block w-100" src="${resultDec['photos'][0]}" alt="First slide">
                </div>
                <div class="carousel-item">
                <img class="d-block w-100" src="${resultDec['photos'][1]}" alt="Second slide">
                </div>
                <div class="carousel-item">
                <img class="d-block w-100" src="${resultDec['photos'][2]}" alt="Third slide">
                </div>
                <div class="carousel-item">
                <img class="d-block w-100" src="${resultDec['photos'][3]}" alt="Third slide">
                </div>
                <div class="carousel-item">
                <img class="d-block w-100" src="${resultDec['photos'][4]}" alt="Third slide">
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            </div>
            `) 
            if (resultDec['status'] == 200) {
                $('#element').html(`
                <img src="http://openweathermap.org/img/wn/${resultDec['weather']['icon']}@2x.png" /><br>
                <h6>${resultDec['weather']['description']}</h6><br>
                <h6>${resultDec['weather']['temp']}<sup>o</sup>C</h6>
                <h6>Feels like: ${resultDec['weather']['feels']}<sup>o</sup>C</h6>
                <h6>Sunrise: ${resultDec['weather']['sunrise']}</h6>
                <h6>Sunset: ${resultDec['weather']['sunset']}</h6>
            `)
            } else {
                $('#element').html('No data')
                menuCont.setContents('No data')
            }
        }
    })
}

//end of get country specs