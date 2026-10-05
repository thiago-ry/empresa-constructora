<?php

session_start();

date_default_timezone_set("America/Argentina/Buenos_Aires");

require_once "../modelos/Asistencia.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: ../vistas/login/");
    exit;
}

$usuario = $_SESSION["usuario"];

$id_usuario_capataz = (int)($usuario["id"] ?? 0);

if ($id_usuario_capataz <= 0) {
    header("Location: ../vistas/dashboard/capataz.php");
    exit;
}

$asistencia = new Asistencia();

$accion = $_GET["accion"] ?? "";

$id_usuario_empleado = (int)(
    $_GET["id_usuario"] ?? 0
);

$id_obra = (int)(
    $_GET["id_obra"] ?? 0
);

if ($id_usuario_empleado <= 0 || $id_obra <= 0) {
    header("Location: ../vistas/empleado_obra/");
    exit;
}

$empleado = $asistencia->empleadoPerteneceObraCapataz(
    $id_usuario_empleado,
    $id_usuario_capataz,
    $id_obra
);

if (!$empleado) {
    header(
        "Location: ../vistas/empleado_obra/?error=acceso"
    );
    exit;
}

$fecha = date("Y-m-d");
$hora = date("H:i:s");

/*
|--------------------------------------------------------------------------
| ENTRADA
|--------------------------------------------------------------------------
*/

if ($accion === "entrada") {

    $resultado = $asistencia->marcarEntrada(
        $id_usuario_empleado,
        $id_obra,
        $fecha,
        $hora
    );

    if ($resultado["success"]) {

        header(
            "Location: ../vistas/empleado_obra/?id_obra="
            . $id_obra
            . "&mensaje=entrada"
        );

        exit;
    }

    header(
        "Location: ../vistas/empleado_obra/?id_obra="
        . $id_obra
        . "&error="
        . urlencode($resultado["mensaje"])
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| SALIDA
|--------------------------------------------------------------------------
*/

if ($accion === "salida") {

    $resultado = $asistencia->marcarSalida(
        $id_usuario_empleado,
        $id_obra,
        $fecha,
        $hora
    );

    if ($resultado["success"]) {

        header(
            "Location: ../vistas/empleado_obra/?id_obra="
            . $id_obra
            . "&mensaje=salida"
        );

        exit;
    }

    header(
        "Location: ../vistas/empleado_obra/?id_obra="
        . $id_obra
        . "&error="
        . urlencode($resultado["mensaje"])
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| AUSENCIA
|--------------------------------------------------------------------------
*/

if ($accion === "ausencia") {

    $resultado = $asistencia->marcarAusencia(
        $id_usuario_empleado,
        $id_obra,
        $fecha
    );

    if ($resultado["success"]) {

        header(
            "Location: ../vistas/empleado_obra/?id_obra="
            . $id_obra
            . "&mensaje=ausencia"
        );

        exit;
    }

    header(
        "Location: ../vistas/empleado_obra/?id_obra="
        . $id_obra
        . "&error="
        . urlencode($resultado["mensaje"])
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| ACCIÓN NO VÁLIDA
|--------------------------------------------------------------------------
*/

header(
    "Location: ../vistas/empleado_obra/?id_obra="
    . $id_obra
);

exit;