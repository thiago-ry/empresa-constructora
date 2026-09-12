<?php

$materiales = $materiales ?? [];
$obra = $obra ?? [];

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-header">
        <div>
            <h1>Nueva solicitud de materiales</h1>
            <p>
                Obra:
                <strong><?= htmlspecialchars($obra["nombre_obra"] ?? "Sin nombre") ?></strong>
            </p>
        </div>

        <a
            href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=listar&id_obra=<?= $obra["id_obra"] ?>"
            class="btn btn-secondary">
            Volver
        </a>
    </div>

    <div class="card">

        <form
            method="POST"
            action="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=guardar">

            <input
                type="hidden"
                name="id_obra"
                value="<?= $obra["id_obra"] ?>">

            <div id="materiales-container">

                <div class="material-row">

                    <div class="form-group">
                        <label>Material</label>

                        <select name="material[]" required>

                            <option value="">Seleccionar material</option>

                            <?php foreach ($materiales as $material): ?>

                                <option value="<?= $material["id_material"] ?>">
                                    <?= htmlspecialchars($material["nombre_material"]) ?>
                                    — Stock: <?= htmlspecialchars($material["stock"]) ?>
                                    <?= htmlspecialchars($material["unidad_medida"]) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cantidad</label>

                        <input
                            type="number"
                            name="cantidad[]"
                            min="0.01"
                            step="0.01"
                            required>
                    </div>

                    <button
                        type="button"
                        class="btn btn-danger btn-remove"
                        onclick="eliminarMaterial(this)"
                        style="display:none;">
                        Eliminar
                    </button>

                </div>

            </div>

            <div style="margin-top:20px;">
                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="agregarMaterial()">
                    + Agregar material
                </button>
            </div>

            <div
                style="
                    display:flex;
                    justify-content:flex-end;
                    gap:10px;
                    margin-top:30px;
                ">

                <a
                    href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=listar&id_obra=<?= $obra["id_obra"] ?>"
                    class="btn btn-secondary">
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-primary">
                    Enviar solicitud
                </button>

            </div>

        </form>

    </div>

</main>

<script>

function agregarMaterial() {

    const container = document.getElementById("materiales-container");

    const filas = container.querySelectorAll(".material-row");

    const nuevaFila = filas[0].cloneNode(true);

    nuevaFila.querySelector("select").value = "";
    nuevaFila.querySelector("input").value = "";

    nuevaFila.querySelector(".btn-remove").style.display = "inline-block";

    container.appendChild(nuevaFila);

    actualizarBotonesEliminar();
}

function eliminarMaterial(boton) {

    const fila = boton.closest(".material-row");

    fila.remove();

    actualizarBotonesEliminar();
}

function actualizarBotonesEliminar() {

    const filas = document.querySelectorAll(".material-row");

    filas.forEach(function(fila, indice) {

        const boton = fila.querySelector(".btn-remove");

        if (filas.length === 1) {
            boton.style.display = "none";
        } else {
            boton.style.display = "inline-block";
        }

    });

}

</script>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>