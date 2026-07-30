<?php

require_once "../../modelos/Rol.php";


require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>Agregar usuario</h1>

        <p>Complete los datos para registrar un nuevo usuario.</p>

    </div>

    <div class="form-card">

        <form
            class="form"
            action="../../controladores/ClienteController.php"
            method="POST"
            autocomplete="off">

            <input
                type="hidden"
                name="accion"
                value="agregar">

            <div class="form-row">

                <div class="form-group">

                    <label>Nombre</label>

                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        maxlength="100"
                        required>

                </div>

                <div class="form-group">

                    <label>Apellido</label>

                    <input
                        type="text"
                        name="apellido"
                        class="input"
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
                        maxlength="150"
                        required>

                </div>

                <div class="form-group">

                    <label>Rol</label>

                    <span class="input"><p style=" margin-top: 13px; font-size: 16px;">Cliente</p></span>

                </div>

            </div>
            <div class="form-row">

                <div class="form-group">

                    <label>Documento</label>

                    <input
                        type="text"
                        name="documento"
                        class="input">

                </div>

                <div class="form-group">

                    <label>Teléfono</label>

                    <input
                        type="text"
                        name="telefono"
                        class="input">

                </div>

            </div>

            <div class="form-row">

                <div class="form-group">

                    <label>Contraseña</label>

                    <input
                        type="password"
                        name="password"
                        class="input"
                        maxlength="255"
                        required>

                </div>

                <div class="form-group">

                    <label>Confirmar contraseña</label>

                    <input
                        type="password"
                        name="confirmar"
                        class="input"
                        maxlength="255"
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
                    type="reset"
                    class="btn btn-warning">

                    <i class="fa-solid fa-rotate-left"></i>

                    Limpiar

                </button>

                <button
                    type="submit"
                    class="btn btn-primary">

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