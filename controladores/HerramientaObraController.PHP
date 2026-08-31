
<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../modelos/HerramientaObra.php";
require_once "../modelos/Herramienta.php";
require_once "../modelos/Obra.php";
require_once "../modelos/Auditoria.php";
require_once "../modelos/UnidadHerramienta.php";


class HerramientaObraController
{
    private $herramientaObra;
    private $herramienta;
    private $obra;
    private $auditoria;
    private $unidad;


    public function __construct()
    {
        $this->herramientaObra = new HerramientaObra();
        $this->herramienta = new Herramienta();
        $this->obra = new Obra();
        $this->auditoria = new Auditoria();
        $this->unidad = new UnidadHerramienta();
    }


    // =========================================================
    // LISTAR
    // =========================================================

    public function listar()
    {
        $id_obra = $_GET["id_obra"] ?? 0;

        $herramientas =
            $this->herramientaObra->obtenerPorObra($id_obra);

        require_once "../vistas/obras/herramientas/index.php";
    }


    // =========================================================
    // CREAR
    // =========================================================

    public function crear()
    {
        $id_obra = $_GET["id_obra"] ?? 0;

        $herramientas =
            $this->herramienta->obtenerDisponibles();

        require_once "../vistas/obras/herramientas/agregar.php";
    }


    // =========================================================
    // GUARDAR ASIGNACIÓN
    // =========================================================

    public function guardar()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return;
        }

        $datos = [
            "id_herramienta" =>
                $_POST["id_herramienta"],

            "id_obra" =>
                $_POST["id_obra"],

            "cantidad" =>
                (int)$_POST["cantidad"],

            "fecha_asignacion" =>
                $_POST["fecha_asignacion"],

            "id_estado_herramienta" => 2,

            "observaciones" =>
                $_POST["observaciones"] ?? null
        ];


        // =====================================================
        // VALIDAR CANTIDAD
        // =====================================================

        if ($datos["cantidad"] <= 0) {

            $_SESSION["error"] =
                "La cantidad debe ser mayor a cero.";

            header(
                "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=crear&id_obra="
                . $datos["id_obra"]
            );

            exit();
        }


        // =====================================================
        // VERIFICAR STOCK DISPONIBLE
        // =====================================================

        $disponibles =
            $this->herramienta
            ->obtenerCantidadDisponible(
                $datos["id_herramienta"]
            );


        if ($datos["cantidad"] > $disponibles) {

            $_SESSION["error"] =
                "No hay suficientes herramientas disponibles. Quedan "
                . $disponibles
                . ".";

            header(
                "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=crear&id_obra="
                . $datos["id_obra"]
            );

            exit();
        }


        // =====================================================
        // GUARDAR ASIGNACIÓN
        // =====================================================

        if ($this->herramientaObra->agregar($datos)) {


            // =================================================
            // MARCAR UNIDADES COMO ASIGNADAS
            // =================================================
            //
            // Estado 2 = Asignada
            //
            // =================================================

            $unidadesDisponibles =
                $this->unidad->obtenerDisponibles(
                    $datos["id_herramienta"]
                );

            $cantidadAsignada =
                $datos["cantidad"];


            for (
                $i = 0;
                $i < $cantidadAsignada;
                $i++
            ) {

                if (!isset($unidadesDisponibles[$i])) {
                    break;
                }

                $this->unidad->cambiarEstado(
                    $unidadesDisponibles[$i]["id_unidad"],
                    2
                );
            }


            // =================================================
            // AUDITORÍA
            // =================================================

            $this->auditoria->registrar([

                "id_usuario" =>
                    $_SESSION["usuario"]["id"],

                "accion" =>
                    "INSERTAR",

                "tabla_afectada" =>
                    "herramienta_obra",

                "id_registro" =>
                    $datos["id_obra"],

                "descripcion" =>
                    "Asignación de herramienta a obra"
            ]);
        }


        header(
            "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra="
            . $datos["id_obra"]
        );

        exit();
    }


    // =========================================================
    // EDITAR
    // =========================================================

    public function editar()
    {
        $id = $_GET["id"] ?? 0;

        $herramientaObra =
            $this->herramientaObra
            ->obtenerPorId($id);

        require_once "../vistas/obras/herramientas/editar.php";
    }


    // =========================================================
    // ACTUALIZAR ASIGNACIÓN
    // =========================================================
    //
    // IMPORTANTE:
    // Ya NO registra devoluciones.
    //
    // =========================================================

    public function actualizar()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return;
        }


        $id =
            $_POST["id_herramienta_obra"];


        $asignacion =
            $this->herramientaObra
            ->obtenerAsignacion($id);


        if (!$asignacion) {

            $_SESSION["error"] =
                "La asignación no existe.";

            header(
                "Location: /empresa_constructora/"
            );

            exit();
        }


        $datos = [

            "id_herramienta_obra" =>
                $id,

            "cantidad_asignada" =>
                (int)$_POST["cantidad_asignada"],

            "observaciones" =>
                $_POST["observaciones"] ?? null
        ];


        // =====================================================
        // NO PERMITIR REDUCIR POR DEBAJO DE LO YA DEVUELTO
        // =====================================================

        if (
            $datos["cantidad_asignada"]
            <
            $asignacion["cantidad_devuelta"]
        ) {

            $_SESSION["error"] =
                "La cantidad asignada no puede ser menor "
                . "que la cantidad ya devuelta.";

            header(
                "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=editar&id="
                . $id
            );

            exit();
        }


        if (
            $this->herramientaObra->editar($datos)
        ) {

            $this->auditoria->registrar([

                "id_usuario" =>
                    $_SESSION["usuario"]["id"],

                "accion" =>
                    "EDITAR",

                "tabla_afectada" =>
                    "herramienta_obra",

                "id_registro" =>
                    $id,

                "descripcion" =>
                    "Actualización de asignación de herramienta"
            ]);
        }


        header(
            "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra="
            . $asignacion["id_obra"]
        );

        exit();
    }


    // =========================================================
    // FORMULARIO DE DEVOLUCIÓN
    // =========================================================

    public function devolver()
    {
        $id =
            $_GET["id"]
            ?? 0;


        $herramientaObra =
            $this->herramientaObra
            ->obtenerPorId($id);


        if (!$herramientaObra) {

            echo "La asignación no existe.";

            exit();
        }


        $devoluciones =
            $this->herramientaObra
            ->obtenerDevoluciones($id);


        $cantidadPendiente =
            $this->herramientaObra
            ->obtenerCantidadPendiente($id);


        require_once
            "../vistas/obras/herramientas/devolver.php";
    }


    // =========================================================
    // GUARDAR DEVOLUCIÓN
    // =========================================================

    public function guardarDevolucion()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return;
        }


        $id =
            $_POST["id_herramienta_obra"];


        // =====================================================
        // OBTENER ASIGNACIÓN
        // =====================================================

        $asignacion =
            $this->herramientaObra
            ->obtenerPorId($id);


        if (!$asignacion) {

            $_SESSION["error"] =
                "La asignación no existe.";

            header(
                "Location: /empresa_constructora/"
            );

            exit();
        }


        $cantidad =
            (int)$_POST["cantidad"];


        // =====================================================
        // CANTIDAD PENDIENTE
        // =====================================================

        $pendiente =
            $this->herramientaObra
            ->obtenerCantidadPendiente($id);


        // =====================================================
        // VALIDACIONES
        // =====================================================

        if ($cantidad <= 0) {

            $_SESSION["error"] =
                "La cantidad a devolver debe ser mayor a cero.";

            header(
                "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=devolver&id="
                . $id
            );

            exit();
        }


        if ($cantidad > $pendiente) {

            $_SESSION["error"] =
                "No puede devolver "
                . $cantidad
                . " herramientas. Solo quedan "
                . $pendiente
                . " pendientes.";

            header(
                "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=devolver&id="
                . $id
            );

            exit();
        }


        // =====================================================
        // REGISTRAR DEVOLUCIÓN
        // =====================================================

        $datos = [

            "id_herramienta_obra" =>
                $id,

            "cantidad" =>
                $cantidad,

            "fecha_devolucion" =>
                $_POST["fecha_devolucion"]
                ?? date("Y-m-d H:i:s"),

            "observaciones" =>
                $_POST["observaciones"]
                ?? null,

            "id_usuario" =>
                $_SESSION["usuario"]["id"]
        ];


        if (
            $this->herramientaObra
            ->registrarDevolucion($datos)
        ) {


            // =================================================
            // LIBERAR UNIDADES INDIVIDUALES
            // =================================================
            //
            // La devolución ya fue registrada.
            //
            // Ahora buscamos las unidades ASIGNADAS de esa
            // herramienta y las cambiamos a DISPONIBLE.
            //
            // Estado Disponible se busca por nombre dentro
            // de UnidadHerramienta.php.
            //
            // =================================================

            $liberadas =
                $this->unidad->liberarUnidades(
                    $asignacion["id_herramienta"],
                    $cantidad
                );


            // =================================================
            // VERIFICAR SI LAS UNIDADES FUERON LIBERADAS
            // =================================================

            if (!$liberadas) {

                $_SESSION["error"] =
                    "La devolución fue registrada, pero no se pudieron liberar las unidades de la herramienta.";

            } else {

                // =================================================
                // VERIFICAR SI LA ASIGNACIÓN TERMINÓ
                // =================================================

                $pendienteFinal =
                    $this->herramientaObra
                    ->obtenerCantidadPendiente($id);


                if ($pendienteFinal <= 0) {

                    $this->herramientaObra
                        ->marcarComoDevuelta($id);
                }


                // =================================================
                // AUDITORÍA
                // =================================================

                $this->auditoria->registrar([

                    "id_usuario" =>
                        $_SESSION["usuario"]["id"],

                    "accion" =>
                        "INSERTAR",

                    "tabla_afectada" =>
                        "devolucion_herramienta",

                    "id_registro" =>
                        $id,

                    "descripcion" =>
                        "Registro de devolución de "
                        . $cantidad
                        . " herramienta(s)"
                ]);


                $_SESSION["success"] =
                    "Devolución registrada correctamente.";
            }
        }


        // =====================================================
        // VOLVER AL LISTADO
        // =====================================================

        header(
            "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra="
            . $asignacion["id_obra"]
        );

        exit();
    }


    // =========================================================
    // ELIMINAR
    // =========================================================

    public function eliminar()
    {
        $id =
            $_GET["id"]
            ?? 0;


        $id_obra =
            $_GET["id_obra"]
            ?? 0;


        if (
            $this->herramientaObra
            ->eliminar($id)
        ) {

            $this->auditoria->registrar([

                "id_usuario" =>
                    $_SESSION["usuario"]["id"],

                "accion" =>
                    "ELIMINAR",

                "tabla_afectada" =>
                    "herramienta_obra",

                "id_registro" =>
                    $id,

                "descripcion" =>
                    "Eliminación de herramienta asignada"
            ]);
        }


        header(
            "Location: /empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra="
            . $id_obra
        );

        exit();
    }
}


// =========================================================
// EJECUCIÓN
// =========================================================

$controller =
    new HerramientaObraController();


$accion =
    $_GET["accion"]
    ?? "listar";


if (method_exists($controller, $accion)) {

    $controller->$accion();

} else {

    echo "Acción no válida";
}