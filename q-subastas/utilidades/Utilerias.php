<?php


define("TIMEDIFF", 2);
define("DOMINIO","http://eago.com.mx/q-subastas/");
define("MAILFROM","contacto@eago.com.mx");


function setNowForSQL(){

	return "DATE_ADD(NOW(), INTERVAL ".TIMEDIFF." HOUR)";
}

function unregister_GLOBALS()
{
    if (!ini_get('register_globals')) {
        return;
    }

    // Might want to change this perhaps to a nicer error
    if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
        die('GLOBALS overwrite attempt detected');
    }

    // Variables that shouldn't be unset
    $noUnset = array('GLOBALS',  '_GET',
                     '_POST',    '_COOKIE',
                     '_REQUEST', '_SERVER',
                     '_ENV',     '_FILES');

    $input = array_merge($_GET,    $_POST,
                         $_COOKIE, $_SERVER,
                         $_ENV,    $_FILES,
                         isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());

    foreach ($input as $k => $v) {
        if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) {
            unset($GLOBALS[$k]);
        }
    }
}


function envia_mail($to, $titulo, $mensaje){
	try{
		// multiple recipients
		//$to = 'miguel.susano@gmail.com';

		// subject

		$headers = array(
		  'From: "Eago" <'.constant("MAILFROM").'>' ,
		  'Reply-To: "No responder" <'.constant("MAILFROM").'>' ,
		  'X-Mailer: PHP/' . phpversion() ,
		  'MIME-Version: 1.0' ,
		  'Content-type: text/html; charset=iso-8859-1'
		);
		$headers = implode( "\r\n" , $headers );

		$mensaje = str_replace("##DOMINIO##", constant("DOMINIO"), $mensaje);

		// Mail it
		return mail($to, $titulo, $mensaje, $headers);
	}catch(Exception $e){
 		return 0;
 	}
}


function envia_mensaje_invitacion($claveApi, $idusuario, $idsubasta){


 		// $mensaje = "<html><meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />	<body><h2>Bienvenido a escudería</h2><p>Usted fue invitado a participar en una subasta por favor concluya el registro para poder participar: <a href=\"msusano.com/Subastas/home.php?s=invitacion&claveapi=". $claveApi."&idusuario=".$idusuario."\">Confirmar mail</a><br /><p>Si no puede dar click en el encace puede copiar el código  ". $claveApi." en la pantalla de verificación </p></body></html>";
	 	// return $mensaje;

	 	$myfile = fopen("utilidades/subastainvitacion.txt", "r") or die("Unable to open file!");
	 	$contenido = "";
		while(!feof($myfile)) {
	  		$contenido .= fgets($myfile);

		}
		$contenido = str_replace("##claveApi##", $claveApi, $contenido);
		$contenido = str_replace("##idusuario##", $idusuario, $contenido);
		$contenido = str_replace("##idsubasta##", $idsubasta, $contenido);
		fclose($myfile);
		return $contenido;


 }

 function envia_mensaje_recuperarcontrasena($claveApi, $correo){


 	$myfile = fopen("utilidades/recuperarcontrasena.txt", "r") or die("Unable to open file!");
 	$contenido = "";
	while(!feof($myfile)) {
  		$contenido .= fgets($myfile);

	}
	$contenido = str_replace("##claveApi##", $claveApi, $contenido);
	$contenido = str_replace("##idusuario##", $idusuario, $contenido);
	$contenido = str_replace("##correo##", $correo, $contenido);
	fclose($myfile);
	return $contenido;
 }

function mensaje_correoregistro($claveApi, $idusuario, $correo){

	$myfile = fopen("utilidades/correoregistro.txt", "r") or die("Unable to open file!");

	$contenido = "";
	while(!feof($myfile)) {
  		$contenido .= fgets($myfile);

	}
	//echo $contenido;


	$contenido = str_replace("##claveApi##", $claveApi, $contenido);
	$contenido = str_replace("##idusuario##", $idusuario, $contenido);
	$contenido = str_replace("##correo##", $correo, $contenido);
	fclose($myfile);
	return $contenido;
 }

 function mensaje_cancela_subasta($motivo, $subastanombre, $nombreusuario){
 	$myfile = fopen("utilidades/subastacancelada.txt", "r") or die("Unable to open file!");

	$contenido = "";
	while(!feof($myfile)) {
  		$contenido .= fgets($myfile);

	}

	$contenido = str_replace("##nombre_subasta##", $subastanombre, $contenido);
	$contenido = str_replace("##motivo##", $motivo, $contenido);
	$contenido = str_replace("##nombre_usuario##", $nombreusuario, $contenido);


	fclose($myfile);
	return $contenido;
 }

?>
