<?php

$raiz = dirname(__DIR__, 3);
$urlBase = "/empresa_constructora";

require_once $raiz . "/config/permisos.php";

verificarPermiso("obras");

$id_obra = $_GET["id_obra"] ?? 0;

require_once $raiz . "/layouts/header.php";
require_once $raiz . "/layouts/sidebar.php";
?>

<main class="content fade-up">

    <!-- Encabezado Principal -->
    <div class="page-header">
        <div>
            <h1>Nueva incidencia</h1>
            <p>Registrar un nuevo problema o reporte en la obra #<?= htmlspecialchars($id_obra) ?>.</p>
        </div>
    </div>

    <!-- Formulario Principal -->
    <div class="form-card">

        <form
            action="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=guardar"
            method="POST"
            enctype="multipart/form-data"
            class="form"
        >

            <input
                type="hidden"
                name="id_obra"
                value="<?= htmlspecialchars($id_obra) ?>"
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
                        value="<?= date("Y-m-d") ?>"
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
                        <option value="" disabled selected>Seleccionar tipo</option>
                        <option value="Material">Material</option>
                        <option value="Seguridad">Seguridad</option>
                        <option value="Clima">Clima</option>
                        <option value="Herramientas">Herramientas</option>
                        <option value="Personal">Personal</option>
                        <option value="Cliente">Cliente</option>
                        <option value="Diseño/Planos">Diseño/Planos</option>
                        <option value="Retraso">Retraso</option>
                    </select>
                </div>

            </div>

            <!-- Fila 2: Estado y Adjuntar Fotos -->
            <div class="form-row">

                <div class="form-group">
                    <label for="estado">Estado inicial</label>
                    <select
                        id="estado"
                        name="estado"
                        class="filter"
                    >
                        <option value="Pendiente" selected>Pendiente</option>
                        <option value="En revisión">En revisión</option>
                        <option value="Resuelta">Resuelta</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fotos">Fotos adjuntas</label>
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
                        Podés seleccionar una o varias imágenes.
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
                    placeholder="Describí en detalle qué ocurrió..."
                    required
                ></textarea>
            </div>

            <!-- Solución -->
            <div class="form-group">
                <label for="solucion">Solución (Opcional)</label>
                <textarea
                    id="solucion"
                    name="solucion"
                    class="textarea"
                    rows="4"
                    placeholder="Indicar la solución aplicada, si corresponde..."
                ></textarea>
            </div>

            <!-- Acciones -->
            <div class="form-actions">

                <a
                    href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=listar&id_obra=<?= $id_obra ?>"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="fas fa-save"></i>
                    Guardar incidencia
                </button>

            </div>

        </form>

    </div>

</main>

<?php require_once $raiz . "/layouts/footer.php"; ?>