<?php

class subastasempresa
{
    
    public function __construct($idSubasta =0, $idEmpresa = 0)
    {
        $this->idSubasta = $idEmpresa;
        $this->idEmpresa = $idEmpresa;
 
       
    }

    // Datos de la tabla "usuario"
    const NOMBRE_TABLA = "subastaempresa";
    const ID_SUBASTA = "idSubasta";
    const ID_EMPRESA = "idEmpresa";
   
    const SIN_RESULTADOS = "No se encontraron resultados";
    const LISTO = "OK";
    const ESTADO_CREACION_EXITOSA = "OK";
    const ESTADO_CREACION_FALLIDA = "ERROR";

    public static function post($peticion)
    {
     
      
        if ($peticion[0] == 'listar') {
            return self::listarEmpresas();
        }else if ($peticion[0] == 'guardar') {
            return self::registrarOut();
        }
        else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }   

    
    private function listarEmpresas()
    {
        $cuerpo = file_get_contents('php://input');
        $empresas = json_decode($cuerpo);
        
        $resultado = self::listar($empresas);
       
        switch (sizeof($resultado)) {
            case 0:
               http_response_code(200);
               throw new ExcepcionApi(self::SIN_RESULTADOS, "OK",200, null);
               break;
            
            default:
                http_response_code(200);
                return $resultado;
        }
        
    }
    private function listar($datosListar)
    {
        $estatus = $_POST['estatus'];
        
        $comando = "SELECT " .
            self::ID_EMPRESA . "," .
            self::DESCRIPCION . "," .
            self::ESTATUS .
            " FROM " . self::NOMBRE_TABLA .
            (($estatus >= 0) ? " WHERE " . self::ESTATUS . "=?" : "");



        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        
        if($estatus >= 0){
            $sentencia->bindParam(1, $estatus);
        }

        if ($sentencia->execute())
            return $sentencia->fetchall(PDO::FETCH_ASSOC);
        else
            return null;
    }

    public static function eliminaEmpresas($idSubasta){
        try {

            
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                $comando = "DELETE FROM  " . self::NOMBRE_TABLA . " WHERE  " .
                    self::ID_SUBASTA . " = ?";
                    

                


                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $idSubasta);
                       

                $resultado = $sentencia->execute();

              
                if ($resultado) {
                    return $resultado;
                } else {
                    return -1;
                }

                        
            
            return -1;
        } catch (PDOException $e) {
            
            print_r($e);
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);
            
        }

    }    

    public static function registrar($empresas, $idSubasta)
    {
        
        $resultado = false;
        try {

            
            foreach ($empresas as $v) {

                $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

                // Sentencia INSERT
                $comando = "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    self::ID_SUBASTA . "," .
                    self::ID_EMPRESA . ")" .
                    " VALUES(?,?)";

                


                $sentencia = $pdo->prepare($comando);
                $sentencia->bindParam(1, $idSubasta);
                $sentencia->bindParam(2, $v);
             

                $resultado = $sentencia->execute();
               

                        
            }

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

    
    
}