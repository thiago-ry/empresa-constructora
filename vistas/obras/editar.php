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

        <h1>Editar Obra</h1>

        <p>Modificar información de la obra.</p>

    </div>

    <div class="card">

        <form action="../../controladores/ObraController.php" method="POST">

            <input type="hidden" name="accion" value="editar">

            <input
                type="hidden"
                name="id_obra"
                value="<?= $obraEditar["id_obra"] ?>">

            <div class="form-grid">

                <div class="form-group">

                    <label>Nombre de la obra</label>

                    <input
                        type="text"
                        name="nombre_obra"
                        value="<?= $obraEditar["nombre_obra"] ?>"
                        required>

                </div>

                <div class="form-group">

                    <label>Cliente</label>

                    <select
                        name="id_cliente"
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

                    <label>Dirección</label>

                    <input
                        type="text"
                        name="direccion"
                        value="<?= $obraEditar["direccion"] ?>"
                        required>

                </div>

                <div class="form-group form-group-full">

                    <label>Descripción</label>

                    <textarea
                        name="descripcion"
                        rows="4"><?= $obraEditar["descripcion"] ?></textarea>

                </div>

                <div class="form-group">

                    <label>Fecha de inicio</label>

                    <input
                        type="date"
                        name="fecha_inicio"
                        value="<?= $obraEditar["fecha_inicio"] ?>">

                </div>

                <div class="form-group">

                    <label>Fecha de finalización</label>

                    <input
                        type="date"
                        name="fecha_fin"
                        value="<?= $obraEditar["fecha_fin"] ?>">

                </div>

                <div class="form-group">

                    <label>Estado</label>

                    <select name="estado" required>

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

                <a
                    href="index.php"
                    class="btn btn-secondary">

                    Cancelar

                </a>

                <button
                    type="submit"
                    class="btn btn-primary">

                    Actualizar Obra

                </button>

            </div>

        </form>

    </div>

</main>

<?php

require_once "../../layouts/footer.php";

?>