<?php

class subservicio
{


    // Datos de la tabla "cotizacion"
    const NOMBRE_TABLA = "subservicios";
    const IDSERVICIO = "idServicio";
    const NOMBRE = "nombre";
    const REQUISITOS = "Requisitos";
    const ESTATUS = "estatus";
    const IDSUBSERVICIO = "idSubservicio";
    const SINRESULTADOS = "No se encontraron resultados.";
    const ESTADO_CREACION_EXITOSA ="OK";
    const ESTADO_CREACION_FALLIDA ="ERROR";
    const ESTADO_FALLA_DESCONOCIDA = "Falla desconocida";
     const ESTADO_ACTUALIZACION_EXITOSA = "Se actualizo Correctamente.";
      const ESTADO_ACTUALIZACION_FALLIDA = "Ocurrio un error al actualizar la información.";
    const ESTADO_BD_ERROR = "Ocurrio un error en BD.";
    public static function post($peticion)
    {
        if ($peticion[0] == 'registro') {
            return self::registrar();
        }else if ($peticion[0] == 'listar') {
            return self::listarSubservicio();
        } else if ($peticion[0] == 'actualizar') {
            return self::actualizarSubServicio();
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

 
    private function crear($subServicio)
    {
        try {
           
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::IDSERVICIO . "," .
                self::NOMBRE . "," .
                self::REQUISITOS . "," .
                self::ESTATUS .")" .
                " VALUES(?,?,?,?)";
                
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $subServicio["idServicio"]);
                       
            $sentencia->bindParam(2, $subServicio["nombre"]);
                       
            $sentencia->bindParam(3, $subServicio["requisitos"]);
                       
            $sentencia->bindParam(4, $subServicio["estatus"]);
            

 
            $resultado = $sentencia->execute();
           
            if ($resultado) {
               return self::ESTADO_CREACION_EXITOSA;
            } else {
                return self::ESTADO_CREACION_FALLIDA;
            }
        } catch (PDOException $e) {
            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }

   
    private function registrar()
    {
        $cuerpo = file_get_contents('php://input');
       
        $resultado = self::crear($_POST);

        switch ($resultado) {
            case self::ESTADO_CREACION_EXITOSA:
                //self::registraServicios($_POST);
               http_response_code(200);
               return 1;
               
                break;
            case self::ESTADO_CREACION_FALLIDA:
                throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
                break;
            default:
                throw new ExcepcionApi(self::ESTADO_FALLA_DESCONOCIDA, "Falla desconocida", 400);
        }
    }

    private function listarSubservicio(){

        try{
            $cuerpo = file_get_contents('php://input');
            $subServicios = json_decode($cuerpo);
            $resultado = self::listar($subServicios);

            http_response_code(200);
            return $resultado;
                    
            
        }catch(Exception $e){
            return new ExcepcionApi(self::SINRESULTADOS,"OK",200,null);
        }

    }

    private function listar($datosListar){

        $idServicio =$_POST['idServicio'];
        $estatus =$_POST['estatus'];
        $comando ="SELECT " .
            self::IDSUBSERVICIO . "," . 
            self::IDSERVICIO . "," .
            self::NOMBRE . "," . 
            self::REQUISITOS . ",".
            self::ESTATUS . 
            " FROM " . self::NOMBRE_TABLA;
            $condicion="";
            if (($estatus >0) ||($idServicio > 0))
                $comando = $comando . " WHERE ";
          
            if ($estatus >0)  {
                $condicion = $condicion . self::ESTATUS ." =? ";
            }
            if ($idServicio) {
                if(strlen($condicion))
                    $condicion = $condicion . " AND ";

                $condicion = $condicion . self::IDSERVICIO ." =? ";
            }
            $comando = $comando . $condicion;
         

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);

        if ($estatus > 0) {
                   $sentencia->bindParam(1,$estatus);
                }        
        if ($idServicio >0) {
            $sentencia->bindParam((($estatus > 0) ? 2:1),$idServicio);
        }

        if ($sentencia->execute())
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        else
            return null;
        
    } 

    private function actualizarSubServicio(){
        
         $cuerpo = file_get_contents('php://input');
         $subServicio = json_decode($cuerpo);

        
         $resultado =  self::factualizar($_POST);
         
         switch ($resultado) {
             case self::ESTADO_ACTUALIZACION_EXITOSA:
                 http_response_code(200);
                 return '1';
                 break;
            case self::ESTADO_ACTUALIZACION_FALLIDA:
                return '0';
                break;
             default:
                 throw new ExcepcionApi(self::ESTADO_CREACION_FALLIDA, "Ha ocurrido un error");
         }

    }   

    private function factualizar($ss){
        
         try{
           // print_r($subservicio);
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

        $idSubServicio = $ss["idSubServicio"];
        $idServicio = $ss["idServicio"];
        $nombre = $ss["nombre"];
        $Requisitos = $ss["requisitos"];
        $estatus = $ss["estatus"];

        $comando = "UPDATE " . self::NOMBRE_TABLA .
                " SET " . self::NOMBRE . "=?" . "," .
                self::REQUISITOS . "=?"  ."," .
                self::ESTATUS . "=?" . 
                " WHERE " . self::IDSUBSERVICIO . "=?" . 
                " AND " . self::IDSERVICIO . "=?";
                
        $sentencia = $pdo->prepare($comando);
        
        $sentencia->bindParam(1,$nombre);
        $sentencia->bindParam(2,$Requisitos);
        $sentencia->bindParam(3,$estatus);
        $sentencia->bindParam(4,$idSubServicio);
        $sentencia->bindParam(5,$idServicio);


 //$comando = "UPDATE subservicios SET nombre='Afinacion de ',Requisitos='esto',estatus=0 WHERE idSubservicio=1 AND idServicio=1";
       // $sentencia =$pdo->prepare($comando);

         $resultado = $sentencia->execute();
         //$ColAfected = $sentencia->rowCount();
        if($resultado){

            return  self::ESTADO_ACTUALIZACION_EXITOSA;

        }
        else{
            return self::ESTADO_ACTUALIZACION_FALLIDA;
        }
     }catch(PDOException $e){
        print_r($e);
         throw new ExcepcionApi(ESTADO_BD_ERROR,$e->getMessage(), 400);
        
     }


    }
}