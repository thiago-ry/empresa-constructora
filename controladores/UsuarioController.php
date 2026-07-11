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



        if(!$usuarioEncontrado){

            echo "<script>
            alert('Usuario no encontrado');
            window.location.href='../vistas/login.php';
            </script>";

            exit();

        }



        if($usuarioEncontrado["estado"] == 0){

            echo "<script>
            alert('El usuario está inactivo');
            window.location.href='../vistas/login.php';
            </script>";

            exit();

        }



        if($password != $usuarioEncontrado["contraseña"]){

            echo "<script>
            alert('Contraseña incorrecta');
            window.location.href='../vistas/login.php';
            </script>";

            exit();

        }



        $this->usuario->registrarIngreso(
            $usuarioEncontrado["id_usuario"]
        );



        $_SESSION["usuario"]=[

            "id"=>$usuarioEncontrado["id_usuario"],
            "nombre"=>$usuarioEncontrado["nombre"],
            "apellido"=>$usuarioEncontrado["apellido"],
            "id_rol"=>$usuarioEncontrado["id_rol"],
            "rol"=>$usuarioEncontrado["nombre_rol"]

        ];




        switch($usuarioEncontrado["nombre_rol"]){


            case "Gerente":
                header("Location: ../vistas/dashboard/gerente.php");
            break;


            case "Administrativo":
                header("Location: ../vistas/dashboard/administrativo.php");
            break;


            case "Jefe de Obra":
                header("Location: ../vistas/dashboard/jefe_obra.php");
            break;


            case "Encargado de Depósito":
                header("Location: ../vistas/dashboard/deposito.php");
            break;


            case "Empleado":
                header("Location: ../vistas/dashboard/empleado.php");
            break;


            case "Cliente":
                header("Location: ../vistas/dashboard/cliente.php");
            break;


            default:

                echo "<script>
                alert('Rol no configurado');
                window.location.href='../vistas/login.php';
                </script>";

            break;

        }


        exit();

    }





    /*
    ==========================
        AGREGAR USUARIO
    ==========================
    */


    public function agregar()
    {

        session_start();



        $datos=[

            "id_rol"=>$_POST["rol"],
            "nombre"=>$_POST["nombre"],
            "apellido"=>$_POST["apellido"],
            "correo"=>$_POST["correo"],
            "contraseña"=>$_POST["password"]

        ];




        if($this->usuario->existeCorreo($datos["correo"])){

            echo "<script>
            alert('El correo ya está registrado');
            window.location.href='../vistas/usuarios/agregar.php';
            </script>";

            exit();

        }




        $id_usuario_creado =
        $this->usuario->agregar($datos);




        // Obtener rol cliente dinámicamente

        $rolCliente =
        $this->usuario->obtenerIdRolCliente();




        if($datos["id_rol"] == $rolCliente["id_rol"]){


            $this->usuario->crearCliente([

                "nombre"=>$datos["nombre"],
                "apellido"=>$datos["apellido"],
                "correo"=>$datos["correo"],
                "id_usuario"=>$id_usuario_creado

            ]);

        }





        $this->auditoria->registrar([

            "id_usuario"=>$_SESSION["usuario"]["id"],
            "accion"=>"INSERTAR",
            "tabla_afectada"=>"usuario",
            "id_registro"=>$id_usuario_creado,
            "descripcion"=>"Registró un nuevo usuario: ".$datos["nombre"]

        ]);




        header("Location: ../vistas/usuarios/index.php");
        exit();

    }








    /*
    ==========================
        EDITAR USUARIO
    ==========================
    */

public function editar()
{

    session_start();


    $datos=[

        "id_usuario"=>$_POST["id_usuario"],
        "id_rol"=>$_POST["id_rol"],
        "nombre"=>$_POST["nombre"],
        "apellido"=>$_POST["apellido"],
        "correo"=>$_POST["correo"]

    ];



    // Rol anterior

    $rolAnterior = 
    $this->usuario->obtenerRolActual(
        $datos["id_usuario"]
    );



    // Obtener rol Cliente

    $rolCliente =
    $this->usuario->obtenerIdRolCliente();




    /*
    ==================================
        SALE DE CLIENTE
    ==================================
    */


    if(

        $rolAnterior["id_rol"] == $rolCliente["id_rol"]
        &&
        $datos["id_rol"] != $rolCliente["id_rol"]

    ){


        // Revisar si tiene obras

        if(
            $this->usuario->tieneObras(
                $datos["id_usuario"]
            )
        ){


            echo "<script>

            alert('No se puede cambiar el rol porque el cliente tiene obras asociadas.');

            window.location.href='../vistas/usuarios/index.php';

            </script>";


            exit();

        }



        // Si no tiene obras elimina cliente

        $this->usuario->eliminarCliente(
            $datos["id_usuario"]
        );


    }






    /*
    ==================================
        ENTRA A CLIENTE
    ==================================
    */


    if(

        $rolAnterior["id_rol"] != $rolCliente["id_rol"]
        &&
        $datos["id_rol"] == $rolCliente["id_rol"]

    ){


        if(
            !$this->usuario->existeCliente(
                $datos["id_usuario"]
            )
        ){


            $this->usuario->crearCliente([

                "nombre"=>$datos["nombre"],
                "apellido"=>$datos["apellido"],
                "correo"=>$datos["correo"],
                "id_usuario"=>$datos["id_usuario"]

            ]);


        }


    }






    /*
    ==================================
        ACTUALIZAR USUARIO
    ==================================
    */


    $this->usuario->editar($datos);





    $this->auditoria->registrar([


        "id_usuario"=>$_SESSION["usuario"]["id"],

        "accion"=>"EDITAR",

        "tabla_afectada"=>"usuario",

        "id_registro"=>$datos["id_usuario"],

        "descripcion"=>"Modificó el usuario ".$datos["nombre"]


    ]);






    header("Location: ../vistas/usuarios/index.php");

    exit();

}
    

    public function eliminar()
    {

        session_start();


        $id=$_GET["id"];



        $this->usuario->bajaLogica($id);




        $this->auditoria->registrar([


            "id_usuario"=>$_SESSION["usuario"]["id"],

            "accion"=>"BAJA",

            "tabla_afectada"=>"usuario",

            "id_registro"=>$id,

            "descripcion"=>"Desactivó un usuario"


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


        $id=$_GET["id"];



        $this->usuario->activarUsuario($id);




        $this->auditoria->registrar([


            "id_usuario"=>$_SESSION["usuario"]["id"],

            "accion"=>"ACTIVAR",

            "tabla_afectada"=>"usuario",

            "id_registro"=>$id,

            "descripcion"=>"Activó nuevamente un usuario"


        ]);




        header("Location: ../vistas/usuarios/index.php");

        exit();

    }


}






$controlador = new UsuarioController();





if(isset($_POST["accion"])) {


    switch($_POST["accion"]) {


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







if(isset($_GET["accion"])) {


    switch($_GET["accion"]) {


        case "baja":

            $controlador->eliminar();

        break;



        case "activar":

            $controlador->activar();

        break;


    }

}


?>