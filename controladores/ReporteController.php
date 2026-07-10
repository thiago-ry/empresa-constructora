<?php

require_once "../modelos/Reporte.php";


class ReporteController
{

    private $reporte;


    public function __construct()
    {
        $this->reporte = new Reporte();
    }



    public function usuarios()
    {

        $usuarios = $this->reporte->usuarios();


        require "../vistas/reportes/usuarios.php";

    }

}



$controlador = new ReporteController();


if(isset($_GET["accion"]))
{

    switch($_GET["accion"])
    {

        case "usuarios":

            $controlador->usuarios();

        break;

    }

}
