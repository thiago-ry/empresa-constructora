<?php

$id_obra = $_GET["id_obra"] ?? 0;

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Asignar empleado
            </h1>

            <p>
                Busque y seleccione un empleado activo para incorporarlo a la obra.
            </p>

        </div>

    </div>


    <div class="form-card">


        <!-- ==================================================
             BUSCADOR
        ================================================== -->

        <div class="form-group">

            <label for="buscarEmpleado">

                <i class="fa-solid fa-magnifying-glass"></i>

                Buscar empleado

            </label>


            <div style="position:relative;">

                <input
                    type="text"
                    id="buscarEmpleado"
                    class="input"
                    placeholder="Buscar por nombre, apellido o DNI..."
                    autocomplete="off">


                <span
                    id="indicadorBusqueda"
                    style="
                        position:absolute;
                        right:15px;
                        top:50%;
                        transform:translateY(-50%);
                        display:none;
                    ">

                    <i class="fa-solid fa-spinner fa-spin"></i>

                </span>

            </div>


            <small style="display:block;margin-top:8px;">
                Utilice el buscador para filtrar empleados disponibles.
            </small>

        </div>



        <!-- ==================================================
             TABLA DE EMPLEADOS
        ================================================== -->

        <div
            class="table-container"
            style="margin-top:20px;">

            <div class="table-header">

                <h2>

                    <i class="fa-solid fa-users"></i>

                    Empleados disponibles

                </h2>


                <span id="contadorEmpleados">
                    0 empleados
                </span>

            </div>


            <div style="overflow-x:auto;">

                <table class="table">

                    <thead>

                        <tr>

                            <th>
                                Empleado
                            </th>

                            <th>
                                Documento
                            </th>

                            <th>
                                Teléfono
                            </th>

                            <th class="no-print">
                                Acción
                            </th>

                        </tr>

                    </thead>


                    <tbody id="tablaEmpleados">

                        <tr>

                            <td
                                colspan="4"
                                style="text-align:center;padding:40px;">

                                <i class="fa-solid fa-users fa-2x"></i>

                                <br><br>

                                Escriba en el buscador para encontrar un empleado.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>



        <!-- ==================================================
             EMPLEADO SELECCIONADO
        ================================================== -->

        <div
            id="empleadoSeleccionado"
            style="
                display:none;
                margin-top:25px;
            ">

            <div
                style="
                    padding:20px;
                    border-radius:10px;
                    border:1px solid rgba(255,255,255,.1);
                ">


                <!-- INFORMACIÓN DEL EMPLEADO -->

                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:15px;
                    ">

                    <div
                        style="
                            width:45px;
                            height:45px;
                            min-width:45px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        ">

                        <i class="fa-solid fa-user"></i>

                    </div>


                    <div>

                        <strong>
                            Empleado seleccionado
                        </strong>


                        <div id="nombreEmpleadoSeleccionado">
                        </div>


                        <small id="documentoEmpleadoSeleccionado">
                        </small>

                    </div>


                    <button
                        type="button"
                        id="quitarEmpleado"
                        class="btn btn-danger"
                        style="margin-left:auto;">

                        <i class="fa-solid fa-xmark"></i>

                        Cambiar

                    </button>

                </div>



                <!-- ==================================================
                     CARGO PARA LA OBRA
                ================================================== -->

                <div
                    id="contenedorCargo"
                    class="form-group"
                    style="
                        display:none;
                        margin-top:22px;
                        margin-bottom:0;
                    ">

                    <label for="id_cargo">

                        <i class="fa-solid fa-briefcase"></i>

                        Cargo en esta obra

                    </label>


                    <select
                        name="id_cargo"
                        id="id_cargo"
                        class="input">

                        <option value="">
                            Seleccione el cargo que desempeñará en esta obra
                        </option>

                    </select>


                    <small
                        id="mensajeCargo"
                        style="
                            display:block;
                            margin-top:8px;
                        ">
                        Seleccione uno de los cargos que puede desempeñar este empleado.

                    </small>

                </div>


            </div>

        </div>



        <!-- ==================================================
             FORMULARIO
        ================================================== -->

        <form
            class="form"
            id="formAsignarEmpleado"
            action="/empresa_constructora/controladores/EmpleadoObraController.php"
            method="POST"
            autocomplete="off"
            style="margin-top:25px;">


            <input
                type="hidden"
                name="accion"
                value="agregar">


            <input
                type="hidden"
                name="id_obra"
                value="<?= htmlspecialchars($id_obra) ?>">


            <input
                type="hidden"
                name="id_usuario"
                id="id_usuario"
                value="">

            <input
                type="hidden"
                name="id_cargo"
                id="id_cargo_form"
                value="">

            <div class="form-row">


                <!-- ==================================================
                     FECHA DE INGRESO
                ================================================== -->

                <div class="form-group">

                    <label for="fecha_ingreso">

                        Fecha de ingreso

                    </label>


                    <input
                        type="date"
                        id="fecha_ingreso"
                        name="fecha_ingreso"
                        class="input"
                        value="<?= date("Y-m-d") ?>"
                        required>

                </div>

            </div>



            <!-- ==================================================
                 OBSERVACIONES
            ================================================== -->

            <div class="form-group">

                <label for="observaciones">

                    Observaciones

                </label>


                <textarea
                    id="observaciones"
                    name="observaciones"
                    class="input"
                    rows="4"
                    placeholder="Ingrese observaciones sobre la asignación"></textarea>

            </div>



            <!-- ==================================================
                 BOTONES
            ================================================== -->

            <div class="form-actions">


                <a
                    href="/empresa_constructora/controladores/EmpleadoObraController.php?accion=listar&id_obra=<?= htmlspecialchars($id_obra) ?>"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancelar

                </a>


                <button
                    type="reset"
                    id="btnLimpiar"
                    class="btn btn-warning">

                    <i class="fa-solid fa-rotate-left"></i>

                    Limpiar

                </button>


                <button
                    type="submit"
                    id="btnAsignar"
                    class="btn btn-primary"
                    disabled>

                    <i class="fa-solid fa-user-plus"></i>

                    Asignar empleado

                </button>


            </div>

        </form>

    </div>

</main>


<script>
    const idObra = <?= json_encode($id_obra) ?>;
</script>

<?php
$script = "empleado-obra";

require_once __DIR__ . "/../../../layouts/footer.php";
?>