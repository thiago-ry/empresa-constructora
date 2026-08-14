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
// OBTENER PERFIL
// ============================================================

$perfil = $usuario->buscarPorId($id_usuario);


if (!$perfil) {

    die("No se encontró el usuario con ID: "
        . $id_usuario);
}


// ============================================================
// CARGOS DEL EMPLEADO
// ============================================================

$cargos = [];

if (
    isset($perfil["nombre_rol"]) &&
    $perfil["nombre_rol"] === "Empleado"
) {

    $cargos = $usuario->obtenerCargosEmpleado(
        $id_usuario
    );
}


// ============================================================
// DATOS PARA LA VISTA
// ============================================================

$nombreCompleto =
    $perfil["nombre"] . " " . $perfil["apellido"];


$iniciales =
    strtoupper(
        substr($perfil["nombre"], 0, 1) .
            substr($perfil["apellido"], 0, 1)
    );


$estadoActivo =
    $perfil["estado"] == 1;


// ============================================================
// LAYOUT
// ============================================================

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">


    <!-- =====================================================
         TÍTULO
    ====================================================== -->

    <div class="page-title no-print">

        <h1>
            Mi perfil
        </h1>

        <p>
            Información personal y datos de su cuenta.
        </p>

    </div>



    <!-- =====================================================
         PERFIL PRINCIPAL
    ====================================================== -->

    <div class="table-container perfil-container">


        <!-- =================================================
             CABECERA
        ================================================== -->

        <div class="perfil-header">


            <div class="perfil-avatar">

                <?= htmlspecialchars($iniciales) ?>

            </div>


            <div class="perfil-header-info">

                <h2>

                    <?= htmlspecialchars($nombreCompleto) ?>

                </h2>

                <p>

                    <?= htmlspecialchars(
                        $perfil["nombre_rol"] ?? "Sin rol"
                    ) ?>

                </p>

            </div>


            <div class="perfil-header-estado">

                <span class="
                    <?= $estadoActivo
                        ? 'badge badge-success'
                        : 'badge badge-danger'
                    ?>
                ">

                    <?= $estadoActivo
                        ? "Activo"
                        : "Inactivo"
                    ?>

                </span>

            </div>


        </div>



        <!-- =================================================
             INFORMACIÓN PERSONAL
        ================================================== -->

        <div class="perfil-section">


            <div class="perfil-section-title">

                <div>

                    <h2>
                        Información personal
                    </h2>

                    <p>
                        Datos personales registrados en el sistema.
                    </p>

                </div>


                <a
                    href="editar.php"
                    class="btn btn-warning">

                    <i class="fa-solid fa-pen-to-square"></i>

                    Editar

                </a>

            </div>



            <div class="perfil-grid">


                <!-- NOMBRE -->

                <div class="perfil-dato">

                    <span>
                        Nombre
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $perfil["nombre"] ?? "No especificado"
                        ) ?>

                    </strong>

                </div>



                <!-- APELLIDO -->

                <div class="perfil-dato">

                    <span>
                        Apellido
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $perfil["apellido"] ?? "No especificado"
                        ) ?>

                    </strong>

                </div>



                <!-- DOCUMENTO -->

                <div class="perfil-dato">

                    <span>
                        Documento
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $perfil["documento"] ?? "No especificado"
                        ) ?>

                    </strong>

                </div>



                <!-- TELÉFONO -->

                <div class="perfil-dato">

                    <span>
                        Teléfono
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $perfil["telefono"] ?? "No especificado"
                        ) ?>

                    </strong>

                </div>



                <!-- DIRECCIÓN -->

                <div class="perfil-dato perfil-dato-completo">

                    <span>
                        Dirección
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $perfil["direccion"] ?? "No especificada"
                        ) ?>

                    </strong>

                </div>


            </div>

        </div>



        <!-- =================================================
             INFORMACIÓN DE CUENTA
        ================================================== -->

        <div class="perfil-section">


            <div class="perfil-section-title">

                <div>

                    <h2>
                        Información de cuenta
                    </h2>

                    <p>
                        Datos relacionados con el acceso al sistema.
                    </p>

                </div>

            </div>



            <div class="perfil-grid">


                <!-- CORREO -->

                <div class="perfil-dato">

                    <span>
                        Correo electrónico
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $perfil["correo"] ?? "No especificado"
                        ) ?>

                    </strong>

                </div>



                <!-- ROL -->

                <div class="perfil-dato">

                    <span>
                        Rol
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $perfil["nombre_rol"] ?? "Sin rol"
                        ) ?>

                    </strong>

                </div>



                <!-- ESTADO -->

                <div class="perfil-dato">

                    <span>
                        Estado
                    </span>

                    <strong>

                        <span class="
                            <?= $estadoActivo
                                ? 'badge badge-success'
                                : 'badge badge-danger'
                            ?>
                        ">

                            <?= $estadoActivo
                                ? "Activo"
                                : "Inactivo"
                            ?>

                        </span>

                    </strong>

                </div>


            </div>

        </div>



        <!-- =================================================
             INFORMACIÓN LABORAL
        ================================================== -->

        <?php if (
            !empty($perfil["salario"]) ||
            !empty($cargos)
        ) { ?>


            <div class="perfil-section">


                <div class="perfil-section-title">

                    <div>

                        <h2>
                            Información laboral
                        </h2>

                        <p>
                            Información relacionada con su actividad en la empresa.
                        </p>

                    </div>

                </div>



                <div class="perfil-grid">


                    <?php if (!empty($perfil["salario"])) { ?>

                        <div class="perfil-dato">

                            <span>
                                Salario
                            </span>

                            <strong>

                                $<?= number_format(
                                        (float)$perfil["salario"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                            </strong>

                        </div>

                    <?php } ?>


                    <?php if (!empty($perfil["nombre_rol"])) { ?>

                        <div class="perfil-dato">

                            <span>
                                Función
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $perfil["nombre_rol"]
                                ) ?>

                            </strong>

                        </div>

                    <?php } ?>


                </div>



                <!-- =========================================
                     CARGOS DEL EMPLEADO
                ========================================== -->

                <?php if (!empty($cargos)) { ?>


                    <div class="perfil-cargos">

                        <span class="perfil-label">
                            Cargos asignados
                        </span>


                        <div class="perfil-cargos-list">


                            <?php foreach ($cargos as $cargo) { ?>


                                <span class="badge badge-success">

                                    <?= htmlspecialchars(
                                        $cargo["nombre_cargo"]
                                    ) ?>

                                </span>


                            <?php } ?>


                        </div>

                    </div>


                <?php } ?>


            </div>


        <?php } ?>



        <!-- =================================================
             SEGURIDAD
        ================================================== -->

        <div class="perfil-section">


            <div class="perfil-section-title">

                <div>

                    <h2>
                        Seguridad
                    </h2>

                    <p>
                        Opciones relacionadas con la seguridad de su cuenta.
                    </p>

                </div>

            </div>



            <div class="perfil-security">


                <div>

                    <strong>
                        Contraseña
                    </strong>

                    <p>
                        Actualice su contraseña para mantener segura su cuenta.
                    </p>

                </div>

                <a
                    href="cambiar_password.php"
                    class="btn btn-primary">

                    <i class="fa-solid fa-key"></i>

                    Cambiar contraseña

                </a>


            </div>


        </div>


    </div>


</main>


<!-- =========================================================
     ESTILOS ESPECÍFICOS DEL PERFIL
========================================================== -->


<?php

$script = "perfil";

require_once "../../layouts/footer.php";

?>