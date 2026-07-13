<?php

if (!isset($etapas)) {

    header("Location: ../../../controladores/EtapaController.php?accion=listar&id_obra=" . $_GET["id_obra"]);

    exit();
}
?>
<?php

require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";
?>
<main class="content">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                Etapas de la Obra
            </h1>
            <p>
                Gestión de etapas constructivas.
            </p>
        </div>
        <a href="/empresa_constructora/vistas/obras/ver.php?id=<?= $_GET["id_obra"] ?>" class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver

        </a>
    </div>
    <div class="table-container">
        <div class="table-header">
            <h2>
                Listado de etapas
            </h2>
            <div>
               <button
                    onclick="window.print()"
                    class="btn btn-primary">
                    <i class="fa-solid fa-print"></i>
                </button>
            <a href="../vistas/obras/etapas/crear.php?id_obra=<?= $_GET["id_obra"] ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
            </a>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Etapa</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th class="no-print">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($etapas as $etapa): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($etapa["nombre_etapa"]) ?>
                        </td>
                        <td>
                            <span class="badge">
                                <?= htmlspecialchars($etapa["estado"]) ?>
                            </span>
                        </td>
                        <td>
                            <?= $etapa["fecha_inicio"] ?>
                        </td>
                        <td>
                            <?= $etapa["fecha_fin"] ?>
                        </td>
                        <td>
                            <a href="/empresa_constructora/controladores/EtapaController.php?accion=editar&id=<?= $etapa["id_etapa"] ?>"
                                class="btn btn-warning">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>