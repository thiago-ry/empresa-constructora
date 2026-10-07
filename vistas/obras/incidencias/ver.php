<?php

$raiz = dirname(__DIR__, 3);
$urlBase = "/empresa_constructora";

require_once $raiz . "/config/permisos.php";

verificarPermiso("obras");

$datos = $datos ?? [];
$fotos = $fotos ?? [];

$id = $datos["id_incidencia"] ?? 0;
$id_obra = $datos["id_obra"] ?? 0;

require_once $raiz . "/layouts/header.php";
require_once $raiz . "/layouts/sidebar.php";
?>

<main class="content fade-up">

    <!-- Encabezado de la Página -->
    <div class="page-header">
        <div class="header-title">
            <div class="badge-id">Incidencia #<?= htmlspecialchars($id) ?></div>
            <h1><?= htmlspecialchars($datos["tipo_incidencia"] ?? 'Incidencia General') ?></h1>
            <p>Registrada en la Obra #<?= htmlspecialchars($id_obra) ?></p>
        </div>

        <div class="btn-container">
            <a href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=listar&id_obra=<?= $id_obra ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a incidencias
            </a>
            <a href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=editar&id=<?= $id ?>" class="btn btn-primary">
                <i class="fas fa-pen"></i> Editar incidencia
            </a>
        </div>
    </div>

    <!-- Layout en 2 Columnas -->
    <div class="incidencia-grid">

        <!-- Columna Principal -->
        <div class="incidencia-main">
            
            <!-- Descripción -->
            <div class="card card-detail">
                <div class="card-header-clean">
                    <i class="fas fa-align-left text-primary"></i>
                    <h2>Descripción del problema</h2>
                </div>
                <div class="card-body">
                    <p class="description-text">
                        <?= nl2br(htmlspecialchars($datos["descripcion"] ?? "Sin descripción detallada registrada.")) ?>
                    </p>
                </div>
            </div>

            <!-- Solución -->
            <div class="card card-detail <?= !empty($datos["solucion"]) ? 'border-success' : '' ?>">
                <div class="card-header-clean">
                    <i class="fas fa-check-circle <?= !empty($datos["solucion"]) ? 'text-success' : 'text-muted' ?>"></i>
                    <h2>Solución registrada</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($datos["solucion"])): ?>
                        <p class="solution-text">
                            <?= nl2br(htmlspecialchars($datos["solucion"])) ?>
                        </p>
                    <?php else: ?>
                        <div class="empty-solution">
                            <i class="fas fa-info-circle"></i>
                            <span>Aún no se ha registrado una solución para esta incidencia.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Galería de Fotos -->
            <div class="card card-detail">
                <div class="card-header-clean space-between">
                    <div class="title-with-icon">
                        <i class="fas fa-camera text-primary"></i>
                        <h2>Evidencia fotográfica</h2>
                    </div>
                    <span class="badge badge-secondary"><?= count($fotos) ?> <?= count($fotos) === 1 ? 'Foto' : 'Fotos' ?></span>
                </div>

                <div class="card-body">
                    <?php if (empty($fotos)): ?>
                        <div class="empty-state">
                            <i class="fas fa-images"></i>
                            <h3>Sin fotografías</h3>
                            <p>No se han adjuntado imágenes a esta incidencia.</p>
                        </div>
                    <?php else: ?>
                        <div class="gallery-grid">
                            <?php foreach ($fotos as $foto): ?>
                                <div class="gallery-item">
                                    <img src="<?= $urlBase ?>/uploads/incidencias/<?= htmlspecialchars($foto["ruta_foto"]) ?>" alt="Evidencia de la incidencia">
                                    <div class="gallery-overlay">
                                        <div class="gallery-info">
                                            <small><i class="far fa-clock"></i> <?= date("d/m/Y H:i", strtotime($foto["fecha"])) ?></small>
                                        </div>
                                        <div class="gallery-actions">
                                            <a href="<?= $urlBase ?>/uploads/incidencias/<?= htmlspecialchars($foto["ruta_foto"]) ?>" target="_blank" class="btn-icon" title="Ver imagen en tamaño completo">
                                                <i class="fas fa-expand"></i>
                                            </a>
                                            <a href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=eliminarFoto&id_foto=<?= $foto["id_foto_incidencia"] ?>" class="btn-icon btn-icon-danger" onclick="return confirm('¿Eliminar esta foto permanentemente?');" title="Eliminar foto">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Columna Lateral / Métricas de la Incidencia -->
        <div class="incidencia-sidebar">
            
            <div class="card card-sidebar">
                <div class="card-header-clean">
                    <h2>Detalles de control</h2>
                </div>
                <div class="card-body compact-body">
                    
                    <!-- Estado -->
                    <div class="sidebar-item">
                        <span class="sidebar-label">Estado Actual</span>
                        <?php
                        $estado = $datos["estado"] ?? 'Pendiente';
                        $badgeClass = "badge-warning";
                        $iconState = "fa-clock";

                        if ($estado === "En revisión") {
                            $badgeClass = "badge-info";
                            $iconState = "fa-search";
                        } elseif ($estado === "Resuelta") {
                            $badgeClass = "badge-success";
                            $iconState = "fa-check-double";
                        }
                        ?>
                        <div>
                            <span class="badge <?= $badgeClass ?> badge-lg">
                                <i class="fas <?= $iconState ?>"></i> <?= htmlspecialchars($estado) ?>
                            </span>
                        </div>
                    </div>

                    <div class="divider-sm"></div>

                    <!-- Obra vinculada -->
                    <div class="sidebar-item">
                        <span class="sidebar-label">Obra Vinculada</span>
                        <div class="sidebar-value-block">
                            <i class="fas fa-building text-secondary"></i>
                            <div>
                                <strong>Obra #<?= htmlspecialchars($id_obra) ?></strong>
                                <a href="<?= $urlBase ?>/vistas/obras/ver.php?id=<?= $id_obra ?>" class="block-link">Ver ficha de la obra &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <div class="divider-sm"></div>

                    <!-- Fecha de Reporte -->
                    <div class="sidebar-item">
                        <span class="sidebar-label">Fecha del Reporte</span>
                        <div class="sidebar-value">
                            <i class="far fa-calendar-alt text-secondary"></i>
                            <strong><?= !empty($datos["fecha"]) ? date("d/m/Y", strtotime($datos["fecha"])) : 'Sin fecha' ?></strong>
                        </div>
                    </div>

                    <div class="divider-sm"></div>

                    <!-- Categoría -->
                    <div class="sidebar-item">
                        <span class="sidebar-label">Tipo de problema</span>
                        <div class="sidebar-value">
                            <i class="fas fa-tag text-secondary"></i>
                            <span><?= htmlspecialchars($datos["tipo_incidencia"] ?? 'No clasificado') ?></span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

</main>

<?php require_once $raiz . "/layouts/footer.php"; ?>