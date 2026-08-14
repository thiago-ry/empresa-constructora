<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once "../../modelos/Usuario.php";


// ============================================================
// SESIÓN
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION["usuario"])) {

    header(
        "Location: /empresa_constructora/vistas/login.php"
    );

    exit;
}


// ============================================================
// ID DEL USUARIO
// ============================================================

$id_usuario = $_SESSION["usuario"]["id"];


// ============================================================
// MODELO
// ============================================================

$usuario = new Usuario();


// ============================================================
// BUSCAR USUARIO
// ============================================================

$perfil = $usuario->buscarPorId($id_usuario);


if (!$perfil) {

    die(
        "No se encontró el usuario con ID: "
        . $id_usuario
    );

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

    <div class="page-title no-print">

        <h1>
            Cambiar contraseña
        </h1>

        <p>
            Actualice la contraseña de su cuenta.
        </p>

    </div>


    <!-- ==================================================
         FORMULARIO
    ================================================== -->

    <div class="form-card">


        <div class="form-section">


            <h2>
                Seguridad de la cuenta
            </h2>


            <p class="form-section-description">

                Ingrese su contraseña actual y establezca
                una nueva contraseña.

            </p>


            <form
                action="../../controladores/PerfilController.php"
                method="POST">


                <!-- ACCIÓN -->

                <input
                    type="hidden"
                    name="accion"
                    value="cambiarContraseña">


                <!-- ==================================================
                     CAMPOS
                ================================================== -->

                <div class="form-grid">


                    <!-- CONTRASEÑA ACTUAL -->

                    <div class="form-group form-group-full">

                        <label for="contraseña_actual">

                            Contraseña actual

                        </label>


                        <input
                            type="password"
                            id="contraseña_actual"
                            name="contraseña_actual"
                            placeholder="Ingrese su contraseña actual"
                            class="input"
                            required>


                    </div>



                    <!-- NUEVA CONTRASEÑA -->

                    <div class="form-group">

                        <label for="nueva_contraseña">

                            Nueva contraseña

                        </label>


                        <input
                            type="password"
                            id="nueva_contraseña"
                            name="nueva_contraseña"
                            placeholder="Ingrese la nueva contraseña"
                            minlength="6"
                            class="input"   
                            required>


                        <small>

                            Mínimo 6 caracteres.

                        </small>


                    </div>



                    <!-- CONFIRMAR -->

                    <div class="form-group">

                        <label for="confirmar_contraseña">

                            Confirmar nueva contraseña

                        </label>


                        <input
                            type="password"
                            id="confirmar_contraseña"
                            name="confirmar_contraseña"
                            placeholder="Repita la nueva contraseña"
                            minlength="6"
                            class="input"
                            required>


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

                        Volver

                    </a>



                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fa-solid fa-key"></i>

                        Cambiar contraseña

                    </button>


                </div>


            </form>


        </div>


    </div>


</main>


<?php

require_once "../../layouts/footer.php";

?>