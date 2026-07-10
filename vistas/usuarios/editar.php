<?php

require_once "../../modelos/Usuario.php";
require_once "../../modelos/Rol.php";

$usuarioModel = new Usuario();
$rolModel = new Rol();

$usuario = $usuarioModel->buscarPorId($_GET["id"]);
$roles = $rolModel->obtenerTodos();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">
        <h1>Editar usuario</h1>
        <p>Modifique los datos del usuario.</p>
    </div>

    <div class="form-card">

        <form class="form"
            action="../../controladores/UsuarioController.php"
            method="POST"
            autocomplete="off">

            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario']; ?>">

            <div class="form-row">

                <div class="form-group">
                    <label>Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        value="<?= $usuario['nombre']; ?>"
                        maxlength="100"
                        required>
                </div>

                <div class="form-group">
                    <label>Apellido</label>
                    <input
                        type="text"
                        name="apellido"
                        class="input"
                        value="<?= $usuario['apellido']; ?>"
                        maxlength="100"
                        required>
                </div>

            </div>

            <div class="form-row">

                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input
                        type="email"
                        name="correo"
                        class="input"
                        value="<?= $usuario['correo']; ?>"
                        maxlength="150"
                        required>
                </div>

                <div class="form-group">

                    <label>Rol</label>

                    <select
                        name="id_rol"
                        class="filter"
                        required>

                        <?php foreach ($roles as $r) { ?>

                            <option
                                value="<?= $r['id_rol']; ?>"
                                <?= $usuario['id_rol'] == $r['id_rol'] ? 'selected' : ''; ?>>

                                <?= $r['nombre_rol']; ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>

            </div>

            <div class="form-actions">

                <a href="index.php" class="btn btn-secondary">
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