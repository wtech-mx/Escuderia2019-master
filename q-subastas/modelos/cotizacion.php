<?php

class cotizacion
{


    // Datos de la tabla "cotizacion"
    const NOMBRE_TABLA = "cotizacion";
    const NOMBRE = "Nombre";
    const CORREO = "Correo";
    const TELEFONO = "Telefono";
    const IDCOTIZACION = "idCotizacion";
    const IDUSUAIO = "idUsuario";
    const MARCA = "Marca";
    const MODELO = "Modelo";
    const TIPO = "Tipo";
    const ESTATUS = "Estatus";
    const FECHA = "fecha";
    const COMENTARIO = "comentario";
    const FECHACRACION = "fechaCreacion";
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_ACTUALIZACION_EXITOSA = "ACTUALIZADO";
    const ESTADO_ELIMINACION_EXITOSA = "BORRADO";
    const ESTADO_CREACION_FALLIDA = "ERROR";


    public static function post($peticion)
    {
      
        
        if ($peticion[0] == 'registro') {
            return self::registrar();
        } if ($peticion[0] == 'listar') {

            return self::listar();
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

 
    private function crear($cotiza)
    {
        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::NOMBRE . "," .
                self::CORREO . "," .
                self::TELEFONO . "," .
                self::MARCA . "," .
                self::MODELO . "," .
                self::TIPO . "," .
                self::ESTATUS . ",".
                self::COMENTARIO . ")" .
                " VALUES(?,?,?,?,?,?,?,?)";
                
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $cotiza["nombre"]);
                       
            $sentencia->bindParam(2, $cotiza["correo"]);
                       
            $sentencia->bindParam(3, $cotiza["telefono"]);
            
            $sentencia->bindParam(4, $cotiza["marca"]);
            
            $sentencia->bindParam(5, $cotiza["modelo"]);

            $sentencia->bindParam(6, $cotiza["tipo"]);

            $Estatus = 0;
            $sentencia->bindParam(7, $Estatus);

            $comentario = json_decode ($cotiza["comentario"]);
            $sentencia->bindParam(8, $comentario);
 
            $resultado = $sentencia->execute();
           
            if ($resultado) {
                 $cotizacionId =$pdo->lastInsertId();

               

                   cotizacionservicios::registrarCS($cotizacionId,$cotiza["subServicios"]);
               return $cotizacionId;
            } else {
                return self::ESTADO_CREACION_FALLIDA;
            }
        } catch (PDOException $e) {
            
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }

   
    private function registrar()
    {
        $cuerpo = file_get_contents('php://input');
        $usuario = json_decode($cuerpo);

        $resultado = self::crear($_POST);

        switch (sizeof($resultado)) {
            case 0:
                http_response_code(200);
               throw new ExcepcionApi(self::SIN_RESULTADOS, "OK",200, null);
                break;
            
            default:
                 http_response_code(200);
                 return $resultado;
                break;
        }


    }

    private function listar(){
        $resultado = self::listaCotizaciones($_POST);

        switch (sizeof($resultado)) {
            case '0':
                http_response_code(200);
                throw new ExcepcionApi(self::SIN_RESULTADOS,"OK",200,null);
                
                break;
            
            default:
                http_response_code(200);
                return $resultado;
                break;
        }
    }

    private function listaCotizaciones($correoUsua){

        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

        $correo = $correoUsua["correoUsua"];
        $texto = $correoUsua["descripcion"];
        $fechaIni = $correoUsua["fechaIni"];
        $fechaFin = $correoUsua["fechaFin"];
        $esAdmin = $correoUsua["esAdmin"];

       $fechasValidas = False;
           if($fechaIni != null && $fechaFin != null){
                $fechasValidas = True;
           }
  

        $comando = "SELECT idCotizacion, Nombre, Correo, Telefono, Marca, Modelo, Tipo, Estatus, fecha, comentario,
(select GROUP_CONCAT(sub.nombre) from Cotizacion_Servicios cs, subservicios sub where cs.idCotizacion = cot.idCotizacion and cs.idSubServicio = sub.idSubservicio) as subservicios
FROM cotizacion cot ". 
                    " WHERE " . (($texto =="") ? ( self::NOMBRE . " like  '%'") : ( self::NOMBRE . " like '%" . $texto . "%'") ) .
                    (($esAdmin != 1) ? (" AND " . self::CORREO . " =  '" . $correo ."'") :(" ") ).
                    (($fechasValidas) ? (" AND " . self::FECHACRACION ." BETWEEN STR_TO_DATE('" . $fechaIni . "', '%m/%d/%Y') AND STR_TO_DATE('". $fechaFin . "', '%m/%d/%Y') " ) : (" ") ). " order by fecha desc ";

                    


        
        $sentencia = $pdo->prepare($comando);

         if ($sentencia->execute()) {
             $resultado = $sentencia->fetchall(PDO::FETCH_ASSOC);

        
             return $resultado;
         }else{
            return null;
         }

    }




  
    
}