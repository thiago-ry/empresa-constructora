<?php

require_once "../../config/permisos.php";

verificarPermiso("herramientas");

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<main class="content">

    <div class="page-title">

        <h1>
            Agregar herramienta
        </h1>

        <p>
            Registre una nueva herramienta para el inventario.
        </p>

    </div>

    <div class="form-card">

        <form
            class="form"
            action="../../controladores/HerramientaController.php?accion=agregar"
            method="POST"
            autocomplete="off">

            <div class="form-row">

                <div class="form-group">

                    <label>
                        Nombre de la herramienta
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        placeholder="Ej: Centa métrica"
                        required>

                </div>

                <div class="form-group">

                    <label>
                        Tipo
                    </label>

                    <select
                        name="tipo"
                        class="filter"
                        required>

                        <option value="">
                            Seleccione un tipo de herramienta
                        </option>

                        <option value="Manual">
                            Manual
                        </option>

                        <option value="Eléntrica">
                            Eléntrica
                        </option>

                        <option value="Medición">
                            Medición
                        </option>

                        <option value="Transporte">
                            Transporte
                        </option>

                        <option value="Maquinaria">
                            Maquinaria
                        </option>

                        <option value="Altura">
                            Altura
                        </option>

                    </select>

                </div>

            </div>
            <div class="form-row">

                <div class="form-group">

                    <label>
                        Marca
                    </label>

                    <input
                        type="text"
                        name="marca"
                        class="input"
                        placeholder="Ej: Marca"
                        required>

                </div>
                <div class="form-group">

                    <label>
                        Modelo
                    </label>

                    <input
                        type="text"
                        name="modelo"
                        class="input"
                        required>

                </div>

            </div>
            <div class="form-row">

                <div class="form-group">

                    <label>
                        Cantidad total
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="cantidad_total"
                        class="input"
                        value="0"
                        required>

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>
                        Fecha de adquisición
                    </label>

                    <input
                        type="date"
                        name="fecha_adquisicion"
                        class="input"
                        value="0"
                        required>
                </div>

                <div class="form-group">
                    <label>
                        Costo
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="costo"
                        class="input"
                        value="0"
                        required>
                </div>

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
                    Guardar herramienta
                </button>
            </div>

        </form>

    </div>

</main>

<?php
require_once "../../layouts/footer.php";
?>