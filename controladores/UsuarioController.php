<?php


require_once "../modelos/Auditoria.php";
require_once "../modelos/Usuario.php";


class UsuarioController
{

    private $auditoria;
    private $usuario;


    public function __construct()
    {

        $this->auditoria = new Auditoria();
        $this->usuario = new Usuario();
    }


    /*
    ==========================
        LOGIN
    ==========================
    */

    public function login()
    {

        session_start();


        $correo = $_POST['correo'];
        $password = $_POST['password'];


        $usuarioEncontrado = $this->usuario->buscarPorCorreo($correo);



        if ($usuarioEncontrado) {


            if ($usuarioEncontrado['estado'] == 0) {
                echo "Usuario inactivo";
                exit();
            }



            if ($password == $usuarioEncontrado['contraseña']) {


                $this->usuario->registrarIngreso(
                    $usuarioEncontrado['id_usuario']
                );


                $_SESSION['usuario'] = [

                    "id" => $usuarioEncontrado['id_usuario'],
                    "nombre" => $usuarioEncontrado['nombre'],
                    "apellido" => $usuarioEncontrado['apellido'],
                    "id_rol" => $usuarioEncontrado['id_rol'],
                    "rol" => $usuarioEncontrado['nombre_rol']

                ];



                switch ($usuarioEncontrado['nombre_rol']) {

                    case "Gerente":

                        header("Location: ../vistas/dashboard/gerente.php");

                        break;


                    case "Administrativo":

                        header("Location: ../vistas/dashboard/administrativo.php");

                        break;


                    case "Jefe de Obra":

                        header("Location: ../vistas/dashboard/jefe_obra.php");

                        break;


                    case "Depósito":

                        header("Location: ../vistas/dashboard/deposito.php");

                        break;


                    case "Empleado":

                        header("Location: ../vistas/dashboard/empleado.php");

                        break;


                    case "Cliente":

                        header("Location: ../vistas/dashboard/cliente.php");

                        break;


                    default:

                        echo "Rol no configurado";

                        break;
                }


                exit();
            } else {
                echo "Contraseña incorrecta";
            }
        } else {
            echo "Usuario no encontrado";
        }
    }



    /*
    ==========================
        AGREGAR
    ==========================
    */


    public function agregar()
    {
        session_start();

        $datos = [

            "id_rol" => $_POST["rol"],
            "nombre" => $_POST["nombre"],
            "apellido" => $_POST["apellido"],
            "correo" => $_POST["correo"],
            "contraseña" => $_POST["password"]

        ];

        if ($this->usuario->existeCorreo($datos["correo"])) {
            echo "<script>alert('El correo ya está registrado. Por favor, utiliza otro correo.');  window.location.href='../vistas/usuarios/agregar.php';</script>";
            exit();
        }



        $id_usuario_creado =
            $this->usuario->agregar($datos);



        $this->auditoria->registrar([


            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "INSERTAR",

            "tabla_afectada" => "usuario",

            "id_registro" => $id_usuario_creado,

            "descripcion" =>
            "Registró un nuevo usuario: " . $datos["nombre"]


        ]);



        header("Location: ../vistas/usuarios/index.php");

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


            "id_usuario" => $_POST["id_usuario"],

            "id_rol" => $_POST["id_rol"],

            "nombre" => $_POST["nombre"],

            "apellido" => $_POST["apellido"],

            "correo" => $_POST["correo"]

        ];



        $this->usuario->editar($datos);



        $this->auditoria->registrar([


            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "EDITAR",

            "tabla_afectada" => "usuario",

            "id_registro" => $datos["id_usuario"],

            "descripcion" =>
            "Modificó el usuario " . $datos["nombre"]


        ]);



        header("Location: ../vistas/usuarios/index.php");

        exit();
    }





    /*
    ==========================
        BAJA
    ==========================
    */


    public function eliminar()
    {
        session_start();

        $id = $_GET["id"];



        $this->usuario->bajaLogica($id);



        $this->auditoria->registrar([


            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "BAJA",

            "tabla_afectada" => "usuario",

            "id_registro" => $id,

            "descripcion" =>
            "Desactivó un usuario"


        ]);



        header("Location: ../vistas/usuarios/index.php");

        exit();
    }





    /*
    ==========================
        ACTIVAR
    ==========================
    */


    public function activar()
    {
        session_start();

        $id = $_GET["id"];


        $this->usuario->activarUsuario($id);



        $this->auditoria->registrar([


            "id_usuario" => $_SESSION["usuario"]["id"],

            "accion" => "ACTIVAR",

            "tabla_afectada" => "usuario",

            "id_registro" => $id,

            "descripcion" =>
            "Activó nuevamente un usuario"


        ]);



        header("Location: ../vistas/usuarios/index.php");

        exit();
    }
}



$controlador = new UsuarioController();



if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

        case "login":
            $controlador->login();
            break;


        case "agregar":
            $controlador->agregar();
            break;


        case "editar":
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
