<?php

require_once "../../modelos/Obra.php";
require_once "../../config/permisos.php";

verificarPermiso("obras");

$obra = new Obra();
$obras = $obra->obtenerTodos();
$estados = $obra->obtenerEstados();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>
<main class="content">
    <div class="page-title no-print">
        <h1>Obras</h1>
        <p>
            Administración de obras registradas.
        </p>
    </div>
    <div class="table-container">
        <!-- Encabezado para impresión -->
        <div class="print-header">
            <h1>Empresa Constructora</h1>
            <h2>Reporte de Obras</h2>
            <p>
                Listado de obras registradas en el sistema.
            </p>
            <p>
                Fecha de generación:
                <?= date("d/m/Y H:i"); ?>
                <br>
                Generado por:
                <?= $_SESSION["usuario"]["nombre"] . " " . $_SESSION["usuario"]["apellido"]; ?>
            </p>
        </div>
        <div class="toolbar no-print">
            <div class="toolbar-left">

                <input
                    type="text"
                    id="buscarObra"
                    class="search-box"
                    placeholder="Buscar obra...">
                <select id="filtroEstado" class="filter">

                    <option value="">
                        Todos los estados
                    </option>

                    <?php foreach ($estados as $estado) { ?>

                        <option value="<?= strtolower($estado) ?>">
                            <?= $estado ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <button
                    onclick="window.print()" class="btn btn-primary">
                    <i class="fa-solid fa-print"></i>
                </button>
                <a href="agregar.php" class="btn btn-primary"> <i class="fa-solid fa-plus"></i></a>
            </div>
        </div>
        <table
            class="table"
            id="tablaObras">

            <thead>
                <tr>
                    <th>Obra</th>
                    <th>Cliente</th>
                    <th>Dirección</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Estado</th>
                    <th class="no-print">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($obras as $o) { ?>
                    <tr>
                        <td>
                            <?= $o["nombre_obra"] ?>
                        </td>

                        <td>
                            <?= $o["nombre_cliente"] . " " . $o["apellido_cliente"] ?>
                        </td>

                        <td>
                            <?= $o["direccion"] ?>
                        </td>

                        <td>
                            <?= $o["fecha_inicio"] ?>
                        </td>

                        <td>
                            <?= $o["fecha_fin"] ?>
                        </td>

                        <td>
                            <?= $o["estado"] ?>
                        </td>

                        <td class="no-print" style="display: flex;">
                            <a href="editar.php?id=<?= $o["id_obra"] ?>" class="btn btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="ver.php?id=<?= $o["id_obra"] ?>" class="btn btn-secondary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<?php
$script = "obras";
require_once "../../layouts/footer.php";
?>