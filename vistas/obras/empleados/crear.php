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

                Ingrese al menos una parte del nombre, apellido o documento.

            </small>

        </div>


        <!-- ==================================================
         TABLA DE EMPLEADOS
    ================================================== -->

        <div class="table-container" style="margin-top:20px;">


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

                            <td colspan="4" style="text-align:center;padding:40px;">

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
                padding:18px;
                border-radius:10px;
                border:1px solid rgba(255,255,255,.1);
            ">


                <div style="display:flex;align-items:center;gap:15px;">


                    <div

                        style="
                        width:45px;
                        height:45px;
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


            </div>


        </div>


        <!-- ==================================================
         FORMULARIO
    ================================================== -->

        <form

            class="form"

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


            <div class="form-row">


                <!-- FECHA -->

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


            <!-- OBSERVACIONES -->

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
        document.addEventListener("DOMContentLoaded", function() {


            /*
            ==================================================
                VARIABLES
            ==================================================
            */

            const inputBusqueda =
                document.getElementById("buscarEmpleado");

            const tabla =
                document.getElementById("tablaEmpleados");

            const contador =
                document.getElementById("contadorEmpleados");

            const indicador =
                document.getElementById("indicadorBusqueda");

            const idUsuario =
                document.getElementById("id_usuario");

            const btnAsignar =
                document.getElementById("btnAsignar");

            const empleadoSeleccionado =
                document.getElementById("empleadoSeleccionado");

            const nombreSeleccionado =
                document.getElementById("nombreEmpleadoSeleccionado");

            const documentoSeleccionado =
                document.getElementById("documentoEmpleadoSeleccionado");

            const quitarEmpleado =
                document.getElementById("quitarEmpleado");

            const btnLimpiar =
                document.getElementById("btnLimpiar");


            const idObra =
                <?= json_encode($id_obra) ?>;


            let temporizador = null;


            /*
            ==================================================
                BUSCAR EMPLEADOS
            ==================================================
            */

            function buscarEmpleados() {


                const busqueda =
                    inputBusqueda.value.trim();


                /*
                ==============================================
                    SI ESTÁ VACÍO
                ==============================================
                */

                if (busqueda.length === 0) {

                    tabla.innerHTML = `

                <tr>

                    <td colspan="4"
                        style="text-align:center;padding:40px;">

                        <i class="fa-solid fa-magnifying-glass fa-2x"></i>

                        <br><br>

                        Escriba en el buscador para encontrar un empleado.

                    </td>

                </tr>

            `;

                    contador.textContent = "0 empleados";

                    return;
                }


                /*
                ==============================================
                    INDICADOR
                ==============================================
                */

                indicador.style.display = "block";


                tabla.innerHTML = `

            <tr>

                <td colspan="4"
                    style="text-align:center;padding:40px;">

                    <i class="fa-solid fa-spinner fa-spin fa-2x"></i>

                    <br><br>

                    Buscando empleados...

                </td>

            </tr>

        `;


                /*
                ==============================================
                    AJAX
                ==============================================
                */

                fetch(
                        "/empresa_constructora/controladores/EmpleadoObraController.php" +
                        "?accion=buscarEmpleados" +
                        "&id_obra=" +
                        encodeURIComponent(idObra) +
                        "&busqueda=" +
                        encodeURIComponent(busqueda)
                    )


                    .then(response => {


                        if (!response.ok) {

                            throw new Error(
                                "Error en la respuesta del servidor."
                            );

                        }


                        return response.json();

                    })


                    .then(data => {


                        indicador.style.display = "none";


                        if (!data.success) {

                            mostrarMensaje(
                                "fa-circle-exclamation",
                                data.mensaje || "No se pudo realizar la búsqueda."
                            );

                            return;
                        }


                        mostrarEmpleados(data.empleados);

                    })


                    .catch(error => {


                        indicador.style.display = "none";


                        console.error(error);


                        mostrarMensaje(
                            "fa-triangle-exclamation",
                            "Ocurrió un error al buscar empleados."
                        );

                    });

            }


            /*
            ==================================================
                MOSTRAR EMPLEADOS
            ==================================================
            */

            function mostrarEmpleados(empleados) {


                contador.textContent =
                    empleados.length +
                    (empleados.length === 1 ?
                        " empleado" :
                        " empleados");


                if (empleados.length === 0) {

                    tabla.innerHTML = `

                <tr>

                    <td colspan="4"
                        style="text-align:center;padding:40px;">

                        <i class="fa-solid fa-user-slash fa-2x"></i>

                        <br><br>

                        <strong>
                            No se encontraron empleados
                        </strong>

                        <br>

                        <small>
                            Pruebe con otro nombre, apellido o DNI.
                        </small>

                    </td>

                </tr>

            `;

                    return;
                }


                tabla.innerHTML = "";


                empleados.forEach(empleado => {


                    const fila =
                        document.createElement("tr");


                    fila.innerHTML = `

                <td>

                    <strong>

                        ${escapeHtml(empleado.apellido)},
                        ${escapeHtml(empleado.nombre)}

                    </strong>

                </td>


                <td>

                    ${escapeHtml(empleado.documento)}

                </td>


                <td>

                    ${escapeHtml(empleado.telefono || "-")}

                </td>


                <td>

                    <button

                        type="button"

                        class="btn btn-primary btn-seleccionar"

                        data-id="${escapeHtml(empleado.id_usuario)}"

                        data-nombre="${escapeHtml(empleado.nombre)}"

                        data-apellido="${escapeHtml(empleado.apellido)}"

                        data-documento="${escapeHtml(empleado.documento)}">

                        <i class="fa-solid fa-user-check"></i>

                        Seleccionar

                    </button>

                </td>

            `;


                    tabla.appendChild(fila);

                });


                /*
                ==============================================
                    BOTONES SELECCIONAR
                ==============================================
                */

                document
                    .querySelectorAll(".btn-seleccionar")
                    .forEach(boton => {


                        boton.addEventListener(
                            "click",
                            function() {


                                seleccionarEmpleado({

                                    id: this.dataset.id,

                                    nombre: this.dataset.nombre,

                                    apellido: this.dataset.apellido,

                                    documento: this.dataset.documento

                                });

                            }
                        );

                    });

            }


            /*
            ==================================================
                SELECCIONAR EMPLEADO
            ==================================================
            */

            function seleccionarEmpleado(empleado) {


                idUsuario.value =
                    empleado.id;


                nombreSeleccionado.textContent =
                    empleado.apellido +
                    ", " +
                    empleado.nombre;


                documentoSeleccionado.textContent =
                    "DNI: " +
                    empleado.documento;


                empleadoSeleccionado.style.display =
                    "block";


                btnAsignar.disabled =
                    false;


                /*
                ==============================================
                    DESHABILITAR BUSCADOR
                ==============================================
                */

                inputBusqueda.disabled =
                    true;


                /*
                ==============================================
                    MARCAR TABLA
                ==============================================
                */

                document
                    .querySelectorAll(".btn-seleccionar")
                    .forEach(boton => {

                        boton.disabled = true;

                    });

            }


            /*
            ==================================================
                QUITAR EMPLEADO
            ==================================================
            */

            quitarEmpleado.addEventListener(
                "click",
                function() {

                    idUsuario.value = "";

                    nombreSeleccionado.textContent = "";

                    documentoSeleccionado.textContent = "";

                    empleadoSeleccionado.style.display =
                        "none";

                    btnAsignar.disabled =
                        true;

                    inputBusqueda.disabled =
                        false;

                    inputBusqueda.focus();

                }
            );


            /*
            ==================================================
                BUSCADOR CON DELAY
            ==================================================
            */

            inputBusqueda.addEventListener(
                "input",
                function() {


                    clearTimeout(temporizador);


                    temporizador = setTimeout(
                        buscarEmpleados,
                        350
                    );

                }
            );


            /*
            ==================================================
                LIMPIAR FORMULARIO
            ==================================================
            */

            btnLimpiar.addEventListener(
                "click",
                function() {


                    setTimeout(function() {


                        inputBusqueda.value = "";

                        inputBusqueda.disabled = false;


                        idUsuario.value = "";


                        empleadoSeleccionado.style.display =
                            "none";


                        nombreSeleccionado.textContent = "";

                        documentoSeleccionado.textContent = "";


                        btnAsignar.disabled =
                            true;


                        contador.textContent =
                            "0 empleados";


                        tabla.innerHTML = `

                    <tr>

                        <td colspan="4"
                            style="text-align:center;padding:40px;">

                            <i class="fa-solid fa-magnifying-glass fa-2x"></i>

                            <br><br>

                            Escriba en el buscador para encontrar un empleado.

                        </td>

                    </tr>

                `;


                    }, 50);

                }
            );


            /*
            ==================================================
                PROTEGER ENVÍO
            ==================================================
            */

            document
                .querySelector("form")
                .addEventListener(
                    "submit",
                    function(evento) {


                        if (!idUsuario.value) {

                            evento.preventDefault();


                            alert(
                                "Debe seleccionar un empleado antes de asignarlo."
                            );


                            inputBusqueda.focus();

                        }

                    }
                );


            /*
            ==================================================
                ESCAPAR HTML
            ==================================================
            */

            function escapeHtml(valor) {


                if (valor === null || valor === undefined) {

                    return "";

                }


                return String(valor)

                    .replace(/&/g, "&amp;")

                    .replace(/</g, "&lt;")

                    .replace(/>/g, "&gt;")

                    .replace(/"/g, "&quot;")

                    .replace(/'/g, "&#039;");

            }


            /*
            ==================================================
                MENSAJE
            ==================================================
            */

            function mostrarMensaje(icono, mensaje) {


                tabla.innerHTML = `

            <tr>

                <td colspan="4"
                    style="text-align:center;padding:40px;">

                    <i class="fa-solid ${icono} fa-2x"></i>

                    <br><br>

                    ${escapeHtml(mensaje)}

                </td>

            </tr>

        `;


                contador.textContent =
                    "0 empleados";

            }

        });
    </script>