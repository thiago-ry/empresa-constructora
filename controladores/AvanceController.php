
<?php

session_start();

require_once "../modelos/Avance.php";
require_once "../modelos/Auditoria.php";
require_once "../modelos/Obra.php";
require_once "../config/permisos.php";

verificarPermiso("obras");

$avance = new Avance();
$auditoria = new Auditoria();
$obra = new Obra();

$accion = $_GET["accion"] ?? "listar";

$id_usuario = $_SESSION["usuario"]["id_usuario"]
    ?? $_SESSION["usuario"]["id"]
    ?? 0;

$id_rol = $_SESSION["usuario"]["id_rol"] ?? 0;


/*
|--------------------------------------------------------------------------
| OBRA DEL CAPATAZ
|--------------------------------------------------------------------------
*/

$obraCapataz = null;
$id_obra_capataz = 0;

if ($id_rol == 7) {

    $obraCapataz = $obra->obtenerObraPorCapataz($id_usuario);

    if (!$obraCapataz) {
        die("No tenés una obra asignada.");
    }

    $id_obra_capataz = (int)$obraCapataz["id_obra"];
}


/*
|--------------------------------------------------------------------------
| VALIDAR OBRA DEL CAPATAZ
|--------------------------------------------------------------------------
*/

function validarObraCapataz($id_obra, $id_obra_capataz)
{
    if ((int)$id_obra !== (int)$id_obra_capataz) {
        die("No tenés permiso para acceder a esta obra.");
    }
}


/*
|--------------------------------------------------------------------------
| ACCIONES
|--------------------------------------------------------------------------
*/

switch ($accion) {

    /*
    |--------------------------------------------------------------------------
    | LISTAR
    |--------------------------------------------------------------------------
    */

    case "listar":

        if ($id_rol == 7) {

            $id_obra = $id_obra_capataz;

            $avances = $avance->obtenerPorObra($id_obra);
            $cantidad = $avance->contar($id_obra);
            $primero = $avance->primero($id_obra);
            $ultimo = $avance->ultimo($id_obra);

            require_once "../vistas/avances/capataz.php";
            exit();
        }

        $id_obra = $_GET["id_obra"] ?? 0;

        if (!$id_obra) {
            die("No se especificó la obra.");
        }

        $avances = $avance->obtenerPorObra($id_obra);
        $cantidad = $avance->contar($id_obra);
        $primero = $avance->primero($id_obra);
        $ultimo = $avance->ultimo($id_obra);

        require_once "../vistas/obras/avances/index.php";
        exit();


    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    case "crear":

        if ($id_rol == 7) {

            $id_obra = $id_obra_capataz;

            require_once "../vistas/avances/crear_capataz.php";
            exit();
        }

        $id_obra = $_GET["id_obra"] ?? 0;

        if (!$id_obra) {
            die("No se especificó la obra.");
        }

        require_once "../vistas/obras/avances/crear.php";
        exit();


    /*
    |--------------------------------------------------------------------------
    | GUARDAR
    |--------------------------------------------------------------------------
    */

    case "guardar":

        if (!isset($_POST["fecha"]) || empty($_POST["fecha"])) {
            die("La fecha es obligatoria.");
        }

        if (!isset($_POST["descripcion"]) || trim($_POST["descripcion"]) === "") {
            die("La descripción es obligatoria.");
        }

        /*
        | Si es Capataz, IGNORAMOS completamente el id_obra
        | enviado por el formulario y usamos el suyo.
        */

        if ($id_rol == 7) {

            $id_obra = $id_obra_capataz;

        } else {

            $id_obra = $_POST["id_obra"] ?? 0;

        }

        if (!$id_obra) {
            die("No se especificó la obra.");
        }

        $datos = [
            "id_obra" => $id_obra,
            "fecha" => $_POST["fecha"],
            "descripcion" => trim($_POST["descripcion"])
        ];

        $guardado = $avance->guardar($datos);

        /*
        | Comprobamos que realmente se haya guardado.
        */

        if (!$guardado) {
            die("No se pudo guardar el avance diario.");
        }

        $id_registro = $avance->ultimoInsertado();

        /*
        | Auditoría
        */

        $auditoria->registrar([
            "id_usuario" => $id_usuario,
            "accion" => "INSERTAR",
            "tabla_afectada" => "avance_diario",
            "id_registro" => $id_registro,
            "descripcion" => "Se registró un nuevo avance diario en la obra ID " . $id_obra
        ]);

        /*
        | Volver al listado
        */

        header(
            "Location: AvanceController.php?accion=listar&id_obra=" . $id_obra
        );

        exit();


    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    case "editar":

        $id = $_GET["id"] ?? 0;

        if (!$id) {
            die("No se especificó el avance.");
        }

        $registro = $avance->buscarPorId($id);

        if (!$registro) {
            die("El avance no existe.");
        }

        /*
        | El Capataz solamente puede editar avances
        | de su propia obra.
        */

        if ($id_rol == 7) {

            validarObraCapataz(
                $registro["id_obra"],
                $id_obra_capataz
            );
        }

        $id_obra = $registro["id_obra"];

        require_once "../vistas/obras/avances/editar.php";
        exit();


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    case "actualizar":

        $id = $_POST["id_avance_diario"] ?? 0;

        if (!$id) {
            die("No se especificó el avance.");
        }

        $registro = $avance->buscarPorId($id);

        if (!$registro) {
            die("El avance no existe.");
        }

        /*
        | Seguridad para Capataz.
        */

        if ($id_rol == 7) {

            validarObraCapataz(
                $registro["id_obra"],
                $id_obra_capataz
            );

            $id_obra = $id_obra_capataz;

        } else {

            $id_obra = $_POST["id_obra"] ?? $registro["id_obra"];
        }

        $datos = [
            "id_avance_diario" => $id,
            "fecha" => $_POST["fecha"],
            "descripcion" => trim($_POST["descripcion"])
        ];

        $actualizado = $avance->actualizar($datos);

        if (!$actualizado) {
            die("No se pudo actualizar el avance.");
        }

        $auditoria->registrar([
            "id_usuario" => $id_usuario,
            "accion" => "EDITAR",
            "tabla_afectada" => "avance_diario",
            "id_registro" => $id,
            "descripcion" => "Se modificó un avance diario de la obra ID " . $id_obra
        ]);

        header(
            "Location: AvanceController.php?accion=listar&id_obra=" . $id_obra
        );

        exit();


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    case "eliminar":

        $id = $_GET["id"] ?? 0;

        if (!$id) {
            die("No se especificó el avance.");
        }

        $registro = $avance->buscarPorId($id);

        if (!$registro) {
            die("El avance no existe.");
        }

        /*
        | Seguridad para Capataz.
        */

        if ($id_rol == 7) {

            validarObraCapataz(
                $registro["id_obra"],
                $id_obra_capataz
            );
        }

        $id_obra = $registro["id_obra"];

        $eliminado = $avance->eliminar($id);

        if (!$eliminado) {
            die("No se pudo eliminar el avance.");
        }

        $auditoria->registrar([
            "id_usuario" => $id_usuario,
            "accion" => "ELIMINAR",
            "tabla_afectada" => "avance_diario",
            "id_registro" => $id,
            "descripcion" => "Se eliminó un avance diario de la obra ID " . $id_obra
        ]);

        header(
            "Location: AvanceController.php?accion=listar&id_obra=" . $id_obra
        );

        exit();


    default:

        die("Acción no válida.");
}