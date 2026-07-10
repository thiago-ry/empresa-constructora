<?php

require_once "../../modelos/Rol.php";

$rol = new Rol();

$roles = $rol->obtenerTodos();


require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">
        <h1>Agregar usuario</h1>
        <p>Complete los datos para registrar un nuevo usuario.</p>
    </div>

    <div class="form-card">

        <form class="form"
            action="../../controladores/UsuarioController.php"
            method="POST"
            autocomplete="off">

            <input type="hidden" name="accion" value="agregar">

            <div class="form-row">

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        class="input"
                        placeholder="Ingrese el nombre"
                        maxlength="100"
                        required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        class="input"
                        placeholder="Ingrese el apellido"
                        maxlength="100"
                        required>
                </div>

            </div>

            <div class="form-row">

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        class="input"
                        placeholder="ejemplo@correo.com"
                        maxlength="150"
                        required>
                </div>

                <div class="form-group">
                    <label for="rol">Rol</label>

                    <select
                        id="rol" name="rol" class="filter" required>
                        <option value="">
                            Seleccione un rol
                        </option>
                        <?php foreach ($roles as $r) { ?>
                            <option value="<?= $r['id_rol']; ?>">
                                <?= $r['nombre_rol']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-row">

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="input"
                        placeholder="Ingrese una contraseña"
                        maxlength="255"
                        required>
                </div>

                <div class="form-group">
                    <label for="confirmar">Confirmar contraseña</label>
                    <input
                        type="password"
                        id="confirmar"
                        name="confirmar"
                        class="input"
                        placeholder="Repita la contraseña"
                        maxlength="255"
                        required>
                </div>

            </div>

            <div class="form-actions">

                <a href="index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Cancelar
                </a>

                <button type="reset" class="btn btn-warning">
                    <i class="fa-solid fa-rotate-left"></i>
                    Limpiar
                </button>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar usuario
                </button>

            </div>

        </form>

    </div>

</main>

<?php
require_once "../../layouts/footer.php";
?>