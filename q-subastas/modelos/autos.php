<?php

class autos
{
    public function __construct()
    {
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "autos";
    const IDAUTO = "idAuto";
    const ENVENTA = "enVenta";
    const PRECIO = "precio";
    const MARCA = "marca";
    const MODELO = "modelo";
    const COLOR = "color";
    const ANIO = "anio";
    const KM = "km";
    const TRANSMISION = "transmision";
    const ESTADO = "estado";
    const CIUDAD = "ciudad";
    const DESCRIPCION = "descripcion";
    const PLACA = "placa";
    const SERIE = "serie";
    const ESTATUS = "estatus";
    const PUBLICADO = "publicado";
    const MOTIVO_PRECIO = "motivo_precio";
    const FECHACREACION = "fechaCreacion";



    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
        if ($peticion[0] == 'guardar') {
            return self::registrarOut();
        } elseif ($peticion[0] == 'info') {
            return self::info($_POST['autoid']);
        } elseif ($peticion[0] == 'subasta') {
            return self::listarPorSubastas();
        } elseif ($peticion[0] == 'actualiza') {
            return self::actualiza($_POST);
        } elseif ($peticion[0] == 'busqueda') {
            return self::busqueda();
        } elseif ($peticion[0] == 'quitarfoto') {
            return autosfotos::eliminar($_POST);
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }


    public function listarPorSubastas()
    {
        try {
            $idsubasta = $_POST['idsubasta'];
            $extraWhen = "";
            $paramId = 1;
            $hasExtra = false;

            if (isset($_POST['idusuario'])) {
                $consulta1 = "select /* *,count(idAuto) as autosGanados, */ @validoParaOfertar:=  if(count(idAuto) < autos_x_usuario,true,false) as validoParaOfertar from (
                  select
			autos_puja.idUsuario,
			autos_puja.idauto ,
            subastas.autos_x_usuario,
            subastas.idSubasta
		from
			autos_puja
            inner join subastas on subastas.idSubasta = autos_puja.idSubasta

            inner join subastas_autos on (subastas_autos.subastaId = subastas.idSubasta and subastas_autos.autoId = autos_puja.idAuto)
		where
		    autos_puja.idpuja = pujaGanadora(autos_puja.idpuja)
        and not DATE_ADD(NOW(), INTERVAL 2 HOUR) between subastas_autos.hora_inicio and subastas_autos.hora_fin
        and fechaInicio <= subastas_autos.hora_fin and fechaFin >= subastas_autos.hora_fin
        ) as t join usuario on (t.idUsuario = usuario.idUsuario) where   usuario.claveapi = ? and t.idSubasta = ?   group by t.idSubasta";
                
        $extraWhen =   " WHEN (exists($consulta1) = false   and  ".setNowForSQL()." BETWEEN  aus.hora_inicio and aus.hora_fin) or (($consulta1) = true  and  ".setNowForSQL()." BETWEEN  aus.hora_inicio and aus.hora_fin )   THEN 1 when @validoParaOfertar = false then 3 ";
        $hasExtra = true;
            }

            $comando =  " SELECT au.idAuto, au.enVenta, au.precio, au.marca as marcaid, marca.descripcion as marca, au.modelo as modeloid, ".
                        " modelo.descripcion as modelo, au.color as colorid, color.descripcion as color, au.anio, au.km, au.km, au.transmision as transmisionid, ".
                        " trans.descripcion as transmision, au.estado as estadoid, est.nombre as estado, au.ciudad as ciudadid, mun.nombre as ciudad,  ".
                        " au.descripcion, au.estatus, au.publicado, au.fechaCreacion, sub.autos_x_usuario, aus.subastaId, ".
                        " (select idFoto from auto_fotos where idAuto = au.idAuto limit 1) as foto, ".
                        " (select GROUP_CONCAT(idFoto) from auto_fotos where idAuto = au.idAuto) AS fotos, ".
                        "  IFNULL((select oferta  from autos_puja ap where ap.idAuto = aus.autoId and ap.hora_puja < sub.fechaFin and estatus = 1 order by ap.oferta desc limit 1), au.precio) as oferta, ".
                        " (select count(*) from autos_puja ap where ap.idAuto = aus.autoId and ap.hora_puja < sub.fechaFin and  estatus = 1) as total_ofertas, ".
                        " sub.idTipoSubasta, (CASE   WHEN ".setNowForSQL()." BETWEEN sub.fechaInicio and sub.fechaFin then 'ACTIVA' WHEN ".setNowForSQL()."  < sub.fechaInicio then 'AGENDADA'    else 'TERMINADA' end) as estatus_subasta, sub.incremento, aus.estatus, aus.motivo,au.nombreContacto, au.telefonoContacto, au.celularContacto, au.correoContacto, au.infoContacto, aus.hora_inicio, aus.hora_fin, ".
                        " case $extraWhen when 1=0 then 3  else 0 end AS ensubasta, sub.revisada ".
                        " FROM subastas_autos as aus, autos as au, cat_marca as marca, cat_modelo as modelo, cat_colores as color, cat_transmision as trans, estados as est, municipios as mun, subastas sub ".
                        " WHERE aus.subastaId = ?  ".
                        " and aus.autoId = au.idAuto  ".
                        " and au.marca = marca.id  ".
                        " and au.modelo = modelo.id  ".
                        " and au.color = color.id  ".
                        " and au.transmision = trans.id  ".
                        " and au.estado = est.id ".
                        " and au.ciudad = mun.id ".
                        " and aus.subastaId = sub.idSubasta ";

            if (isset($_POST["autoid"])) {
                $comando .=  " and aus.autoId = ?";
            }

            $comando .= "  order by aus.hora_inicio asc";


            //  echo $consulta1;
            $conexion = ConexionBD::obtenerInstancia()->obtenerBD();

            $gsent = $conexion->prepare('set sql_mode=""');
            $gsent->execute();

            $sentencia = $conexion->prepare($comando);

            if ($hasExtra) {
                $sentencia->bindParam($paramId++, $_POST["idusuario"]);
                $sentencia->bindParam($paramId++, $idsubasta);

                $sentencia->bindParam($paramId++, $_POST["idusuario"]);
                $sentencia->bindParam($paramId++, $idsubasta);
            }

            $sentencia->bindParam($paramId++, $idsubasta);
            if (isset($_POST["autoid"])) {
                $sentencia->bindParam($paramId++, $_POST["autoid"]);
            }


            if ($sentencia->execute()) {
                return $sentencia->fetchall(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        } catch (Excepcion $e) {
            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
        }
    }

    private function info($idauto)
    {
        try {
            $comando =  " SELECT au.idAuto, au.enVenta, au.precio, au.marca as marcaid, marca.descripcion as marca, au.modelo as modeloid,  ".
                " modelo.descripcion as modelo, au.color as colorid, color.descripcion as color, au.anio, au.km, au.km, au.transmision as transmisionid,  ".
                " trans.descripcion as transmision, au.estado as estadoid, est.nombre as estado, au.ciudad as ciudadid, mun.nombre as ciudad,   ".
                " au.descripcion, au.estatus, au.publicado, au.fechaCreacion,  ".
                " (select idFoto from auto_fotos where idAuto = au.idAuto limit 1) as foto,  ".
                "  (select GROUP_CONCAT(idFoto) from auto_fotos where idAuto = au.idAuto) AS fotos,  ".
                "  (select GROUP_CONCAT(idFeature) from autos_catacteristicas where idAuto = au.idAuto) AS caracteristicasids,  ".
                "  (select GROUP_CONCAT(feat.descripcion) from autos_catacteristicas ac, cat_features feat where  ac.idFeature = feat.id and ac.idAuto = $idauto ) as caracteristicas, au.motivo_precio, au.placa, au.serie, au.nombreContacto, au.telefonoContacto, au.celularContacto, au.correoContacto, au.infoContacto ".
                " FROM autos as au, cat_marca as marca, cat_modelo as modelo, cat_colores as color, cat_transmision as trans, estados as est, municipios as mun  ".
                " WHERE au.marca = marca.id   ".
                " and au.modelo = modelo.id   ".
                " and au.color = color.id   ".
                " and au.transmision = trans.id   ".
                " and au.estado = est.id  ".
                " and au.ciudad = mun.id  ".
                " and au.idAuto =   ".$idauto;




            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

            if ($sentencia->execute()) {
                return $sentencia->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        } catch (Excepcion $e) {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
        }
    }

    private function busqueda()
    {
        $precioIni = $_POST["precioIni"];
        $precioFin = $_POST["precioFin"];
        $kmIni = $_POST["kmIni"];
        $kmFin = $_POST["kmFin"];
        $anio = $_POST["anio"];
        $marcaId = $_POST["marcaId"];
        $modeloId = $_POST["modeloId"];
        $estadoId = $_POST["estadoId"];
        $descripcion = $_POST["descripcion"];


        $comando ="SELECT au.idAuto, au.enVenta, au.precio, au.marca as marcaid, marca.descripcion as marca, au.modelo as modeloid, modelo.descripcion as modelo," .
            " au.color as colorid, au.descripcion as color, au.anio, au.km, au.km, au.transmision as transmisionid, trans.descripcion as transmision, " .
            " au.estado as estadoid, est.nombre as estado, au.ciudad as ciudadid, mun.nombre as ciudad, au.descripcion, au.estatus," .
            " au.publicado, au.fechaCreacion, " .
            " (select idFoto from auto_fotos where idAuto = au.idAuto limit 1) as foto," .
            " (select GROUP_CONCAT(idFoto) from auto_fotos where idAuto = au.idAuto) AS fotos, au.nombreContacto, au.telefonoContacto, au.celularContacto, au.correoContacto, au.infoContacto " .
            " FROM  autos as au, cat_marca as marca, cat_modelo as modelo, cat_colores as color, cat_transmision as trans, estados as est, municipios as mun " .
            " WHERE  au.marca = marca.id " .
            " and au.modelo = modelo.id " .
            " and au.color = color.id " .
            " and au.transmision = trans.id " .
            " and au.estado = est.id " .
            " and au.ciudad = mun.id " .
            " and au.enVenta = 1 " .
            (($precioIni <=0 && $precioFin <=0)? "":" and precio between  ? AND ?") .
            (($anio <=0) ?"":" and anio = ? ") .
            (($kmIni <=0 && $kmFin<=0) ? "" : " and au.km between ? AND ? ") .
            (($marcaId <=0) ? "" : " and au.marca = ? ") .
            (($estadoId<=0) ? "" : " and au.estado = ? ")  .
            (($descripcion =='') ? "" :  " and au.descripcion like '%?%' ") .
            (($modeloId <= 0) ? "" :  " and au.modelo = ? ");

        $comando .= " ORDER BY au.fechaCreacion DESC ";

        //print_r($comando);



        $sentencia =ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        $paramNum = 1;

        if ($precioIni >0 && $precioFin >0) {
            $sentencia->bindParam($paramNum, $precioIni);
            $paramNum++;
            $sentencia->bindParam($paramNum, $precioFin);
            $paramNum++;
        }
        if ($anio >0) {
            $sentencia->bindParam($paramNum, $anio);
            $paramNum++;
        }
        if ($kmIni >0 && $kmFin>0) {
            $sentencia->bindParam($paramNum, $kmIni);
            $paramNum++;
            $sentencia->bindParam($paramNum, $kmFin);
            $paramNum++;
        }
        if ($marcaId >0) {
            $sentencia->bindParam($paramNum, $marcaId);
            $paramNum++;
        }
        if ($estadoId>0) {
            $sentencia->bindParam($paramNum, $estadoId);
            $paramNum++;
        }
        if ($descripcion !="") {
            $sentencia->bindParam($paramNum, $descripcion);
            $paramNum++;
        }
        if ($modeloId >0) {
            $sentencia->bindParam($paramNum, $modeloId);
        }

        if ($sentencia->execute()) {
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }


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

    private function registrar($auto)
    {
        try {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::ENVENTA . "," .
                self::PRECIO ."," .
                self::MARCA . "," .
                self::MODELO . ",".
                self::COLOR . ",".
                self::ANIO . ",".
                self::KM . ",".
                self::TRANSMISION . ",".
                self::ESTADO . ",".
                self::CIUDAD . ",".
                self::DESCRIPCION . ",".
                self::PLACA . ",".
                self::SERIE . ",".
                self::ESTATUS . ",".
                self::PUBLICADO . ",".
                self::MOTIVO_PRECIO.", nombreContacto, telefonoContacto, celularContacto, correoContacto, infoContacto)" .
                " VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $desc = json_decode($auto["descripcion"]);
            $infoContacto = json_decode($auto["infoContacto"]);
            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $auto["enVenta"]);
            $sentencia->bindParam(2, $auto["precio"]);
            $sentencia->bindParam(3, $auto["marca"]);
            $sentencia->bindParam(4, $auto["modelo"]);
            $sentencia->bindParam(5, $auto["color"]);
            $sentencia->bindParam(6, $auto["anio"]);
            $sentencia->bindParam(7, $auto["km"]);
            $sentencia->bindParam(8, $auto["transmision"]);
            $sentencia->bindParam(9, $auto["estado"]);
            $sentencia->bindParam(10, $auto["ciudad"]);
            $sentencia->bindParam(11, $desc);
            $sentencia->bindParam(12, $auto["placa"]);
            $sentencia->bindParam(13, $auto["serie"]);
            $sentencia->bindParam(14, $auto["estatus"]);
            $sentencia->bindParam(15, $auto["publicado"]);
            $sentencia->bindParam(16, $auto["motivo_precio"]);
            $sentencia->bindParam(17, $auto["nombreContacto"]);
            $sentencia->bindParam(18, $auto["telefonoContacto"]);
            $sentencia->bindParam(19, $auto["celularContacto"]);
            $sentencia->bindParam(20, $auto["correoContacto"]);
            $sentencia->bindParam(21, $infoContacto);

            //idAuto, enVenta, marca, modelo, color, anio, km, transmision, estado, ciudad, descripcion, estatus, publicado, fechaCreacion




            $resultado = $sentencia->execute();


            if ($resultado) {
                $idAuto = $pdo->lastInsertId();


                autosfeatures::registrar($auto["features"], $idAuto);
                autosfotos::registrar($auto["fotos"], $idAuto);


                if ($auto["idSubasta"] > 0) {
                    subastasautos::registrar($idAuto, $auto["idSubasta"]);

                    subastasautos::programarauto($idAuto, json_decode($auto["horaInicio"]), json_decode($auto["horaFin"]), $auto["idSubasta"]);
                }

                return $idAuto;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
        }
    }

    private function actualiza($auto)
    {
        try {
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "UPDATE " . self::NOMBRE_TABLA . " SET " .
            self::ENVENTA   . " = ?,".
            self::PRECIO  . " = ?,".
            self::MARCA   . " = ?,".
            self::MODELO  . " = ?,".
            self::COLOR  . " = ?,".
            self::ANIO  . " = ?,".
            self::KM  . " = ?,".
            self::TRANSMISION  . " = ?,".
            self::ESTADO  . " = ?,".
            self::CIUDAD  . " = ?,".
            self::DESCRIPCION  . " = ?,".
            self::PLACA  . " = ?,".
            self::SERIE  . " = ?,".
            self::ESTATUS  . " = ?,".
            self::PUBLICADO  . " = ?,".
            self::MOTIVO_PRECIO  . " = ?,".
            nombreContacto   . " = ?,".
            telefonoContacto  . " = ?,".
            celularContacto  . " = ?,".
            correoContacto  . " = ?,".
            infoContacto  . " = ?".
                " WHERE idAuto = ? ";


            $desc = json_decode($auto["descripcion"]);
            $infoContacto = json_decode($auto["infoContacto"]);
            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $auto["enVenta"]);
            $sentencia->bindParam(2, $auto["precio"]);
            $sentencia->bindParam(3, $auto["marca"]);
            $sentencia->bindParam(4, $auto["modelo"]);
            $sentencia->bindParam(5, $auto["color"]);
            $sentencia->bindParam(6, $auto["anio"]);
            $sentencia->bindParam(7, $auto["km"]);
            $sentencia->bindParam(8, $auto["transmision"]);
            $sentencia->bindParam(9, $auto["estado"]);
            $sentencia->bindParam(10, $auto["ciudad"]);
            $sentencia->bindParam(11, $desc);
            $sentencia->bindParam(12, $auto["placa"]);
            $sentencia->bindParam(13, $auto["serie"]);
            $sentencia->bindParam(14, $auto["estatus"]);
            $sentencia->bindParam(15, $auto["publicado"]);
            $sentencia->bindParam(16, $auto["motivo_precio"]);
            $sentencia->bindParam(17, $auto["nombreContacto"]);
            $sentencia->bindParam(18, $auto["telefonoContacto"]);
            $sentencia->bindParam(19, $auto["celularContacto"]);
            $sentencia->bindParam(20, $auto["correoContacto"]);
            $sentencia->bindParam(21, $infoContacto);
            $sentencia->bindParam(22, $auto["idAuto"]);


            if ($sentencia->execute()) {
                $idAuto = $auto["idAuto"];

                if (count($auto["features"]) > 0) {
                    autosfeatures::elimina($idAuto);
                    autosfeatures::registrar($auto["features"], $idAuto);
                }

                if (count($auto["fotos"]) > 0) {
                    autosfotos::elemina($idAuto);
                    autosfotos::registrar($auto["fotos"], $idAuto);
                }

                subastasautos::programarauto($idAuto, json_decode($auto["horaInicio"]), json_decode($auto["horaFin"]), $auto["idSubasta"]);
                if ($sentencia->rowCount()) {
                    return true;
                } else {
                    //echo $query->debugDumpParams();
                    //print_r( $query->errorInfo());
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();

            //throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
        }
    }
}
