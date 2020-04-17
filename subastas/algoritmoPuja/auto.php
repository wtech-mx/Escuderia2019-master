<?php
/**
 * Created by PhpStorm.
 * User: lalo
 * Date: 10/08/18
 * Time: 7:58 PM
 */

class auto
{
    var $idAuto;
    var $precio;

    public function __toString()
    {
        return "Id auto ".(string)$this->idAuto." precio ".(string)$this->precio;
    }
}