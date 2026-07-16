<?php

session_start();

require_once "../modelos/Avance.php";
require_once "../modelos/Auditoria.php";
require_once "../config/permisos.php";

verificarPermiso("obras");

$avance = new Avance();
$auditoria = new Auditoria();

$accion = $_GET["accion"] ?? "listar";

switch ($accion) {

    case "listar":

        $id_obra = $_GET["id_obra"];

        $avances = $avance->obtenerPorObra($id_obra);
        $cantidad = $avance->contar($id_obra);
        $primero = $avance->primero($id_obra);
        $ultimo = $avance->ultimo($id_obra);

        require_once "../vistas/obras/avances/index.php";

    break;


    case "crear":

        $id_obra = $_GET["id_obra"];

        require_once "../vistas/obras/avances/crear.php";

    break;


    case "guardar":

        $datos = [
            "id_obra" => $_POST["id_obra"],
            "fecha" => $_POST["fecha"],
            "descripcion" => $_POST["descripcion"]
        ];

        $avance->guardar($datos);

        $id_registro = $avance->ultimoInsertado();

        $auditoria->registrar([
            "id_usuario" => $_SESSION["usuario"]["id"],
            "accion" => "INSERTAR",
            "tabla_afectada" => "avance_diario",
            "id_registro" => $id_registro,
            "descripcion" => "Se registró un nuevo avance diario en la obra ID " . $_POST["id_obra"]
        ]);

        header("Location: AvanceController.php?accion=listar&id_obra=" . $_POST["id_obra"]);

        exit();

    break;


    case "editar":

        $id = $_GET["id"];

        $registro = $avance->buscarPorId($id);

        require_once "../vistas/obras/avances/editar.php";

    break;


    case "actualizar":

        $datos = [
            "id_avance_diario" => $_POST["id_avance_diario"],
            "fecha" => $_POST["fecha"],
            "descripcion" => $_POST["descripcion"]
        ];

        $avance->actualizar($datos);

        $auditoria->registrar([
            "id_usuario" => $_SESSION["usuario"]["id"],
            "accion" => "EDITAR",
            "tabla_afectada" => "avance_diario",
            "id_registro" => $_POST["id_avance_diario"],
            "descripcion" => "Se modificó un avance diario de la obra ID " . $_POST["id_obra"]
        ]);

        header("Location: AvanceController.php?accion=listar&id_obra=" . $_POST["id_obra"]);

        exit();

    break;


    case "eliminar":

        $id = $_GET["id"];

        $registro = $avance->buscarPorId($id);

        $avance->eliminar($id);

        $auditoria->registrar([
            "id_usuario" => $_SESSION["usuario"]["id"],
            "accion" => "ELIMINAR",
            "tabla_afectada" => "avance_diario",
            "id_registro" => $id,
            "descripcion" => "Se eliminó un avance diario de la obra ID " . $registro["id_obra"]
        ]);

        header("Location: AvanceController.php?accion=listar&id_obra=" . $registro["id_obra"]);

        exit();

    break;

}