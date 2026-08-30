<?php

require_once "../../modelos/Obra.php";
require_once "../../modelos/Usuario.php";
require_once "../../config/permisos.php";

verificarPermiso("obras");

$obra = new Obra();
$usuario = new Usuario();

$obraEditar = $obra->buscarPorId($_GET["id"]);

$clientes = $usuario->obtenerClientes(true);
$jefesObra = $obra->obtenerJefesObra();

$estados = $obra->obtenerEstados();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1 class="title">
            Editar Obra
        </h1>

        <p class="subtitle">
            Modificar información de la obra.
        </p>

    </div>


    <div class="card form-card">

        <form
            class="form form-edit-obra"
            action="../../controladores/ObraController.php"
            method="POST"
            autocomplete="off">

            <input
                type="hidden"
                name="accion"
                value="editar">

            <input
                type="hidden"
                name="id_obra"
                value="<?= $obraEditar["id_obra"] ?>">


            <div class="form-grid">


                <!-- NOMBRE -->

                <div class="form-group">

                    <label class="label">
                        Nombre de la obra
                    </label>

                    <input
                        type="text"
                        name="nombre_obra"
                        class="input input-text"
                        value="<?= htmlspecialchars($obraEditar["nombre_obra"]) ?>"
                        required>

                </div>


                <!-- CLIENTE -->

                <div class="form-group">

                    <label class="label">
                        Cliente
                    </label>

                    <select
                        name="id_usuario"
                        class="select filter"
                        required>

                        <?php foreach ($clientes as $c) { ?>

                            <option
                                value="<?= $c["id_usuario"] ?>"
                                <?= $obraEditar["id_usuario"] == $c["id_usuario"] ? "selected" : "" ?>>

                                <?= htmlspecialchars(
                                    $c["nombre"] . " " . $c["apellido"]
                                ) ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- JEFE DE OBRA -->

                <div class="form-group">

                    <label class="label">
                        Jefe de Obra
                    </label>

                    <select
                        name="id_jefe_obra"
                        class="select filter"
                        required>

                        <option value="">
                            Seleccione un Jefe de Obra
                        </option>

                        <?php foreach ($jefesObra as $jefe) { ?>

                            <option
                                value="<?= $jefe["id_usuario"] ?>"
                                <?= $obraEditar["id_jefe_obra"] == $jefe["id_usuario"] ? "selected" : "" ?>>

                                <?= htmlspecialchars(
                                    $jefe["nombre"] . " " . $jefe["apellido"]
                                ) ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


                <!-- DIRECCIÓN -->

                <div class="form-group form-group-full">

                    <label class="label">
                        Dirección
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        class="input input-text"
                        value="<?= htmlspecialchars($obraEditar["direccion"]) ?>"
                        required>

                </div>


                <!-- DESCRIPCIÓN -->

                <div class="form-group form-group-full">

                    <label class="label">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        class="textarea"
                        rows="4"><?= htmlspecialchars($obraEditar["descripcion"]) ?></textarea>

                </div>


                <!-- FECHA INICIO -->

                <div class="form-group">

                    <label class="label">
                        Fecha de inicio
                    </label>

                    <input
                        type="date"
                        name="fecha_inicio"
                        class="input input-date"
                        value="<?= htmlspecialchars($obraEditar["fecha_inicio"]) ?>">

                </div>


                <!-- FECHA FIN -->

                <div class="form-group">

                    <label class="label">
                        Fecha de finalización
                    </label>

                    <input
                        type="date"
                        name="fecha_fin"
                        class="input input-date"
                        value="<?= htmlspecialchars($obraEditar["fecha_fin"]) ?>">

                </div>


                <!-- ESTADO -->

                <div class="form-group">

                    <label class="label">
                        Estado
                    </label>

                    <select
                        name="estado"
                        class="filter"
                        required>

                        <?php foreach ($estados as $estado) { ?>

                            <option
                                value="<?= htmlspecialchars($estado) ?>"
                                <?= $obraEditar["estado"] == $estado ? "selected" : "" ?>>

                                <?= htmlspecialchars($estado) ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>


            </div>


            <div class="form-actions">

                <a
                    href="index.php"
                    class="btn btn-secondary btn-cancel">

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