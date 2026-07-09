<?php
require_once "../../config/seguridad.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard | BUILDPRO</title>
</head>

<body>

<h1>Bienvenido <?= $_SESSION['usuario']['nombre'] . " " . $_SESSION['usuario']['apellido']; ?></h1>

<p>Rol: <?= $_SESSION['usuario']['rol']; ?></p>

<a href="../../controladores/LogoutController.php">Cerrar sesión</a>

</body>
</html>