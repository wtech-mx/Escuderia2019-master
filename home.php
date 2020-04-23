<?php
session_start();
require_once('utilidades/Utilerias.php');
unregister_GLOBALS();

?>
<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Escudería</title>

        <link href="css/jquery-ui.css" rel="stylesheet" />
        <link href="css/materialize.css" rel="stylesheet"/>
        <link href="css/font-awesome.css" rel="stylesheet"/>
        <link href="css/styles.css" rel="stylesheet"/>
        <link href="css/mediaQueries.css" rel="stylesheet"/>


		<script type="text/javascript" src="scripts/jquery-1.12.4.js"></script>
		<script type="text/javascript" src="scripts/jquery-ui.js"></script>
		<script type="text/javascript" src="scripts/jssor.slider-22.2.8.debug.js" ></script>
		<script type="text/javascript" src="scripts/dobPicker.min.js"></script>
		<script type="text/javascript" src="scripts/jquery.colorbox.js"></script>
		<script type="text/javascript" src="scripts/materialize.js"></script>

        <script type="text/javascript" src="scripts/index2.js<?php echo "?" . rand(0, 9999999); ?>"></script>
        <script type="text/javascript" src="scripts/utilidades.js<?php echo "?" . rand(0, 9999999); ?>"></script>
        <script type="text/javascript" src="scripts/request.js<?php echo "?" . rand(0, 9999999); ?>"></script>
        <script type="text/javascript" src="scripts/imgSlider.js<?php echo "?" . rand(0, 9999999); ?>"></script>
        <script type="text/javascript" src="scripts/entities.js<?php echo "?" . rand(0, 9999999); ?>"></script>
        <script type="text/javascript" src="scripts/CotizacionServicios.js<?php echo "?" . rand(0, 9999999); ?>"></script>

    </head>
    <body>
        <!-- contenedor "absoluto"" del sitio -->
        <div class="mainContainer">
            <!-- contenedor del encabezado del sitio -->
            <div class="mainHeader"></div>
            <!-- contenedor del cuerpo principal del sitio -->
            <div class="mainMinusHeader">
                <div class="mainBody"></div>
            </div>
        </div>
        <footer class="mainFooter"></footer>

    </body>
