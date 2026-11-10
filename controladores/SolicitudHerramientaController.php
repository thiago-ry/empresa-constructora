
<?php

session_start();


require_once __DIR__ . "/../modelos/Conexion.php";
require_once __DIR__ . "/../modelos/HerramientaObra.php";
require_once __DIR__ . "/../modelos/UnidadHerramienta.php";
require_once __DIR__ . "/../modelos/SolicitudHerramienta.php";
require_once __DIR__ . "/../modelos/Herramienta.php";
require_once __DIR__ . "/../modelos/Obra.php";

class SolicitudHerramientaController
{
    private $solicitud;
    private $herramienta;
    private $obra;
    private $herramientaObra;
    private $unidadHerramienta;

public function __construct()
{
    $db = new Conexion();
    $conexion = $db->conectar();

    $this->solicitud = new SolicitudHerramienta($conexion);
    $this->herramientaObra = new HerramientaObra($conexion);
    $this->unidadHerramienta = new UnidadHerramienta($conexion);

    $this->herramienta = new Herramienta();
    $this->obra = new Obra();
}

    private function idUsuario()
    {
        return (int)(
            $_SESSION["usuario"]["id_usuario"]
            ?? $_SESSION["usuario"]["id"]
            ?? 0
        );
    }

    private function redirigir($url)
    {
        header("Location: " . $url);
        exit();
    }

    public function crear()
    {
        $id_usuario = $this->idUsuario();

        if ($id_usuario <= 0) {
            $this->redirigir("/empresa_constructora/vistas/login.php");
        }

        $obra = $this->obra->obtenerObraPorCapataz($id_usuario);

        if (!$obra) {
            $_SESSION["error"] =
                "No tenés una obra asignada para solicitar herramientas.";

            $this->redirigir("/empresa_constructora/");
        }

        $id_obra = (int)$obra["id_obra"];
        $herramientas = $this->herramienta->obtenerDisponibles();

        require __DIR__ . "/../vistas/obras/herramientas/solicitar.php";
    }

    public function guardar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->redirigir(
                "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=crear"
            );
        }

        $id_usuario = $this->idUsuario();
        $obra = $this->obra->obtenerObraPorCapataz($id_usuario);

        if ($id_usuario <= 0 || !$obra) {
            $_SESSION["error"] = "No se pudo validar tu obra asignada.";
            $this->redirigir("/empresa_constructora/");
        }

        $id_obra = (int)$obra["id_obra"];
        $ids = $_POST["id_herramienta"] ?? [];
        $cantidades = $_POST["cantidad"] ?? [];
        $observaciones = trim($_POST["observaciones"] ?? "");

        $detalles = [];

        foreach ($ids as $i => $id) {
            $id = (int)$id;
            $cantidad = (int)($cantidades[$i] ?? 0);

            if ($id > 0 && $cantidad > 0) {
                $detalles[] = [
                    "id_herramienta" => $id,
                    "cantidad" => $cantidad
                ];
            }
        }

        if (!$detalles) {
            $_SESSION["error"] =
                "Agregá al menos una herramienta con una cantidad válida.";

            $this->redirigir(
                "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=crear"
            );
        }

        try {
            $conexion = new Conexion();
            $db = $conexion->conectar();
            $db->beginTransaction();

            $id_solicitud = $this->solicitud->crear(
                $id_obra,
                $id_usuario,
                $observaciones !== "" ? $observaciones : null
            );

            foreach ($detalles as $detalle) {
                if (!$this->solicitud->agregarDetalle(
                    $id_solicitud,
                    $detalle["id_herramienta"],
                    $detalle["cantidad"]
                )) {
                    throw new Exception("No se pudo guardar un detalle.");
                }
            }

            $db->commit();

            $_SESSION["success"] =
                "La solicitud se registró correctamente.";
        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }

            $_SESSION["error"] =
                "No se pudo registrar la solicitud. Revisá los datos e intentá nuevamente.";
        }

        $this->redirigir(
            "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=listar"
        );
    }

    public function listar()
    {
        $id_usuario = $this->idUsuario();

        if ($id_usuario <= 0) {
            $this->redirigir("/empresa_constructora/vistas/login.php");
        }

        $obra = $this->obra->obtenerObraPorCapataz($id_usuario);

        if (!$obra) {
            $_SESSION["error"] = "No tenés una obra asignada.";
            $this->redirigir("/empresa_constructora/");
        }

        $solicitudes = $this->solicitud->obtenerPorObra(
            (int)$obra["id_obra"]
        );

        require __DIR__ . "/../vistas/obras/herramientas/solicitudes.php";
    }

    
public function detalle()
{
    $id = (int)($_GET["id"] ?? 0);
    $idUsuario = $this->idUsuario();
    $idRol = (int)($_SESSION["usuario"]["id_rol"] ?? 0);

    // Solo estos roles pueden consultar los detalles.
    if (!in_array($idRol, [2, 3, 5, 7], true)) {
        http_response_code(403);
        exit("No tenés permiso para consultar solicitudes.");
    }

    if ($id <= 0) {
        $_SESSION["error"] = "La solicitud indicada no es válida.";
        $this->redirigir(
            "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"
        );
    }

    $solicitud = $this->solicitud->obtenerPorId($id);

    if (!$solicitud) {
        $_SESSION["error"] = "La solicitud no existe.";
        $this->redirigir(
            "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"
        );
    }

    // El capataz solamente puede consultar solicitudes de su propia obra.
    if ($idRol === 7) {
        $obra = $this->obra->obtenerObraPorCapataz($idUsuario);

        if (
            !$obra ||
            (int)$obra["id_obra"] !== (int)$solicitud["id_obra"]
        ) {
            http_response_code(403);
            exit("No tenés permiso para consultar esta solicitud.");
        }
    }

    // Depósito, gerente y administrativo pueden consultar cualquier solicitud.
    $detalles = $this->solicitud->obtenerDetalle($id);

    require __DIR__ . "/../vistas/obras/herramientas/detalle_solicitud.php";
}

    public function gestionar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_rol = (int)($_SESSION["usuario"]["id_rol"] ?? 0);

        // Gerente, Administrativo o Encargado de Depósito
        if (!in_array($id_rol, [2, 3, 5], true)) {
            http_response_code(403);
            exit("No tenés permiso para gestionar solicitudes.");
        }

        $solicitudes = $this->solicitud->obtenerTodas();

        require __DIR__ . "/../vistas/obras/herramientas/gestionar_solicitudes.php";
    }


    public function aprobar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            exit("Método no permitido.");
        }

        $idRol = (int)($_SESSION["usuario"]["id_rol"] ?? 0);

        if (!in_array($idRol, [2, 3, 5], true)) {
            http_response_code(403);
            exit("No tenés permiso para aprobar solicitudes.");
        }

        $idSolicitud = (int)($_POST["id_solicitud"] ?? 0);

        if ($idSolicitud <= 0) {
            $_SESSION["error"] = "Solicitud inválida.";
            $this->redirigir(
                "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"
            );
        }

        try {
            $resultado = $this->solicitud->cambiarEstadoPendiente(
                $idSolicitud,
                "Aprobada"
            );

            if (!$resultado) {
                $_SESSION["error"] =
                    "No se pudo aprobar. Verificá que la solicitud exista y esté pendiente.";
            } else {
                $_SESSION["success"] = "Solicitud aprobada correctamente.";
            }
        } catch (Exception $e) {
            error_log("Error al aprobar solicitud: " . $e->getMessage());
            $_SESSION["error"] = "Ocurrió un error al aprobar la solicitud.";
        }

        $this->redirigir(
            "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"
        );
    }

    public function rechazar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            exit("Método no permitido.");
        }

        $idRol = (int)($_SESSION["usuario"]["id_rol"] ?? 0);

        if (!in_array($idRol, [2, 3, 5], true)) {
            http_response_code(403);
            exit("No tenés permiso para rechazar solicitudes.");
        }

        $idSolicitud = (int)($_POST["id_solicitud"] ?? 0);

        if ($idSolicitud <= 0) {
            $_SESSION["error"] = "Solicitud inválida.";
            $this->redirigir(
                "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"
            );
        }

        try {
            $resultado = $this->solicitud->cambiarEstadoPendiente(
                $idSolicitud,
                "Rechazada"
            );

            if (!$resultado) {
                $_SESSION["error"] =
                    "No se pudo rechazar. Verificá que la solicitud exista y esté pendiente.";
            } else {
                $_SESSION["success"] = "Solicitud rechazada correctamente.";
            }
        } catch (Exception $e) {
            error_log("Error al rechazar solicitud: " . $e->getMessage());
            $_SESSION["error"] = "Ocurrió un error al rechazar la solicitud.";
        }

        $this->redirigir(
            "/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=gestionar"
        );
    }
    public function entregar()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        exit("Método no permitido.");
    }

    $idRol = (int)($_SESSION["usuario"]["id_rol"] ?? 0);

    if (!in_array($idRol, [2, 3, 5], true)) {
        http_response_code(403);
        exit("No tenés permiso para registrar entregas.");
    }

    $idSolicitud = (int)($_POST["id_solicitud"] ?? 0);

    $url = "/empresa_constructora/controladores/"
         . "SolicitudHerramientaController.php?accion=gestionar";

    if ($idSolicitud <= 0) {
        $_SESSION["error"] = "La solicitud indicada no es válida.";
        $this->redirigir($url);
    }

    $conexion = null;

    try {
        // Todos los modelos involucrados comparten esta conexión.
        $db = new Conexion();
        $conexion = $db->conectar();

        $solicitudModel = new SolicitudHerramienta($conexion);
        $asignacionModel = new HerramientaObra($conexion);
        $unidadModel = new UnidadHerramienta($conexion);

        $conexion->beginTransaction();

        // Bloquear la solicitud para evitar procesarla dos veces.
        $stmt = $conexion->prepare(
            "SELECT id_solicitud, id_obra, estado
             FROM solicitud_herramienta
             WHERE id_solicitud = ?
             FOR UPDATE"
        );

        $stmt->execute([$idSolicitud]);
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            !$solicitud ||
            $solicitud["estado"] !== "Aprobada"
        ) {
            throw new Exception(
                "La solicitud no existe o no está aprobada."
            );
        }

        $detalles = $solicitudModel->obtenerDetalle($idSolicitud);

        if (!$detalles) {
            throw new Exception(
                "La solicitud no contiene herramientas."
            );
        }

        // Obtener los estados por nombre, no por un ID supuesto.
        $stmtEstado = $conexion->prepare(
            "SELECT id_estado_herramienta
             FROM estado_herramienta
             WHERE LOWER(nombre) = ?
             LIMIT 1"
        );

        $stmtEstado->execute(["disponible"]);
        $estadoDisponible = $stmtEstado->fetchColumn();

        $stmtEstado->execute(["asignada"]);
        $estadoAsignada = $stmtEstado->fetchColumn();

        if (!$estadoDisponible || !$estadoAsignada) {
            throw new Exception(
                "Falta configurar el estado Disponible o Asignada."
            );
        }

        // Comprobar y reservar las unidades de cada herramienta.
        $stmtUnidades = $conexion->prepare(
            "SELECT u.id_unidad
             FROM unidad_herramienta u
             WHERE u.id_herramienta = ?
               AND u.id_estado_herramienta = ?
             ORDER BY u.numero_unidad ASC
             LIMIT ?
             FOR UPDATE"
        );

        $stmtCambiarUnidad = $conexion->prepare(
            "UPDATE unidad_herramienta
             SET id_estado_herramienta = ?
             WHERE id_unidad = ?
               AND id_estado_herramienta = ?"
        );

        foreach ($detalles as $detalle) {
            $idHerramienta = (int)$detalle["id_herramienta"];
            $cantidad = (int)$detalle["cantidad"];

            if ($idHerramienta <= 0 || $cantidad <= 0) {
                throw new Exception(
                    "Hay un detalle de herramienta inválido."
                );
            }

            $stmtUnidades->bindValue(
                1,
                $idHerramienta,
                PDO::PARAM_INT
            );
            $stmtUnidades->bindValue(
                2,
                (int)$estadoDisponible,
                PDO::PARAM_INT
            );
            $stmtUnidades->bindValue(
                3,
                $cantidad,
                PDO::PARAM_INT
            );
            $stmtUnidades->execute();

            $unidades = $stmtUnidades->fetchAll(PDO::FETCH_ASSOC);

            if (count($unidades) < $cantidad) {
                throw new Exception(
                    "No hay suficientes unidades disponibles de "
                    . $detalle["herramienta"] . "."
                );
            }

            // Registrar la asignación asociada a la solicitud.
            $ok = $asignacionModel->agregar([
                "id_herramienta" => $idHerramienta,
                "id_obra" => (int)$solicitud["id_obra"],
                "id_solicitud" => $idSolicitud,
                "cantidad" => $cantidad,
                "fecha_asignacion" => date("Y-m-d"),
                "observaciones" => "Entrega de solicitud #"
                    . $idSolicitud,
                "id_estado_herramienta" => (int)$estadoAsignada
            ]);

            if (!$ok) {
                throw new Exception(
                    "No se pudo registrar la asignación."
                );
            }

            // Cambiar el estado de cada unidad entregada.
            foreach ($unidades as $unidad) {
                $stmtCambiarUnidad->execute([
                    (int)$estadoAsignada,
                    (int)$unidad["id_unidad"],
                    (int)$estadoDisponible
                ]);

                if ($stmtCambiarUnidad->rowCount() !== 1) {
                    throw new Exception(
                        "No se pudo actualizar una unidad."
                    );
                }
            }
        }

        // La solicitud cambia de estado al final de la operación.
        if (!$solicitudModel->marcarEntregada($idSolicitud)) {
            throw new Exception(
                "No se pudo actualizar el estado de la solicitud."
            );
        }

        $conexion->commit();

        $_SESSION["success"] =
            "La entrega se registró correctamente.";

    } catch (Throwable $e) {
        if ($conexion instanceof PDO && $conexion->inTransaction()) {
            $conexion->rollBack();
        }

        error_log(
            "Error al entregar solicitud #"
            . $idSolicitud . ": " . $e->getMessage()
        );

        $_SESSION["error"] =
            "No se pudo registrar la entrega. "
            . $e->getMessage();
    }

    $this->redirigir($url);
}
}

$controller = new SolicitudHerramientaController();
$accion = $_GET["accion"] ?? "listar";

if (
    method_exists($controller, $accion) &&
    in_array(
        $accion,
        [
            "crear",
            "guardar",
            "listar",
            "detalle",
            "gestionar",
            "aprobar",
            "rechazar",
            "entregar"
        ],
        true
    )
) {
    $controller->$accion();
} else {
    http_response_code(404);
    echo "Acción no válida.";
}
