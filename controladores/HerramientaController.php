
<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/Herramienta.php";
require_once "../modelos/UnidadHerramienta.php";


class HerramientaController
{
    private $auditoria;
    private $herramienta;
    private $unidad;


    public function __construct()
    {
        $this->auditoria = new Auditoria();
        $this->herramienta = new Herramienta();
        $this->unidad = new UnidadHerramienta();
    }


    /*
    ==================================
        AGREGAR HERRAMIENTA
    ==================================
    */

    public function agregar()
    {
        session_start();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: ../vistas/herramientas/");
            exit;
        }


        $datos = [

            "nombre" => trim($_POST["nombre"]),
            "tipo" => trim($_POST["tipo"]),
            "marca" => trim($_POST["marca"]),
            "modelo" => trim($_POST["modelo"]),
            "cantidad_total" => $_POST["cantidad_total"],
            "fecha_adquisicion" => $_POST["fecha_adquisicion"],
            "costo" => $_POST["costo"]

        ];


        if (
            empty($datos["nombre"]) ||
            empty($datos["cantidad_total"])
        ) {

            $_SESSION["error"] = "Complete los campos obligatorios.";

            header("Location: ../vistas/herramientas/agregar.php");

            exit;
        }


        if (
            !is_numeric($datos["cantidad_total"]) ||
            $datos["cantidad_total"] <= 0
        ) {

            $_SESSION["error"] = "La cantidad debe ser mayor a cero.";

            header("Location: ../vistas/herramientas/agregar.php");

            exit;
        }


        /*
        ==============================
        Guardar herramienta general
        ==============================
        */

        $id_herramienta = $this->herramienta->agregar($datos);


        if ($id_herramienta) {

            /*
            ==============================
            Crear unidades automáticamente
            ==============================
            */

            $unidadesCreadas = $this->unidad->crearUnidades(
                $id_herramienta,
                $datos["cantidad_total"]
            );

            if (!$unidadesCreadas) {
                $_SESSION["error"] =
                    "La herramienta fue registrada, pero no se pudieron crear sus unidades.";

                header("Location: ../vistas/herramientas/");
                exit;
            }


            /*
            ==============================
            AUDITORÍA
            ==============================
            */

            $this->auditoria->registrar([

                "id_usuario" => $_SESSION["usuario"]["id"],
                "accion" => "INSERTAR",
                "tabla_afectada" => "herramienta",
                "id_registro" => $id_herramienta,

                "descripcion" =>
                "Se registró la herramienta "
                    . strtoupper($datos["nombre"])
                    . " con "
                    . $datos["cantidad_total"]
                    . " unidades."

            ]);


            $_SESSION["success"] =
                "Herramienta registrada correctamente.";
        } else {

            $_SESSION["error"] =
                "No se pudo registrar la herramienta.";
        }


        header("Location: ../vistas/herramientas/");

        exit;
    }


    /*
    ==================================
        MOSTRAR EDITAR
    ==================================
    */

    public function editar()
    {

        $id = $_GET["id"] ?? null;


        if (!$id) {

            header("Location: ../vistas/herramientas/");

            exit;
        }


        $herramienta =
            $this->herramienta->buscarPorId($id);


        if (!$herramienta) {

            echo "No se encontraron los datos de la herramienta.";

            exit;
        }


        require_once "../vistas/herramientas/editar.php";
    }


    /*
    ==================================
        ACTUALIZAR
    ==================================
    */

    public function actualizar()
    {

        session_start();


        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            header("Location: ../vistas/herramientas/");

            exit;
        }


        $datos = [

            "id_herramienta" => $_POST["id_herramienta"],
            "nombre" => trim($_POST["nombre"]),
            "tipo" => trim($_POST["tipo"]),
            "marca" => trim($_POST["marca"]),
            "modelo" => trim($_POST["modelo"]),
            "cantidad_total" => $_POST["cantidad_total"],
            "fecha_adquisicion" => $_POST["fecha_adquisicion"],
            "costo" => $_POST["costo"]

        ];


        if ($this->herramienta->editar($datos)) {

            $this->auditoria->registrar([

                "id_usuario" => $_SESSION["usuario"]["id"],
                "accion" => "EDITAR",
                "tabla_afectada" => "herramienta",
                "id_registro" => $datos["id_herramienta"],

                "descripcion" =>
                "Se actualizaron los datos de la herramienta "
                    . strtoupper($datos["nombre"])

            ]);


            $_SESSION["success"] =
                "Herramienta actualizada correctamente.";
        } else {

            $_SESSION["error"] =
                "No se pudo actualizar.";
        }


        header("Location: ../vistas/herramientas/");

        exit;
    }


    /*
    ==================================
        LISTAR
    ==================================
    */

    public function listar()
    {

        header("Location: ../vistas/herramientas/");

        exit;
    }


    /*
    ==================================
        VER DETALLE
    ==================================
    */

    public function ver()
    {

        $id = $_GET["id"] ?? 0;

        $herramienta =
            $this->herramienta->obtenerDetalle($id);


        if (!$herramienta) {

            header("Location: ../vistas/herramientas/");

            exit;
        }


        require_once "../vistas/herramientas/ver.php";
    }


    /*
    ==================================
        OBTENER UNIDADES
    ==================================
    
    Esta acción se utiliza mediante AJAX
    desde el modal de unidades.
    */

    public function unidades()
    {

        header("Content-Type: application/json; charset=UTF-8");


        $id = isset($_GET["id"])
            ? (int) $_GET["id"]
            : 0;


        /*
        ==============================
        Validar ID
        ==============================
        */

        if ($id <= 0) {

            echo json_encode([
                "success" => false,
                "message" => "ID de herramienta inválido."
            ]);

            exit;
        }


        /*
        ==============================
        Verificar herramienta
        ==============================
        */

        $herramienta =
            $this->herramienta->obtenerDetalle($id);


        if (!$herramienta) {

            echo json_encode([
                "success" => false,
                "message" => "No se encontró la herramienta."
            ]);

            exit;
        }


        /*
        ==============================
        Obtener unidades
        ==============================
        */

        $unidades =
            $this->unidad->obtenerPorHerramienta($id);


        /*
        ==============================
        Contadores
        ==============================
        */

        $total = count($unidades);

        $disponibles = 0;
        $asignadas = 0;
        $reparacion = 0;
        $fueraServicio = 0;


        foreach ($unidades as $unidad) {

            $estado = strtolower(
                trim($unidad["estado"])
            );


            switch ($estado) {

                case "disponible":

                    $disponibles++;

                    break;


                case "asignada":

                    $asignadas++;

                    break;


                case "en reparación":
                case "en reparacion":

                    $reparacion++;

                    break;


                case "fuera de servicio":

                    $fueraServicio++;

                    break;
            }
        }


        /*
        ==============================
        Respuesta JSON
        ==============================
        */

        echo json_encode([

            "success" => true,

            "herramienta" => [
                "id_herramienta" =>
                $herramienta["id_herramienta"],

                "nombre" =>
                $herramienta["nombre"],

                "marca" =>
                $herramienta["marca"] ?? "",

                "modelo" =>
                $herramienta["modelo"] ?? ""
            ],

            "resumen" => [

                "total" => $total,

                "disponibles" =>
                $disponibles,

                "asignadas" =>
                $asignadas,

                "reparacion" =>
                $reparacion,

                "fuera_servicio" =>
                $fueraServicio
            ],

            "unidades" => $unidades

        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
}


/*
=========================================================
ROUTER
=========================================================
*/

$controller = new HerramientaController();


$accion = $_GET["accion"] ?? "";


switch ($accion) {

    case "agregar":

        $controller->agregar();

        break;


    case "editar":

        $controller->editar();

        break;


    case "actualizar":

        $controller->actualizar();

        break;


    case "listar":

        $controller->listar();

        break;


    case "ver":

        $controller->ver();

        break;


    /*
    =====================================================
    VER UNIDADES
    =====================================================
    */

    case "unidades":

        $controller->unidades();

        break;


    default:

        header("Location: ../vistas/herramientas/");

        exit;
}
