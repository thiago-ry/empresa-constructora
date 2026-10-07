<?php

$raiz = dirname(__DIR__, 3);
$urlBase = "/empresa_constructora";

require_once $raiz . "/config/permisos.php";

verificarPermiso("obras");

$id_obra = $_GET["id_obra"] ?? 0;

$incidencias = $incidencias ?? [];

$resumen = $resumen ?? [
    "total" => 0,
    "pendientes" => 0,
    "revision" => 0,
    "resueltas" => 0
];

require_once $raiz . "/layouts/header.php";
require_once $raiz . "/layouts/sidebar.php";
?>

<main class="content fade-up">

    <!-- Encabezado Principal -->
    <div class="page-header">
        <div>
            <h1>Incidencias de la obra #<?= htmlspecialchars($id_obra) ?></h1>
            <p>Registro y seguimiento de problemas detectados durante la obra.</p>
        </div>

        <a
            href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=crear&id_obra=<?= $id_obra ?>"
            class="btn btn-primary"
        >
            <i class="fas fa-plus"></i>
            Nueva incidencia
        </a>
    </div>

    <!-- TARJETAS DE RESUMEN (MÉTRICAS) -->
    <div class="card-grid">

        <div class="kpi-card">
            <i class="fas fa-list-check"></i>
            <h2><?= htmlspecialchars($resumen["total"]) ?></h2>
            <h4>Total de incidencias</h4>
        </div>

        <div class="kpi-card">
            <i class="fas fa-clock"></i>
            <h2 style="color: var(--warning);"><?= htmlspecialchars($resumen["pendientes"]) ?></h2>
            <h4>Pendientes</h4>
        </div>

        <div class="kpi-card">
            <i class="fas fa-magnifying-glass"></i>
            <h2 style="color: var(--info);"><?= htmlspecialchars($resumen["revision"]) ?></h2>
            <h4>En revisión</h4>
        </div>

        <div class="kpi-card">
            <i class="fas fa-circle-check"></i>
            <h2 style="color: var(--success);"><?= htmlspecialchars($resumen["resueltas"]) ?></h2>
            <h4>Resueltas</h4>
        </div>

    </div>

    <!-- TABLA DE INCIDENCIAS -->
    <div class="table-container">

        <div class="table-header">
            <h2>Incidencias registradas</h2>
        </div>

        <?php if (empty($incidencias)): ?>

            <div class="empty-state" style="margin: 20px; border: none;">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>No hay incidencias</h3>
                <p>Todavía no se registraron incidencias para esta obra.</p>
            </div>

        <?php else: ?>

            <table class="table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($incidencias as $item): ?>

                    <tr>

                        <td>
                            <strong>#<?= htmlspecialchars($item["id_incidencia"]) ?></strong>
                        </td>

                        <td>
                            <?= !empty($item["fecha"]) ? date("d/m/Y", strtotime($item["fecha"])) : '-' ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($item["tipo_incidencia"] ?? 'N/A') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                mb_strimwidth(
                                    $item["descripcion"] ?? "",
                                    0,
                                    70,
                                    "..."
                                )
                            ) ?>
                        </td>

                        <td>
                            <?php
                            $estado = $item["estado"] ?? 'Pendiente';
                            $badgeClass = "badge-warning";

                            if ($estado === "En revisión") {
                                $badgeClass = "badge-info";
                            } elseif ($estado === "Resuelta") {
                                $badgeClass = "badge-success";
                            }
                            ?>

                            <span class="badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($estado) ?>
                            </span>
                        </td>

                        <td>
                            <div class="table-actions">

                                <a
                                    href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=ver&id=<?= $item["id_incidencia"] ?>"
                                    class="btn btn-secondary"
                                    title="Ver detalle"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a
                                    href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=editar&id=<?= $item["id_incidencia"] ?>"
                                    class="btn btn-warning"
                                    title="Editar"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a
                                    href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=eliminar&id=<?= $item["id_incidencia"] ?>"
                                    class="btn btn-danger"
                                    title="Eliminar"
                                    onclick="return confirm('¿Está seguro de eliminar esta incidencia?');"
                                >
                                    <i class="fas fa-trash"></i>
                                </a>

                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

    <!-- NAVEGACIÓN -->
    <div class="form-actions" style="justify-content: flex-start; margin-top: 25px;">
        <a
            href="<?= $urlBase ?>/vistas/obras/ver.php?id=<?= $id_obra ?>"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left"></i>
            Volver a la obra
        </a>
    </div>

</main>

<?php require_once $raiz . "/layouts/footer.php"; ?>