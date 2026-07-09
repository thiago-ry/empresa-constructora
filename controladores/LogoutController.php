<?php
session_start();

require_once "../modelos/Usuario.php";

if(isset($_SESSION['usuario'])){

    $usuario = new Usuario();

    $usuario->registrarSalida($_SESSION['usuario']['id']);

}

session_destroy();

header("Location: ../vistas/login.php");
exit();
?>