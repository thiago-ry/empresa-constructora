<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/Obra.php";

class ObraController
{
    private $auditoria;
    private $obra;

    public function __construct()
    {
        $this->auditoria = new Auditoria();
        $this->obra = new Obra();
    }

    /*
    ==========================
        AGREGAR
    ==========================
    */

    public function agregar()
    {
        
        session_start();

        $datos = [

            "id_usuario" => $_POST["id_usuario"],
            "nombre_obra" => $_POST["nombre_obra"],
            "direccion" => $_POST["direccion"],
            "descripcion" => $_POST["descripcion"],
            "fecha_inicio" => $_POST["fecha_inicio"],
            "fecha_fin" => $_POST["fecha_fin"],
            "estado" => $_POST["estado"]

        ];

        $id_obra = $this->obra->agregar($datos);

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "INSERTAR",

            "tabla_afectada" => "obra",

            "id_registro" => $id_obra,

            "descripcion" => "Registró la obra: " . $datos["nombre_obra"]

        ]);

        header("Location: ../vistas/obras/index.php");
        exit();
    }

    /*
    ==========================
        EDITAR
    ==========================
    */

    public function editar()
    {
        session_start();

        $datos = [

            "id_obra" => $_POST["id_obra"],
            "id_usuario" => $_POST["id_usuario"],
            "nombre_obra" => $_POST["nombre_obra"],
            "direccion" => $_POST["direccion"],
            "descripcion" => $_POST["descripcion"],
            "fecha_inicio" => $_POST["fecha_inicio"],
            "fecha_fin" => $_POST["fecha_fin"],
            "estado" => $_POST["estado"]

        ];

        $this->obra->editar($datos);

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "EDITAR",

            "tabla_afectada" => "obra",

            "id_registro" => $datos["id_obra"],

            "descripcion" => "Modificó la obra " . $datos["nombre_obra"]

        ]);

        header("Location: ../vistas/obras/index.php");
        exit();
    }

    /*
    ==========================
        BAJA
    ==========================
    */

    public function eliminar()
    {
        session_start();

        $id = $_GET["id"];

        $this->obra->bajaLogica($id);

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "BAJA",

            "tabla_afectada" => "obra",

            "id_registro" => $id,

            "descripcion" => "Desactivó una obra"

        ]);

        header("Location: ../vistas/obras/index.php");
        exit();
    }

    /*
    ==========================
        ACTIVAR
    ==========================
    */

    public function activar()
    {
        session_start();

        $id = $_GET["id"];

        $this->obra->activarObra($id);

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "ACTIVAR",

            "tabla_afectada" => "obra",

            "id_registro" => $id,

            "descripcion" => "Activó nuevamente una obra"

        ]);

        header("Location: ../vistas/obras/index.php");
        exit();
    }
}

$controlador = new ObraController();

if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

        case "agregar":
            $controlador->agregar();
            break;

        case "editar":
            $controlador->editar();
            break;
    }
}

if (isset($_GET["accion"])) {

    switch ($_GET["accion"]) {

        case "baja":
            $controlador->eliminar();
            break;

        case "activar":
            $controlador->activar();
            break;
    }
}
