<?php

require_once "../../modelos/Material.php";
require_once "../../config/permisos.php";

verificarPermiso("materiales");

$materialModel = new Material();
$id = $_GET["id"];
$material = $materialModel->obtenerPorId($id);

if (!$material) {

    header("Location: index.php");
    exit;
}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>
            Editar material
        </h1>

        <p>
            Modifique los datos del material.
        </p>

    </div>

    <div class="form-card">

        <form
            class="form"
            action="../../controladores/MaterialController.php?accion=editar"
            method="POST"
            autocomplete="off">

            <input
                type="hidden"
                name="id_material"
                value="<?= $material["id_material"]; ?>">

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Nombre del material
                    </label>

                    <input
                        type="text"
                        name="nombre_material"
                        class="input"
                        value="<?= $material["nombre_material"]; ?>"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        Unidad de medida
                    </label>

                    <select
                        name="unidad_medida"
                        class="filter"
                        required>

                        <option
                            value="Unidad"
                            <?= $material["unidad_medida"] == "Unidad" ? "selected" : ""; ?>>
                            Unidad
                        </option>
                        <option
                            value="Bolsa"
                            <?= $material["unidad_medida"] == "Bolsa" ? "selected" : ""; ?>>
                            Bolsa
                        </option>
                        <option
                            value="Kg"
                            <?= $material["unidad_medida"] == "Kg" ? "selected" : ""; ?>>
                            Kg
                        </option>
                        <option
                            value="Litro"
                            <?= $material["unidad_medida"] == "Litro" ? "selected" : ""; ?>>
                            Litro
                        </option>
                        <option
                            value="Metro"
                            <?= $material["unidad_medida"] == "Metro" ? "selected" : ""; ?>>
                            Metro
                        </option>
                        <option
                            value="m²"
                            <?= $material["unidad_medida"] == "m²" ? "selected" : ""; ?>>
                            m²
                        </option>
                        <option
                            value="m³"
                            <?= $material["unidad_medida"] == "m³" ? "selected" : ""; ?>>
                            m³
                        </option>
                        <option
                            value="Rollo"
                            <?= $material["unidad_medida"] == "Rollo" ? "selected" : ""; ?>>
                            Rollo
                        </option>
                        <option
                            value="Balde"
                            <?= $material["unidad_medida"] == "Balde" ? "selected" : ""; ?>>
                            Balde
                        </option>

                    </select>

                </div>

            </div>

            <div class="form-group">

                <label>
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    class="textarea"><?= $material["descripcion"]; ?></textarea>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Stock actual
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="stock"
                        class="input"
                        value="<?= $material["stock"]; ?>"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        Stock mínimo
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="stock_minimo"
                        class="input"
                        value="<?= $material["stock_minimo"]; ?>"
                        required>
                </div>

            </div>

            <div class="form-group">

                <label>
                    Estado
                </label>

                <select
                    name="estado"
                    class="filter">

                    <option
                        value="1"
                        <?= $material["estado"] == 1 ? "selected" : ""; ?>>
                        Activo
                    </option>
                    <option
                        value="0"
                        <?= $material["estado"] == 0 ? "selected" : ""; ?>>
                        Inactivo
                    </option>

                </select>

            </div>

            <div class="form-actions">

                <a
                    href="index.php"
                    class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i>
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</main>

<?php

require_once "../../layouts/footer.php";

?>