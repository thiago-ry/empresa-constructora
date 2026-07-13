<?php

/** @var array $etapa */

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";
?>

<main class="content">

    <div class="page-title">
        <h1>Editar etapa</h1>
        <p>Modifique los datos de la etapa de la obra.</p>
    </div>
    <div class="form-card">
        <form class="form" action="/empresa_constructora/controladores/EtapaController.php" method="POST">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id_etapa" value="<?= $etapa["id_etapa"] ?>">
            <input type="hidden" name="id_obra" value="<?= $etapa["id_obra"] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_etapa">Nombre de etapa</label>
                    <input type="text" id="nombre_etapa" name="nombre_etapa" class="input" value="<?= htmlspecialchars($etapa["nombre_etapa"]) ?>" required>
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" class="filter" required>
                        <option value="">Seleccione un estado</option>
                        <option value="Pendiente" <?= $etapa["estado"] == "Pendiente" ? "selected" : "" ?>>
                            Pendiente
                        </option>
                        <option value="En Proceso" <?= $etapa["estado"] == "En Proceso" ? "selected" : "" ?>>
                            En Proceso
                        </option>
                        <option value="Finalizada" <?= $etapa["estado"] == "Finalizada" ? "selected" : "" ?>>
                            Finalizada
                        </option>
                        <option value="Cancelada" <?= $etapa["estado"] == "Cancelada" ? "selected" : "" ?>>
                            Cancelada
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="input" value="<?= $etapa["fecha_inicio"] ?>">
                </div>
                <div class="form-group">
                    <label for="fecha_fin">Fecha fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="input" value="<?= $etapa["fecha_fin"] ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="input" rows="4"><?= htmlspecialchars($etapa["descripcion"]) ?></textarea>
                </div>
            </div>
            <div class="form-actions">

                <a href="/empresa_constructora/vistas/obras/etapas/index.php?id_obra=<?= $etapa["id_obra"] ?>" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>