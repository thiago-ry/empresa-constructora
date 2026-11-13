
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
if ((int)$_SESSION["usuario"]["id_rol"] !== 6) {
    http_response_code(403);
    exit("No tenés autorización para acceder a este apartado.");
}

$idCliente = (int)$_SESSION["usuario"]["id"];

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

$accion = $_GET["accion"] ?? "dashboard";

$accionesPermitidas = [
    "dashboard",
    "obra",
    "avances",
    "etapas",
    "fotos",
    "documentos",
    "equipo",
    "presupuesto"
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
$idObra = (int)($_GET["id_obra"] ?? 0);

if (!empty($obrasCliente)) {

    // Si no se especifica una obra, se selecciona la primera.
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

$nombreCliente = $_SESSION["usuario"]["nombre"] . " " .
                 $_SESSION["usuario"]["apellido"];

// ==========================================
// CARGAR VISTA
// ==========================================

require_once __DIR__ . "/../vistas/cliente/portal.php";
