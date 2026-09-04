
<?php
$host     = "localhost";
$usuario  = "root";
$password = "";          // o tu contraseña si tienes
$database = "constructora"; // 👈 AQUÍ: Debe decir 'constructora'

$conexion = new mysqli($host, $usuario, $password, $database);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer conjunto de caracteres a utf8
$conexion->set_charset("utf8mb4");
?>