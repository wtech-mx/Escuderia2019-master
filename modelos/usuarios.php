<?php

class usuarios
{

    public function __construct($nombre ="", $appaterno = "", $apmaterno = "", $correo ="", $verificado = 0, $contrasena="", $valido=0, $publico = 1, $esadmin=0, $claveApi = "", $telefono = 0)
    {
        $this->nombre = $nombre;
        $this->appaterno = $appaterno;
        $this->apmaterno = $apmaterno;
        $this->correo = $correo;
        $this->verificado = $verificado;
        $this->contrasena = $contrasena;
        $this->valido = $valido;
        $this->publico = $publico;
        $this->esadmin= $esadmin;
        $this->claveApi = $claveApi;
        $this->idUsuario = 0;
        $this->telefono = $telefono;

    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "usuario";
    const APPATERNO = "appaterno";
    const APMATERNO = "apmaterno";
    const FECHA_NAC = "fecha_nacimiento";
    const ID_USUARIO = "idUsuario";
    const NOMBRE = "nombre";
    const CONTRASENA = "contrasena";
    const CORREO = "correo";
    const TELEFONO = "telefono";
    const VERIFICADO = "verificado";
    const CLAVE_API = "claveApi";
    const MAIL_BIENVENIDO = "Bienvenido a Escuderï¿½a";
    const ESTADO_CREACION_EXITOSA = 200;
    const ESTADO_CREACION_FALLIDA = 400;
    const ESTADO_URL_INCORRECTA = "Error en el mÃ©todo";
    const PASSWORD_DEFAULT = "1234567890";



    public static function post($peticion)
    {

        if ($peticion[0] == 'registro') {
            return self::crear($_POST);
        } else if ($peticion[0] == 'login') {
            return self::login();
        }else if ($peticion[0] == 'rememberme'){
            return self::rememberme();
        }else if($peticion[0] == 'reenviarcorreo'){
            return self::reenviarcorreo();
        }else if($peticion[0] == 'validarcorreo'){
            return self::validarcorreo();
        }else if($peticion[0] == 'logout'){
            return self::logout();
        }else if($peticion[0] == 'correoexiste'){
            return self::correoexiste();
        }else if($peticion[0] == 'recuperar') {
            return self::recuperar();
        }else if($peticion[0] == 'validacodigoverificacion'){
            return self::validacodigoverificacion();
        }else if($peticion[0] == 'cambiarcontasena'){
            return self::cambiarcontasena();
        }else if ($peticion[0] == 'info'){
                return self::info();
        }else if($peticion[0] == 'preregistro'){
            return self::preregistro($_POST);
        }else if($peticion[0] == 'listar'){

            return self::listarUsuarios($_POST);
      }else{
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }

    public static function ValidaSesion($apikey, $idusuario)
    {

       try{
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $comando = "select count(*) as cuenta from usuario where idUsuario = ? and claveApi = ?";
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $idusuario);
            $sentencia->bindParam(2, $apikey);
            if($sentencia->execute()){
                $resultado = $sentencia->fetch();
                if($resultado["cuenta"] > 0)
                    return true;
                else
                    return false;

            }else{
                return false;
            }
       }catch (Exception $e) {

            return false;

        }


    }


    private function crear($usuario)
    {

        $contrasenaEncriptada = self::encriptarContrasena( trim($usuario["password"]));



        $claveApi = self::generarClaveApi();

        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE . "," .
                self::APPATERNO . "," .
                self::APMATERNO . "," .
                self::CONTRASENA . "," .
                self::FECHA_NAC . "," .
                self::CLAVE_API . "," .
                self::CORREO . "," .
                self::TELEFONO. "," .
                self::VERIFICADO . ")" .
                " VALUES(?,?,?,?,?,?,?,?,?)";


            $sentencia = $pdo->prepare($comando);


            $sentencia->bindParam(1, $usuario["nombre"]);

            $sentencia->bindParam(2, $usuario["appaterno"]);

            $sentencia->bindParam(3, $usuario["apmaterno"]);

            $sentencia->bindParam(4, $contrasenaEncriptada);

            $fecha = $usuario["yyyy"]."-".$usuario["mm"]."-".$usuario["dd"];

            $sentencia->bindParam(5, $fecha);

            $sentencia->bindParam(6, $claveApi);

            $sentencia->bindParam(7, trim($usuario["email"]));

            $sentencia->bindParam(8, $usuario["telefono"]);

            $verificado = 0;
            $sentencia->bindParam(9, $verificado);



            $resultado = $sentencia->execute();
            $usuarioid = $pdo->lastInsertId();

            if ($resultado) {
				envia_mail($usuario["email"], self::MAIL_BIENVENIDO, mensaje_correoregistro($claveApi, $usuarioid  ,$usuario["email"] ));

                return $usuarioid;
            } else {
                return 0;
            }
        } catch (Exception $e) {

            return ExcepcionApi("Error al procesar la solicitud", $e->getMessage(), 500);

        }

    }

    public static function invitarUsuario($usuario, $idSubasta){



        $claveApi = self::generarClaveApi();

        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();


            $comando = "SELECT ".self::ID_USUARIO.",".self::CORREO." from ". self::NOMBRE_TABLA . " where ".self::CORREO . " = ? limit 1";

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $usuario->correo);

            $cuenta = 0;
            $resultado = false;
            $usuarioid = 0;

            if ($sentencia->execute()){
                $resultado = $sentencia->fetchall(PDO::FETCH_ASSOC);
                $cuenta = count($resultado);
                if($cuenta > 0 ){

                    $usuarioid = $resultado[0][self::ID_USUARIO];

                    $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                    $comando = "delete from subasta_usuario where idSubasta = ".$idSubasta. " and idUsuario = ".$usuarioid ;

                    $sentencia = $pdo->prepare($comando);

                    $sentencia->execute();

                }
            }
            else{
                $cuenta = 0;
            }




            if($cuenta < 1){




                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::NOMBRE . "," .
                    self::APPATERNO . "," .
                    self::APMATERNO . "," .
                    self::CORREO . "," .
                    self::VERIFICADO . "," .
                    self::TELEFONO . "," .
                    self::CLAVE_API .")" .
                    " VALUES(?,?,?,?,?,?,?)";


                $sentencia = $pdo->prepare($comando);

                $usuario->verificado = 0;


                $sentencia->bindParam(1, $usuario->nombre);
                $sentencia->bindParam(2, $usuario->appaterno);
                $sentencia->bindParam(3, $usuario->apmaterno);
                $sentencia->bindParam(4, $usuario->correo);
                $sentencia->bindParam(5, $usuario->verificado);
                $sentencia->bindParam(6, $usuario->telefono);
                $sentencia->bindParam(7, $claveApi);

                $resultado = $sentencia->execute();
                $usuarioid = $pdo->lastInsertId();


            }else{

				$comando = "update " . self::NOMBRE_TABLA . " set claveApi = ? where correo = ?";
                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $claveApi);
				$sentencia->bindParam(2, $usuario->correo);
				$resultado = $sentencia->execute();
			}


            if ($usuarioid > 0) {

                $comando = " insert into subasta_usuario ( idSubasta, idUsuario) values (?,?)";


                $sentencia = $pdo->prepare($comando);

                $sentencia->bindParam(1, $idSubasta);
                $sentencia->bindParam(2, $usuarioid);
                try {
                  if($sentencia->execute() && $cuenta==0){
                      $invitacion = new invitacion($usuarioid, $idSubasta);
                      invitacion::crear($invitacion);
                      envia_mail($usuario->correo, "Escudería - Ha sido invitado a particiar en una subasta", envia_mensaje_invitacion($claveApi, $usuarioid, $idSubasta));
                      return true;
                  }else{
                      return false;
                  }
                } catch (Exception $e) {
                  if ($e->errorInfo[1] == 1062) { // duplicate key
                     return true;
                  } else {
                     throw $e;
                  }
                }


            } else {
                return false;
            }
        } catch (PDOException $e) {


            throw new ExcepcionApi("ERROR", $e->getMessage(), 400);
            return false;
        }

    }
    private function encriptarContrasena($contrasenaPlana)
    {
        if ($contrasenaPlana)
            return password_hash($contrasenaPlana, PASSWORD_DEFAULT);
        else return null;
    }

    private static function generarClaveApi()
    {
        return md5(microtime().rand());
    }
    // private function registrar()
    // {
    //     $cuerpo = file_get_contents('php://input');
    //     $usuario = json_decode($cuerpo);

    //     $resultado = self::crear($_POST);

    //     switch ($resultado) {
    //         case self::ESTADO_CREACION_EXITOSA:
    //            http_response_code(200);
    //            return "OK";

    //             break;
    //         case self::ESTADO_CREACION_FALLIDA:
    //             throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
    //             break;
    //         default:
    //             throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Falla desconocida", 400);
    //     }
    // }

    public function validarcorreo(){

        $correo = $_POST["correo"];
        $idusuario = $_POST["idusuario"];
        $apikey = $_POST["apikey"];

        $comando = "SELECT nombre, appaterno, apmaterno, correo, verificado, publico, es_admin, claveApi, idUsuario from usuario where correo =? and idUsuario = ? and claveApi = ?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $correo);
        $sentencia->bindParam(2, $idusuario);
        $sentencia->bindParam(3, $apikey);
        if ($sentencia->execute())
        {
            $fetch = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            if(sizeof($fetch) > 0 ){

                $usuario = new usuarios();
                $usuario->idUsuario = $fetch[0]["idUsuario"];
                $usuario->nombre = $fetch[0]["nombre"];
                $usuario->appaterno = $fetch[0]["appaterno"];
                $usuario->apmaterno = $fetch[0]["apmaterno"];
                $usuario->correo = $fetch[0]["correo"];
                $usuario->verificado = $fetch[0]["verificado"];
                $usuario->valido = 1;
                $usuario->publico = $fetch[0]["publico"];
                $usuario->esadmin = $fetch[0]["es_admin"];
                $usuario->claveApi = $fetch[0]["claveApi"];

                $comando = "update usuario set verificado = 1, vigencia = ".setNowForSQL()." where correo =? and idUsuario = ?  ";
                $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                $sentencia->bindParam(1, $correo);
                $sentencia->bindParam(2, $idusuario);
                if ($sentencia->execute())
                {
                    $_SESSION['claveapi']  = $apikey;
                    $_SESSION['idusuario']  = $idusuario;
                    $_SESSION['correo']  = $correo;
                    return $usuario;
                }else{

                    $usuario = new usuarios();
                    $usuario->idUsuario = 0;
                    return $usuario;
                }


            }else{
                throw new ExcepcionApi(0, "Los datos no son correctos", 500);
            }
        }else{
            throw new ExcepcionApi(0, "Error al validar el correo", 500);
        }

    }
    public function reenviarcorreo(){


        $correo = $_POST["correo"];
        $idusuario = $_POST["idusuario"];
        $comando = "SELECT nombre, appaterno, apmaterno, correo, verificado, contrasena, publico, es_admin from usuario where correo =? and idUsuario = ? and verificado = 0";
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $correo);
        $sentencia->bindParam(2, $idusuario);

        if ($sentencia->execute())
        {
            $fetch = $sentencia->fetch(PDO::FETCH_ASSOC);
            if($fetch["correo"] == $correo){
                $claveApi = self::generarClaveApi();
                $comando = "update ".self::NOMBRE_TABLA." set claveApi = '".$claveApi."', vigencia = DATE_ADD(".setNowForSQL().", INTERVAL 8 HOUR) where correo = '".  $correo."'";
                $sentencia = $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                if($sentencia->execute()){

                     envia_mail($correo, self::MAIL_BIENVENIDO, mensaje_correoregistro($claveApi, $idusuario, $correo), "yo@msusano.com" );
                     return 1;
                }
            }else{
                return 0;
            }

        }else{
            return -1;
        }

    }

    public function rememberme(){
        $claveapi = $_POST["claveapi"];
        $comando = "SELECT idUsuario, nombre, appaterno, apmaterno, correo, verificado, contrasena, publico, es_admin, claveApi from usuario where ".self::CLAVE_API." = '".$claveapi."' and ".setNowForSQL()." < vigencia+1 ";
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $claveapi);

        if ($sentencia->execute())
        {
            $fetch = $sentencia->fetch(PDO::FETCH_ASSOC);
            $usuario = new usuarios();
            if($fetch["contrasena"] != null){

                $usuario->idUsuario = $fetch["idUsuario"];
                $usuario->nombre = $fetch["nombre"];
                $usuario->appaterno = $fetch["appaterno"];
                $usuario->apmaterno = $fetch["apmaterno"];
                $usuario->correo = $fetch["correo"];
                $usuario->verificado = $fetch["verificado"];
                $usuario->contrasena = $fetch["contrasena"];
                $usuario->valido = 1;
                $usuario->publico = $fetch["publico"];
                $usuario->esadmin = $fetch["es_admin"];
                $usuario->claveApi = $fetch["claveApi"];
            }

            return $usuario;
        }
        else
            return "error";

    }

    private function login(){

        $mail = $_POST["email"];
        $password = $_POST["password"];

            // Sentencia INSERT
        $comando = "SELECT nombre, appaterno, apmaterno, correo, verificado, contrasena, publico, es_admin, idUsuario from usuario where correo =? ";


         $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
         $sentencia = $pdo->prepare($comando);
        $sentencia->bindParam(1, $mail);
        if ($sentencia->execute())
        {
            $fetch = $sentencia->fetch(PDO::FETCH_ASSOC);

            $valido = password_verify($password, $fetch["contrasena"]);

            if($valido == 1 &&  $fetch["verificado"] == 1){
                $claveApi = self::generarClaveApi();
                $comando = "update ".self::NOMBRE_TABLA." set claveApi = '".$claveApi."', vigencia = DATE_ADD(".setNowForSQL().", INTERVAL 8 HOUR) where correo = '".  $mail."'";
                $sentencia = $pdo->prepare($comando);
                $sentencia->execute();

            }else{
                $claveApi = "";
            }
            $usuario = new usuarios();
            $usuario->nombre = $fetch["nombre"];
            $usuario->appaterno = $fetch["appaterno"];
            $usuario->apmaterno = $fetch["apmaterno"];
            $usuario->correo = $fetch["correo"];
            $usuario->verificado = $fetch["verificado"];
            $usuario->contrasena = $fetch["contrasena"];
            $usuario->valido = $valido;
            $usuario->publico = $fetch["publico"];
            $usuario->esadmin = $fetch["es_admin"];
            $usuario->idUsuario = $fetch["idUsuario"];
            $usuario->claveApi =  $claveApi;

            if($valido == 1){
                $_SESSION['claveapi']  = $claveApi;
                $_SESSION['idusuario']  = $fetch["idUsuario"];
                $_SESSION['correo']  = $fetch["correo"];
                $_SESSION['es_admin']  = $fetch["es_admin"];
            }


            return $usuario;
        }
        else
            return "error";





    }

    private function logout(){
        $comando = "update ".self::NOMBRE_TABLA." set claveApi = '' where claveApi = ?";
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $sentencia = $pdo->prepare($comando);
        $sentencia->bindParam(1, $_POST["claveapi"]);
        if ($sentencia->execute())
        {
            return 1;
        }
        else{
            return 0;
        }


    }

    private function obtenerUsuarioPorCorreo($correo)
    {
        $comando = "SELECT " .
            self::NOMBRE . "," .
            self::CONTRASENA . "," .
            self::CORREO . "," .
            self::CLAVE_API .
            " FROM " . self::NOMBRE_TABLA .
            " WHERE " . self::CORREO . "=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $sentencia->bindParam(1, $correo);

        if ($sentencia->execute())
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        else
            return null;
    }
    private function correoexiste(){
            $comando = "SELECT count(*) as VALIDA from " . self::NOMBRE_TABLA ." WHERE " . self::CORREO . "= ? ";
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
            $sentencia->bindParam(1, $_POST["correo"]);
             if ($sentencia->execute())
                return $sentencia->fetch(PDO::FETCH_ASSOC)["VALIDA"];
            else
                return -1;

    }

    private function recuperar(){

        $mail = $_POST["mail"];
        $comando = "SELECT count(*) as VALIDA from " . self::NOMBRE_TABLA ." WHERE " . self::CORREO . "= ? ";
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $mail);


        if ($sentencia->execute())
        {
            if($sentencia->fetch(PDO::FETCH_ASSOC)["VALIDA"] > 0)
            {

                $claveApi = substr(self::generarClaveApi(),23);
                $comando = "update ".self::NOMBRE_TABLA." set claveApi = ? where correo = ?";
                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $claveApi);
                $sentencia->bindParam(2, $mail);
                if ($sentencia->execute())
                {
                    envia_mail($_POST["mail"], "Escudería - recuperar contraseña", envia_mensaje_recuperarcontrasena($claveApi, $mail));
                    return 1;
                }
                else{
                    return -1;
                }

            }else{
                return -1;
            }
        }else
            return -1;


    }
    private function validacodigoverificacion(){

        $mail = $_POST["mail"];
        $claveapi = $_POST["claveapi"];
        $comando = "SELECT count(*) as VALIDA from usuario where correo = ? AND claveApi = ?";
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $mail);
        $sentencia->bindParam(2, $claveapi);
        if ($sentencia->execute())
        {
            return $sentencia->fetch(PDO::FETCH_ASSOC)["VALIDA"];
        }else{
            return -1;
        }
    }

    private function cambiarcontasena(){
        $mail = $_POST["mail"];
        $claveapi = $_POST["claveapi"];
        $password = self::encriptarContrasena( $_POST["password"]);
        $comando = "update usuario  set contrasena = ? where correo = ? and claveApi = ?";
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $sentencia = $pdo->prepare($comando);
        $sentencia->bindParam(1, $password);
        $sentencia->bindParam(2, $mail);
        $sentencia->bindParam(3, $claveapi);
        if ($sentencia->execute())
        {
            return 1;
        }else{
            return 0;
        }

    }

    public function info(){

        $idusuario = $_POST["idUsuario"];
        $comando = "SELECT `idUsuario`, `nombre`, `appaterno`, `apmaterno`, `fecha_nacimiento`, `correo`, `telefono`, `verificado`, `claveApi` FROM `usuario` WHERE idUsuario = ? ";
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $sentencia = $pdo->prepare($comando);
        $sentencia->bindParam(1, $idusuario);
        if ($sentencia->execute())
        {
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        }else{
            return null;
        }

    }


    private function listarUsuarios(){

        $comando = "SELECT idUsuario,nombre,appaterno,apmaterno,correo,ifnull(telefono,'') telefono FROM `usuario` ";
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $sentencia = $pdo->prepare($comando);

        if ($sentencia->execute())
        {
            return $sentencia->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return null;
        }

    }
     private function preregistro($usuario){

        $contrasenaEncriptada = self::encriptarContrasena( $usuario["password"]);



        $claveApi = self::generarClaveApi();

        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT

            //print_r($usuario);
            $comando = "UPDATE " . self::NOMBRE_TABLA . " SET  " .
                self::NOMBRE . " = ?," .
                self::APPATERNO . " = ?," .
                self::APMATERNO . " = ?," .
                self::CONTRASENA . " = ?," .
                self::FECHA_NAC . " = ?," .
                self::CLAVE_API . " = ?," .
                self::TELEFONO. " = ?," .
                self::VERIFICADO . " = ? " .
                " WHERE idUsuario = ? and correo = ?";


            $sentencia = $pdo->prepare($comando);


            $sentencia->bindParam(1, $usuario["nombre"]);

            $sentencia->bindParam(2, $usuario["appaterno"]);

            $sentencia->bindParam(3, $usuario["apmaterno"]);

            $sentencia->bindParam(4, $contrasenaEncriptada);

            $fecha = $usuario["yyyy"]."-".$usuario["mm"]."-".$usuario["dd"];

            $sentencia->bindParam(5, $fecha);

            $sentencia->bindParam(6, $claveApi);

            $sentencia->bindParam(7, $usuario["telefono"]);

            $verificado = 1;
            $sentencia->bindParam(8, $verificado);

            $sentencia->bindParam(9, $usuario["idUsuario"]);

            $sentencia->bindParam(10, $usuario["email"]);



            $resultado = $sentencia->execute();

            if ($resultado) {

                $comando = "update invitacion set estatus = 1 where idUsuario = ? and idSubasta = ?";

                $sentencia = $pdo->prepare($comando);

                $sentencia->bindParam(1, $usuario["idUsuario"]);

                $sentencia->bindParam(2, $usuario["idSubasta"]);

                $sentencia->execute();

                return $usuario["idUsuario"];
            } else {
                return 0;
            }
        } catch (PDOException $e) {

            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);

        }

    }

}
