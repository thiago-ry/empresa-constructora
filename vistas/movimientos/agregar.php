<?php

require_once "../../modelos/Material.php";
require_once "../../config/permisos.php";

verificarPermiso("materiales");

$materialModel = new Material();

$id_material = $_GET["id"] ?? 0;

$material = $materialModel->buscar($id_material);

if (!$material) {

    header("Location: ../materiales/");
    exit;

}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>
            Registrar movimiento
        </h1>

        <p>
            Ingrese un movimiento de stock para el material seleccionado.
        </p>

    </div>

    <div class="form-card">

        <form
            class="form"
            action="../../controladores/MovimientoMaterialController.php?accion=agregar"
            method="POST"
            autocomplete="off">

            <input
                type="hidden"
                name="id_material"
                value="<?= $material["id_material"]; ?>">

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Material
                    </label>

                    <input
                        type="text"
                        class="input"
                        value="<?= $material["nombre_material"]; ?>"
                        readonly>

                </div>

                <div class="form-group">

                    <label>
                        Stock actual
                    </label>

                    <input
                        type="text"
                        class="input"
                        value="<?= $material["stock"]; ?>"
                        readonly>

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Tipo de movimiento
                    </label>

                    <select
                        name="tipo"
                        class="filter"
                        required>

                        <option value="">
                            Seleccione...
                        </option>

                        <option value="INGRESO">
                            Ingreso
                        </option>

                        <option value="EGRESO">
                            Egreso
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>
                        Cantidad
                    </label>

                    <input
                        type="number"
                        name="cantidad"
                        class="input"
                        min="1"
                        step="0.01"
                        required>

                </div>

            </div>

            <div class="form-group">

                <label>
                    Observación
                </label>

                <textarea
                    name="observacion"
                    class="textarea"
                    placeholder="Ej.: Compra al proveedor, entrega a obra, devolución, etc."></textarea>

            </div>

            <div class="form-actions">

                <a
                    href="index.php?id=<?= $material["id_material"]; ?>"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancelar

                </a>

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="fa-solid fa-floppy-disk"></i>

                    Registrar movimiento

                </button>

            </div>

        </form>

    </div>

</main>

<?php

require_once "../../layouts/footer.php";

?>