<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/Usuario.php";
require_once "../modelos/Empleado.php";
require_once "../config/permisos.php";

class EmpleadoController
{
    private $auditoria;
    private $usuario;
    private $empleado;

    public function __construct()
    {
        $this->auditoria = new Auditoria();
        $this->usuario = new Usuario();
        $this->empleado = new Empleado();
    }

    public function index()
    {
        verificarPermiso("empleados");

        $busqueda = trim(
            $_GET["busqueda"] ?? ""
        );

        $estado = $_GET["estado"] ?? "";

        $id_cargo = $_GET["id_cargo"] ?? "";

        if (
            $estado !== "" &&
            $estado !== "1" &&
            $estado !== "0"
        ) {
            $estado = "";
        }

        if (
            $id_cargo !== "" &&
            !is_numeric($id_cargo)
        ) {
            $id_cargo = "";
        }

        $empleados = $this->empleado->obtenerTodos(
            $busqueda,
            $estado,
            $id_cargo
        );

        $cargos = $this->empleado->obtenerTodosLosCargos();

        $estadisticas =
            $this->empleado->obtenerEstadisticas();

        $totalEmpleados =
            $estadisticas["total"] ?? 0;

        $empleadosActivos =
            $estadisticas["activos"] ?? 0;

        $empleadosInactivos =
            $estadisticas["inactivos"] ?? 0;

        require_once "../vistas/empleados/index.php";
    }

    public function ver()
    {
        verificarPermiso("empleados");

        if (
            !isset($_GET["id"]) ||
            empty($_GET["id"])
        ) {
            header(
                "Location: ../vistas/empleados/index.php"
            );
            exit;
        }

        $id = intval($_GET["id"]);

        $empleado =
            $this->usuario->buscarPorId($id);

        if (
            !$empleado ||
            !isset($empleado["nombre_rol"]) ||
            $empleado["nombre_rol"] != "Empleado"
        ) {
            header(
                "Location: ../vistas/empleados/index.php"
            );
            exit;
        }

        $cargos =
            $this->usuario->obtenerCargosEmpleado($id);

        require_once "../vistas/empleados/ver.php";
    }

    public function buscar()
    {
        verificarPermiso("empleados");

        $texto = trim(
            $_GET["buscar"] ?? ""
        );

        $empleados =
            $this->empleado->obtenerTodos(
                $texto,
                "",
                ""
            );

        $busqueda = $texto;
        $estado = "";
        $id_cargo = "";

        $cargos =
            $this->empleado->obtenerTodosLosCargos();

        $estadisticas =
            $this->empleado->obtenerEstadisticas();

        $totalEmpleados =
            $estadisticas["total"] ?? 0;

        $empleadosActivos =
            $estadisticas["activos"] ?? 0;

        $empleadosInactivos =
            $estadisticas["inactivos"] ?? 0;

        require_once "../vistas/empleados/index.php";
    }

    public function eliminar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        verificarPermiso("empleados");

        if (
            !isset($_GET["id"]) ||
            empty($_GET["id"])
        ) {
            header(
                "Location: ../vistas/empleados/index.php"
            );
            exit;
        }

        $id = intval($_GET["id"]);

        $empleado =
            $this->usuario->buscarPorId($id);

        if (
            !$empleado ||
            !isset($empleado["nombre_rol"]) ||
            $empleado["nombre_rol"] != "Empleado"
        ) {
            header(
                "Location: ../vistas/empleados/index.php"
            );
            exit;
        }

        if (
            $this->usuario->empleadoEnObra($id)
        ) {
            echo "<script>
                    alert(
                        'No se puede dar de baja a este empleado porque está asignado a una obra.'
                    );
                    window.location.href='../vistas/empleados/index.php';
                  </script>";
            exit;
        }

        $this->usuario->bajaLogica($id);

        $this->auditoria->registrar([
            "id_usuario" =>
                $_SESSION["usuario"]["id"],

            "accion" => "BAJA",

            "tabla_afectada" => "usuario",

            "id_registro" => $id,

            "descripcion" =>
                "Desactivó un empleado"
        ]);

        header(
            "Location: ../vistas/empleados/index.php"
        );
        exit;
    }

    public function activar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        verificarPermiso("empleados");

        if (
            !isset($_GET["id"]) ||
            empty($_GET["id"])
        ) {
            header(
                "Location: ../vistas/empleados/index.php"
            );
            exit;
        }

        $id = intval($_GET["id"]);

        $empleado =
            $this->usuario->buscarPorId($id);

        if (
            !$empleado ||
            !isset($empleado["nombre_rol"]) ||
            $empleado["nombre_rol"] != "Empleado"
        ) {
            header(
                "Location: ../vistas/empleados/index.php"
            );
            exit;
        }

        $this->usuario->activarUsuario($id);

        $this->auditoria->registrar([
            "id_usuario" =>
                $_SESSION["usuario"]["id"],

            "accion" => "ACTIVAR",

            "tabla_afectada" => "usuario",

            "id_registro" => $id,

            "descripcion" =>
                "Activó nuevamente un empleado"
        ]);

        header(
            "Location: ../vistas/empleados/index.php"
        );
        exit;
    }

    public function agregar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        verificarPermiso("empleados");

        $rolEmpleado =
            $this->usuario->obtenerIdRolEmpleado();

        if (!$rolEmpleado) {
            die(
                "ERROR: No se encontró el rol Empleado en la base de datos."
            );
        }

        $datos = [
            "id_rol" =>
                $rolEmpleado["id_rol"],

            "nombre" =>
                $_POST["nombre"] ?? "",

            "apellido" =>
                $_POST["apellido"] ?? "",

            "documento" =>
                $_POST["documento"] ?? "",

            "telefono" =>
                $_POST["telefono"] ?? "",

            "direccion" =>
                $_POST["direccion"] ?? "",

            "salario" =>
                $_POST["salario"] ?? 0,

            "correo" =>
                $_POST["correo"] ?? "",

            "contraseña" =>
                $_POST["password"] ?? ""
        ];

        if (
            empty($datos["nombre"]) ||
            empty($datos["apellido"]) ||
            empty($datos["correo"]) ||
            empty($datos["contraseña"])
        ) {
            die(
                "ERROR: Faltan datos obligatorios."
            );
        }

        $confirmar =
            $_POST["confirmar"] ?? "";

        if (
            $datos["contraseña"] !==
            $confirmar
        ) {
            echo "<script>
                    alert('Las contraseñas no coinciden.');
                    window.location.href='../vistas/empleados/agregar.php';
                  </script>";
            exit;
        }

        if (
            $this->usuario->existeCorreo(
                $datos["correo"]
            )
        ) {
            echo "<script>
                    alert('El correo ya está registrado.');
                    window.location.href='../vistas/empleados/agregar.php';
                  </script>";
            exit;
        }

        try {
            $idUsuario =
                $this->usuario->agregar($datos);

            if (!$idUsuario) {
                die(
                    "ERROR: No se pudo crear el empleado."
                );
            }

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

            $this->auditoria->registrar([
                "id_usuario" =>
                    $_SESSION["usuario"]["id"],

                "accion" =>
                    "INSERTAR",

                "tabla_afectada" =>
                    "usuario",

                "id_registro" =>
                    $idUsuario,

                "descripcion" =>
                    "Registró un nuevo empleado"
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

    public function editar()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        verificarPermiso("empleados");

        $id = intval(
            $_POST["id_usuario"] ?? 0
        );

        if (!$id) {
            header(
                "Location: ../vistas/empleados/index.php"
            );
            exit;
        }

        $empleadoActual =
            $this->usuario->buscarPorId($id);

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

        $rolEmpleado =
            $this->usuario->obtenerIdRolEmpleado();

        if (!$rolEmpleado) {
            die(
                "ERROR: No se encontró el rol Empleado."
            );
        }

        $datos = [
            "id_usuario" =>
                $id,

            "id_rol" =>
                $rolEmpleado["id_rol"],

            "nombre" =>
                $_POST["nombre"] ?? "",

            "apellido" =>
                $_POST["apellido"] ?? "",

            "documento" =>
                $_POST["documento"] ?? "",

            "telefono" =>
                $_POST["telefono"] ?? "",

            "direccion" =>
                $_POST["direccion"] ?? "",

            "salario" =>
                $_POST["salario"] ?? 0,

            "correo" =>
                $_POST["correo"] ?? ""
        ];

        if (
            $empleadoActual["correo"] !=
            $datos["correo"] &&
            $this->usuario->existeCorreo(
                $datos["correo"],
                $id
            )
        ) {
            echo "<script>
                    alert('El correo ya se encuentra registrado.');
                    window.location.href='../vistas/empleados/index.php';
                  </script>";
            exit;
        }

        try {
            $this->usuario->editar($datos);

            if (
                isset($_POST["cargos"]) &&
                is_array($_POST["cargos"])
            ) {
                $this->usuario->guardarCargosEmpleado(
                    $id,
                    $_POST["cargos"]
                );
            }

            $this->auditoria->registrar([
                "id_usuario" =>
                    $_SESSION["usuario"]["id"],

                "accion" =>
                    "EDITAR",

                "tabla_afectada" =>
                    "usuario",

                "id_registro" =>
                    $id,

                "descripcion" =>
                    "Modificó los datos de un empleado"
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


$controlador = new EmpleadoController();


if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

        case "agregar":
            $controlador->agregar();
            break;

        case "editar":
            $controlador->editar();
            break;
    }

    exit;
}


if (isset($_GET["accion"])) {

    switch ($_GET["accion"]) {

        case "baja":
            $controlador->eliminar();
            break;

        case "activar":
            $controlador->activar();
            break;

        case "ver":
            $controlador->ver();
            break;

        case "buscar":
            $controlador->buscar();
            break;

        case "index":
            $controlador->index();
            break;
    }

    exit;
}


$controlador->index();