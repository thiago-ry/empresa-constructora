<?php

require_once "../../modelos/Obra.php";
require_once "../../modelos/Cliente.php";
require_once "../../config/permisos.php";

verificarPermiso("obras");

$obra = new Obra();
$cliente = new Cliente();

$obraEditar = $obra->buscarPorId($_GET["id"]);
$clientes = $cliente->obtenerTodos();
$estados = $obra->obtenerEstados();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">
        <h1 class="title">Editar Obra</h1>
        <p class="subtitle">Modificar información de la obra.</p>
    </div>

    <div class="card form-card">

        <form class="form form-edit-obra"
            action="../../controladores/ObraController.php"
            method="POST"
            autocomplete="off">

            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id_obra" value="<?= $obraEditar["id_obra"] ?>">

            <div class="form-grid">

                <div class="form-group">
                    <label class="label">Nombre de la obra</label>
                    <input
                        type="text"
                        name="nombre_obra"
                        class="input input-text"
                        value="<?= $obraEditar["nombre_obra"] ?>"
                        required>
                </div>

                <div class="form-group">
                    <label class="label">Cliente</label>
                    <select
                        name="id_cliente"
                        class="select filter"
                        required>
                        <?php foreach ($clientes as $c) { ?>
                            <option
                                value="<?= $c["id_cliente"] ?>"
                                <?= $obraEditar["id_cliente"] == $c["id_cliente"] ? "selected" : "" ?>>
                                <?= $c["nombre"] . " " . $c["apellido"] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group form-group-full">
                    <label class="label">Dirección</label>
                    <input
                        type="text"
                        name="direccion"
                        class="input input-text"
                        value="<?= $obraEditar["direccion"] ?>"
                        required>
                </div>

                <div class="form-group form-group-full">
                    <label class="label">Descripción</label>
                    <textarea
                        name="descripcion"
                        class="textarea"
                        rows="4"><?= $obraEditar["descripcion"] ?></textarea>
                </div>

                <div class="form-group">
                    <label class="label">Fecha de inicio</label>
                    <input
                        type="date"
                        name="fecha_inicio"
                        class="input input-date"
                        value="<?= $obraEditar["fecha_inicio"] ?>">
                </div>

                <div class="form-group">
                    <label class="label">Fecha de finalización</label>
                    <input
                        type="date"
                        name="fecha_fin"
                        class="input input-date"
                        value="<?= $obraEditar["fecha_fin"] ?>">
                </div>

                <div class="form-group">
                    <label class="label">Estado</label>
                    <select
                        name="estado"
                        class="filter"
                        required>
                        <?php foreach ($estados as $estado) { ?>
                            <option
                                value="<?= $estado ?>"
                                <?= $obraEditar["estado"] == $estado ? "selected" : "" ?>>
                                <?= $estado ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary btn-cancel">
                    <i class="fa-solid fa-arrow-left"></i>
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="btn btn-primary btn-submit">
                    <i class="fa-solid fa-pen"></i>
                    Actualizar Obra
                </button>
            </div>

        </form>

    </div>

</main>


<?php

require_once "../../layouts/footer.php";

?>