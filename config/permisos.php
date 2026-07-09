<?php
require_once "../modelos/Permiso.php";
function verificarPermiso($permiso)
{
    session_start();
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../login.php");
        exit();
    }
    $modelo = new Permiso();
    $id_rol = $_SESSION['usuario']['id_rol'];
    if (!$modelo->tienePermiso($id_rol,$permiso)) {
        echo "No tiene permisos para acceder a este módulo";
        exit();
    }

}