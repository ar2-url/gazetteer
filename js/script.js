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

  let cityIcon = new L.ExtraMarkers.icon({
    icon: 'fa-building-o',
    markerColor: 'green',
    shape: 'square',
    prefix: 'fa'
  })
  
  let capitalIcon = new L.ExtraMarkers.icon({
    icon: 'fa-star',
    markerColor: 'yellow',
    shape: 'square',
    prefix: 'fa'
  })

  //list countries in menu
  $.get('php/listCountries.php', data => {
    let countries = JSON.parse(data);
    let str = ''
    for (let i = 0; i < countries.length; i++) {
        str +=`<a id="${countries[i]['name']}" class="dropdown-item" href="#">` + countries[i]['name'] + `<span class="float-right">${countries[i]['code']}</span</a>`
    }
  
    $('#countries').html(str)
  
    for (let i = 0; i < countries.length; i++) {
      $(`#${countries[i]['name']}`).bind('click', renderInfo)
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
              
              console.log(name)
              getCountrySpec(name)
          }
      })
  })    
  
  // get country name for specs
  
  const renderInfo = event => {
    getCountrySpec(event.target.id)    
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
              let newbutton = new L.easyButton('<img src="images/weather.png" style="width: 20px;"/>', function() {
              $('#mymodal').modal('show')
                }).addTo(mymap)
              $('#label').html(`<span>${resultDec['name']}</span>`)
              let border = L.geoJSON(resultDec['feature']).addTo(mymap)
              mymap.fitBounds(border.getBounds())
              let capital = L.marker([resultDec['capitalLat'], resultDec['capitalLon']], {icon: capitalIcon}).addTo(mymap)
              capital.bindPopup(`<h5>${resultDec['capital']}</h5>`).bindTooltip(resultDec['capital'])
              if (resultDec['status'] == 200) {
                  for (let i = 0; i < resultDec['cities'].length; i++) {
                     if (resultDec['cities'][i]['city'] != resultDec['capital']) {
                      let marker = L.marker([resultDec['cities'][i]['lat'], resultDec['cities'][i]['lng']], {icon: cityIcon}).addTo(mymap)
                      marker.bindPopup(`<h5>${resultDec['cities'][i]['city']}</h5>`).bindTooltip(resultDec['cities'][i]['city'])
                     }
                  }
              }
              let photos = ''
              if (resultDec['photos'] != 'No data') {
                  for (let i = 1; i < resultDec['photos'].length; i++) {
                      photos += `
                          <div class="carousel-item">
                          <img class="d-block w-100" src="${resultDec['photos'][i]}" />
                          </div>`
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
              <img class="d-block w-100" src="${resultDec['photos'][0]}" />
              </div>`
                  + photos +
              `</div>
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

              let weather = `
              <div class="carousel-item active h-50 w-70">
                <p>${resultDec['weather'][0]['date']}</p>
                <img src="http://openweathermap.org/img/wn/${resultDec['weather'][0]['icon']}@2x.png" />
                <p>${resultDec['weather'][0]['description']}</p>
                <p>Temp: ${resultDec['weather'][0]['temp']}<sup>o</sup>C</p>
                <p>Feels like: ${resultDec['weather'][0]['feels']}<sup>o</sup>C</p>
                <p>Pressure: ${resultDec['weather'][0]['pressure']} hPa</p>
                <p>Rain: ${resultDec['weather'][0]['rain']} mm</p>
              </div>`
              
              for (let i = 1; i < resultDec['weather'].length - 1; i++) {
                weather += `<div class="carousel-item h-50 w-70">
                                <p>${resultDec['weather'][i]['date']}</p>
                                <img src="http://openweathermap.org/img/wn/${resultDec['weather'][i]['icon']}@2x.png" />
                                <p>${resultDec['weather'][i]['description']}</p>
                                <p>Temp: ${resultDec['weather'][i]['temp']}<sup>o</sup>C</p>
                                <p>Feels like: ${resultDec['weather'][i]['feels']}<sup>o</sup>C</p>
                                <p>Pressure: ${resultDec['weather'][i]['pressure']} hPa</p>
                                <p>Rain: ${resultDec['weather'][i]['rain']} mm</p>
                            </div>`
              }

              let control = `<li data-target="#weatherCar" data-slide-to="0" class="active" style="margin: 0; padding: 0;"></li>`
              for (let i = 1; i < resultDec['weather'].length - 1; i++) {
                control += `<li data-target="#weatherCar" data-slide-to="${i}" style="margin: 0; padding: 0;"></li>`
              }
//*************************************** */
              $('#element').html(`
              <div id="weatherCar" class="carousel slide bg-dark text-light" data-ride="carousel">

              <ol class="carousel-indicators">`
                 + control +
              `</ol>
              
              <div class="carousel-inner text-center">`
                 + weather +
              `</div>
              <a class="carousel-control-prev" href="#weatherCar" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#weatherCar" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
              </div>
              `)
          }
      })
  }
  
  //end of get country specs