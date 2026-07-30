<?php

require_once "../../modelos/Cliente.php";
require_once "../../config/permisos.php";
require_once "../../modelos/Usuario.php";

verificarPermiso("clientes");

$cliente = new Cliente();

$clientes = $cliente->obtenerTodos();
$estadisticas = $cliente->obtenerEstadisticas();

$usuario = new Usuario();
$usuarios = $usuario->obtenerTodos();


require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title no-print">

        <h1>Clientes</h1>

        <p>
            Administración de clientes registrados.
        </p>

    </div>

    <div class="alert-container no-print">

        <div class="alert alert-info">
            <p>
                Total
            </p>
            <h3><?= $estadisticas['total_clientes']; ?> clientes</h3>
        </div>

        <div class="alert alert-success">
            <p>
                Activos
            </p>
            <h3><?= $estadisticas['activos']; ?> clientes activos</h3>
        </div>

        <div class="alert alert-danger">
            <p>
                Inactivos
            </p>
            <h3><?= $estadisticas['inactivos']; ?> clientes inactivos</h3>
        </div>

    </div>

    <div class="table-container">

        <div class="print-header">

            <h1>
                Empresa Constructora
            </h1>

            <h2>
                Reporte de Clientes
            </h2>

            <p>
                Listado de clientes registrados.
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
                    id="buscarCliente"
                    class="search-box"
                    placeholder="Buscar cliente...">

                <select
                    id="filtroEstado"
                    class="filter">

                    <option value="">
                        Todos los estados
                    </option>

                    <option value="Activo">
                        Activos
                    </option>

                    <option value="Inactivo">
                        Inactivos
                    </option>

                </select>

            </div>

            <div style="display: flex; flex-direction: column; margin: 20px;">

                <button
                    onclick="window.print()"
                    class="btn btn-primary"
                    style="margin-bottom: 10px;">

                    <i class="fa-solid fa-print"></i>
                    Imprimir

                </button>

                <a
                    href="agregar.php"
                    class="btn btn-primary">

                    <i class="fa-solid fa-plus"></i>
                    Agregar

                </a>

            </div>

        </div>

        <table class="table" id="tablaClientes">

            <thead>

                <tr>

                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Obras</th>
                    <th>Estado</th>
                    <th class="no-print">Acciones</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($clientes as $c) { ?>

                    <tr
                        data-estado="<?= $c['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>">

                        <td>
                            <?= $c["nombre"] . " " . $c["apellido"]; ?>
                        </td>

                        <td>
                            <?= $c["documento"]; ?>
                        </td>

                        <td title="<?= $c["correo"]; ?>">
                            <?= strlen($c["correo"]) > 30
                                ? substr($c["correo"], 0, 30) . "..."
                                : $c["correo"]; ?>
                        </td>

                        <td>
                            <?= $c["telefono"]; ?>
                        </td>

                        <td>
                            <?= $c["total_obras"]; ?>
                        </td>

                        <td>

                            <span class="<?= $c['estado'] == 1 ? 'badge badge-success' : 'badge badge-danger' ?>">

                                <?= $c['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>

                            </span>

                        </td>

                        <td class="no-print">

                            <div class="table-actions">

                                <a
                                    href="ver.php?id=<?= $c['id_usuario']; ?>"
                                    class="btn btn-secondary">

                                    <i class="fa-solid fa-eye"></i>

                                </a>

                                <a href="editar.php?id=<?= $c['id_usuario']; ?>" class="btn btn-warning">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <?php if ($c["estado"] == 1) { ?>

                                    <?php if ($usuario->tieneObras($c["id_usuario"])) { ?>

                                        <button
                                            class="btn btn-secondary"
                                            disabled
                                            title="No se puede dar de baja porque tiene obras asociadas">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>

                                    <?php } else { ?>

                                        <a
                                            href="../../controladores/ClienteController.php?accion=baja&id=<?= $c['id_usuario']; ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('¿Está seguro que desea desactivar a este cliente?');">
                                            <i class="fa-solid fa-times"></i>
                                        </a>

                                    <?php } ?>

                                <?php } else { ?>

                                    <a
                                        href="../../controladores/ClienteController.php?accion=activar&id=<?= $c['id_usuario']; ?>"
                                        class="btn btn-success">
                                        <i class="fa-solid fa-check"></i>
                                    </a>

                                <?php } ?>


                            </div>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</main>

<?php
$script = "clientes";

require_once "../../layouts/footer.php";
?>