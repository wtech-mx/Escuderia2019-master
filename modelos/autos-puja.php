<?php

class autospuja
{

    public function __construct($idAuto =0, $idSubasta = 0)
    {
        $this->idAuto = $idAuto;
        $this->idSubasta = $idSubasta;


    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "autos_puja";
    const ID_AUTO = "idAuto";
    const ID_SUBASTA = "idSubasta";
    const ID_USUARIO = "idUsuario";
    const OFERTA = "oferta";
    const ESTATUS = "estatus";
    const MOTIVO = "motivo";

    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {

        if ($peticion[0] == 'ofertar') {
            return self::insertaOferta();
        }else if ($peticion[0] == 'ofertasxusuario'){

            return self::totalpujaxusuario($_POST);
        }
        else if($peticion[0] == 'listar') {

            return self::listar();
        } else if($peticion[0] == 'ganadores'){

        }else if($peticion[0] == 'autosofertados'){
            return self::autosofertados();
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }







    public static function insertaOferta()
    {


        try {


                $tiposubasta = 0;
                $valida = 1;
                $motivo = '';
                $incremento = 0;
 //               print_r($_SESSION);
                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();


                $comando = "select case when ".setNowForSQL()." < subastas.fechaFin then true else false END as valida,case when ".setNowForSQL()." < subastas_autos.hora_fin then true else false END as valida_, subastas.idTipoSubasta as tiposubasta, subastas.incremento, concat(subastas_autos.hora_inicio,' - ' ,subastas_autos.hora_fin )as horario  from subastas join subastas_autos on (idSubasta = subastaId) where idSubasta = ?  and autoId = ? ";
                $comando2 = $comando;
                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $_POST["id_subasta"]);
                $sentencia->bindParam(2, $_POST["id_auto"]);
                $resultado = $sentencia->execute();
                $fetch =  $sentencia->fetch(PDO::FETCH_ASSOC);

                $tiposubasta = $fetch["tiposubasta"];
                $incremento =  $fetch["incremento"];

                if($fetch["valida"] == 0 || $fetch["valida_"]  == 0  ){
                    $valida = 0;
                    $motivo = "Imposible ofertar, la substasta ha terminado ($fetch[horario])";
                }




                $comando = "SELECT  ifnull(max(oferta),0) as maxoferta, (select precio from autos au where au.idAuto= ? limit 1) as inicial FROM autos_puja ap, subastas s WHERE idAuto=? and ap.idSubasta = ? and hora_puja < s.fechaFin and estatus = 1";
                if($tiposubasta == 2){
                  $comando .= " and  idUsuario = (select idUsuario from usuario where claveApi = ?)";
                }
                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $_POST["id_auto"]);
                $sentencia->bindParam(2, $_POST["id_auto"]);
                $sentencia->bindParam(3, $_POST["id_subasta"]);
                if($tiposubasta == 2){
                  $sentencia->bindParam(4, $_POST["claveapi"]);
                }
                $resultado = $sentencia->execute();
                $fetch =  $sentencia->fetch(PDO::FETCH_ASSOC);


                if($valida == 1){
                    if(intval($fetch["maxoferta"]) >= $_POST["oferta"] && $tiposubasta == 1 ){
                        $valida = 0;
                        $motivo =  "Su oferta no fue registrada debido a que existe una oferta igual o superior a la tuya";
                    }else{
                        $valida = 1;
                        $motivo =  "";
                    }
                }
                if($valida == 1){
                    if($_POST["oferta"] -$fetch["maxoferta"] < $incremento ){

                        $valida = 0;
                        $motivo = "La oferta no cumple con las reglas del incremento. ";

                        if($tiposubasta != 1){
                            $motivo .= " Su última oferta fue: ".$fetch["maxoferta"];
                        }
                    }
                    if($_POST["oferta"] -$fetch["inicial"] < $incremento ){

                        $valida = 0;
                        $motivo = "La oferta no cumple con las reglas del incremento. La oferta debe ser mayor al precio de salida.";

                    }

                }


                // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::ID_AUTO . "," .
                    self::ID_SUBASTA . "," .
                    self::ID_USUARIO  . "," .
                    self::OFERTA. ",".
                    self::ESTATUS.",".
                    self::MOTIVO.")" .
                    " VALUES(?,?,?,?,?,?)";

                $u = new usuarios();

                $s = new subastas();

                $s = $s->infoSubasta($_POST["id_subasta"]);

                $autosofertados = self::autosofertados();
                $totalxusuario = sizeof($autosofertados);
                $ofertavalida = false;

            /*
              foreach($autosofertados as $auto){


                    if(intval($auto["auto"]) == intval($_POST["id_auto"])){
                        $ofertavalida = true;
                        break;
                    }
                }
                if(!$ofertavalida){
                    $valida = 0;
                    $motivo = "Imposible ofertar, solamente se puede participar por ".$s[0]["ofertas_x_usuarios"]. " autos en esta subasta.";
                }
*/


               if($valida == 1){
                    if($totalxusuario <= $s[0]["ofertas_x_usuarios"] || $ofertavalida){
                        $valida = 1;
                    }else{
                        $valida = 0;
                        $motivo = "Imposible ofertar, solamente se puede participar por ".$s[0]["ofertas_x_usuarios"]. " autos en esta subasta.";
                    }
                }

                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $_POST["id_auto"]);
                $sentencia->bindParam(2, $_POST["id_subasta"]);
                $sentencia->bindParam(3, $_SESSION['idusuario']);
                $sentencia->bindParam(4, $_POST["oferta"]);
                $sentencia->bindParam(5, $valida);
                $sentencia->bindParam(6, $motivo);
                $resultado = $sentencia->execute();

                if($valida == 1)
                  if ($resultado ) {
                      $motivo = "Su oferta fue registrada";

                  } else {
                      $motivo = "Ocurrió un error al registrar su oferta";
                  }
                return $motivo;


        } catch (Excepcion $e) {

            return "Ocurrió un error al registrar su oferta ";
        }

    }

    public static function totalpujaxusuario(){
        try{
            //$comando = "SELECT count(*) as total_ofertas FROM autos_puja ap, usuario u WHERE ap.idUsuario = u.idUsuario and ap.idSubasta = ? and u.claveApi = ?";

            $comando = "SELECT count(distinct ap.idAuto) as total_ofertas FROM autos_puja ap, usuario u WHERE ap.idUsuario = u.idUsuario and ap.idSubasta = ? and u.claveApi = ?";
            //print_r($comando);
            //print_r($_POST);
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $_POST["id_subasta"]);
            $sentencia->bindParam(2, $_SESSION["claveapi"]);

            $resultado = $sentencia->execute();

            $fetch =  $sentencia->fetch(PDO::FETCH_ASSOC);

            return $fetch["total_ofertas"];

        }catch(Excepcion $e){

            return -1;
        }

    }

    public static function autosofertados(){
         try{
            //$comando = "SELECT count(*) as total_ofertas FROM autos_puja ap, usuario u WHERE ap.idUsuario = u.idUsuario and ap.idSubasta = ? and u.claveApi = ?";

            $comando = "SELECT distinct ap.idAuto as auto FROM autos_puja ap, usuario u WHERE ap.idUsuario = u.idUsuario and ap.idSubasta = ? and u.claveApi = ? and ap.estatus = 1";
            //print_r($comando);
            //print_r($_POST);
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $_POST["id_subasta"]);
            $sentencia->bindParam(2, $_SESSION["claveapi"]);

            $resultado = $sentencia->execute();

            $fetch =  $sentencia->fetchall(PDO::FETCH_ASSOC);

            return $fetch;

        }catch(Excepcion $e){
            return -1;
        }

    }

    public static function listar(){

        try{
        $comando = "SELECT ap.idAuto, ap.idPuja, ap.oferta, ap.idUsuario, ap.hora_puja, ap.idSubasta, concat(u.nombre, ' ', u.appaterno, ' ', u.apmaterno) as nombre_usuario
            FROM autos_puja ap, usuario u  WHERE
            ap.idUsuario = u.idUsuario
            and ap.idSubasta = ? and ap.idAuto = ?
            order by
            ap.oferta, ap.hora_puja desc";
              $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $_POST["id_subasta"]);
            $sentencia->bindParam(2, $_POST["id_auto"]);

            $resultado = $sentencia->execute();
            return $sentencia->fetchall(PDO::FETCH_ASSOC);

        }catch(Excepcion $e){
            print_r($e);
            return null;
        }
    }
    public static function resultadoGanador($idsubasta){
      try{
        $comando = "select idPuja,idAuto,idUsuario,(select nombre from usuario u where u.idUsuario = ap.idUsuario limit 1)usuario,(select (select descripcion from cat_marca where id = autos.marca) from autos   where idAuto = ap.idAuto) as carro  from autos_puja ap where idPuja = pujaGanadora(idPuja)  and idSubasta = ?";
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $sentencia = $pdo->prepare($comando);

        $sentencia->bindParam(1, $idsubasta);


        $resultado = $sentencia->execute();
      return $sentencia->fetchall(PDO::FETCH_ASSOC);
      }catch(Excepcion $e){
          print_r($e);
          return null;
      }
    }

     public static function xsubasta($idsubasta, $sort){

        try{
        $comando = "SELECT UNIX_TIMESTAMP(ap.hora_puja) ts ,ap.idAuto, ap.idPuja, ap.oferta, ap.idUsuario, ap.hora_puja, ap.idSubasta, concat(u.nombre, ' ', u.appaterno, ' ', u.apmaterno) as nombre_usuario, ap.hora_puja, s.fechaFin, case when ap.hora_puja < s.fechaFin and ap.estatus =1 then 1 else 0 end as puja_valida, marca.descripcion as marca, modelo.descripcion as modelo, au.precio, au.anio, (ap.oferta-au.precio) as ganancia
          ,if(ap.estatus =1,'Válida','Inválida') as estatus,    ap.motivo   FROM autos_puja ap, usuario u, subastas s, autos au, cat_marca marca, cat_modelo modelo  WHERE
            ap.idUsuario = u.idUsuario
            and ap.idSubasta = s.idSubasta
            and ap.idSubasta = ?
            and ap.idAuto = au.idAuto
            and au.marca = marca.id
            and au.modelo = modelo.id
            and ap.hora_puja between s.fechaInicio and s.fechaFin
            order by
            ap.hora_puja " .(($sort == 1) ? "desc" : "asc" );


              $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $idsubasta);


            $resultado = $sentencia->execute();
            return $sentencia->fetchall(PDO::FETCH_ASSOC);

        }catch(Excepcion $e){
            print_r($e);
            return null;
        }
    }

	public static function xsubatasxusuario($idsubasta){
		 try{
        $comando = "SELECT
					  ap.idAuto,
					  ap.idPuja,
					  ap.idUsuario,
					  s.fechaFin,
					  au.anio,
					  marca.descripcion as marca,
					  modelo.descripcion as modelo,
					  au.precio,
					  MAX(ap.oferta) AS oferta,
					  ap.hora_puja,
					  CASE
						WHEN ap.hora_puja <= s.fechaFin and  ap.estatus =1
						THEN 1
						ELSE 0
					  END AS puja_valida,
					  CONCAT(u.nombre, ' ', u.appaterno, ' ', u.apmaterno) AS nombre_usuario,
            if(ap.estatus =1,'Válida','Inválida') as estatus,
            ap.motivo
					FROM
					  autos_puja ap,
					  usuario u,
					  subastas s,
					  autos au,
					  cat_marca marca,
					  cat_modelo modelo
					WHERE
					  ap.idSubasta = ?
					  AND ap.idUsuario = u.idUsuario
					  AND ap.idSubasta = s.idSubasta
					  AND ap.idAuto    = au.idAuto
					  AND au.marca     = marca.id
					  AND au.modelo    = modelo.id
					GROUP BY
					  ap.idAuto,
					  ap.idUsuario
					ORDER BY
					  ap.idAuto,
					  MAX(ap.oferta) DESC";


              $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $idsubasta);


            $resultado = $sentencia->execute();
            return $sentencia->fetchall(PDO::FETCH_ASSOC);

        }catch(Excepcion $e){
            print_r($e);
            return null;
        }

	}
    public static function ganadores($idsubasta){
        $comando = "SELECT ap.idAuto, ap.idPuja, ap.oferta, ap.idUsuario, ap.hora_puja, ap.idSubasta, concat(u.nombre, ' ', u.appaterno, ' ', u.apmaterno) as nombre_usuario FROM autos_puja ap, usuario u, subastas s WHERE ap.idUsuario = u.idUsuario and ap.idSubasta = ?   and s.idSubasta = ap.idSubasta and ap.hora_puja < s.fechaFin +1 ";


    }

    public static function participantes($idSubasta){
        $comando = "select distinct idUsuario from autos_puja where idSubasta = ?";
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $sentencia = $pdo->prepare($comando);
        $sentencia->bindParam(1, $idsubasta);
        $resultado = $sentencia->execute();
        return $sentencia->fetchall(PDO::FETCH_ASSOC);
    }

}
