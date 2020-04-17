<?php

class invitacion
{
	const ESTADO_URL_INCORRECTA = "Url incorrecta";
	public function __construct($idUsuario = 0, $idSubasta = 0)
    {
        $this->idUsuario = $idUsuario;
        $this->idSubasta = $idSubasta;
        $this->fecha = time();
        $this->estatus = 0;
    }
    public static function post($peticion)
    {
        if ($peticion[0] == 'crear') {
            return self::crear($_POST);
        } else {
            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, "Url mal formada", 400);
        }
    }
    public static function crear($invitacion)
    {

        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "INSERT INTO invitacion ( idUsuario, idSubasta )  VALUES(?,?) ON DUPLICATE KEY UPDATE idSubasta=idSubasta;";


            $sentencia = $pdo->prepare($comando);


            $sentencia->bindParam(1, $invitacion->idUsuario);

            $sentencia->bindParam(2, $invitacion->idSubasta);


            $resultado = $sentencia->execute();
            $usuarioid = $pdo->lastInsertId();

            if ($resultado) {
            	return true;

                return false;
            } else {
                return false;
            }
        } catch (PDOException $e) {

            throw new ExcepcionApi(self::ESTADO_URL_INCORRECTA, $e->getMessage(), 400);

        }

    }

}
