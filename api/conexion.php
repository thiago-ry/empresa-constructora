<?php
$host     = "localhost";
$usuario  = "root";
$password = "";          
$database = "constructora"; 

$conexion = new mysqli($host, $usuario, $password, $database);

if ($conexion->connect_error) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(["status" => "error", "message" => "Error de conexión a la Base de Datos: " . $conexion->connect_error]);
    exit();
}

$conexion->set_charset("utf8mb4");
?>