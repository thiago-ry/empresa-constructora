<?php

require_once "../modelos/Reporte.php";


class ReporteController
{

    private $reporte;
    private $auditoria;


    public function __construct()
    {
        $this->reporte = new Reporte();
        $this->auditoria = new Auditoria();
    }



    public function usuarios()
    {

        $usuarios = $this->reporte->usuarios();


        require "../vistas/reportes/usuarios.php";
    }
    public function obras()
    {
        $obras = $this->reporte->obras();

        require "../vistas/reportes/obras.php";
    }

    public function auditoria()
    {
        $filtros = [
            "buscar" => $_GET["buscar"] ?? "",
            "tipo" => $_GET["tipo"] ?? "",
            "accion" => $_GET["accion_filtro"] ?? "",
            "modulo" => $_GET["modulo"] ?? "",
            "fecha_desde" => $_GET["fecha_desde"] ?? "",
            "fecha_hasta" => $_GET["fecha_hasta"] ?? ""
        ];

        $registros = $this->auditoria->obtenerReporte($filtros);

        require "../vistas/reportes/auditoria.php";
    }
}


$controlador = new ReporteController();


if (isset($_GET["accion"])) {

    switch ($_GET["accion"]) {

        case "usuarios":
            $controlador->usuarios();
            break;

        case "obras":
            $controlador->obras();
            break;

        case "auditoria":
            $controlador->auditoria();
            break;
    }
}
