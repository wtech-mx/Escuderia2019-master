<?php
/**
 * Created by PhpStorm.
 * User: lalo
 * Date: 09/08/18
 * Time: 8:14 PM
 */

class ganador
{
    var $idUsuario;
    var $oferta;
    var $idPuja;

    public function __toString()
    {
        return "Id usuario ganador ".(string)$this->idUsuario." oferta ".(string)$this->oferta." idPuja ".(string)$this->idPuja;
    }
}

?>