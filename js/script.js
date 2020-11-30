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
  let basic = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
  attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery   <a href="https://www.mapbox.com/">Mapbox</a>',
  maxZoom: 18,
  id: 'mapbox/streets-v11',
  tileSize: 512,
  zoomOffset: -1,
  accessToken: token,
  }).addTo(mymap);
  let menuCont = L.control.slideMenu().addTo(mymap);
  let newbutton = L.easyButton(`<img src="images/weather.png" style="width: 20px;"/>`, function(btn, map) {
    $('#mymodal').modal('show')
    }).addTo(mymap)

  let night = L.terminator()
  let dayLayer = {
    'map': basic,
    'satelite': satMap
  }
  let nightLayer = {
    'night': night
  }
  L.control.layers(dayLayer, nightLayer).addTo(mymap)
  
  setInterval(() => {
    night.setTime()
  }, 60000)

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

  let weatherControl = new L.control.weather({
    lang: "es",
    units: "metric"
  }).addTo(mymap); 

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
  
 let marker = {}
 let border = {}
 let layerGroup = L.layerGroup()
 let capital = {}
  const getCountrySpec = (myCountry) => {
     $(".leaflet-popup").remove(); 
      $.ajax({
          url: 'php/countriesWith-99.php',
          type: 'POST',
          dataTpe: 'json',
          data: {countryName: myCountry},
          success: result => {
              let resultDec = JSON.parse(result)
              console.log(resultDec)
              
              $('#label').html(`<span>${resultDec['name']}</span>`)
              layerGroup.clearLayers()
              if (border) {
                mymap.removeLayer(border)
              }
              border = L.geoJSON(resultDec['feature']).addTo(mymap)
              mymap.fitBounds(border.getBounds())
              if (resultDec['status'] == 200) {
               for (let i = 0; i < resultDec['cities'].length; i++) {
                     if (resultDec['cities'][i]['city'] != resultDec['capital']) {  
                     marker = new L.marker([resultDec['cities'][i]['lat'], resultDec['cities'][i]['lng']], {icon: cityIcon})
                      marker.bindPopup('loading...').bindTooltip(resultDec['cities'][i]['city'])
                      marker.on('click', function(e) {
                        let popup = e.target.getPopup()
                        let url = `https://en.wikipedia.org/w/api.php?action=query&origin=*&format=json&prop=extracts&exsentences=10&exlimit=1&titles=${resultDec['cities'][i]['city']}&explaintext=1&formatversion=2`
                        $.ajax({
                          url: url,
                          dataType: 'json',
                          success: data => {
                            popup.setContent(`<h5 id="popHeader" class="sticky-top font-weight-bold text-center">${resultDec['cities'][i]['city']}</h5><p>Population: ${resultDec['cities'][i]['population']}</p><p>${data['query']['pages'][0]['extract']}<p>`)
                            popup.update()
                          }
                        })
                      })
                      layerGroup.addLayer(marker)
                     } else {
                      capital = L.marker([resultDec['capitalLat'], resultDec['capitalLon']], {icon: capitalIcon}).addTo(mymap)
                      capital.bindPopup('loading...').bindTooltip(resultDec['capital'])
                      capital.on('click', function(e) {
                        let popup = e.target.getPopup()
                        let url = `https://en.wikipedia.org/w/api.php?action=query&origin=*&format=json&prop=extracts&exsentences=10&exlimit=1&titles=${resultDec['capital']}&explaintext=1&formatversion=2`
                        $.ajax({
                          url: url,
                          dataTpe: 'json',
                          success: capital => {
                            popup.setContent(`<h5 id="popHeader" class="sticky-top font-weight-bold text-center">${resultDec['capital']}</h5><p>Population: ${resultDec['cities'][i]['population']}</p><p>${capital['query']['pages'][0]['extract']}</p>`)
                            popup.update()
                          }
                        })
                      })
                      layerGroup.addLayer(capital)
                     }
                  }
                  layerGroup.addTo(mymap)
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
              // sliding menu content
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
              // weather modal content
              let weather = '';
              
              for (let i = 0; i < resultDec['weather']['forecast'].length; i++) {
                weather += `<div id="weatherDescription">
                            <img src="https://openweathermap.org/img/wn/${resultDec['weather']['forecast'][i]['icon']}@2x.png" /><br>
                            <span>${resultDec['weather']['forecast'][i]['description']}</span><br>
                            <span>${resultDec['weather']['forecast'][i]['hour']}</span>
                            </div>`
              }

              $('#mymodal').html(`
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="modal-header" class="modal-header bg-secondary text-white">
                      <div class="col-4">
                        <h4>${resultDec['weather']['day']}</h4>
                        <p>${resultDec['weather']['date']}</p>
                      </div>  
                      <div class="col-8">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <span><strong>Temp:</strong> ${resultDec['weather']['temp']}<sup>o</sup>C</span>
                        <span><strong>Feels like:</strong> ${resultDec['weather']['feels_like']}<sup>o</sup>C</span><br><br>
                        <span><strong>Sunrise:</strong> ${resultDec['weather']['sunrise']}</span>
                        <span><strong>Sunset:</strong> ${resultDec['weather']['sunset']}</span>
                        
                      </div>  
                    </div>
                    <div id="element" class="modal-body bg-secondary text-white w-70">`
                    
                    + weather +

                    `</div>
                  </div>
               </div>
              `)
          }
      })
  }
  
  //end of get country specs