<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.1.1">
    <title>Gazetteer</title>
    <link rel="icon" href="./images/globe.png">
    
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="./vendors/bootstrap/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="./vendors/leaflet/leaflet.css" type="text/css">
    <link rel="stylesheet" href="./vendors/font-awesome-4.7.0/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="./vendors/Leaflet.SlideMenu/L.Control.SlideMenu.css" type="text/css">
    <link rel="stylesheet" href="./vendors/Leaflet.EasyButton-master/src/easy-button.css" type="text/css">
    <link rel="stylesheet" href="./vendors/Leaflet.ExtraMarkers-master/Leaflet.ExtraMarkers-master/dist/css/leaflet.extra-markers.min.css">
    <link rel="stylesheet" href="./vendors/Leaflet.Weather-master/Leaflet.Weather.css" type="text/css">
    <link rel="stylesheet" href="./css/style.css" type="text/css">
  </head>
  <body>
    
    <nav class="navbar navbar-expand-sm navbar-sticky-top bg-dark navbar-dark">
      <a class="navbar-brand" href="#">Gazetteer</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#items1">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="items1">
        <ul class="nav navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a id="label" class="nav-link dropdown-toggle" href="#" id="navbardropdown" data-toggle="dropdown" style="display: inline-block;">
              Countries
            </a>
            <div class="dropdown-menu dropdown-menu-right" id="countries"></div>
          </li>
        </ul>
      </div>
    </nav>

    <main class="container-fluid">
      
      <div id="mapid" class="container-fluid">
        <button type="button" id="modalButton" class="btn" data-toggle="modal" data-target="#mymodal"></button>
        <div id="mymodal" class="modal fade" role="dialog">
            
        </div>
      </div>
    </main>
    
    <div id="preloader"></div>

    <script src="./vendors/jquery/jquery-3.5.1.js"></script>
    <script src="./vendors/bootstrap/js/bootstrap.js"></script>
    <script src="./vendors/leaflet/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet"></script>
    <script src="https://unpkg.com/@joergdietrich/leaflet.terminator"></script>
    <script src="./vendors/Leaflet.SlideMenu/L.Control.SlideMenu.js"></script>
    <script src="./vendors/Leaflet.EasyButton-master/src/easy-button.js"></script>
    <script src="./vendors/Leaflet.ExtraMarkers-master/Leaflet.ExtraMarkers-master/dist/js/leaflet.extra-markers.js"></script>
    <script src="./vendors/Leaflet.Weather-master/Leaflet.Weather.js"></script>
    <script src="./js/script.js"></script>
</body>
</html>



