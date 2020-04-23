<?php

session_start();

require_once("views/VistaApi.php");

require_once("views/VistaJson.php");

require_once("modelos/usuarios.php");

require_once('utilidades/ConexionBD.php');

require_once('utilidades/ExcepcionApi.php');

require_once('utilidades/Utilerias.php');

unregister_GLOBALS();

?>

<!doctype html>



<html lang="en">

	<head>

		<meta charset="utf-8">

		<title>EAGO</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<link rel="shortcut icon" type="image/png" href="images/logoHeader.png"/>
	<!--estilos y scripsts new frontend-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <!-- jQuery Custom Scroller CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <!-- Our Custom CSS -->
    <style type="text/css">

        @import "https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700";
        body {
            font-family: 'Poppins', sans-serif;
            background: #fafafa;
        }

        p {
            font-family: 'Poppins', sans-serif;
            font-size: 1.1em;
            font-weight: 300;
            line-height: 1.7em;
            color: #999;
        }

        a,
        a:hover,
        a:focus {
            color: inherit;
            text-decoration: none;
            transition: all 0.3s;
        }

        .navbar {
            padding: 15px 10px;
            background: #fff;
            border: none;
            border-radius: 0;
            margin-bottom: 40px;
            box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        .navbar-btn {
            box-shadow: none;
            outline: none !important;
            border: none;
        }

        .line {
            width: 100%;
            height: 1px;
            border-bottom: 1px dashed #ddd;
            margin: 40px 0;
        }


        #sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100vh;
            z-index: 999;
            background: #05123F;
            color: #fff;
            transition: all 0.3s;
            overflow-y: scroll;
            box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.2);
        }

        #sidebar.active {
            left: 0;
        }

        #dismiss {
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            background: #05123F;
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            -webkit-transition: all 0.3s;
            -o-transition: all 0.3s;
            transition: all 0.3s;
        }

        #dismiss:hover {
            background: #fff;
            color: #05123F;
        }

        .overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.7);
            z-index: 998;
            opacity: 0;
            transition: all 0.5s ease-in-out;
        }
        .overlay.active {
            display: block;
            opacity: 1;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #05123F;
        }

        #sidebar ul.components {
            padding: 20px 0;
            border-bottom: 1px solid #47748b;
        }

        #sidebar ul p {
            color: #fff;
            padding: 10px;
        }

        #sidebar ul li a {
            padding: 10px;
            font-size: 1.1em;
            display: block;
        }

        #sidebar ul li a:hover {
            color: #05123F;
            background: #fff;
        }

        #sidebar ul li.active>a,
        a[aria-expanded="true"] {
            color: #fff;
            background: #05123F;
        }

        a[data-toggle="collapse"] {
            position: relative;
        }

        .dropdown-toggle::after {
            display: block;
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
        }

        ul ul a {
            font-size: 0.9em !important;
            padding-left: 30px !important;
            background: #05123F;
        }

        ul.CTAs {
            padding: 20px;
        }

        ul.CTAs a {
            text-align: center;
            font-size: 0.9em !important;
            display: block;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        a.download {
            background: #fff;
            color: #05123F;
        }

        a.article,
        a.article:hover {
            background: #05123F !important;
            color: #fff !important;
        }



        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
            position: absolute;
            top: 0;
            right: 0;
        }
    </style>
    <!-- Scrollbar Custom CSS -->

    <style type="text/css">
          .input-izquerdo{
          border-radius: 15px  0 0 15px;
            }
          .input-derecho{
          border-radius: 0  15px 15px 0;
            display: flex;
            align-items: center;
            padding: .375rem .75rem;
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            text-align: center;
            white-space: nowrap;
            border: transparent;
          }

          .boton-redondeado{
          border-radius: 18px;
          border-color: transparent;
          }

          .border-circle{
          border-radius: 50px;
          border-color: transparent;
          }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#sidebar").mCustomScrollbar({
                theme: "minimal"
            });

            $('#dismiss, .overlay').on('click', function () {
                $('#sidebar').removeClass('active');
                $('.overlay').removeClass('active');
            });

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').addClass('active');
                $('.overlay').addClass('active');
                $('.collapse.in').toggleClass('in');
                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            });
        });
    </script>
	<!--estilos y scripsts new frontend-->
        <link href="css/jquery-ui.css" rel="stylesheet" />
        <link href="css/materialize.css" rel="stylesheet"/>
        <link href="css/font-awesome.css" rel="stylesheet"/>
        <link href="css/styles.css?<?php echo "?" . rand(0, 9999999); ?>" rel="stylesheet"/>
        <link href="css/mediaQueries.css?v=2" rel="stylesheet"/>
        <link href="css/jcarousel.basic.css" rel="stylesheet"/>
	</head>
	<body>
		<!-- contenedor "absoluto"" del sitio -->
		<main class="mainContainer">
			<!-- contenedor del encabezado del sitio -->
			<div class="mainHeader">
				<!-- En ésta parte esta: el titulo de la pagina y los botones principales para registrarse o entrar -->
				<div>
					<h1>ESCUDERIA</h1>
					<div class="menuitem" name="registro">
						Regístrate
					</div>

					<div class="menuitem" name="login">
						Entrar
					</div>
				</div>
				<!-- menú principal; botones de redes sociales -->
				<ul>
				</ul>
				<!-- apartado de fecha y hora -->
				<!-- <div><label>JUEVES, 19 DE ENERO DE 2017</label><label> 11:28:17</label></div>-->
			</div>
			<!-- contenedor del cuerpo principal del sitio -->
			<div class="mainMinusHeader">
				<!-- <div id="btnClose">[X]</div> -->
				<div class="mainBody"></div>
			</div>

		</main>
		<footer class="mainFooter"></footer>

		<div class="modalWindow"></div>
		<script type="text/javascript" src="scripts/jquery-1.12.4.js"></script>
		<script type="text/javascript" src="scripts/jquery-ui.js"></script>
		<script type="text/javascript" src="scripts/jssor.slider-22.2.8.debug.js" ></script>
		<script type="text/javascript" src="scripts/dobPicker.min.js"></script>
		<script type="text/javascript" src="scripts/jquery.colorbox.js"></script>
		<script type="text/javascript" src="scripts/materialize.js"></script>

		<script type="text/javascript" src="scripts/main.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/header.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/footer.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/admin.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/utilidades.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/request.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/imgSlider.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/uisearch.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/entities.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/modulos/admin/adminsubastas.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/modulos/admin/altaauto.js?<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/Venta-Autos.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/adminhome.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/MisAutos.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/modulos/admin/resultadossubastas.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/jcarousel.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/MisCotizaciones.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/Contactanos.js<?php echo "?" . rand(0, 9999999); ?>"></script>
		<script type="text/javascript" src="scripts/modulos/admin/useradmin.js<?php echo "?" . rand(0, 9999999); ?>"></script>
<?php
if(isset($_SESSION['claveapi'])){
    if(!usuarios::ValidaSesion($_SESSION['claveapi'], $_SESSION['idusuario'])){
       echo "<script> alert('Su sesión ha caducado'); sessionStorage.clear(); window.location.href =  siteurl+'home.php'; </script>";
    }
}
?>
	</body>
	<!-- contenedor del cuerpo principal del sitio -->
</html>

<?php exit();?>

