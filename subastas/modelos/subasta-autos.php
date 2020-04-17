<?php

class subastasautos
{
    
    public function __construct($idSubasta =0, $idAuto = 0)
    {
        $this->idSubasta = $idSubasta;
        $this->idAuto = $idAuto;
 
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "subastas_autos";
    const ID_SUBASTA = "subastaId";
    const ID_AUTO = "autoId";
   
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
     
      
        if ($peticion[0] == 'listar') {
            return self::listarEmpresas();
        }else if ($peticion[0] == 'guardar') {
            return self::registrar();
        }else if ($peticion[0] == 'cancelar') {
            return self::cancelar();
        }else if ($peticion[0] == 'programar') {
            return self::programar();
        }else if ($peticion[0] == 'info') {
            return self::info($_POST["id"]);
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

    public function info($id){
          try{

            $comando =  " select hora_inicio, hora_fin, estatus, motivo from subastas_autos where autoId = ?  ";
            $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
             $sentencia->bindParam(1, $id);
            if ($sentencia->execute())
                return $sentencia->fetch(PDO::FETCH_ASSOC);
            else
                return null;
    }catch(Excepcion $e){
        throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
    }

    }
    public function programarauto($id, $fechaIni, $fechaFin, $idSubasta){
        try{    
            $comando = "UPDATE  subastas_autos set hora_inicio = ? , hora_fin  = ? where autoId = ? and subastaId = ?" ;
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();    
            
            

            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $fechaIni);
            $sentencia->bindParam(2, $fechaFin);
            $sentencia->bindParam(3, $id);
            $sentencia->bindParam(4, $idSubasta);
           
        
            $sentencia->execute();
           
        }catch(PDOException $e){
           
        }  
    }

    public function programar(){
        $json = json_decode($_POST["datos"]);
         
        $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

        foreach ($json as &$o) {

            $comando = "UPDATE  subastas_autos set hora_inicio = ? , hora_fin  = ? where autoId = ? and subastaId = ?" ;
                

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $o->fechaIni);
            $sentencia->bindParam(2, $o->fechaFin);
            $sentencia->bindParam(3, $o->idAuto);
            $sentencia->bindParam(4, $o->idSubasta);

            $resultado = $sentencia->execute();
           

           $comando = "UPDATE  subastas set revisada = 1 where  idSubasta = ?" ;
                

            $sentencia = $pdo->prepare($comando);
          
            $sentencia->bindParam(1, $o->idSubasta);

            $resultado = $sentencia->execute();
        }

    } 

    public static function registrar($idAuto, $idSubasta)
    {
        
        $resultado = false;
        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            
            $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                self::ID_SUBASTA . "," .
                self::ID_AUTO . ")" .
                " VALUES(?,?)";

            $sentencia = $pdo->prepare($comando);
            $sentencia->bindParam(1, $idSubasta);
            $sentencia->bindParam(2, $idAuto);
         

            $resultado = $sentencia->execute();
                        
            

             if ($resultado) {
                return $resultado;
            } else {
                return false;
            }
            
        } catch (PDOException $e) {
            
            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }
    public static function cancelar(){
        if(!usuarios::ValidaSesion($_SESSION["claveapi"], $_SESSION["idusuario"])){
            throw new ExcepcionApi("El usuario no tiene permisos de ejecutar esta operación", "error", 500);
        }

        try{
            
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            $comando = "update " . self::NOMBRE_TABLA . " set estatus =  -1, motivo = ? where autoId = ? and subastaId = ? ";
            $sentencia = $pdo->prepare($comando);
           
            $sentencia->bindParam(1, $_POST["motivo"]);
            $sentencia->bindParam(2, $_POST["idauto"]);
            $sentencia->bindParam(3, $_POST["idSubasta"]);
            $resultado = $sentencia->execute();
                        
            if ($resultado) {
                return true;
            } else {
                return false;
            }


        }catch(Excepcion $e){
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 500);

        }
    }

    
    
}