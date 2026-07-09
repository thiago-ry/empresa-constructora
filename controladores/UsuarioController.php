<?php

require_once "../modelos/Usuario.php";

class UsuarioController
{
    private $usuario;
    public function __construct()
    {
        $this->usuario = new Usuario();
    }

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

        $this->usuario->registrarIngreso($usuarioEncontrado['id_usuario']);

        $_SESSION['usuario'] = [
            "id" => $usuarioEncontrado['id_usuario'],
            "nombre" => $usuarioEncontrado['nombre'],
            "apellido" => $usuarioEncontrado['apellido'],
            "id_rol" => $usuarioEncontrado['id_rol'],
            "rol" => $usuarioEncontrado['nombre_rol']
        ];

        header("Location: ../vistas/dashboard/index.php");
        exit();

    } else {
        echo "Contraseña incorrecta";
    }

} else {
            echo "Usuario no encontrado";
        }
    }
}


// Ejecutar login
if (isset($_POST['accion']) && $_POST['accion'] == "login") {

    $controlador = new UsuarioController();

    $controlador->login();
}
