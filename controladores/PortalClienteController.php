
<?php

require_once __DIR__ . "/../modelos/PortalCliente.php";

// ==========================================
// CONTROL DE SESIÓN
// ==========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario"])) {
    header("Location: ../vistas/login.php");
    exit();
}

// Solamente puede ingresar el rol Cliente.
if ((int)($_SESSION["usuario"]["id_rol"] ?? 0) !== 6) {
    http_response_code(403);
    exit("No tenés autorización para acceder a este apartado.");
}

$idCliente = (int)(
    $_SESSION["usuario"]["id_usuario"]
    ?? $_SESSION["usuario"]["id"]
    ?? 0
);

if ($idCliente <= 0) {
    http_response_code(401);
    exit("No se pudo identificar al cliente.");
}

// ==========================================
// INICIALIZAR MODELO
// ==========================================

$cliente = new PortalCliente();

// ==========================================
// ACCIÓN SOLICITADA
// ==========================================

$accion = $_POST["accion"] ?? $_GET["accion"] ?? "dashboard";

$accionesPermitidas = [
    "dashboard",
    "obra",
    "avances",
    "etapas",
    "fotos",
    "documentos",
    "equipo",
    "presupuesto",
    "aprobar_presupuesto",
    "rechazar_presupuesto"
];

if (!in_array($accion, $accionesPermitidas, true)) {
    http_response_code(404);
    exit("El apartado solicitado no existe.");
}

// ==========================================
// OBRAS DEL CLIENTE
// ==========================================

$obrasCliente = $cliente->obtenerObrasCliente($idCliente);

$obraActual = null;

$idObra = (int)(
    $_POST["id_obra"]
    ?? $_GET["id_obra"]
    ?? 0
);

if (!empty($obrasCliente)) {

    if ($idObra <= 0) {
        $idObra = (int)$obrasCliente[0]["id_obra"];
    }

    // Verificar que la obra pertenezca al cliente.
    $obraActual = $cliente->obtenerObraCliente(
        $idObra,
        $idCliente
    );

    if (!$obraActual) {
        http_response_code(403);
        exit("No tenés autorización para consultar esta obra.");
    }
}

// ==========================================
// RESPUESTA DEL CLIENTE AL PRESUPUESTO
// ==========================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    in_array($accion, [
        "aprobar_presupuesto",
        "rechazar_presupuesto"
    ], true)
) {

    if (!$obraActual) {
        http_response_code(403);
        exit("No tenés autorización para responder este presupuesto.");
    }

    $idPresupuesto = (int)(
        $_POST["id_presupuesto"] ?? 0
    );

    $decision = $accion === "aprobar_presupuesto"
        ? "Aprobado"
        : "Rechazado";

    $resultado = $cliente->responderPresupuesto(
        $idPresupuesto,
        $idObra,
        $idCliente,
        $decision
    );

    $_SESSION["mensaje_presupuesto"] = $resultado["mensaje"];

    $_SESSION["tipo_mensaje_presupuesto"] = $resultado["ok"]
        ? "success"
        : "error";

    header(
        "Location: PortalClienteController.php?" .
        http_build_query([
            "accion" => "presupuesto",
            "id_obra" => $idObra,
            "id_presupuesto" => $idPresupuesto
        ])
    );

    exit();
}

// ==========================================
// VARIABLES DEL PORTAL
// ==========================================

$avances = [];
$etapas = [];
$fotografias = [];
$documentos = [];
$empleados = [];

$capataz = null;
$jefeObra = null;

$resumenEtapas = [
    "total" => 0,
    "finalizadas" => 0,
    "en_proceso" => 0,
    "pendientes" => 0,
    "canceladas" => 0,
    "avance" => 0
];

$presupuestos = [];
$presupuestoSeleccionado = null;
$detallePresupuesto = [];
$manoObraPresupuesto = [];

// ==========================================
// CARGAR INFORMACIÓN DE LA OBRA
// ==========================================

if ($obraActual) {

    // Avances diarios.
    $avances = $cliente->obtenerAvances($idObra);

    // Etapas.
    $etapas = $cliente->obtenerEtapas($idObra);

    $resumenEtapas = $cliente->obtenerResumenEtapas($idObra);

    // Fotografías.
    $fotografias = $cliente->obtenerFotografias($idObra);

    // Documentación.
    $documentos = $cliente->obtenerDocumentos($idObra);

    // Equipo de trabajo.
    $capataz = $cliente->obtenerCapataz($idObra);

    $jefeObra = $cliente->obtenerJefeObra($idObra);

    $empleados = $cliente->obtenerEmpleados($idObra);

    // Presupuestos.
    $presupuestos = $cliente->obtenerPresupuestos($idObra);

    if (!empty($presupuestos)) {

        $idPresupuestoSolicitado = (int)(
            $_GET["id_presupuesto"] ?? 0
        );

        // Buscar la versión solicitada.
        foreach ($presupuestos as $presupuesto) {

            if (
                (int)$presupuesto["id_presupuesto"]
                === $idPresupuestoSolicitado
            ) {
                $presupuestoSeleccionado = $presupuesto;
                break;
            }
        }

        // Si no se seleccionó una versión válida,
        // utilizar la más reciente.
        if (!$presupuestoSeleccionado) {
            $presupuestoSeleccionado = $presupuestos[0];
        }

        $idPresupuesto = (int)$presupuestoSeleccionado["id_presupuesto"];

        $detallePresupuesto = $cliente->obtenerDetallePresupuesto(
            $idPresupuesto,
            $idObra
        );

        $manoObraPresupuesto = $cliente->obtenerManoObraPresupuesto(
            $idPresupuesto,
            $idObra
        );
    }
}

// ==========================================
// DATOS PARA LA VISTA
// ==========================================

$tituloPortal = "Portal del cliente";

$rutaCliente = "../controladores/PortalClienteController.php";

$nombreCliente = (
    $_SESSION["usuario"]["nombre"] ?? ""
) . " " . (
    $_SESSION["usuario"]["apellido"] ?? ""
);

// ==========================================
// CARGAR VISTA
// ==========================================

require_once __DIR__ . "/../vistas/cliente/portal.php";
