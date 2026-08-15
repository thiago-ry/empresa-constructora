document.addEventListener("DOMContentLoaded", function () {

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

    const idCargo =
        document.getElementById("id_cargo");

    /*
    ==================================================
        ID CARGO QUE SE ENVÍA AL FORMULARIO
    ==================================================
    */

    const idCargoForm =
        document.getElementById("id_cargo_form");


    const contenedorCargo =
        document.getElementById("contenedorCargo");

    const mensajeCargo =
        document.getElementById("mensajeCargo");

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

    const form =
        document.getElementById("formAsignarEmpleado");


    /*
    ==================================================
        ID DE LA OBRA
    ==================================================

        idObra viene definido desde la vista PHP.

    ==================================================
    */


    let temporizador = null;


    /*
    ==================================================
        BUSCAR EMPLEADOS
    ==================================================
    */

    function buscarEmpleados() {

        const busqueda =
            inputBusqueda.value.trim();


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
                    data.mensaje ||
                    "No se pudo realizar la búsqueda."
                );

                return;
            }


            mostrarEmpleados(
                data.empleados || []
            );

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
            (
                empleados.length === 1
                    ? " empleado"
                    : " empleados"
            );


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
                    function () {

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


        /*
        ==============================================
            LIMPIAR CARGO ANTERIOR
        ==============================================
        */

        idCargo.value = "";

        idCargoForm.value = "";


        nombreSeleccionado.textContent =
            empleado.apellido +
            ", " +
            empleado.nombre;


        documentoSeleccionado.textContent =
            "DNI: " +
            empleado.documento;


        empleadoSeleccionado.style.display =
            "block";


        /*
        ==============================================
            DESHABILITAR BUSCADOR
        ==============================================
        */

        inputBusqueda.disabled =
            true;


        /*
        ==============================================
            DESHABILITAR BOTONES
        ==============================================
        */

        document
            .querySelectorAll(".btn-seleccionar")
            .forEach(boton => {

                boton.disabled = true;

            });


        /*
        ==============================================
            CARGAR CARGOS
        ==============================================
        */

        cargarCargos(empleado.id);


        /*
        ==============================================
            TODAVÍA NO HABILITAR ASIGNAR
        ==============================================
        */

        btnAsignar.disabled =
            true;

    }


    /*
    ==================================================
        CARGAR CARGOS DEL EMPLEADO
    ==================================================
    */

    function cargarCargos(idEmpleado) {

        contenedorCargo.style.display =
            "block";


        idCargo.innerHTML = `

            <option value="">
                Cargando cargos...
            </option>

        `;


        idCargo.disabled = true;


        idCargoForm.value = "";


        mensajeCargo.textContent =
            "Consultando los cargos disponibles del empleado...";


        fetch(
            "/empresa_constructora/controladores/EmpleadoObraController.php" +
            "?accion=obtenerCargos" +
            "&id_usuario=" +
            encodeURIComponent(idEmpleado)
        )

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    "Error al consultar los cargos."
                );

            }

            return response.json();

        })

        .then(data => {

            /*
            ==========================================
                ERROR DEL SERVIDOR
            ==========================================
            */

            if (!data.success) {

                idCargo.innerHTML = `

                    <option value="">
                        No se pudieron obtener los cargos
                    </option>

                `;

                idCargo.disabled = true;

                idCargoForm.value = "";

                mensajeCargo.textContent =
                    data.mensaje ||
                    "No se pudieron obtener los cargos.";

                btnAsignar.disabled = true;

                return;
            }


            /*
            ==========================================
                SIN CARGOS
            ==========================================
            */

            if (
                !data.cargos ||
                data.cargos.length === 0
            ) {

                idCargo.innerHTML = `

                    <option value="">
                        Este empleado no tiene cargos asignados
                    </option>

                `;

                idCargo.disabled = true;

                idCargoForm.value = "";

                mensajeCargo.textContent =
                    "El empleado debe tener al menos un cargo registrado antes de ser asignado a una obra.";

                btnAsignar.disabled = true;

                return;
            }


            /*
            ==========================================
                CARGOS ENCONTRADOS
            ==========================================
            */

            idCargo.innerHTML = `

                <option value="">
                    Seleccione el cargo para esta obra
                </option>

            `;


            data.cargos.forEach(cargo => {

                const option =
                    document.createElement("option");


                option.value =
                    cargo.id_cargo;


                option.textContent =
                    cargo.nombre_cargo;


                idCargo.appendChild(option);

            });


            idCargo.disabled = false;


            mensajeCargo.textContent =
                "Seleccione el cargo que desempeñará el empleado en esta obra.";

        })

        .catch(error => {

            console.error(error);


            idCargo.innerHTML = `

                <option value="">
                    Error al cargar los cargos
                </option>

            `;


            idCargo.disabled = true;

            idCargoForm.value = "";


            mensajeCargo.textContent =
                "Ocurrió un error al consultar los cargos.";


            btnAsignar.disabled = true;

        });

    }


    /*
    ==================================================
        CAMBIO DE CARGO
    ==================================================
    */

    idCargo.addEventListener(
        "change",
        function () {

            /*
            ==========================================
                COPIAR CARGO AL FORMULARIO
            ==========================================
            */

            idCargoForm.value =
                idCargo.value;


            /*
            ==========================================
                HABILITAR ASIGNAR
            ==========================================
            */

            if (
                idUsuario.value &&
                idCargo.value
            ) {

                btnAsignar.disabled =
                    false;

            } else {

                btnAsignar.disabled =
                    true;

            }

        }
    );


    /*
    ==================================================
        CAMBIAR EMPLEADO
    ==================================================
    */

    quitarEmpleado.addEventListener(
        "click",
        function () {

            /*
            ==============================================
                LIMPIAR EMPLEADO
            ==============================================
            */

            idUsuario.value = "";

            nombreSeleccionado.textContent = "";
            documentoSeleccionado.textContent = "";

            empleadoSeleccionado.style.display =
                "none";


            /*
            ==============================================
                LIMPIAR CARGO
            ==============================================
            */

            idCargo.value = "";

            idCargoForm.value = "";


            idCargo.innerHTML = `

                <option value="">
                    Seleccione el cargo para esta obra
                </option>

            `;


            idCargo.disabled = true;


            contenedorCargo.style.display =
                "none";


            /*
            ==============================================
                DESHABILITAR ASIGNAR
            ==============================================
            */

            btnAsignar.disabled =
                true;


            /*
            ==============================================
                MOSTRAR TODOS LOS EMPLEADOS
            ==============================================
            */

            inputBusqueda.disabled =
                false;

            inputBusqueda.value = "";


            buscarEmpleados();


            /*
            ==============================================
                ENFOCAR BUSCADOR
            ==============================================
            */

            setTimeout(function () {

                inputBusqueda.focus();

            }, 100);

        }
    );


    /*
    ==================================================
        BUSCADOR CON DELAY
    ==================================================
    */

    inputBusqueda.addEventListener(
        "input",
        function () {

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
        function () {

            setTimeout(function () {

                inputBusqueda.value = "";

                inputBusqueda.disabled =
                    false;


                idUsuario.value =
                    "";


                idCargo.value =
                    "";

                idCargoForm.value =
                    "";


                idCargo.innerHTML = `

                    <option value="">
                        Seleccione el cargo para esta obra
                    </option>

                `;


                idCargo.disabled =
                    true;


                contenedorCargo.style.display =
                    "none";


                empleadoSeleccionado.style.display =
                    "none";


                nombreSeleccionado.textContent =
                    "";

                documentoSeleccionado.textContent =
                    "";


                btnAsignar.disabled =
                    true;


                contador.textContent =
                    "0 empleados";


                tabla.innerHTML = `

                    <tr>

                        <td
                            colspan="4"
                            style="text-align:center;padding:40px;"
                        >

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

    form.addEventListener(
        "submit",
        function (evento) {

            /*
            ==========================================
                SIN EMPLEADO
            ==========================================
            */

            if (!idUsuario.value) {

                evento.preventDefault();

                alert(
                    "Debe seleccionar un empleado antes de asignarlo."
                );

                inputBusqueda.focus();

                return;

            }


            /*
            ==========================================
                SIN CARGO
            ==========================================
            */

            if (!idCargo.value) {

                evento.preventDefault();

                alert(
                    "Debe seleccionar el cargo que desempeñará el empleado en esta obra."
                );

                idCargo.focus();

                return;

            }


            /*
            ==========================================
                ASEGURAR CARGO EN EL FORMULARIO
            ==========================================
            */

            idCargoForm.value =
                idCargo.value;

        }
    );


    /*
    ==================================================
        ESCAPAR HTML
    ==================================================
    */

    function escapeHtml(valor) {

        if (
            valor === null ||
            valor === undefined
        ) {

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
        MOSTRAR MENSAJE
    ==================================================
    */

    function mostrarMensaje(icono, mensaje) {

        tabla.innerHTML = `

            <tr>

                <td
                    colspan="4"
                    style="text-align:center;padding:40px;"
                >

                    <i class="fa-solid ${icono} fa-2x"></i>

                    <br><br>

                    ${escapeHtml(mensaje)}

                </td>

            </tr>

        `;


        contador.textContent =
            "0 empleados";

    }


    /*
    ==================================================
        CARGAR EMPLEADOS AL INICIAR
    ==================================================
    */

    buscarEmpleados();

});