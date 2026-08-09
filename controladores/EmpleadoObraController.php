<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/EmpleadoObra.php";
require_once "../modelos/Usuario.php";

class EmpleadoObraController
{
    private $auditoria;
    private $empleadoObra;

    public function __construct()
    {
        $this->auditoria = new Auditoria();
        $this->empleadoObra = new EmpleadoObra();
    }

    /*
    ==========================
        LISTAR
    ==========================
    */

    public function listar()
    {
        session_start();

        $id_obra = $_GET["id_obra"] ?? 0;

        $empleados = $this->empleadoObra->obtenerPorObra($id_obra);

        require "../vistas/obras/empleados/index.php";
    }


    /*
    ==========================
        CREAR
    ==========================
    */

    public function crear()
    {
        session_start();

        $id_obra = $_GET["id_obra"] ?? 0;

        require "../vistas/obras/empleados/crear.php";
    }


    /*
    ==========================
        BUSCAR EMPLEADOS
        AJAX
    ==========================
    */

    public function buscarEmpleados()
    {
        session_start();

        header("Content-Type: application/json; charset=UTF-8");

        $id_obra = $_GET["id_obra"] ?? 0;
        $busqueda = $_GET["busqueda"] ?? "";

        /*
        ==========================================
            VALIDAR OBRA
        ==========================================
        */

        if (empty($id_obra)) {

            echo json_encode([
                "success" => false,
                "mensaje" => "No se especificó la obra."
            ]);

            exit();
        }

        /*
        ==========================================
            BUSCAR EMPLEADOS
        ==========================================
        */

        $usuario = new Usuario();

        $empleados = $usuario->obtenerEmpleadosDisponiblesPorObra(
            $id_obra,
            $busqueda
        );

        echo json_encode([
            "success" => true,
            "empleados" => $empleados
        ]);

        exit();
    }


    /*
    ==========================
        AGREGAR
    ==========================
    */

    public function agregar()
    {
        session_start();

        /*
        ==========================================
            VALIDACIONES BÁSICAS
        ==========================================
        */

        $id_usuario = $_POST["id_usuario"] ?? 0;
        $id_obra = $_POST["id_obra"] ?? 0;
        $fecha_ingreso = $_POST["fecha_ingreso"] ?? "";
        $observaciones = $_POST["observaciones"] ?? "";

        if (
            empty($id_usuario) ||
            empty($id_obra) ||
            empty($fecha_ingreso)
        ) {

            header(
                "Location: ../vistas/obras/empleados/index.php?id_obra="
                . $id_obra
                . "&error=datos"
            );

            exit();
        }

        /*
        ==========================================
            EVITAR DUPLICADOS
        ==========================================
        */

        if (
            $this->empleadoObra->existeEmpleadoActivo(
                $id_usuario,
                $id_obra
            )
        ) {

            header(
                "Location: ../vistas/obras/empleados/index.php?id_obra="
                . $id_obra
                . "&error=duplicado"
            );

            exit();
        }

        /*
        ==========================================
            DATOS
        ==========================================
        */

        $datos = [

            "id_usuario" => $id_usuario,

            "id_obra" => $id_obra,

            "fecha_ingreso" => $fecha_ingreso,

            "observaciones" => $observaciones

        ];

        /*
        ==========================================
            ASIGNAR
        ==========================================
        */

        $this->empleadoObra->asignar($datos);

        /*
        ==========================================
            AUDITORÍA
        ==========================================
        */

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "INSERTAR",

            "tabla_afectada" => "empleado_obra",

            "id_registro" => $id_usuario,

            "descripcion" => "Asignó un empleado a una obra"

        ]);

        /*
        ==========================================
            REDIRECCIÓN
        ==========================================
        */

        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $id_obra
        );

        exit();
    }


    /*
    ==========================
        EDITAR
    ==========================
    */

    public function editar()
    {
        session_start();

        $datos = [

            "id_empleado_obra" => $_POST["id_empleado_obra"],

            "fecha_ingreso" => $_POST["fecha_ingreso"],

            "observaciones" => $_POST["observaciones"]

        ];

        $this->empleadoObra->editar($datos);

        /*
        ==========================================
            AUDITORÍA
        ==========================================
        */

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "EDITAR",

            "tabla_afectada" => "empleado_obra",

            "id_registro" => $_POST["id_empleado_obra"],

            "descripcion" => "Editó una asignación de empleado"

        ]);

        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $_POST["id_obra"]
        );

        exit();
    }


    /*
    ==========================
        RETIRAR
    ==========================
    */

    public function retirar()
    {
        session_start();

        $datos = [

            "id_empleado_obra" => $_POST["id_empleado_obra"],

            "fecha_egreso" => $_POST["fecha_egreso"],

            "motivo_egreso" => $_POST["motivo_egreso"],

            "observaciones" => $_POST["observaciones"]

        ];

        $this->empleadoObra->retirar($datos);

        /*
        ==========================================
            AUDITORÍA
        ==========================================
        */

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "EDITAR",

            "tabla_afectada" => "empleado_obra",

            "id_registro" => $_POST["id_empleado_obra"],

            "descripcion" => "Retiró un empleado de una obra"

        ]);

        header(
            "Location: ../vistas/obras/empleados/index.php?id_obra="
            . $_POST["id_obra"]
        );

        exit();
    }
}


/*
==================================================
    INSTANCIA
==================================================
*/

$controlador = new EmpleadoObraController();


/*
==================================================
    ACCIONES GET
==================================================
*/

if (isset($_GET["accion"])) {

    switch ($_GET["accion"]) {

        case "listar":

            $controlador->listar();

            break;


        case "crear":

            $controlador->crear();

            break;


        case "buscarEmpleados":

            $controlador->buscarEmpleados();

            break;
    }
}


/*
==================================================
    ACCIONES POST
==================================================
*/

if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

        case "agregar":

            $controlador->agregar();

            break;


        case "editar":

            $controlador->editar();

            break;


        case "retirar":

            $controlador->retirar();

            break;
    }
}