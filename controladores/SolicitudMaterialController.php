<?php

require_once __DIR__ . "/../modelos/SolicitudMaterial.php";
require_once __DIR__ . "/../modelos/Material.php";
require_once __DIR__ . "/../modelos/Obra.php";
require_once __DIR__ . "/../config/permisos.php";
require_once __DIR__ . "/../modelos/Auditoria.php";

verificarPermiso("obras");

$solicitudModel = new SolicitudMaterial();
$materialModel = new Material();
$obraModel = new Obra();
$auditoria = new Auditoria();

$accion = $_GET["accion"] ?? "listar";

switch ($accion) {

    /*
    |--------------------------------------------------------------------------
    | LISTAR SOLICITUDES
    |--------------------------------------------------------------------------
    */

    case "listar":

        $id_obra = $_GET["id_obra"] ?? 0;

        if (!$id_obra) {
            die("Obra no especificada.");
        }

        $obra = $obraModel->buscarPorId($id_obra);

        if (!$obra) {
            die("La obra no existe.");
        }

        $solicitudes = $solicitudModel->obtenerPorObra($id_obra);

        require __DIR__ . "/../vistas/obras/materiales/index.php";

        break;


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR FORMULARIO NUEVA SOLICITUD
    |--------------------------------------------------------------------------
    */

    case "crear":

        $id_obra = $_GET["id_obra"] ?? 0;

        if (!$id_obra) {
            die("Obra no especificada.");
        }

        $obra = $obraModel->buscarPorId($id_obra);

        if (!$obra) {
            die("La obra no existe.");
        }

        $materiales = $materialModel->obtenerTodos();

        require __DIR__ . "/../vistas/obras/materiales/crear.php";

        break;


    /*
    |--------------------------------------------------------------------------
    | GUARDAR SOLICITUD
    |--------------------------------------------------------------------------
    */

    case "guardar":

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../index.php");
            exit;
        }

        $id_obra = $_POST["id_obra"] ?? 0;
        $materiales = $_POST["material"] ?? [];
        $cantidades = $_POST["cantidad"] ?? [];

        if (!$id_obra) {
            die("Obra no especificada.");
        }

        if (empty($materiales) || empty($cantidades)) {
            die("Debe agregar al menos un material.");
        }

        /*
        |--------------------------------------------------------------------------
        | Crear cabecera de solicitud
        |--------------------------------------------------------------------------
        */

        $id_solicitud = $solicitudModel->crear($id_obra);

        if (!$id_solicitud) {
            die("No se pudo crear la solicitud.");
        }

        /*
        |--------------------------------------------------------------------------
        | Agregar materiales
        |--------------------------------------------------------------------------
        */

        $cantidadDetalles = 0;

        foreach ($materiales as $indice => $id_material) {

            $cantidad = $cantidades[$indice] ?? 0;

            if (
                empty($id_material) ||
                !is_numeric($cantidad) ||
                (float)$cantidad <= 0
            ) {
                continue;
            }

            $resultado = $solicitudModel->agregarDetalle(
                $id_solicitud,
                $id_material,
                $cantidad
            );

            if (!$resultado) {

                $solicitudModel->eliminar($id_solicitud);

                die("No se pudo agregar uno de los materiales.");
            }

            $cantidadDetalles++;
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar que haya al menos un detalle válido
        |--------------------------------------------------------------------------
        */

        if ($cantidadDetalles === 0) {

            $solicitudModel->eliminar($id_solicitud);

            die("Debe indicar al menos un material con una cantidad válida.");
        }

        /*
        |--------------------------------------------------------------------------
        | Auditoría
        |--------------------------------------------------------------------------
        */

        $auditoria->registrar([
            "id_usuario" => $_SESSION["usuario"]["id"],
            "accion" => "INSERTAR",
            "tabla_afectada" => "solicitud_material",
            "id_registro" => $id_solicitud,
            "descripcion" =>
                "Registró una nueva solicitud de materiales para la obra ID "
                . $id_obra
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redireccionar
        |--------------------------------------------------------------------------
        */

        header(
            "Location: SolicitudMaterialController.php?accion=listar&id_obra="
            . $id_obra
        );

        exit;

        break;


    /*
    |--------------------------------------------------------------------------
    | VER DETALLE
    |--------------------------------------------------------------------------
    */

    case "detalle":

        $id_solicitud = $_GET["id"] ?? 0;

        if (!$id_solicitud) {
            die("Solicitud no especificada.");
        }

        $solicitud = $solicitudModel->obtenerPorId($id_solicitud);

        if (!$solicitud) {
            die("La solicitud no existe.");
        }

        $obra = $obraModel->buscarPorId(
            $solicitud["id_obra"]
        );

        $detalle = $solicitudModel->obtenerDetalle(
            $id_solicitud
        );

        require __DIR__ . "/../vistas/obras/materiales/detalle.php";

        break;


    /*
    |--------------------------------------------------------------------------
    | LISTAR SOLICITUDES PARA GERENTE
    |--------------------------------------------------------------------------
    */

    case "gerente":

        $solicitudes = $solicitudModel->obtenerTodas();

        require __DIR__ . "/../vistas/obras/materiales/todas.php";

        break;


    /*
    |--------------------------------------------------------------------------
    | ACCIÓN DESCONOCIDA
    |--------------------------------------------------------------------------
    */

    default:

        die("Acción no válida.");

        break;
}