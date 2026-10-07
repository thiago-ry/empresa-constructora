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

    <!-- Encabezado Principal -->
    <div class="page-header">
        <div>
            <h1>Editar incidencia #<?= htmlspecialchars($id) ?></h1>
            <p>Modifica los datos, estado o gestiona las fotos de la incidencia.</p>
        </div>
    </div>

    <!-- Formulario Principal -->
    <div class="form-card mb-4">

        <form
            action="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=actualizar"
            method="POST"
            enctype="multipart/form-data"
            class="form"
        >

            <input
                type="hidden"
                name="id_incidencia"
                value="<?= htmlspecialchars($id) ?>"
            >

            <!-- Fila 1: Fecha y Tipo de Incidencia -->
            <div class="form-row">

                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input
                        type="date"
                        id="fecha"
                        name="fecha"
                        class="input"
                        value="<?= htmlspecialchars($datos["fecha"] ?? "") ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="tipo_incidencia">Tipo de incidencia</label>
                    <select
                        id="tipo_incidencia"
                        name="tipo_incidencia"
                        class="filter"
                        required
                    >
                        <?php
                        $tipos = [
                            "Material",
                            "Seguridad",
                            "Clima",
                            "Herramientas",
                            "Personal",
                            "Cliente",
                            "Diseño/Planos",
                            "Retraso"
                        ];
                        $tipoActual = $datos["tipo_incidencia"] ?? "";
                        ?>

                        <?php foreach ($tipos as $tipo): ?>
                            <option
                                value="<?= htmlspecialchars($tipo) ?>"
                                <?= $tipoActual === $tipo ? "selected" : "" ?>
                            >
                                <?= htmlspecialchars($tipo) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <!-- Fila 2: Estado y Agregar Nuevas Fotos -->
            <div class="form-row">

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <?php $estadoActual = $datos["estado"] ?? "Pendiente"; ?>
                    <select
                        id="estado"
                        name="estado"
                        class="filter"
                    >
                        <option value="Pendiente" <?= $estadoActual === "Pendiente" ? "selected" : "" ?>>
                            Pendiente
                        </option>
                        <option value="En revisión" <?= $estadoActual === "En revisión" ? "selected" : "" ?>>
                            En revisión
                        </option>
                        <option value="Resuelta" <?= $estadoActual === "Resuelta" ? "selected" : "" ?>>
                            Resuelta
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fotos">
                        Subir nuevas fotos
                    </label>
                    <input
                        type="file"
                        id="fotos"
                        name="fotos[]"
                        class="input"
                        style="padding-top: 10px;"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                    >
                    <small style="color: var(--text-secondary); margin-top: 4px; display: block;">
                        Las fotos que selecciones aquí se sumarán a las ya cargadas.
                    </small>
                </div>

            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea
                    id="descripcion"
                    name="descripcion"
                    class="textarea"
                    rows="4"
                    required
                ><?= htmlspecialchars($datos["descripcion"] ?? "") ?></textarea>
            </div>

            <!-- Solución -->
            <div class="form-group">
                <label for="solucion">Solución</label>
                <textarea
                    id="solucion"
                    name="solucion"
                    class="textarea"
                    rows="4"
                    placeholder="Escribe la resolución o medidas tomadas..."
                ><?= htmlspecialchars($datos["solucion"] ?? "") ?></textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="form-actions">

                <a
                    href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=ver&id=<?= $id ?>"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="fas fa-save"></i>
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

    <!-- FOTOS ACTUALES DE LA INCIDENCIA -->
    <div class="card card-detail mt-4" style="margin-top: 25px;">
        <div class="card-header-clean space-between">
            <div class="title-with-icon">
                <i class="fas fa-images text-primary"></i>
                <h2>Fotos cargadas actualmente</h2>
            </div>
            <span class="badge badge-secondary"><?= count($fotos) ?> <?= count($fotos) === 1 ? 'Foto' : 'Fotos' ?></span>
        </div>

        <div class="card-body" style="padding: 24px;">
            <?php if (empty($fotos)): ?>
                <div class="empty-state" style="padding: 30px; border: none;">
                    <i class="fas fa-image" style="font-size: 40px;"></i>
                    <p style="margin-top: 10px; color: var(--text-secondary);">Esta incidencia aún no tiene fotos adjuntas.</p>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="gallery-item">
                            <img src="<?= $urlBase ?>/uploads/incidencias/<?= htmlspecialchars($foto["ruta_foto"]) ?>" alt="Foto de incidencia">
                            <div class="gallery-overlay">
                                <div class="gallery-info">
                                    <small><i class="far fa-clock"></i> <?= date("d/m/Y H:i", strtotime($foto["fecha"])) ?></small>
                                </div>
                                <div class="gallery-actions">
                                    <a href="<?= $urlBase ?>/uploads/incidencias/<?= htmlspecialchars($foto["ruta_foto"]) ?>" target="_blank" class="btn-icon" title="Ver foto grande">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                    <a 
                                        href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=eliminarFoto&id_foto=<?= $foto["id_foto_incidencia"] ?>&redirect=editar" 
                                        class="btn-icon btn-icon-danger" 
                                        onclick="return confirm('¿Eliminar esta foto?');" 
                                        title="Eliminar esta foto"
                                    >
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

</main>

<?php require_once $raiz . "/layouts/footer.php"; ?>