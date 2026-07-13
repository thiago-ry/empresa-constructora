<?php

require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");


require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>
<main class="content">
    <div class="page-title">
        <h1>Nueva etapa</h1>
        <p>
            Complete los datos para registrar una nueva etapa de la obra.
        </p>
    </div>
    <div class="form-card">
        <form class="form"
            action="../../../controladores/EtapaController.php"
            method="POST">
            <input type="hidden" name="accion" value="guardar">
            <input
                type="hidden"
                name="id_obra"
                value="<?= $_GET["id_obra"] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre_etapa">
                        Nombre de etapa
                    </label>
                    <input
                        type="text"
                        id="nombre_etapa"
                        name="nombre_etapa"
                        class="input"
                        placeholder="Ej: Cimentación"
                        maxlength="100"
                        required>
                </div>
                <div class="form-group">
                    <label for="estado">
                        Estado
                    </label>

                    <select
                        id="estado"
                        name="estado"
                        class="filter"
                        required>

                        <option value="">
                            Seleccione un estado
                        </option>

                        <option value="Pendiente">
                            Pendiente
                        </option>

                        <option value="En Proceso">
                            En Proceso
                        </option>

                        <option value="Finalizada">
                            Finalizada
                        </option>

                        <option value="Cancelada">
                            Cancelada
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_inicio">
                        Fecha inicio
                    </label>
                    <input
                        type="date"
                        id="fecha_inicio"
                        name="fecha_inicio"
                        class="input">
                </div>
                <div class="form-group">
                    <label for="fecha_fin">
                        Fecha fin
                    </label>
                    <input
                        type="date"
                        id="fecha_fin"
                        name="fecha_fin"
                        class="input">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="descripcion">
                        Descripción
                    </label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        class="input"
                        rows="4"
                        placeholder="Descripción de la etapa"></textarea>
                </div>
            </div>
            <div class="form-actions">
                <a
                    href="index.php?id_obra=<?= $_GET["id_obra"] ?>"
                    class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Cancelar
                </a>
                <button
                    type="reset"
                    class="btn btn-warning">
                    <i class="fa-solid fa-rotate-left"></i>
                    Limpiar
                </button>
                <button
                    type="submit"
                    class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar etapa
                </button>
            </div>
        </form>
    </div>
</main>
<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>