<?php

class subastas
{

    public function __construct($idSubasta =0, $idTipoSubasta = 0)
    {
        $this->idSubasta = $idSubasta;
        $this->idTipoSubasta = $idTipoSubasta;
        $this->fechaIni = date("Y-m-d");
        $this->fechaFin = date("Y-m-d");

    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "subastas";
    const NOMBRE_SUBASTA = "nombreSubasta";
    const ID_SUBASTA = "idSubasta";
    const ID_TIPOSUBASTA = "IdTipoSubasta";
    const FECHA_INICIO = "fechaInicio";
    const FECHA_FIN = "fechaFin";
    const INCREMENTO = "incremento";
    const VISIBLE = "visible";
    const OFERTAS = "ofertas_x_usuarios";
    const AUTOSXUSUARIO = "autos_x_usuario";
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {


        if ($peticion[0] == 'listar') {
            return self::listar($_POST);
        }else if ($peticion[0] == 'guardar') {
            return self::registrar($_POST);
        }else if ($peticion[0] == 'publicar'){
            return self::publicaOut();
        }else if($peticion[0] == 'info'){
              return self::infoSubasta($_POST['id']);
        }
        else if ($peticion[0] == 'xusuario'){
            return self::xusuario();
        }else if($peticion[0] == 'participantes'){
            return self::participantes($_POST);
        }else if($peticion[0] == 'revisarresultados'){
            return self::revisarresultados($_POST);
        }else if($peticion[0] == 'revisarresultadosmax'){
            return self::revisarresultadosmax($_POST);
        }
        else if($peticion[0] == 'cancelar'){
            return self::cancelar($_POST);
        }else if($peticion[0] == 'remover_participante'){
            return self::remover_participante($_POST);
        }else if($peticion[0] == 'estatus'){
            return self::estatus($_POST['id']);
        }else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }



    private function listar($datosListar)
    {
        try{
            $estatus = $datosListar['estatus'];
            $estatusWhere = "";
            $empresa = $datosListar['empresa'];
            $empresaFrom = "";
            $empresaWhere = "";
            $subastaId = $datosListar['subastaId'];
            $subastaIdWhere = "";

            if($estatus > -1){
                $estatusWhere = " and visible = " .$estatus;
            }
            if($empresa > -1){
                $empresaFrom = ", subastaempresa sube, empresas e ";
                $empresaWhere = " and s.idSubasta = sube.idSubasta and e.idEmpresa = sube.idEmpresa and e.idEmpresa = " . $empresa;
            }
            if($subastaId > -1){
                $subastaIdWhere = " and idSubasta = ".$subastaId;

            }

            $comando = "select s.idSubasta, nombreSubasta, idTipoSubasta, tipo.tipoSubasta, fechaInicio, fechaFin, CASE WHEN visible = -1 then 'CANCELADA' WHEN  ".setNowForSQL()." BETWEEN fechaInicio and fechaFin then 'ACTIVA' WHEN ".setNowForSQL()." < fechaInicio then 'AGENDADA' else 'TERMINADA' end as estatus, visible, case visible when 0 then 'NO PUBLICADA' else 'PUBLICADA' end as publicada,(select GROUP_CONCAT(emp.nombreEmpresa) from subastaempresa se, empresas emp where s.idSubasta = se.idSubasta and se.idEmpresa = emp.idEmpresa) as empresas, (select GROUP_CONCAT(emp.idEmpresa) from subastaempresa se, empresas emp where s.idSubasta = se.idSubasta and se.idEmpresa = emp.idEmpresa) as empresasId,incremento, ofertas_x_usuarios, autos_x_usuario, (select count(*) from subastas_autos suba where suba.subastaId = s.idSubasta ) as total_autos, (select count(*) from subasta_usuario subu where subu.idSubasta = s.idSubasta) as total_participantes, (select count(*) from autos_puja aupu  where hora_puja between s.fechaInicio and s.fechaFin and aupu.idSubasta = s.idSubasta) as total_ofertas, s.revisada, s.fecha_cierre, s.autos_x_usuario,  (select count(*) from subastas_autos suba where suba.subastaId = s.idSubasta ) as total_autos,  timediff(fechaFin, fechaInicio) /10000 as diff from subastas s, tiposubastas tipo " . $empresaFrom." where s.idTipoSubasta = tipo.idTipo  " . $empresaWhere . $estatusWhere . $subastaIdWhere . " order by fechaFin desc" ;



            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);


            if ($sentencia->execute())
                return $sentencia->fetchall(PDO::FETCH_ASSOC);
            else
                return null;
        }catch(Exception $e){
            print_r($e);
            return null;
        }

    }

    public function infoSubasta($id)
    {
      /*Modificar para sacar motivo de cancelacion cuando sea el caso*/



        $comando = "select s.idSubasta, nombreSubasta, idTipoSubasta, tipo.tipoSubasta, fechaInicio, fechaFin, CASE WHEN visible = -1 then 'CANCELADA' when visible = 0 then 'CERRADA' WHEN  ".setNowForSQL()." BETWEEN fechaInicio and fechaFin then 'ACTIVA' WHEN ".setNowForSQL()." < fechaInicio then 'AGENDADA' else 'TERMINADA' end as estatus, visible, case visible when 0 then 'NO PUBLICADA' else 'PUBLICADA' end as publicada,(select GROUP_CONCAT(emp.nombreEmpresa) from subastaempresa se, empresas emp where s.idSubasta = se.idSubasta and se.idEmpresa = emp.idEmpresa) as empresas, (select GROUP_CONCAT(emp.idEmpresa) from subastaempresa se, empresas emp where s.idSubasta = se.idSubasta and se.idEmpresa = emp.idEmpresa) as empresasId, incremento, ofertas_x_usuarios, autos_x_usuario, (select count(*) from subastas_autos suba where suba.subastaId = s.idSubasta ) as total_autos, IFNULL((select max(hora_fin) from subastas_autos sa where sa.subastaId = s.idSubasta),s.fechaInicio )  as fecha_tentativa, s.revisada  from subastas s, tiposubastas tipo  where s.idTipoSubasta = tipo.idTipo and s.idSubasta = ?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $id);


        if ($sentencia->execute())
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        else
            return null;

    }

     private function xusuario()
    {
      /*Ver porque no esta mandando los cambios cuando se edita, o ver forma de estar recargando constante mente a la vista del usuario*/
        $comando = "select s.idSubasta, nombreSubasta, idTipoSubasta, tipo.tipoSubasta, fechaInicio, fechaFin,
CASE WHEN visible = -1 then 'CANCELADA' when visible = 0 then 'CERRADA' WHEN  ".setNowForSQL()." BETWEEN fechaInicio and fechaFin then 'ACTIVA' WHEN ".setNowForSQL()." < fechaInicio then 'AGENDADA' else 'TERMINADA' end  as estatus, visible,
(case visible when 0 then 'NO PUBLICADA' else 'PUBLICADA' end) as publicada,
(select GROUP_CONCAT(emp.nombreEmpresa) from subastaempresa se, empresas emp where s.idSubasta = se.idSubasta and se.idEmpresa = emp.idEmpresa) as empresas, (select GROUP_CONCAT(emp.idEmpresa) from subastaempresa se, empresas emp where s.idSubasta = se.idSubasta and se.idEmpresa = emp.idEmpresa) as empresasId,incremento, (select count(*) from subastas_autos suba where suba.subastaId = s.idSubasta ) as total_autos from subastas s, tiposubastas tipo  where s.idTipoSubasta = tipo.idTipo
and s.idSubasta in (select su.idSubasta from subasta_usuario su, usuario u, subastas sub where su.idUsuario = u.idUsuario and sub.idSubasta = su.idSubasta and sub.visible = 1 and u.claveApi =  ? and sub.fechaInicio BETWEEN
DATE_ADD(".setNowForSQL() .", INTERVAL -6 MONTH) AND DATE_ADD(".setNowForSQL() .", INTERVAL 6 MONTH)) order by fechaFin desc ";



        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $sentencia = $pdo->prepare($comando);//


        $sentencia->bindParam(1, $_POST["idusuario"]);


        if ($sentencia->execute())
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        else
            return null;

    }

    /*
    private function registrarOut()
    {
        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);

        $resultado = self::registrar($_POST);

        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
               http_response_code(200);
               return "OK";

                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            default:
                http_response_code(200);
                return $resultado;
        }
    }
    */

    private function publicaOut(){

        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);

        $resultado = self::publicar($_POST);

        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
               http_response_code(200);
               return "OK";

                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            default:
                http_response_code(200);
                return $resultado;
        }
    }

    private function registrar($subastas)
    {


        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();


            if($subastas["idSubasta"] == "0"){
            // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::NOMBRE_SUBASTA . ",".
                    self::ID_TIPOSUBASTA . "," .
                    self::FECHA_INICIO . "," .
                    self::FECHA_FIN . ",".
                    self::INCREMENTO.",".
                    self::OFERTAS.",".
                    self::AUTOSXUSUARIO.")" .
                    " VALUES(?,?,?,?,?,?,?)";




                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $subastas["nombreSubasta"]);
                $sentencia->bindParam(2, $subastas["IdTipoSubasta"]);
                $sentencia->bindParam(3, $subastas["fechaInicio"]);
                $sentencia->bindParam(4, $subastas["fechaFin"]);
                $sentencia->bindParam(5, $subastas["incremento"]);
                $sentencia->bindParam(6, $subastas["ofertas_x_usuarios"]);
                $sentencia->bindParam(7, $subastas["autos_x_usuario"]);




                $resultado = $sentencia->execute();


                if ($resultado) {
                    $subastaid = $pdo->lastInsertId();


                    subastasempresa::registrar($subastas["empresas"], $subastaid);
                    return $subastaid;
                } else {
                    return -1;
                }
            }
            else{
                try{

                    $comando = "UPDATE " . self::NOMBRE_TABLA . " SET  ".
                    self::NOMBRE_SUBASTA . "= ?, ".
                    self::ID_TIPOSUBASTA . "= ?, ".
                    self::FECHA_INICIO . "= ?, ".
                    self::FECHA_FIN . " = ?, ".
                    self::INCREMENTO." = ?, " .
                    self::OFERTAS." = ?, " .
                    self::AUTOSXUSUARIO." = ? " .
                    " WHERE ".self::ID_SUBASTA." = ?";

                    $sentencia = $pdo->prepare($comando);
                    $sentencia->bindParam(1, $subastas["nombreSubasta"]);
                    $sentencia->bindParam(2, $subastas["IdTipoSubasta"]);
                    $sentencia->bindParam(3, $subastas["fechaInicio"]);
                    $sentencia->bindParam(4, $subastas["fechaFin"]);
                    $sentencia->bindParam(5, $subastas["incremento"]);
                    $sentencia->bindParam(6, $subastas["ofertas_x_usuarios"]);
                    $sentencia->bindParam(7, $subastas["autos_x_usuario"]);
                    $sentencia->bindParam(8, $subastas["idSubasta"]);
                    $resultado = $sentencia->execute();


                    if ($resultado) {
                        subastasempresa::eliminaEmpresas( $subastas["idSubasta"]);
                        subastasempresa::registrar($subastas["empresas"], $subastas["idSubasta"]);
                        return  $subastas["idSubasta"];
                    } else {
                        return ExcepcionApi(0, "No se insertó el registro", 500);
                    }
                }catch(Exception $e){

                    return ExcepcionApi(0, $e->getMessage(), 500);
                }

            }


        } catch (Exception $e) {



        }

    }

    private function publicar($subastas)
    {



        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "UPDATE " . self::NOMBRE_TABLA . " SET  " .
                self::VISIBLE . " = ? WHERE ".self::ID_SUBASTA." = ?";




            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $subastas["visible"]);
            $sentencia->bindParam(2, $subastas["idSubasta"]);





            $resultado = $sentencia->execute();


            if ($resultado) {


                return $subastas["idSubasta"];

            } else {
                return -1;
            }
        } catch (PDOException $e) {

            //print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);

        }

    }

    private function participantes($params){


        try{
            $comando = "SELECT su.idSubasta, u.idUsuario, u.nombre, u.appaterno, u.apmaterno, u.correo, u.vigencia, u.verificado FROM subasta_usuario su, usuario u WHERE su.idUsuario = u.idUsuario and su.idSubasta = ?" ;

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $params["id_subasta"]);


            if ($sentencia->execute())
                return $sentencia->fetchall(PDO::FETCH_ASSOC);
            else
                return new ExcepcionApi("error", "Ocurrió un error al obtener los participantes", 400);
        }catch(Exception $e){

            return new ExcepcionApi("error", $e->getMessage(), 400);
        }


    }

    private function remover_participante($params){

        if($_SESSION['es_admin'] != 1){
            return new ExcepcionApi("Error", "El usuario no tiene permisos para realizar esta operación", 500);
        }

        $comando = " delete from subasta_usuario where idSubasta = ? and idUsuario = ?";
        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $params["subasta"]);
        $sentencia->bindParam(2, $params["usuario"]);
        if ($sentencia->execute()){
            return 1;
        }else{
            return 0;
        }

    }

    private function asignarGanadorPorSubasta(&$resultados,&$oferta,&$ganadores, &$casiGanadores){
            $resultado = $resultados[$oferta["idAuto"]];

            if($resultado->usuarioganador != $oferta["idUsuario"]){
                if($$ganadores[$resultado->usuarioganador]['contador'] > 0){
                   $ganadores[$resultado->usuarioganador]['contador'] = $ganadores[$resultado->usuarioganador]['contador']-1;
                    $casiGanadores[$oferta["idAuto"]] = $oferta;
                }

            }


                $ganadores[$oferta["idUsuario"]]['contador'] = $ganadores[$oferta["idUsuario"]]['contador']+1;

                $resultado->oferta = $oferta["oferta"];
                $resultado->ganancia = $oferta["ganancia"];
                $resultado->usuarioganador =$oferta["idUsuario"];
                $resultado->usuario = $oferta["nombre_usuario"];

                $resultado->puja = $oferta;
                $resultado->hora_puja = $oferta["hora_puja"];

                $ganadores[$oferta["idUsuario"]]['gananciaOfrecida'][$oferta["idAuto"]] =  $resultado->ganancia;

                array_push($resultado->ofertas, $oferta);
    }

    private function revisarresultados($datos){

        $subasta = self::listar($datos)[0];
        $oAutospuja = new autospuja(0,0);
        $resultadosxauto = array();

        $pujas = $oAutospuja::xsubasta($subasta['idSubasta'], $datos['sort']);


        $oAutos = new autos();
        $autos = $oAutos::listarPorSubastas();
        // $participantes = $oAutospuja::participantes();
        $participantes = array();
        $ganadores = $oAutospuja::resultadoGanador($subasta['idSubasta']);
        $resultados = array();


        // $ganadores = array();
        // $casiGanadores = array();

        foreach ($autos as &$auto) {
            //print_r($auto);

            $oresultado = new resultados($auto["idAuto"], $auto["marca"], $auto["modelo"], $auto["anio"], $auto["precio"], $auto["foto"]);

            $resultados[$auto["idAuto"]]  = $oresultado;


        }
        foreach ($ganadores as $p) {
          $participantes[$p['idAuto']] = $p['idPuja'];
        }

        // foreach ($participantes as &$p) {
        //     $ganadores[$p["idUsuario"]] =array();
        //     $ganadores[$p["idUsuario"]]['contador'] = 0;
        //     $ganadores[$p["idUsuario"]]['gananciaOfrecida']= array();
        // }

      //  print_r($pujas);
        $i = 1;
        foreach($pujas as &$oferta){

            $resultado = &$resultados[$oferta["idAuto"]];

            if($oferta['idPuja'] == $participantes[$oferta["idAuto"]]){
              $resultado->oferta = $oferta["oferta"];
              $resultado->ganancia = $oferta["ganancia"];
              $resultado->usuarioganador =$oferta["idUsuario"];
              $resultado->usuario = $oferta["nombre_usuario"];

              $resultado->puja = $oferta;
              $resultado->hora_puja = $oferta["hora_puja"];
            }


            array_push($resultado->ofertas, $oferta);
            // if($oferta["ganancia"] > $resultado->ganancia  && $oferta["puja_valida"] == 1  ){
            //
            //     if($ganadores[$oferta["idUsuario"]]['contador'] < $subasta["autos_x_usuario"] ) {
            //             if($resultado->usuarioganador != $oferta["idUsuario"]){
            //                 if($ganadores[$resultado->usuarioganador]['contador'] > 0){
            //                     $ganadores[$resultado->usuarioganador]['contador'] = $ganadores[$resultado->usuarioganador]['contador']-1;
            //                     $casiGanadores[$oferta["idAuto"]] = $oferta;
            //                 }
            //
            //             }
            //
            //
            //             $ganadores[$oferta["idUsuario"]]['contador'] = $ganadores[$oferta["idUsuario"]]['contador']+1;
            //
            //             $resultado->oferta = $oferta["oferta"];
            //             $resultado->ganancia = $oferta["ganancia"];
            //             $resultado->usuarioganador =$oferta["idUsuario"];
            //             $resultado->usuario = $oferta["nombre_usuario"];
            //
            //             $resultado->puja = $oferta;
            //             $resultado->hora_puja = $oferta["hora_puja"];
            //             $resultado->ts = $oferta["ts"];
            //
            //             $ganadores[$oferta["idUsuario"]]['gananciaOfrecida'][$oferta["idAuto"]] =  $oferta;
            //
            //
            //             // tema 2
            //
            //
            //                 $menorGanancia = array('ganancia' => 0);
            //
            //                 foreach ($ganadores[$oferta["idUsuario"]]['gananciaOfrecida'] as $idAuto => $oferta2) {
            //                     if($oferta["ganancia"] > $oferta2["ganancia"]    )                        {
            //                         if($menorGanancia['ganancia'] == 0 || $oferta2["ganancia"] >  $menorGanancia['ganancia']){
            //                             $menorGanancia['idAuto'] = $idAuto;
            //                             $menorGanancia['oferta'] = $oferta2;
            //                             $menorGanancia['ganancia'] = $oferta2["ganancia"];
            //                         }
            //                     }
            //                 }
            //                 if($menorGanancia['ganancia'] != 0 ){
            //                     $oferta2 = $menorGanancia['oferta'];
            //                     $resultado2 = $resultados[$oferta2["idAuto"]];
            //                      $ganadores[$oferta["idUsuario"]]['contador'] = $ganadores[$oferta2["idUsuario"]]['contador']+1;
            //
            //                     $resultado2->oferta = $oferta2["oferta"];
            //                     $resultado2->ganancia = $oferta2["ganancia"];
            //                     $resultado2->usuarioganador =$oferta2["idUsuario"];
            //                     $resultado2->usuario = $oferta2["nombre_usuario"];
            //
            //                     $resultado2->puja = $oferta2;
            //                     $resultado2->hora_puja = $oferta2["hora_puja"];
            //                     $resultado2->ts = $oferta2["ts"];
            //
            //                     $ganadores[$oferta2["idUsuario"]]['gananciaOfrecida'][$oferta2["idAuto"]] =  $resultado2->ganancia;
            //
            //
            //                 }
            //
            //
            //
            //             array_push($resultado->ofertas, $oferta);
            //     }
            //     else{
            //       // esta linea estaba comentada
            //         array_push($resultado->ofertas, $oferta);
            //     }
            //
            //
            // }
            // else{
            //     array_push($resultado->ofertas, $oferta);
            // }



        }


        return $resultados;


    }

    private function revisarresultadosmax($datos){

        $subasta = self::listar($datos)[0];
        $oAutospuja = new autospuja(0,0);
        $resultadosxauto = array();

        $pujas = $oAutospuja::xsubatasxusuario($subasta['idSubasta']);


        $oAutos = new autos();
        $autos = $oAutos::listarPorSubastas();
        //$participantes = $oAutospuja::participantes($subasta['idSubasta']);
        $resultados = array();
        //$ganadores = array();
        foreach ($ganadores as $p) {
          $participantes[$p['idAuto']] = $p['idPuja'];
        }
        foreach ($autos as &$auto) {
            //print_r($auto);

            $oresultado = new resultados($auto["idAuto"], $auto["marca"], $auto["modelo"], $auto["anio"], $auto["precio"], $auto["foto"]);
            $resultados[$auto["idAuto"]]  = $oresultado;


        }
        foreach ($participantes as &$p) {
            $ganadores[$p["idUsuario"]] =0;
        }

       // print_r($pujas);
        $i = 1;
        foreach($pujas as &$oferta){

            $resultado = $resultados[$oferta["idAuto"]];

                        if($oferta['idPuja'] == $participantes[$oferta["idAuto"]]){
                          $resultado->oferta = $oferta["oferta"];
                          $resultado->ganancia = $oferta["ganancia"];
                          $resultado->usuarioganador =$oferta["idUsuario"];
                          $resultado->usuario = $oferta["nombre_usuario"];

                          $resultado->puja = $oferta;
                          $resultado->hora_puja = $oferta["hora_puja"];
                        }


                        array_push($resultado->ofertas, $oferta);

            if($oferta["oferta"] > $resultado->oferta && $oferta["puja_valida"] == 1 ){

                if($ganadores[$oferta["idUsuario"]] < $subasta["autos_x_usuario"]) {
                        if($resultado->usuarioganador != $oferta["idUsuario"]){
                            if($ganadores[$resultado->usuarioganador] > 0){
                                $ganadores[$resultado->usuarioganador] = $ganadores[$resultado->usuarioganador]-1;
                            }

                        }
                        $ganadores[$oferta["idUsuario"]] = $ganadores[$oferta["idUsuario"]]+1;
                        $resultado->oferta = $oferta["oferta"];
                        $resultado->usuarioganador =$oferta["idUsuario"];
                        $resultado->usuario = $oferta["nombre_usuario"];

                        $resultado->puja = $oferta;
                        $resultado->hora_puja = $oferta["hora_puja"];
                        $resultado->estatus = $oferta['estatus'];
                        $resultado->motivo = $oferta['motivo'];



                        array_push($resultado->ofertas, $oferta);
                }
                else{
                    array_push($resultado->ofertas, $oferta);
                }


            }
            else{
                array_push($resultado->ofertas, $oferta);
            }



        }


        return $resultados;


    }


    private function cancelar($params){


       if($_SESSION['es_admin'] != 1){
        return new ExcepcionApi("Error", "El usuario no tiene permisos para realizar esta operación", 500);
       }

        try{
            $comando = "update subastas set motivo_cancelacion = ?, visible = -1,  fecha_cierre = ".setNowForSQL()."   WHERE idSubasta = ?" ;

            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            $sentencia->bindParam(1, $params["motivo"]);
            $sentencia->bindParam(2, $params["id_subasta"]);


            if ($sentencia->execute())
            {

                $comando = " SELECT su.idSubasta, s.nombreSubasta, us.nombre, us.correo  ";
                $comando .= " FROM subasta_usuario su, subastas s, usuario us WHERE s.idSubasta = ?";
                $comando .= " and s.idSubasta = su.idSubasta ";
                $comando .= " and su.idUsuario = us.idUsuario ";
                $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                $sentencia->bindParam(1, $params["id_subasta"]);
                if ($sentencia->execute())
                {
                    $participantes = $sentencia->fetchall(PDO::FETCH_ASSOC);
                    foreach ($participantes as $row) {
                       $mensaje = mensaje_cancela_subasta($params["motivo"], $row["nombreSubasta"], $row["nombre"]);
                       envia_mail($row["correo"], "Subasta cancelada", $mensaje);
                    }

                }
                return 1;
            }else
                return new ExcepcionApi("error", "Ocurrió un error al obtener los participantes", 500);
        }catch(Exception $e){

            return new ExcepcionApi("error", $e->getMessage(), 500);
        }


    }
     private function estatus($id)
    {
        $comando = "select revisada from subastas where idSubasta = ?";

        //print_r($comando);

        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
        $sentencia = $pdo->prepare($comando);


        $sentencia->bindParam(1, $id);


        if ($sentencia->execute())
        {
            return  $sentencia->fetchall(PDO::FETCH_ASSOC);

        }
        else{
            return 0;
        }


    }


}
