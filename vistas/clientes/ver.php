<?php

require_once "../../modelos/Cliente.php";
require_once "../../config/permisos.php";

verificarPermiso("clientes");

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$modeloCliente = new Cliente();

$cliente = $modeloCliente->obtenerPorId($_GET["id"]);

if (!$cliente) {
    header("Location: index.php");
    exit;
}

$obras = $modeloCliente->obtenerObras($_GET["id"]);
$resumen = $modeloCliente->obtenerResumen($_GET["id"]);

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title no-print">

        <h1>
            <?= $cliente["nombre"] . " " . $cliente["apellido"]; ?>
        </h1>

        <p>
            Información completa del cliente.
        </p>

    </div>

    <div class="alert-container">

        <div class="alert alert-info">

            <p>Total Obras</p>

            <h3>
                <?= $resumen["total_obras"]; ?>
            </h3>

        </div>

        <div class="alert alert-success">

            <p>Obras Activas</p>

            <h3>
                <?= $resumen["obras_activas"] ?? 0; ?>
            </h3>

        </div>

        <div class="alert alert-warning">

            <p>Obras Finalizadas</p>

            <h3>
                <?= $resumen["obras_finalizadas"] ?? 0; ?>
            </h3>

        </div>

    </div>

    <div class="table-container">

        <h2 style="margin-bottom:20px;">
            Datos del Cliente
        </h2>

        <table class="table">

            <tbody>

                <tr>
                    <th>Nombre Completo</th>
                    <td><?= $cliente["nombre"] . " " . $cliente["apellido"]; ?></td>
                </tr>

                <tr>
                    <th>Documento</th>
                    <td><?= $cliente["documento"]; ?></td>
                </tr>

                <tr>
                    <th>Correo</th>
                    <td><?= $cliente["correo"]; ?></td>
                </tr>

                <tr>
                    <th>Teléfono</th>
                    <td><?= $cliente["telefono"]; ?></td>
                </tr>

                <tr>
                    <th>Dirección</th>
                    <td><?= $cliente["direccion"] ?: "No registrada"; ?></td>
                </tr>

                <tr>
                    <th>Estado</th>
                    <td>

                        <span class="<?= $cliente['estado'] == 1 ? 'badge badge-success' : 'badge badge-danger' ?>">

                            <?= $cliente['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>

                        </span>

                    </td>
                </tr>

            </tbody>

        </table>

    </div>

    <div class="table-container">

        <div class="toolbar">

            <div class="toolbar-left">

                <h2>
                    Obras Asociadas
                </h2>

            </div>

            <div>

                <a
                    href="index.php"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>
                    Volver

                </a>

            </div>

        </div>

        <table class="table">

            <thead>

                <tr>

                    <th>Obra</th>
                    <th>Dirección</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

                <?php if (empty($obras)) { ?>

                    <tr>

                        <td colspan="6">

                            No posee obras registradas.

                        </td>

                    </tr>

                <?php } ?>

                <?php foreach ($obras as $obra) { ?>

                    <tr>

                        <td>
                            <?= $obra["nombre_obra"]; ?>
                        </td>

                        <td>
                            <?= $obra["direccion"]; ?>
                        </td>

                        <td>
                            <?= date("d/m/Y", strtotime($obra["fecha_inicio"])); ?>
                        </td>

                        <td>

                            <?= !empty($obra["fecha_fin"])
                                ? date("d/m/Y", strtotime($obra["fecha_fin"]))
                                : "-"; ?>

                        </td>

                        <td>
                            <?= $obra["estado"]; ?>
                        </td>

                        <td>

                            <a
                                href="../obras/ver.php?id=<?= $obra["id_obra"]; ?>"
                                class="btn btn-info">

                                <i class="fa-solid fa-eye"></i>

                            </a>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</main>

<?php require_once "../../layouts/footer.php"; ?>