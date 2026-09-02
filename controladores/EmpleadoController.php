<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/Usuario.php";
require_once "../config/permisos.php";

class EmpleadoController
{
    private $auditoria;
    private $usuario;

    public function __construct()
    {
        $this->auditoria = new Auditoria();
        $this->usuario = new Usuario();
    }


    /*
    ==========================================================
        INDEX
    ==========================================================
    */

    public function index()
    {
        verificarPermiso("empleados");

        $empleados = $this->usuario->obtenerEmpleados();

        require_once "../vistas/empleados/index.php";
    }


    /*
    ==========================================================
        VER EMPLEADO
    ==========================================================
    */

    public function ver()
    {
        verificarPermiso("empleados");

        if (!isset($_GET["id"]) || empty($_GET["id"])) {

            header("Location: ../vistas/empleados/index.php");
            exit;
        }

        $id = intval($_GET["id"]);

        $empleado = $this->usuario->buscarPorId($id);

        if (
            !$empleado ||
            !isset($empleado["nombre_rol"]) ||
            $empleado["nombre_rol"] != "Empleado"
        ) {

            header("Location: ../vistas/empleados/index.php");
            exit;
        }

        $cargos = $this->usuario->obtenerCargosEmpleado($id);

        require_once "../vistas/empleados/ver.php";
    }


    /*
    ==========================================================
        BUSCAR
    ==========================================================
    */

    public function buscar()
    {
        verificarPermiso("empleados");

        $texto = $_GET["buscar"] ?? "";

        $empleados = $this->usuario->obtenerEmpleados();

        if (!empty(trim($texto))) {

            $texto = strtolower(trim($texto));

            $empleados = array_filter(
                $empleados,
                function ($empleado) use ($texto) {

                    $nombre = strtolower(
                        $empleado["nombre"] ?? ""
                    );

                    $apellido = strtolower(
                        $empleado["apellido"] ?? ""
                    );

                    $documento = strtolower(
                        $empleado["documento"] ?? ""
                    );

                    $nombreCompleto = $nombre . " " . $apellido;

                    return
                        strpos($nombre, $texto) !== false ||
                        strpos($apellido, $texto) !== false ||
                        strpos($documento, $texto) !== false ||
                        strpos($nombreCompleto, $texto) !== false;
                }
            );
        }

        require_once "../vistas/empleados/index.php";
    }


    /*
    ==========================================================
        DAR DE BAJA
    ==========================================================
    */

    public function eliminar()
    {
        session_start();

        if (!isset($_GET["id"]) || empty($_GET["id"])) {

            header("Location: ../vistas/empleados/index.php");
            exit;
        }

        $id = intval($_GET["id"]);

        $empleado = $this->usuario->buscarPorId($id);

        if (
            !$empleado ||
            !isset($empleado["nombre_rol"]) ||
            $empleado["nombre_rol"] != "Empleado"
        ) {

            header("Location: ../vistas/empleados/index.php");
            exit;
        }


        /*
        ======================================================
            VERIFICAR SI ESTÁ ASIGNADO A UNA OBRA
        ======================================================
        */

        if ($this->usuario->empleadoEnObra($id)) {

            echo "<script>

                alert('No se puede dar de baja a este empleado porque está asignado a una obra.');

                window.location.href='../vistas/empleados/index.php';

            </script>";

            exit;
        }


        $this->usuario->bajaLogica($id);


        /*
        ======================================================
            AUDITORÍA
        ======================================================
        */

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "BAJA",

            "tabla_afectada" => "usuario",

            "id_registro" => $id,

            "descripcion" => "Desactivó un empleado"

        ]);


        header("Location: ../vistas/empleados/index.php");

        exit;
    }


    /*
    ==========================================================
        ACTIVAR
    ==========================================================
    */

    public function activar()
    {
        session_start();

        if (!isset($_GET["id"]) || empty($_GET["id"])) {

            header("Location: ../vistas/empleados/index.php");
            exit;
        }

        $id = intval($_GET["id"]);

        $empleado = $this->usuario->buscarPorId($id);

        if (
            !$empleado ||
            !isset($empleado["nombre_rol"]) ||
            $empleado["nombre_rol"] != "Empleado"
        ) {

            header("Location: ../vistas/empleados/index.php");
            exit;
        }


        $this->usuario->activarUsuario($id);


        /*
        ======================================================
            AUDITORÍA
        ======================================================
        */

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "ACTIVAR",

            "tabla_afectada" => "usuario",

            "id_registro" => $id,

            "descripcion" => "Activó nuevamente un empleado"

        ]);


        header("Location: ../vistas/empleados/index.php");

        exit;
    }


    /*
    ==========================================================
        AGREGAR EMPLEADO
    ==========================================================
    */

    public function agregar()
    {
        session_start();


        /*
        ======================================================
            OBTENER ROL EMPLEADO
        ======================================================
        */

        $rolEmpleado = $this->usuario->obtenerIdRolEmpleado();

        if (!$rolEmpleado) {

            die(
                "ERROR: No se encontró el rol Empleado en la base de datos."
            );
        }


        /*
        ======================================================
            DATOS DEL EMPLEADO
        ======================================================
        */

        $datos = [

            "id_rol" => $rolEmpleado["id_rol"],

            "nombre" => $_POST["nombre"] ?? "",

            "apellido" => $_POST["apellido"] ?? "",

            "documento" => $_POST["documento"] ?? "",

            "telefono" => $_POST["telefono"] ?? "",

            "direccion" => $_POST["direccion"] ?? "",

            "salario" => $_POST["salario"] ?? 0,

            "correo" => $_POST["correo"] ?? "",

            "contraseña" => $_POST["password"] ?? ""

        ];


        /*
        ======================================================
            VALIDAR DATOS OBLIGATORIOS
        ======================================================
        */

        if (
            empty($datos["nombre"]) ||
            empty($datos["apellido"]) ||
            empty($datos["correo"]) ||
            empty($datos["contraseña"])
        ) {

            die("ERROR: Faltan datos obligatorios.");
        }


        /*
        ======================================================
            CONFIRMAR CONTRASEÑA
        ======================================================
        */

        $confirmar = $_POST["confirmar"] ?? "";

        if ($datos["contraseña"] !== $confirmar) {

            echo "<script>

                alert('Las contraseñas no coinciden.');

                window.location.href='../vistas/empleados/agregar.php';

            </script>";

            exit;
        }


        /*
        ======================================================
            VERIFICAR CORREO
        ======================================================
        */

        if ($this->usuario->existeCorreo($datos["correo"])) {

            echo "<script>

                alert('El correo ya está registrado.');

                window.location.href='../vistas/empleados/agregar.php';

            </script>";

            exit;
        }


        /*
        ======================================================
            CREAR EMPLEADO
        ======================================================
        */

        try {

            $idUsuario = $this->usuario->agregar($datos);

            if (!$idUsuario) {

                die(
                    "ERROR: No se pudo crear el empleado."
                );
            }


            /*
            ==================================================
                GUARDAR CARGOS
            ==================================================
            */

            if (
                isset($_POST["cargos"]) &&
                is_array($_POST["cargos"]) &&
                !empty($_POST["cargos"])
            ) {

                $this->usuario->guardarCargosEmpleado(
                    $idUsuario,
                    $_POST["cargos"]
                );
            }


            /*
            ==================================================
                AUDITORÍA
            ==================================================
            */

            $this->auditoria->registrar([

                "id_usuario" => $_SESSION["usuario"]["id"],

                "accion" => "INSERTAR",

                "tabla_afectada" => "usuario",

                "id_registro" => $idUsuario,

                "descripcion" => "Registró un nuevo empleado"

            ]);


            header(
                "Location: ../vistas/empleados/index.php"
            );

            exit;


        } catch (PDOException $e) {

            die(
                "ERROR DE BASE DE DATOS:<br><br>" .
                $e->getMessage()
            );
        }
    }


    /*
    ==========================================================
        EDITAR EMPLEADO
    ==========================================================
    */

    public function editar()
    {
        session_start();

        $id = intval(
            $_POST["id_usuario"] ?? 0
        );

        if (!$id) {

            header(
                "Location: ../vistas/empleados/index.php"
            );

            exit;
        }


        /*
        ======================================================
            EMPLEADO ACTUAL
        ======================================================
        */

        $empleadoActual = $this->usuario->buscarPorId($id);

        if (
            !$empleadoActual ||
            !isset($empleadoActual["nombre_rol"]) ||
            $empleadoActual["nombre_rol"] != "Empleado"
        ) {

            header(
                "Location: ../vistas/empleados/index.php"
            );

            exit;
        }


        /*
        ======================================================
            ROL EMPLEADO
        ======================================================
        */

        $rolEmpleado =
            $this->usuario->obtenerIdRolEmpleado();


        if (!$rolEmpleado) {

            die(
                "ERROR: No se encontró el rol Empleado."
            );
        }


        /*
        ======================================================
            DATOS
        ======================================================
        */

        $datos = [

            "id_usuario" => $id,

            "id_rol" => $rolEmpleado["id_rol"],

            "nombre" => $_POST["nombre"] ?? "",

            "apellido" => $_POST["apellido"] ?? "",

            "documento" => $_POST["documento"] ?? "",

            "telefono" => $_POST["telefono"] ?? "",

            "direccion" => $_POST["direccion"] ?? "",

            "salario" => $_POST["salario"] ?? 0,

            "correo" => $_POST["correo"] ?? ""

        ];


        /*
        ======================================================
            VERIFICAR CORREO
        ======================================================
        */

        if (

            $empleadoActual["correo"] != $datos["correo"]

            &&

            $this->usuario->existeCorreo(
                $datos["correo"]
            )

        ) {

            echo "<script>

                alert('El correo ya se encuentra registrado.');

                window.location.href='../vistas/empleados/index.php';

            </script>";

            exit;
        }


        /*
        ======================================================
            ACTUALIZAR EMPLEADO
        ======================================================
        */

        try {

            $this->usuario->editar($datos);


            /*
            ==================================================
                ACTUALIZAR CARGOS
            ==================================================
            */

            if (
                isset($_POST["cargos"]) &&
                is_array($_POST["cargos"])
            ) {

                $this->usuario->guardarCargosEmpleado(
                    $id,
                    $_POST["cargos"]
                );
            }


            /*
            ==================================================
                AUDITORÍA
            ==================================================
            */

            $this->auditoria->registrar([

                "id_usuario" => $_SESSION["usuario"]["id"],

                "accion" => "EDITAR",

                "tabla_afectada" => "usuario",

                "id_registro" => $id,

                "descripcion" => "Modificó los datos de un empleado"

            ]);


            header(
                "Location: ../vistas/empleados/index.php"
            );

            exit;


        } catch (PDOException $e) {

            die(
                "ERROR DE BASE DE DATOS:<br><br>" .
                $e->getMessage()
            );
        }
    }
}


/*
==============================================================
    CREAR CONTROLADOR
==============================================================
*/

$controlador = new EmpleadoController();


/*
==============================================================
    ACCIONES POST
==============================================================
*/

if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

        case "agregar":

            $controlador->agregar();

            break;


        case "editar":

            $controlador->editar();

            break;
    }
}


/*
==============================================================
    ACCIONES GET
==============================================================
*/

if (isset($_GET["accion"])) {

    switch ($_GET["accion"]) {

        case "baja":

            $controlador->eliminar();

            break;


        case "activar":

            $controlador->activar();

            break;
    }
}