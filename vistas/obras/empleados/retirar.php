<?php

require_once "../../../modelos/EmpleadoObra.php";
require_once "../../../config/permisos.php";

verificarPermiso("obras");


$id = $_GET["id"] ?? 0;

$empleadoObra = new EmpleadoObra();

$empleado =
    $empleadoObra->buscarPorId($id);


if (!$empleado) {

    header(
        "Location: ../../../controladores/EmpleadoObraController.php?accion=listar&id_obra="
            . ($_GET["id_obra"] ?? 0)
    );

    exit();
}


$id_obra =
    $empleado["id_obra"];

$id_usuario =
    $empleado["id_usuario"];


/*
==========================================================
    OTRAS OBRAS
==========================================================
*/

$otrasObras =
    $empleadoObra->obtenerOtrasObrasActivas(
        $id_usuario,
        $id_obra
    );


require_once "../../../layouts/header.php";
require_once "../../../layouts/sidebar.php";

?>

<main class="content">


    <!-- ==================================================
         ENCABEZADO
    ================================================== -->

    <div class="page-title no-print">

        <h1>
            Retirar empleado
        </h1>

        <p>
            Registrar el retiro del empleado de esta obra.
        </p>

    </div>


    <!-- ==================================================
         FORM CARD
    ================================================== -->

    <div class="form-card">


        <!-- ==================================================
             INFORMACIÓN
        ================================================== -->

        <div
            style="
                display:flex;
                align-items:flex-start;
                gap:15px;
                padding:18px 20px;
                margin-bottom:25px;
                border-radius:10px;
                border:1px solid rgba(255,193,7,.25);
                background:rgba(255,193,7,.08);
            ">

            <div
                style="
                    width:42px;
                    height:42px;
                    min-width:42px;
                    border-radius:50%;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    background:rgba(255,193,7,.15);
                ">

                <i class="fa-solid fa-triangle-exclamation"></i>

            </div>


            <div>

                <strong>
                    Retiro de empleado
                </strong>

                <p
                    style="
                        margin:5px 0 0;
                        opacity:.75;
                    ">
                    El retiro conservará el historial del empleado.
                    No se eliminarán registros del sistema.
                </p>

            </div>

        </div>


        <!-- ==================================================
             EMPLEADO
        ================================================== -->

        <div
            style="
                padding:22px;
                border-radius:12px;
                border:1px solid rgba(255,255,255,.08);
                margin-bottom:28px;
            ">

            <div
                style="
                    display:flex;
                    align-items:center;
                    gap:16px;
                ">

                <div
                    style="
                        width:52px;
                        height:52px;
                        min-width:52px;
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:rgba(255,255,255,.06);
                    ">

                    <i class="fa-solid fa-user"></i>

                </div>


                <div>

                    <small
                        style="
                            display:block;
                            opacity:.55;
                        ">
                        Empleado
                    </small>

                    <strong
                        style="
                            font-size:19px;
                        ">

                        <?= htmlspecialchars(
                            $empleado["apellido"]
                                . ", "
                                . $empleado["nombre"]
                        ) ?>

                    </strong>


                    <div
                        style="
                            margin-top:4px;
                            opacity:.7;
                            font-size:14px;
                        ">

                        DNI:
                        <?= htmlspecialchars(
                            $empleado["documento"] ?? "-"
                        ) ?>

                    </div>

                </div>

            </div>


            <div
                style="
                    display:grid;
                    grid-template-columns:
                        repeat(auto-fit,minmax(180px,1fr));
                    gap:20px;
                    margin-top:22px;
                ">

                <div>

                    <small>
                        Cargo en esta obra
                    </small>

                    <div style="margin-top:5px;">

                        <strong>

                            <?= htmlspecialchars(
                                $empleado["nombre_cargo"]
                                    ?? "Sin cargo"
                            ) ?>

                        </strong>

                    </div>

                </div>


                <div>

                    <small>
                        Fecha de ingreso
                    </small>

                    <div style="margin-top:5px;">

                        <strong>

                            <?= !empty($empleado["fecha_ingreso"])
                                ? date(
                                    "d/m/Y",
                                    strtotime(
                                        $empleado["fecha_ingreso"]
                                    )
                                )
                                : "-"
                            ?>

                        </strong>

                    </div>

                </div>


                <div>

                    <small>
                        Otras obras activas
                    </small>

                    <div style="margin-top:5px;">

                        <strong>

                            <?= count($otrasObras) ?>

                        </strong>

                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             FORMULARIO
        ================================================== -->

        <form
            id="formRetirarEmpleado"
            action="../../../controladores/EmpleadoObraController.php"
            method="POST"
            class="form">

            <input
                type="hidden"
                name="accion"
                value="retirar">

            <input
                type="hidden"
                name="id_empleado_obra"
                value="<?= htmlspecialchars($empleado["id_empleado_obra"]) ?>">

            <input
                type="hidden"
                name="id_obra"
                value="<?= htmlspecialchars($id_obra) ?>">

            <input
                type="hidden"
                name="id_usuario"
                value="<?= htmlspecialchars($id_usuario) ?>">

            <input
                type="hidden"
                name="alcance"
                id="alcance"
                value="actual">


            <!-- FECHA -->

            <div class="form-group">

                <label for="fecha_egreso">

                    <i class="fa-solid fa-calendar"></i>

                    Fecha de egreso

                </label>

                <input
                    type="date"
                    name="fecha_egreso"
                    id="fecha_egreso"
                    class="input"
                    value="<?= date("Y-m-d") ?>"
                    required>

            </div>


            <!-- MOTIVO -->

            <div class="form-group">

                <label for="motivo_egreso">

                    <i class="fa-solid fa-circle-question"></i>

                    Motivo del retiro

                </label>


                <select
                    name="motivo_egreso"
                    id="motivo_egreso"
                    class="input"
                    required>

                    <option value="">
                        Seleccione un motivo
                    </option>

                    <option value="Renuncia">
                        Renuncia
                    </option>

                    <option value="Despido">
                        Despido
                    </option>

                    <option value="Finalización de contrato">
                        Finalización de contrato
                    </option>

                    <option value="Finalización de trabajo">
                        Finalización de trabajo
                    </option>

                    <option value="Cambio de obra">
                        Cambio de obra
                    </option>

                    <option value="Accidente / licencia">
                        Accidente / licencia
                    </option>

                    <option value="Decisión de la empresa">
                        Decisión de la empresa
                    </option>

                    <option value="Otro">
                        Otro
                    </option>

                </select>

            </div>


            <!-- OBSERVACIONES -->

            <div class="form-group">

                <label for="observaciones">

                    <i class="fa-solid fa-note-sticky"></i>

                    Observaciones

                </label>


                <textarea
                    name="observaciones"
                    id="observaciones"
                    class="input"
                    rows="4"
                    placeholder="Ingrese información adicional..."></textarea>

            </div>


            <!-- BOTONES -->

            <div
                class="form-actions"
                style="
                    margin-top:30px;
                    padding-top:22px;
                    border-top:1px solid rgba(255,255,255,.08);
                ">

                <a
                    href="../../../controladores/EmpleadoObraController.php?accion=listar&id_obra=<?= htmlspecialchars($id_obra) ?>"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancelar

                </a>


                <button
                    type="submit"
                    class="btn btn-danger">

                    <i class="fa-solid fa-user-minus"></i>

                    Continuar

                </button>

            </div>

        </form>

    </div>

</main>


<!-- ======================================================
     MODAL
====================================================== -->

<div
    id="modalRetiro"
    class="modal-overlay"
    style="display:none;">

    <div class="modal-card">


        <div class="modal-header">

            <div>

                <h2>
                    Retiro de empleado
                </h2>

                <p>
                    Este empleado tiene otras asignaciones activas.
                </p>

            </div>


            <button
                type="button"
                id="cerrarModal"
                class="modal-close">

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>


        <div class="modal-body">


            <div
                style="
                    display:flex;
                    gap:14px;
                    align-items:flex-start;
                    padding:15px;
                    border-radius:10px;
                    background:rgba(255,193,7,.08);
                    margin-bottom:20px;
                ">

                <i class="fa-solid fa-circle-info"></i>

                <div>

                    <strong>
                        <?= htmlspecialchars(
                            $empleado["nombre"]
                                . " "
                                . $empleado["apellido"]
                        ) ?>
                    </strong>

                    <p style="margin:4px 0 0;opacity:.7;">

                        Está actualmente asignado a
                        <strong><?= count($otrasObras) ?></strong>
                        obra<?= count($otrasObras) == 1 ? "" : "s" ?>
                        adicional<?= count($otrasObras) == 1 ? "" : "es" ?>.

                    </p>

                </div>

            </div>


            <!-- ==================================================
                 OPCIONES
            ================================================== -->

            <div class="retiro-opciones">


                <!-- SOLO ESTA -->

                <label class="retiro-option">

                    <input
                        type="radio"
                        name="tipo_retiro"
                        value="actual"
                        checked>

                    <div>

                        <strong>
                            Solo esta obra
                        </strong>

                        <small>
                            El empleado continuará activo en las demás obras.
                        </small>

                    </div>

                </label>


                <!-- SELECCIONADAS -->

                <label class="retiro-option">

                    <input
                        type="radio"
                        name="tipo_retiro"
                        value="seleccionadas">

                    <div>

                        <strong>
                            Obras seleccionadas
                        </strong>

                        <small>
                            Seleccione de qué otras obras desea retirarlo.
                        </small>

                    </div>

                </label>


                <!-- TODAS -->

                <label class="retiro-option">

                    <input
                        type="radio"
                        name="tipo_retiro"
                        value="todas">

                    <div>

                        <strong>
                            Todas las obras activas
                        </strong>

                        <small>
                            El empleado será retirado de todas sus obras.
                        </small>

                    </div>

                </label>

            </div>


            <!-- ==================================================
                 OBRAS
            ================================================== -->

            <div
                id="listaObras"
                style="display:none;margin-top:20px;">

                <h3 style="margin-bottom:12px;">

                    Seleccione las obras

                </h3>


                <?php foreach ($otrasObras as $obra): ?>

                    <label
                        class="obra-checkbox">

                        <input
                            type="checkbox"
                            name="obras_seleccionadas[]"
                            value="<?= htmlspecialchars($obra["id_obra"]) ?>">


                        <div>

                            <strong>

                                <?= htmlspecialchars(
                                    $obra["nombre_obra"]
                                ) ?>

                            </strong>


                            <small>

                                <?= htmlspecialchars(
                                    $obra["nombre_cargo"]
                                        ?? "Sin cargo"
                                ) ?>

                            </small>

                        </div>

                    </label>

                <?php endforeach; ?>

            </div>


            <!-- ==================================================
                 RECOMENDACIÓN
            ================================================== -->

            <div
                id="recomendacionRetiro"
                style="
                    margin-top:20px;
                    padding:15px;
                    border-radius:10px;
                    background:rgba(255,255,255,.04);
                ">

                <i class="fa-solid fa-lightbulb"></i>

                <span>
                    Seleccione una opción para continuar.
                </span>

            </div>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                id="cancelarModal"
                class="btn btn-secondary">

                Cancelar

            </button>


            <button
                type="button"
                id="confirmarRetiro"
                class="btn btn-danger">

                <i class="fa-solid fa-user-minus"></i>

                Confirmar retiro

            </button>

        </div>

    </div>

</div>


<style>
    /* ==========================================================
   MODAL
========================================================== */

    .modal-overlay {

        position: fixed;

        inset: 0;

        background: rgba(0, 0, 0, .65);

        backdrop-filter: blur(4px);

        display: flex;

        align-items: center;

        justify-content: center;

        z-index: 9999;

        padding: 20px;
    }


    .modal-card {

        width: 100%;

        max-width: 650px;

        max-height: 90vh;

        overflow: auto;

        background: var(--surface, #1c1c1c);

        border-radius: 14px;

        border: 1px solid rgba(255, 255, 255, .1);

        box-shadow: 0 25px 70px rgba(0, 0, 0, .45);

    }


    .modal-header {

        display: flex;

        justify-content: space-between;

        align-items: flex-start;

        padding: 22px;

        border-bottom: 1px solid rgba(255, 255, 255, .08);

    }


    .modal-header h2 {

        margin: 0;

    }


    .modal-header p {

        margin: 5px 0 0;

        opacity: .65;

    }


    .modal-close {

        border: 0;

        background: none;

        color: inherit;

        font-size: 20px;

        cursor: pointer;

    }


    .modal-body {

        padding: 22px;

    }


    .modal-footer {

        display: flex;

        justify-content: flex-end;

        gap: 10px;

        padding: 18px 22px;

        border-top: 1px solid rgba(255, 255, 255, .08);

    }


    /* ==========================================================
   OPCIONES
========================================================== */

    .retiro-opciones {

        display: flex;

        flex-direction: column;

        gap: 10px;
    }


    .retiro-option {

        display: flex;

        align-items: flex-start;

        gap: 13px;

        padding: 16px;

        border: 1px solid rgba(255, 255, 255, .08);

        border-radius: 10px;

        cursor: pointer;

        transition: .2s;

    }


    .retiro-option:hover {

        background: rgba(255, 255, 255, .04);

    }


    .retiro-option input {

        margin-top: 4px;

    }


    .retiro-option strong {

        display: block;

    }


    .retiro-option small {

        display: block;

        margin-top: 4px;

        opacity: .6;

    }


    /* ==========================================================
   OBRAS
========================================================== */

    .obra-checkbox {

        display: flex;

        align-items: center;

        gap: 12px;

        padding: 14px;

        margin-bottom: 8px;

        border: 1px solid rgba(255, 255, 255, .08);

        border-radius: 9px;

        cursor: pointer;

    }


    .obra-checkbox:hover {

        background: rgba(255, 255, 255, .04);

    }


    .obra-checkbox small {

        display: block;

        margin-top: 3px;

        opacity: .6;

    }
</style>


<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {


            const formulario =
                document.getElementById(
                    "formRetirarEmpleado"
                );


            const modal =
                document.getElementById(
                    "modalRetiro"
                );


            const cerrarModal =
                document.getElementById(
                    "cerrarModal"
                );


            const cancelarModal =
                document.getElementById(
                    "cancelarModal"
                );


            const confirmarRetiro =
                document.getElementById(
                    "confirmarRetiro"
                );


            const alcance =
                document.getElementById(
                    "alcance"
                );


            const listaObras =
                document.getElementById(
                    "listaObras"
                );


            const recomendacion =
                document.getElementById(
                    "recomendacionRetiro"
                );


            const motivo =
                document.getElementById(
                    "motivo_egreso"
                );


            /*
            ======================================================
                ENVIAR FORMULARIO
            ======================================================
            */

            formulario.addEventListener(
                "submit",
                function(evento) {

                    evento.preventDefault();


                    /*
                    ----------------------------------------------
                        SI NO HAY OTRAS OBRAS
                    ----------------------------------------------
                    */

                    const cantidadObras =
                        <?= count($otrasObras) ?>;


                    if (cantidadObras === 0) {

                        alcance.value =
                            "actual";


                        formulario.submit();

                        return;
                    }


                    /*
                    ----------------------------------------------
                        MOSTRAR MODAL
                    ----------------------------------------------
                    */

                    modal.style.display =
                        "flex";

                }
            );


            /*
            ======================================================
                CAMBIO DE OPCIÓN
            ======================================================
            */

            document
                .querySelectorAll(
                    'input[name="tipo_retiro"]'
                )
                .forEach(
                    function(radio) {

                        radio.addEventListener(
                            "change",
                            actualizarModal
                        );

                    }
                );


            /*
            ======================================================
                ACTUALIZAR MODAL
            ======================================================
            */

            function actualizarModal() {

                const seleccionado =
                    document.querySelector(
                        'input[name="tipo_retiro"]:checked'
                    ).value;


                alcance.value =
                    seleccionado;


                /*
                ----------------------------------------------
                    MOSTRAR / OCULTAR OBRAS
                ----------------------------------------------
                */

                if (
                    seleccionado ===
                    "seleccionadas"
                ) {

                    listaObras.style.display =
                        "block";

                } else {

                    listaObras.style.display =
                        "none";

                }


                /*
                ----------------------------------------------
                    RECOMENDACIÓN
                ----------------------------------------------
                */

                if (
                    motivo.value ===
                    "Renuncia"
                ) {

                    recomendacion.innerHTML = `

                    <i class="fa-solid fa-lightbulb"></i>

                    <span>

                        Por tratarse de una renuncia,
                        se recomienda retirar al empleado
                        de todas sus obras activas.

                    </span>

                `;

                } else if (
                    motivo.value ===
                    "Cambio de obra"
                ) {

                    recomendacion.innerHTML = `

                    <i class="fa-solid fa-lightbulb"></i>

                    <span>

                        En un cambio de obra,
                        normalmente se recomienda retirar
                        solamente de la obra actual.

                    </span>

                `;

                } else if (
                    motivo.value ===
                    "Accidente / licencia"
                ) {

                    recomendacion.innerHTML = `

                    <i class="fa-solid fa-circle-info"></i>

                    <span>

                        Un accidente o licencia no implica
                        necesariamente la desvinculación
                        de las demás obras.

                    </span>

                `;

                } else {

                    recomendacion.innerHTML = `

                    <i class="fa-solid fa-lightbulb"></i>

                    <span>

                        Seleccione el alcance del retiro
                        según la situación del empleado.

                    </span>

                `;

                }

            }


            motivo.addEventListener(
                "change",
                actualizarModal
            );

            confirmarRetiro.addEventListener(
                "click",
                function() {

                    const tipo =
                        document.querySelector(
                            'input[name="tipo_retiro"]:checked'
                        ).value;


                    if (
                        tipo ===
                        "seleccionadas"
                    ) {

                        const seleccionadas =
                            document.querySelectorAll(
                                'input[name="obras_seleccionadas[]"]:checked'
                            );


                        if (
                            seleccionadas.length === 0
                        ) {

                            alert(
                                "Debe seleccionar al menos una obra."
                            );

                            return;
                        }

                    }


                    alcance.value =
                        tipo;


                    formulario.submit();

                }
            );

            cerrarModal.addEventListener(
                "click",
                function() {

                    modal.style.display =
                        "none";

                }
            );


            cancelarModal.addEventListener(
                "click",
                function() {

                    modal.style.display =
                        "none";

                }
            );

            modal.addEventListener(
                "click",
                function(evento) {

                    if (
                        evento.target === modal
                    ) {

                        modal.style.display =
                            "none";

                    }

                }
            );

            document.addEventListener(
                "keydown",
                function(evento) {

                    if (
                        evento.key === "Escape"
                    ) {

                        modal.style.display =
                            "none";

                    }

                }
            );

        }
    );
</script>


<?php

require_once "../../../layouts/footer.php";

?>