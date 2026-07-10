<?php

require_once "../../modelos/Dashboard.php";

$dashboard = new Dashboard();
$datos = $dashboard->obtenerDatos();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<main class="content">

<div class="page-title">
<h1>Dashboard</h1>
<p>Bienvenido, <?= $_SESSION['usuario']['nombre']; ?>.</p>
</div>

<div class="card-grid">

<div class="card">
<h3>Obras Activas</h3>
<h2><?= $datos['obras']; ?></h2>
<p>Actualmente en ejecución.</p>
</div>

<div class="card">
<h3>Clientes</h3>
<h2><?= $datos['clientes']; ?></h2>
<p>Registrados en el sistema.</p>
</div>

<div class="card">
<h3>Empleados</h3>
<h2><?= $datos['empleados']; ?></h2>
<p>Personal activo.</p>
</div>

<div class="card">
<h3>Materiales</h3>
<h2><?= $datos['materiales']; ?></h2>
<p>En inventario.</p>
</div>

</div>

</main>

<?php
require_once "../../layouts/footer.php";
?>