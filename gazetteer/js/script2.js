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

// get current position

navigator.geolocation.getCurrentPosition(position => {
    let lat = position.coords.latitude;
    let lng = position.coords.longitude
    
    L.marker([lat, lng]).addTo(mymap);
// get current country
    $.post('php/reverseGeo.php', {
        lat: lat,
        lng: lng
    }, response => {
        let name = JSON.parse(response)
        let country = name['address']['country']
        $.post('php/countryList.php', {
            countryName: country
        }, result => {
            let decoded = JSON.parse(result)
            let border = L.geoJSON(decoded).addTo(mymap)
            mymap.fitBounds(border.getBounds())
            $('#element1').slideDown()
            //get population, capital, currency
            $.post('php/getStuff1.php', {
                countryName: country
            }, popAndCap => {
                let popAndCapPar = JSON.parse(popAndCap)
                if (!popAndCapPar) {
                    $('#element').append('No data')
                } else {
                    $('#element').html(
                        `<h5>${popAndCapPar['name']}</h5><br>
                         <p>Capital: ${popAndCapPar['capital']}</p><br>
                         <p>Population: ${popAndCapPar['population']}</p><br>
                         <p>Currency: ${popAndCapPar['curr_Code']}/ ${popAndCapPar['curr_Name']}/ ${popAndCapPar['curr_Symbol']}</p><br>
                         <img src="${popAndCapPar['flag']}" alt="CountryFlag" class="img-fluid" opacity="1"/><br>`
                    )
                }
                // get rate against USD
                let code = popAndCapPar['curr_Code']
                $.post('php/getRate.php', {
                    code: code
                }, rate => {
                    if (!rate) {
                        $('#element').append('No data')
                    } else {
                        let rateDeco = JSON.parse(rate)
                        $('#element').append(`<br><p>Exchange rate: ${code}/USD ${rateDeco}</p>`)
                        // get wiki link
                        $.post('php/getWiki.php', {
                            country: popAndCapPar['name']
                        }, links => {
                            let linkDec = JSON.parse(links)
                            if (!linkDec) {
                                $('#element').append('No data')
                            } else {
                                $('#element').append(`<br><a href="${linkDec}" style="color: whitesmoke; text-decoration: none;">Wikipedia</a>`)
                                // get weather
                                $.post('php/getWeather.php', {
                                    capital: popAndCapPar['capital']
                                }, weather => {
                                    let weatherDec = JSON.parse(weather)
                                    console.log(weatherDec)
                                    if (!weatherDec) {
                                        $('#weather').html('No data')
                                    } else {
                                        $('#weather').html(
                                            `<h5>Today</h5><br>
                                             <img src="http://openweathermap.org/img/wn/${weatherDec['icon']}@2x.png" class="img-fluid" /><br>
                                             <p>${weatherDec['description']}</p><br>
                                             <p>Temperature: ${weatherDec['temperature']}<sup>o</sup>C</p><br>
                                             <hr>
                                             <h5>Tomorrow</h5><br>
                                             <img src="http://openweathermap.org/img/wn/${weatherDec['tomorrow']['icon']}@2x.png" class="img-fluid" /><br>
                                             <p>${weatherDec['tomorrow']['description']}</p>
                                             <p>Temperature: ${weatherDec['tomorrow']['temperature']}<sup>o</sup>C</p><br>`
                                        )
                                    }
                                })
                            }
                        })
                    }

                })
            })
        })
    })
})

// renders chosen country border on click
const getCountrySpec = e => {
    $('.leaflet-interactive').remove();
    $('#element1').slideUp()
    $.post('php/countryList.php', {
        countryName: e.target.innerHTML
    }, result => {
        let feature = JSON.parse(result)
        let addedGeo = L.geoJSON(feature).addTo(mymap)
        mymap.fitBounds(addedGeo.getBounds())
            //get population, capital, currenccy
            $('#element1').slideDown()
            $.post('php/getStuff1.php', {
                countryName: e.target.innerHTML
            }, popAndCap => {
                let popAndCapPar = JSON.parse(popAndCap)
                if (!popAndCapPar && popAndCapPar['status'] != 'ok') {
                    $('#element').html('No data')
                } else {
                    $('#element').html(
                        `<h5>${popAndCapPar['name']}</h5><br>
                         <p>Capital: ${popAndCapPar['capital']}</p><br>
                         <p>Population: ${popAndCapPar['population']}</p><br>
                         <p>Currency: ${popAndCapPar['curr_Code']}/ ${popAndCapPar['curr_Name']}/ ${popAndCapPar['curr_Symbol']}</p><br>
                         <img src="${popAndCapPar['flag']}" alt="CountryFlag" class="img-fluid" opacity="1"/><br>`
                    )
                }
                //get rate against dollar
                let code = popAndCapPar['curr_Code']
                $.post('php/getRate.php', {
                    code: code
                }, rate => {
                    if (!rate) {
                        $('#element').append('No data')
                    } else {
                        // get wiki links
                        let rateDeco = JSON.parse(rate)
                        $('#element').append(`<br><p>Exchange rate: ${code}/USD ${rateDeco}</p>`)
                        $.post('php/getWiki.php', {
                            country: popAndCapPar['name']
                        }, links => {
                            let linkDec = JSON.parse(links)
                            if (!linkDec) {
                                $('#element').append('No data')
                            } else {
                                $('#element').append(`<br><a href="${linkDec}" style="color: whitesmoke; text-decoration: none;">Wikipedia</a>`)
                                // get weather
                                $.post('php/getWeather.php', {
                                    capital: popAndCapPar['capital']
                                }, weather => {
                                    let weatherDec = JSON.parse(weather)
                                    if (!weatherDec) {
                                        
                                    } else {
                                        $('#weather').html(
                                            `<h5>Today</h5><br>
                                             <img src="http://openweathermap.org/img/wn/${weatherDec['icon']}@2x.png" class="img-fluid" /><br>
                                             <p>${weatherDec['description']}</p><br>
                                             <p>Temperature: ${weatherDec['temperature']}<sup>o</sup>C</p><br>
                                             <hr style="color: white;">
                                             <h5>Tomorrow</h5><br>
                                             <img src="http://openweathermap.org/img/wn/${weatherDec['tomorrow']['icon']}@2x.png" class="img-fluid"/><br>
                                             <p>${weatherDec['tomorrow']['description']}</p>
                                             <p>Temperature: ${weatherDec['tomorrow']['temperature']}<sup>o</sup>C</p><br>`
                                        )
                                    }
                                })
                            }
                        })
                    }
                })
            })
        })
    
    
}