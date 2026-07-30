<?php

require_once "../modelos/Auditoria.php";
require_once "../modelos/Cliente.php";
require_once "../config/permisos.php";

class ClienteController
{
    private $auditoria;
    private $cliente;

    public function __construct()
    {
        $this->auditoria = new Auditoria();
        $this->cliente = new Cliente();
    }

    public function index()
    {
        verificarPermiso("clientes");

        $clientes = $this->cliente->obtenerTodos();
        $estadisticas = $this->cliente->obtenerEstadisticas();

        require_once "../vistas/clientes/index.php";
    }

    public function ver()
    {
        verificarPermiso("clientes");

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header("Location: index.php?controller=Cliente&action=index");
            exit;
        }

        $id = intval($_GET['id']);

        $cliente = $this->cliente->obtenerPorId($id);

        if (!$cliente) {
            header("Location: index.php?controller=Cliente&action=index");
            exit;
        }

        $obras = $this->cliente->obtenerObras($id);
        $totalObras = $this->cliente->contarObras($id);

        require_once "../vistas/clientes/ver.php";
    }

    public function buscar()
    {
        verificarPermiso("clientes");

        $texto = $_GET['buscar'] ?? '';

        if (empty($texto)) {
            $clientes = $this->cliente->obtenerTodos();
        } else {
            $clientes = $this->cliente->buscar($texto);
        }

        $estadisticas = $this->cliente->obtenerEstadisticas();

        require_once "../vistas/clientes/index.php";
    }

    public function eliminar()
    {

        session_start();

        $id = $_GET["id"];

        if ($this->cliente->tieneObras($id)) {

            echo "<script>

            alert('No se puede dar de baja a este cliente porque tiene obras asociadas.');

            window.location.href='../vistas/clientes/index.php';

        </script>";

            exit();
        }

        $this->cliente->bajaLogica($id);

        $this->auditoria->registrar([

            "id_usuario"      => $_SESSION["usuario"]["id"],

            "accion"          => "BAJA",

            "tabla_afectada"  => "usuario",

            "id_registro"     => $id,

            "descripcion"     => "Desactivó un usuario"

        ]);

        header("Location: ../vistas/clientes/index.php");

        exit();
    }

    public function activar()
    {

        session_start();

        $id = $_GET["id"];

        $this->cliente->activarUsuario($id);

        $this->auditoria->registrar([

            "id_usuario"      => $_SESSION["usuario"]["id"],

            "accion"          => "ACTIVAR",

            "tabla_afectada"  => "usuario",

            "id_registro"     => $id,

            "descripcion"     => "Activó nuevamente un usuario"

        ]);

        header("Location: ../vistas/clientes/index.php");

        exit();
    }

    public function agregar()
    {

        session_start();

        $datos = [

            "nombre" => $_POST["nombre"],
            "apellido" => $_POST["apellido"],
            "documento" => $_POST["documento"],
            "telefono" => $_POST["telefono"],
            "correo" => $_POST["correo"],
            "contraseña" => $_POST["password"]
        ];

        if ($this->cliente->existeCorreo($datos["correo"])) {

            echo "<script>

                alert('El correo ya está registrado');

                window.location.href='../vistas/clientes/agregar.php';

            </script>";

            exit();
        }

        $idUsuario = $this->cliente->agregar($datos);

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "INSERTAR",

            "tabla_afectada" => "usuario",

            "id_registro" => $idUsuario,

            "descripcion" => "Registró un nuevo usuario"

        ]);

        header("Location: ../vistas/clientes/index.php");

        exit();
    }

    public function editar()
    {

        session_start();

        $datos = [
            "id_usuario" => $_POST["id_usuario"],
            "nombre" => $_POST["nombre"],
            "apellido" => $_POST["apellido"],
            "documento"=>$_POST["documento"],
            "telefono" => $_POST["telefono"],
            "correo" => $_POST["correo"]
        ];

        $usuarioActual = $this->cliente->buscarPorId($datos["id_usuario"]);

        if (

            $usuarioActual["correo"] != $datos["correo"]

            &&

            $this->cliente->existeCorreo($datos["correo"])

        ) {
            echo "<script>
            alert('El correo ya se encuentra registrado.');
            window.location.href='../vistas/clientes/index.php';
        </script>";

            exit();
        }

        $this->cliente->editar($datos);

        /*
    ==========================
        AUDITORÍA
    ==========================
    */

        $this->auditoria->registrar([

            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "EDITAR",

            "tabla_afectada" => "usuario",

            "id_registro" => $datos["id_usuario"],

            "descripcion" => "Modificó el usuario"

        ]);

        header("Location: ../vistas/clientes/index.php");

        exit();
    }
}

$controlador = new ClienteController();


if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

        case "agregar":

            $controlador->agregar();
            break;

        case "editar";
            $controlador->editar();
            break;
    }
}





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
