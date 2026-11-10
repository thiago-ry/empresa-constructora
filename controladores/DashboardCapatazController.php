
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../modelos/Obra.php";
require_once __DIR__ . "/../modelos/EmpleadoObra.php";
require_once __DIR__ . "/../modelos/Asistencia.php";
require_once __DIR__ . "/../modelos/Avance.php";
require_once __DIR__ . "/../modelos/Incidencia.php";
require_once __DIR__ . "/../modelos/Tarea.php";
require_once __DIR__ . "/../modelos/SolicitudHerramienta.php";

// ==========================================
// VALIDAR USUARIO
// ==========================================

if (!isset($_SESSION["usuario"])) {
    header("Location: ../vistas/login.php");
    exit;
}

$usuario = $_SESSION["usuario"];

$id_usuario = (int)($usuario["id_usuario"] ?? $usuario["id"] ?? 0);
$id_rol = (int)($usuario["id_rol"] ?? 0);

if ($id_rol !== 7 || $id_usuario <= 0) {
    header("Location: ../vistas/login.php");
    exit;
}

// ==========================================
// INSTANCIAR MODELOS
// ==========================================

$modeloObra = new Obra();
$modeloEmpleado = new EmpleadoObra();
$modeloAsistencia = new Asistencia();
$modeloAvance = new Avance();
$modeloIncidencia = new Incidencia();
$modeloTarea = new Tarea();
$modeloSolicitud = new SolicitudHerramienta();

// ==========================================
// VARIABLES INICIALES
// ==========================================

$obra = null;

$empleados = [];
$asistencias = [];
$avances = [];
$tareas = [];
$incidencias = [];
$solicitudesHerramientas = [];

$resumenEmpleados = [
    "total" => 0,
    "activos" => 0,
    "retirados" => 0
];

$resumenAsistencia = [
    "total" => 0,
    "completas" => 0,
    "sin_salida" => 0,
    "ausentes" => 0,
    "tardes" => 0
];

$resumenIncidencias = [
    "total" => 0,
    "pendientes" => 0,
    "revision" => 0,
    "resueltas" => 0
];

$estadisticas = [
    "empleados" => 0,
    "presentes" => 0,
    "ausentes" => 0,
    "tardes" => 0,
    "sin_salida" => 0,
    "avances" => 0,
    "tareas" => 0,
    "incidencias" => 0,
    "incidencias_pendientes" => 0,
    "solicitudes_pendientes" => 0
];

$hoy = date("Y-m-d");
$id_obra = 0;

// ==========================================
// OBTENER OBRA DEL CAPATAZ
// ==========================================

$obra = $modeloObra->obtenerObraPorCapataz($id_usuario);

if ($obra) {

    $id_obra = (int)$obra["id_obra"];

    // ======================================
    // EMPLEADOS
    // ======================================

    $empleados = $modeloEmpleado->obtenerActivos($id_obra);

    $resumenEmpleados = $modeloEmpleado->obtenerResumen($id_obra);

    // ======================================
    // ASISTENCIAS
    // ======================================

    $resumenAsistencia = $modeloAsistencia->obtenerResumenPorCapataz(
        $id_usuario,
        "",
        $id_obra,
        $hoy,
        $hoy
    );

    $asistencias = $modeloAsistencia->obtenerHistorialPorCapataz(
        $id_usuario,
        "",
        $id_obra,
        $hoy,
        $hoy
    );

    // ======================================
    // AVANCES
    // ======================================

    $avances = $modeloAvance->obtenerPorObra($id_obra);

    // ======================================
    // TAREAS
    // ======================================

    $tareas = $modeloTarea->listarCapataz($id_usuario);

    // Mantener únicamente las tareas de esta obra
    $tareas = array_values(array_filter($tareas, function ($tarea) use ($id_obra) {
        return (int)$tarea["id_obra"] === $id_obra;
    }));

    // ======================================
    // INCIDENCIAS
    // ======================================

    $resumenIncidencias = $modeloIncidencia->obtenerResumen($id_obra);

    $incidencias = $modeloIncidencia->obtenerPorObra($id_obra);

    // ======================================
    // SOLICITUDES DE HERRAMIENTAS
    // ======================================

    $solicitudesHerramientas = $modeloSolicitud->obtenerPorObra($id_obra);

    // ======================================
    // ESTADÍSTICAS
    // ======================================

    $estadisticas["empleados"] = (int)($resumenEmpleados["activos"] ?? 0);

    $estadisticas["presentes"] =
        (int)($resumenAsistencia["completas"] ?? 0) +
        (int)($resumenAsistencia["sin_salida"] ?? 0);

    $estadisticas["ausentes"] = (int)($resumenAsistencia["ausentes"] ?? 0);

    $estadisticas["tardes"] = (int)($resumenAsistencia["tardes"] ?? 0);

    $estadisticas["sin_salida"] = (int)($resumenAsistencia["sin_salida"] ?? 0);

    $estadisticas["avances"] = count($avances);

    $estadisticas["tareas"] = count($tareas);

    $estadisticas["incidencias"] = (int)($resumenIncidencias["total"] ?? 0);

    $estadisticas["incidencias_pendientes"] =
        (int)($resumenIncidencias["pendientes"] ?? 0);

    foreach ($solicitudesHerramientas as $solicitud) {
        if (($solicitud["estado"] ?? "") === "Pendiente") {
            $estadisticas["solicitudes_pendientes"]++;
        }
    }

    // Últimos registros
    $asistencias = array_slice($asistencias, 0, 5);
    $avances = array_slice($avances, 0, 5);
    $incidencias = array_slice($incidencias, 0, 5);
    $solicitudesHerramientas = array_slice($solicitudesHerramientas, 0, 5);
}

// ==========================================
// DATOS PARA LA VISTA
// ==========================================

$obraCapataz = $obra;
$resumenCapataz = $estadisticas;
$asistenciasHoy = $asistencias;
$ultimosAvances = $avances;
$tareasCapataz = $tareas;
$incidenciasCapataz = $incidencias;

$urlsCapataz = [
    "tareas" => "../tareas/index.php",
    "asignar_tarea" => "../tareas/crear.php",
    "asistencias" => "../asistencias/index.php",
    "avances" => "../avances/index.php",
    "registrar_avance" => "../avances/crear.php",
    "registrar_incidencia" => "../incidencias/crear.php",
    "solicitar_herramientas" => "../herramientas/solicitar.php",
    "empleados" => "../empleados/index.php"
];

// ==========================================
// CARGAR VISTA
// ==========================================

require_once __DIR__ . "/../vistas/dashboard/capataz.php";
