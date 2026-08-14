<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../modelos/Usuario.php";
require_once "../modelos/Auditoria.php";


class PerfilController
{

    private $usuario;
    private $auditoria;


    public function __construct()
    {

        $this->usuario = new Usuario();

        $this->auditoria = new Auditoria();

    }


    // ============================================================
    // EDITAR PERFIL
    // ============================================================

    public function editar()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        // --------------------------------------------------------
        // VERIFICAR SESIÓN
        // --------------------------------------------------------

        if (!isset($_SESSION["usuario"])) {

            header(
                "Location: /empresa_constructora/vistas/login.php"
            );

            exit;
        }


        // --------------------------------------------------------
        // ID DEL USUARIO LOGUEADO
        // --------------------------------------------------------

        $id_usuario = $_SESSION["usuario"]["id"];


        // --------------------------------------------------------
        // DATOS RECIBIDOS
        // --------------------------------------------------------

        $datos = [

            "id_usuario" => $id_usuario,

            "nombre" =>
                trim($_POST["nombre"] ?? ""),

            "apellido" =>
                trim($_POST["apellido"] ?? ""),

            "documento" =>
                trim($_POST["documento"] ?? ""),

            "telefono" =>
                trim($_POST["telefono"] ?? ""),

            "direccion" =>
                trim($_POST["direccion"] ?? ""),

            "correo" =>
                trim($_POST["correo"] ?? "")

        ];


        // --------------------------------------------------------
        // VALIDACIONES
        // --------------------------------------------------------

        if (
            empty($datos["nombre"]) ||
            empty($datos["apellido"]) ||
            empty($datos["documento"]) ||
            empty($datos["correo"])
        ) {

            echo "<script>

                alert('Complete todos los campos obligatorios.');

                window.location.href =
                '../vistas/perfil/editar.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // VALIDAR CORREO
        // --------------------------------------------------------

        if (
            !filter_var(
                $datos["correo"],
                FILTER_VALIDATE_EMAIL
            )
        ) {

            echo "<script>

                alert('Ingrese un correo electrónico válido.');

                window.location.href =
                '../vistas/perfil/editar.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // OBTENER USUARIO ACTUAL
        // --------------------------------------------------------

        $usuarioActual =
            $this->usuario->buscarPorId($id_usuario);


        if (!$usuarioActual) {

            echo "<script>

                alert('No se encontró el usuario.');

                window.location.href =
                '../vistas/perfil/index.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // VALIDAR CORREO DUPLICADO
        // --------------------------------------------------------

        if (
            $usuarioActual["correo"] !==
            $datos["correo"]
        ) {

            $correoExistente =
                $this->usuario->existeCorreo(
                    $datos["correo"]
                );


            if ($correoExistente) {

                echo "<script>

                    alert('El correo ya se encuentra registrado.');

                    window.location.href =
                    '../vistas/perfil/editar.php';

                </script>";

                exit;
            }

        }


        // --------------------------------------------------------
        // CONSERVAR ROL Y SALARIO
        // --------------------------------------------------------

        $datos["id_rol"] =
            $usuarioActual["id_rol"];

        $datos["salario"] =
            $usuarioActual["salario"];


        // --------------------------------------------------------
        // ACTUALIZAR
        // --------------------------------------------------------

        $resultado =
            $this->usuario->editar($datos);


        if (!$resultado) {

            echo "<script>

                alert('No se pudieron actualizar los datos.');

                window.location.href =
                '../vistas/perfil/editar.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // ACTUALIZAR SESIÓN
        // --------------------------------------------------------

        $_SESSION["usuario"]["nombre"] =
            $datos["nombre"];

        $_SESSION["usuario"]["apellido"] =
            $datos["apellido"];


        // --------------------------------------------------------
        // AUDITORÍA
        // --------------------------------------------------------

        $this->auditoria->registrar([

            "id_usuario" =>
                $_SESSION["usuario"]["id"],

            "accion" =>
                "EDITAR",

            "tabla_afectada" =>
                "usuario",

            "id_registro" =>
                $id_usuario,

            "descripcion" =>
                "Modificó sus datos personales desde el perfil"

        ]);


        // --------------------------------------------------------
        // FINALIZAR
        // --------------------------------------------------------

        echo "<script>

            alert('Perfil actualizado correctamente.');

            window.location.href =
            '../vistas/perfil/index.php';

        </script>";

        exit;
    }


    // ============================================================
    // CAMBIAR CONTRASEÑA
    // ============================================================

    public function cambiarContraseña()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        // --------------------------------------------------------
        // VERIFICAR SESIÓN
        // --------------------------------------------------------

        if (!isset($_SESSION["usuario"])) {

            header(
                "Location: /empresa_constructora/vistas/login.php"
            );

            exit;
        }


        // --------------------------------------------------------
        // ID DEL USUARIO LOGUEADO
        // --------------------------------------------------------

        $id_usuario =
            $_SESSION["usuario"]["id"];


        // --------------------------------------------------------
        // DATOS DEL FORMULARIO
        // --------------------------------------------------------

        $contraseñaActual =
            $_POST["contraseña_actual"] ?? "";

        $nuevaContraseña =
            $_POST["nueva_contraseña"] ?? "";

        $confirmarContraseña =
            $_POST["confirmar_contraseña"] ?? "";


        // --------------------------------------------------------
        // VALIDAR CAMPOS
        // --------------------------------------------------------

        if (
            empty($contraseñaActual) ||
            empty($nuevaContraseña) ||
            empty($confirmarContraseña)
        ) {

            echo "<script>

                alert('Complete todos los campos.');

                window.location.href =
                '../vistas/perfil/cambiar_password.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // OBTENER USUARIO
        // --------------------------------------------------------

        $usuarioActual =
            $this->usuario->buscarPorId(
                $id_usuario
            );


        if (!$usuarioActual) {

            echo "<script>

                alert('No se encontró el usuario.');

                window.location.href =
                '../vistas/perfil/index.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // VERIFICAR CONTRASEÑA ACTUAL
        // --------------------------------------------------------

        if (
            $contraseñaActual !==
            $usuarioActual["contraseña"]
        ) {

            echo "<script>

                alert('La contraseña actual es incorrecta.');

                window.location.href =
                '../vistas/perfil/cambiar_password.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // VALIDAR LONGITUD
        // --------------------------------------------------------

        if (
            strlen($nuevaContraseña) < 6
        ) {

            echo "<script>

                alert('La nueva contraseña debe tener al menos 6 caracteres.');

                window.location.href =
                '../vistas/perfil/cambiar_password.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // CONFIRMAR CONTRASEÑA
        // --------------------------------------------------------

        if (
            $nuevaContraseña !==
            $confirmarContraseña
        ) {

            echo "<script>

                alert('Las nuevas contraseñas no coinciden.');

                window.location.href =
                '../vistas/perfil/cambiar_password.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // EVITAR MISMA CONTRASEÑA
        // --------------------------------------------------------

        if (
            $nuevaContraseña ===
            $contraseñaActual
        ) {

            echo "<script>

                alert('La nueva contraseña debe ser diferente a la actual.');

                window.location.href =
                '../vistas/perfil/cambiar_password.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // CAMBIAR CONTRASEÑA
        // --------------------------------------------------------

        $resultado =
            $this->usuario->cambiarContraseña(
                $id_usuario,
                $nuevaContraseña
            );


        if (!$resultado) {

            echo "<script>

                alert('No se pudo cambiar la contraseña.');

                window.location.href =
                '../vistas/perfil/cambiar_password.php';

            </script>";

            exit;
        }


        // --------------------------------------------------------
        // AUDITORÍA
        // --------------------------------------------------------

        $this->auditoria->registrar([

            "id_usuario" =>
                $_SESSION["usuario"]["id"],

            "accion" =>
                "EDITAR",

            "tabla_afectada" =>
                "usuario",

            "id_registro" =>
                $id_usuario,

            "descripcion" =>
                "Cambió su contraseña desde el perfil"

        ]);


        // --------------------------------------------------------
        // FINALIZAR
        // --------------------------------------------------------

        echo "<script>

            alert('Contraseña cambiada correctamente.');

            window.location.href =
            '../vistas/perfil/index.php';

        </script>";

        exit;
    }

}


// ============================================================
// INSTANCIAR CONTROLADOR
// ============================================================

$controlador =
    new PerfilController();


// ============================================================
// ACCIONES POST
// ============================================================

if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {


        // --------------------------------------------------------
        // EDITAR PERFIL
        // --------------------------------------------------------

        case "editar":

            $controlador->editar();

            break;


        // --------------------------------------------------------
        // CAMBIAR CONTRASEÑA
        // --------------------------------------------------------

        case "cambiarContraseña":

            $controlador->cambiarContraseña();

            break;


        default:

            echo "Acción no válida.";

            break;
    }

}