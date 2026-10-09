<?php

session_start();

require_once __DIR__ . "/../modelos/Tarea.php";

if (!isset($_SESSION["usuario"])) {
    header("Location: /empresa_constructora/vistas/login.php");
    exit;
}

$usuario = $_SESSION["usuario"];

$id_usuario = (int) (
    $usuario["id_usuario"] ?? $usuario["id"] ?? 0
);

$id_rol = (int) ($usuario["id_rol"] ?? 0);

if ($id_usuario <= 0) {
    http_response_code(403);
    exit("No se pudo identificar al usuario.");
}

$tareaModelo = new Tarea();

$urlVistas = "/empresa_constructora/vistas/tareas/";

$accion = $_GET["accion"] ?? "";

function redirigirTareas($url, $mensaje = "")
{
    if ($mensaje !== "") {
        $url .= (strpos($url, "?") === false ? "?" : "&")
            . "mensaje=" . urlencode($mensaje);
    }

    header("Location: " . $url);
    exit;
}

// CAPATAZ
if ($id_rol === 7) {

    switch ($accion) {

        case "crear":

            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                http_response_code(405);
                exit("Método no permitido.");
            }

            $id_obra = (int) ($_POST["id_obra"] ?? 0);

            $urlCrear = $urlVistas . "crear.php?id_obra=" . $id_obra;

            $titulo = trim($_POST["titulo"] ?? "");
            $descripcion = trim($_POST["descripcion"] ?? "");
            $prioridad = $_POST["prioridad"] ?? "Media";
            $fecha_limite = $_POST["fecha_limite"] ?? null;
            $empleados = $_POST["empleados"] ?? [];

            if (
                $id_obra <= 0 ||
                $titulo === "" ||
                $descripcion === "" ||
                !in_array(
                    $prioridad,
                    ["Baja", "Media", "Alta"],
                    true
                ) ||
                !is_array($empleados) ||
                empty($empleados)
            ) {
                redirigirTareas(
                    $urlCrear,
                    "Completá los campos y seleccioná al menos un empleado."
                );
            }

            if (
                $fecha_limite !== null &&
                $fecha_limite !== ""
            ) {
                $fecha = DateTime::createFromFormat(
                    "!Y-m-d",
                    $fecha_limite
                );

                if (
                    !$fecha ||
                    $fecha->format("Y-m-d") !== $fecha_limite ||
                    $fecha_limite < date("Y-m-d")
                ) {
                    redirigirTareas(
                        $urlCrear,
                        "La fecha límite no es válida."
                    );
                }
            } else {
                $fecha_limite = null;
            }

            try {

                $tareaModelo->crear(
                    [
                        "titulo" => $titulo,
                        "id_usuario" => $id_usuario,
                        "id_obra" => $id_obra,
                        "descripcion" => $descripcion,
                        "prioridad" => $prioridad,
                        "fecha_limite" => $fecha_limite
                    ],
                    $empleados
                );

                redirigirTareas(
                    $urlVistas . "index.php",
                    "Tarea creada y asignada correctamente."
                );

            } catch (Exception $e) {

                error_log(
                    "Error al crear tarea: " . $e->getMessage()
                );

                redirigirTareas(
                    $urlCrear,
                    "No se pudo crear la tarea. Verificá los datos y los empleados."
                );
            }

            break;

        case "revisar":

            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                http_response_code(405);
                exit("Método no permitido.");
            }

            $id_asignacion = (int) (
                $_POST["id_tarea_empleado"] ?? 0
            );

            $estado = $_POST["estado"] ?? "";

            $observacion = trim(
                $_POST["observacion_capataz"] ?? ""
            );

            if (
                $id_asignacion <= 0 ||
                !in_array(
                    $estado,
                    ["Completada", "En proceso"],
                    true
                )
            ) {
                redirigirTareas(
                    $urlVistas . "index.php",
                    "Los datos de la revisión no son válidos."
                );
            }

            try {

                $resultado = $tareaModelo->revisar(
                    $id_asignacion,
                    $id_usuario,
                    $estado,
                    $observacion
                );

                redirigirTareas(
                    $urlVistas . "index.php",
                    $resultado
                        ? "La revisión se actualizó correctamente."
                        : "No se pudo actualizar. Verificá que la tarea esté pendiente de revisión."
                );

            } catch (Exception $e) {

                error_log(
                    "Error al revisar tarea: " . $e->getMessage()
                );

                redirigirTareas(
                    $urlVistas . "index.php",
                    "Ocurrió un error al revisar la tarea."
                );
            }

            break;

        default:

            redirigirTareas($urlVistas . "index.php");
    }
}

// EMPLEADO
if ($id_rol === 1) {

    switch ($accion) {

        case "actualizar_estado":

            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                http_response_code(405);
                exit("Método no permitido.");
            }

            $id_asignacion = (int) (
                $_POST["id_tarea_empleado"] ?? 0
            );

            $estado = $_POST["estado"] ?? "";

            $observacion = trim(
                $_POST["observacion_empleado"] ?? ""
            );

            if (
                $id_asignacion <= 0 ||
                !in_array(
                    $estado,
                    ["En proceso", "Por revisar"],
                    true
                )
            ) {
                redirigirTareas(
                    $urlVistas . "mis_tareas.php",
                    "Los datos enviados no son válidos."
                );
            }

            try {

                $resultado = $tareaModelo->actualizarEstadoEmpleado(
                    $id_asignacion,
                    $id_usuario,
                    $estado,
                    $observacion
                );

                redirigirTareas(
                    $urlVistas . "mis_tareas.php",
                    $resultado
                        ? "El estado de tu tarea se actualizó correctamente."
                        : "No se pudo actualizar esa tarea."
                );

            } catch (Exception $e) {

                error_log(
                    "Error al actualizar tarea: " . $e->getMessage()
                );

                redirigirTareas(
                    $urlVistas . "mis_tareas.php",
                    "Ocurrió un error al actualizar la tarea."
                );
            }

            break;

        default:

            redirigirTareas($urlVistas . "mis_tareas.php");
    }
}

http_response_code(403);
exit("No tenés permisos para acceder al módulo de tareas.");