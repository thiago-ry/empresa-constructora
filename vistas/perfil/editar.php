<?php

require_once "../../modelos/Usuario.php";


// ============================================================
// SESIÓN
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION["usuario"])) {

    header("Location: /empresa_constructora/vistas/login.php");

    exit;
}


// ============================================================
// ID DEL USUARIO LOGUEADO
// ============================================================

$id_usuario = $_SESSION["usuario"]["id"];


// ============================================================
// MODELO
// ============================================================

$usuario = new Usuario();


// ============================================================
// OBTENER DATOS DEL USUARIO
// ============================================================

$perfil = $usuario->buscarPorId($id_usuario);


if (!$perfil) {

    die("No se encontró el usuario.");
}


// ============================================================
// LAYOUT
// ============================================================

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>


<main class="content">


    <!-- ==================================================
         TÍTULO
    ================================================== -->

    <div class="page-title">

        <h1>Editar perfil</h1>

        <p>
            Modifique sus datos personales.
        </p>

    </div>


    <!-- ==================================================
         FORMULARIO
    ================================================== -->

    <div class="form-card">


        <form
            action="../../controladores/PerfilController.php"
            method="POST">


            <!-- ==================================================
                 ACCIÓN
            ================================================== -->

            <input
                type="hidden"
                name="accion"
                value="editar"
                class="input">


            <input
                type="hidden"
                name="id_usuario"
                value="<?= $perfil["id_usuario"]; ?>"
                class="input">


            <!-- ==================================================
                 INFORMACIÓN PERSONAL
            ================================================== -->

            <div class="form-section">

                <h2>
                    Información personal
                </h2>

                <p class="form-section-description">
                    Actualice sus datos personales y de contacto.
                </p>


                <div class="form-grid">


                    <!-- NOMBRE -->

                    <div class="form-group">

                        <label for="nombre">
                            Nombre
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="<?= htmlspecialchars($perfil["nombre"]); ?>"
                            required
                            class="input">

                    </div>


                    <!-- APELLIDO -->

                    <div class="form-group">

                        <label for="apellido">
                            Apellido
                        </label>

                        <input
                            type="text"
                            id="apellido"
                            name="apellido"
                            value="<?= htmlspecialchars($perfil["apellido"]); ?>"
                            required
                class="input"
                            >

                    </div>


                    <!-- DOCUMENTO -->

                    <div class="form-group">

                        <label for="documento">
                            Documento
                        </label>

                        <input
                            type="text"
                            id="documento"
                            name="documento"
                            value="<?= htmlspecialchars($perfil["documento"]); ?>"
                            required
                class="input"
                            >

                    </div>


                    <!-- TELÉFONO -->

                    <div class="form-group">

                        <label for="telefono">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            id="telefono"
                            name="telefono"
                            value="<?= htmlspecialchars($perfil["telefono"] ?? ""); ?>"
                class="input"
                            >

                    </div>


                    <!-- DIRECCIÓN -->

                    <div class="form-group form-group-full">

                        <label for="direccion">
                            Dirección
                        </label>

                        <input
                            type="text"
                            id="direccion"
                            name="direccion"
                            class="input"
                            value="<?= htmlspecialchars($perfil["direccion"] ?? ""); ?>">

                    </div>


                    <!-- CORREO -->

                    <div class="form-group form-group-full">

                        <label for="correo">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            class="input"
                            value="<?= htmlspecialchars($perfil["correo"]); ?>"
                            required>

                    </div>


                </div>

            </div>


            <!-- ==================================================
                 INFORMACIÓN DEL SISTEMA
            ================================================== -->

            <div class="form-section">


                <h2>
                    Información de cuenta
                </h2>

                <p class="form-section-description">
                    Estos datos son administrados por el sistema.
                </p>


                <div class="form-grid">


                    <!-- ROL -->

                    <div class="form-group">

                        <label>
                            Rol
                        </label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars($perfil["nombre_rol"]); ?>"
                            disabled>

                    </div>


                    <!-- ESTADO -->

                    <div class="form-group">

                        <label>
                            Estado
                        </label>

                        <input
                            type="text"
                            value="<?= $perfil["estado"] == 1 ? "Activo" : "Inactivo"; ?>"
                            disabled>

                    </div>


                </div>

            </div>


            <!-- ==================================================
                 BOTONES
            ================================================== -->

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

                    <i class="fa-solid fa-floppy-disk"></i>

                    Guardar cambios

                </button>


            </div>


        </form>


    </div>


</main>


<?php

require_once "../../layouts/footer.php";

?>