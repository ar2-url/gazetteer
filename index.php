<?php include_once("index.html"); ?>
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
    <link rel="stylesheet" href="./css/style.css" type="text/css">
    
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="./vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="./vendors/leaflet/leaflet.css" type="text/css">
    <link rel="stylesheet" href="./vendors/fontawesome-free-5.15.1-web/css/fontawesome.css">
    <link rel="stylesheet" href="./vendors/Leaflet.SlideMenu/L.Control.SlideMenu.css">
  </head>
  <body>
    
    <nav class="navbar navbar-expand-sm navbar-sticky-top bg-dark navbar-dark">
      <a class="navbar-brand" href="#">Gazetteer</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#items1">
        <span class="navbar-toggler-icon-sm"></span>
      </button>

      <div class="collapse navbar-collapse" id="items1">
        <ul class="nav navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbardropdown" data-toggle="dropdown">
              Countries
            </a>
            <div class="dropdown-menu dropdown-menu-right" id="countries"></div>
          </li>
        </ul>
      </div>
    </nav>

    <main class="container-fluid">
      
      <div id="mapid" class="container-fluid">
        <button type="button" id="modalButton" class="pull-right" data-toggle="modal" data-target="#mymodal"></button>
        <div id="mymodal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="element" class="modal-body">

                      

                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </main>
    
    <div id="preloader"></div>

    <script src="./vendors/jquery/jquery-3.5.1.js"></script>
    <script src="./vendors/bootstrap/js/bootstrap.js"></script>
    <script src="./vendors/leaflet/leaflet.js"></script>
    <script src="./vendors/Leaflet.SlideMenu/L.Control.SlideMenu.js"></script>
    <script src="./js/script.js"></script>
</body>
</html>





