<?php

require_once "../../config/permisos.php";

verificarPermiso("materiales");

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<main class="content">

    <div class="page-title">

        <h1>
            Agregar material
        </h1>

        <p>
            Registre un nuevo material para el inventario.
        </p>

    </div>

    <div class="form-card">

        <form
            class="form"
            action="../../controladores/MaterialController.php?accion=agregar"
            method="POST"
            autocomplete="off">

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Nombre del material
                    </label>

                    <input
                        type="text"
                        name="nombre_material"
                        class="input"
                        placeholder="Ej: Cemento Portland"
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

                        <option value="">
                            Seleccione una unidad
                        </option>

                        <option value="Unidad">
                            Unidad
                        </option>

                        <option value="Bolsa">
                            Bolsa
                        </option>

                        <option value="Kg">
                            Kg
                        </option>

                        <option value="Metro">
                            Metro
                        </option>

                        <option value="m²">
                            m²
                        </option>

                        <option value="m³">
                            m³
                        </option>

                        <option value="Rollo">
                            Rollo
                        </option>

                        <option value="Balde">
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
                    class="textarea"
                    placeholder="Descripción del material"></textarea>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Stock inicial
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="stock"
                        class="input"
                        value="0"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        Stock mínimo
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="stock_minimo"
                        class="input"
                        value="0"
                        required>

                </div>

            </div>

            <div class="form-group">

                <label>
                    Estado
                </label>

                <select
                    name="estado"
                    class="filter"
                    required>

                    <option value="1">
                        Activo
                    </option>

                    <option value="0">
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
                    class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Guardar material
                </button>
            </div>

        </form>

    </div>

</main>

<?php
require_once "../../layouts/footer.php";
?>