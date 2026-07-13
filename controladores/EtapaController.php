<?php

require_once "../modelos/Etapa.php";

class EtapaController
{

    private $etapa;

    public function __construct()
    {
        $this->etapa = new Etapa();
    }

    public function listar()
    {
        $id_obra = $_GET["id_obra"];
        $etapas = $this->etapa->obtenerPorObra($id_obra);
        require_once "../vistas/obras/etapas/index.php";
    }

    public function crear()
    {
        $id_obra = $_GET["id_obra"];
        require_once "../vistas/obras/etapas/crear.php";
    }


    public function guardar()
    {

        $datos = [

            "id_obra" => $_POST["id_obra"],
            "nombre_etapa" => $_POST["nombre_etapa"],
            "descripcion" => $_POST["descripcion"],
            "fecha_inicio" => $_POST["fecha_inicio"],
            "fecha_fin" => $_POST["fecha_fin"],
            "estado" => $_POST["estado"]
        ];
        $this->etapa->crear($datos);
        header(
            "Location: EtapaController.php?accion=listar&id_obra=" . $_POST["id_obra"]
        );
        exit();
    }

    public function editar()
    {
        $id = $_GET["id"];
        $etapa = $this->etapa->buscarPorId($id);
        require_once "../vistas/obras/etapas/editar.php";
    }

    public function actualizar()
    {
        $datos = [
            "id_etapa" => $_POST["id_etapa"],
            "nombre_etapa" => $_POST["nombre_etapa"],
            "descripcion" => $_POST["descripcion"],
            "fecha_inicio" => $_POST["fecha_inicio"],
            "fecha_fin" => $_POST["fecha_fin"],
            "estado" => $_POST["estado"]
        ];
        $this->etapa->actualizar($datos);
        header(
            "Location: EtapaController.php?accion=listar&id_obra=" . $_POST["id_obra"]
        );
        exit();
    }
}

$controlador = new EtapaController();

$accion = "";
if (isset($_GET["accion"])) {
    $accion = $_GET["accion"];
}

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
}

switch ($accion) {

    case "listar":
        $controlador->listar();
        break;

    case "crear":
        $controlador->crear();
        break;

    case "guardar":
        $controlador->guardar();
        break;

    case "editar":
        $controlador->editar();
        break;

    case "actualizar":
        $controlador->actualizar();
        break;
}
