<?php

if (!isset($avances)) {

    header("Location: ../../../controladores/AvanceController.php?accion=listar&id_obra=" . $_GET["id_obra"]);

    exit();

}


$cantidad = $cantidad ?? 0;
$primero = $primero ?? null;
$ultimo = $ultimo ?? null;

require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Diario de Obra
            </h1>

            <p class="page-subtitle">
                Registro de los avances diarios realizados en la obra.
            </p>

        </div>

        <a href="/empresa_constructora/vistas/obras/ver.php?id=<?= $_GET["id_obra"] ?>"
            class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver

        </a>

    </div>


    <div class="cards-grid">

        <div class="dashboard-card">

            <h3>Total de avances</h3>

            <h2>
                <?= $cantidad ?>
            </h2>

        </div>

        <div class="dashboard-card">

            <h3>Primer avance</h3>

            <h2>

                <?= $primero ? date("d/m/Y", strtotime($primero["fecha"])) : "-" ?>

            </h2>

        </div>

        <div class="dashboard-card">

            <h3>Último avance</h3>

            <h2>

                <?= $ultimo ? date("d/m/Y", strtotime($ultimo["fecha"])) : "-" ?>

            </h2>

        </div>

    </div>


    <div class="table-container">

        <div class="table-header">

            <h2>

                Listado de avances

            </h2>

            <div>

                <button
                    onclick="window.print()"
                    class="btn btn-primary">

                    <i class="fa-solid fa-print"></i>

                </button>

                <a
                    href="/empresa_constructora/controladores/AvanceController.php?accion=crear&id_obra=<?= $_GET["id_obra"] ?>"
                    class="btn btn-primary">

                    <i class="fa-solid fa-plus"></i>

                </a>

            </div>

        </div>


        <?php if (count($avances) > 0) { ?>

            <table class="table">

                <thead>

                    <tr>

                        <th>Fecha</th>

                        <th>Actividad realizada</th>

                        <th class="no-print">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($avances as $avance) { ?>

                        <tr>

                            <td>

                                <?= date("d/m/Y", strtotime($avance["fecha"])) ?>

                            </td>

                            <td>

                                <?= nl2br(htmlspecialchars($avance["descripcion"])) ?>

                            </td>

                            <td class="no-print">

                                <a
                                    href="/empresa_constructora/controladores/AvanceController.php?accion=editar&id=<?= $avance["id_avance_diario"] ?>&id_obra=<?= $_GET["id_obra"] ?>" class="btn btn-warning">

                                    <i class="fa-solid fa-pen"></i>

                                </a>

                                <a
                                    href="/empresa_constructora/controladores/AvanceController.php?accion=eliminar&id=<?= $avance["id_avance_diario"] ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('¿Desea eliminar este avance?')">

                                    <i class="fa-solid fa-trash"></i>

                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div style="padding:40px;text-align:center;">

                <i class="fa-solid fa-clipboard-list fa-3x"></i>

                <br><br>

                <h3>

                    Todavía no hay avances registrados.

                </h3>

                <p>

                    Presione el botón <strong>+</strong> para registrar el primer avance diario.

                </p>

            </div>

        <?php } ?>

    </div>

</main>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>