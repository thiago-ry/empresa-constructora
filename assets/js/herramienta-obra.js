document.addEventListener("DOMContentLoaded", function () {

    const buscarHerramienta = document.getElementById("buscarHerramienta");
    const tablaHerramientas = document.getElementById("tablaHerramientas");
    const contadorHerramientas = document.getElementById("contadorHerramientas");

    const herramientaSeleccionada = document.getElementById("herramientaSeleccionada");
    const nombreHerramientaSeleccionada = document.getElementById("nombreHerramientaSeleccionada");
    const disponiblesHerramientaSeleccionada = document.getElementById("disponiblesHerramientaSeleccionada");

    const idHerramienta = document.getElementById("id_herramienta");
    const cantidad = document.getElementById("cantidad");
    const mensajeCantidad = document.getElementById("mensajeCantidad");

    const quitarHerramienta = document.getElementById("quitarHerramienta");
    const btnAsignar = document.getElementById("btnAsignar");
    const btnLimpiar = document.getElementById("btnLimpiar");

    let disponiblesSeleccionados = 0;


    // ==================================================
    // BUSCADOR
    // ==================================================

    buscarHerramienta.addEventListener("input", function () {

        const texto = this.value.toLowerCase().trim();

        const filas = document.querySelectorAll(".fila-herramienta");

        let cantidadVisibles = 0;

        filas.forEach(function (fila) {

            const nombre = fila.dataset.nombre || "";
            const descripcion = fila.dataset.descripcion || "";

            const coincide =
                nombre.includes(texto) ||
                descripcion.includes(texto);

            if (coincide) {

                fila.style.display = "";
                cantidadVisibles++;

            } else {

                fila.style.display = "none";

            }

        });

        contadorHerramientas.textContent =
            cantidadVisibles + " herramientas";

    });



    // ==================================================
    // SELECCIONAR HERRAMIENTA
    // ==================================================

    document.querySelectorAll(".btn-seleccionar-herramienta").forEach(function (boton) {

        boton.addEventListener("click", function () {

            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const disponibles = parseInt(this.dataset.disponibles);

            idHerramienta.value = id;

            disponiblesSeleccionados = disponibles;

            nombreHerramientaSeleccionada.textContent = nombre;

            disponiblesHerramientaSeleccionada.textContent =
                disponibles + " unidades disponibles";

            cantidad.max = disponibles;

            cantidad.value = 1;

            mensajeCantidad.textContent =
                "Puede asignar hasta " + disponibles + " unidades.";

            herramientaSeleccionada.style.display = "block";

            btnAsignar.disabled = false;

            // Ocultar las demás filas
            document.querySelectorAll(".fila-herramienta").forEach(function (fila) {
                fila.style.display = "none";
            });

            // Mostrar solo la herramienta seleccionada
            this.closest(".fila-herramienta").style.display = "";

        });

    });



    // ==================================================
    // CAMBIAR HERRAMIENTA
    // ==================================================

    quitarHerramienta.addEventListener("click", function () {

        idHerramienta.value = "";

        disponiblesSeleccionados = 0;

        herramientaSeleccionada.style.display = "none";

        btnAsignar.disabled = true;

        cantidad.value = 1;

        cantidad.removeAttribute("max");

        mensajeCantidad.textContent =
            "Seleccione una herramienta para consultar la cantidad disponible.";

        buscarHerramienta.value = "";

        document.querySelectorAll(".fila-herramienta").forEach(function (fila) {
            fila.style.display = "";
        });

        contadorHerramientas.textContent =
            document.querySelectorAll(".fila-herramienta").length + " herramientas";

    });



    // ==================================================
    // VALIDAR CANTIDAD
    // ==================================================

    cantidad.addEventListener("input", function () {

        const valor = parseInt(this.value);

        if (valor > disponiblesSeleccionados) {

            this.value = disponiblesSeleccionados;

        }

        if (valor < 1 || isNaN(valor)) {

            this.value = 1;

        }

    });



    // ==================================================
    // LIMPIAR FORMULARIO
    // ==================================================

    btnLimpiar.addEventListener("click", function () {

        setTimeout(function () {

            idHerramienta.value = "";

            herramientaSeleccionada.style.display = "none";

            btnAsignar.disabled = true;

            cantidad.value = 1;

            cantidad.removeAttribute("max");

            disponiblesSeleccionados = 0;

            mensajeCantidad.textContent =
                "Seleccione una herramienta para consultar la cantidad disponible.";

            buscarHerramienta.value = "";

            document.querySelectorAll(".fila-herramienta").forEach(function (fila) {
                fila.style.display = "";
            });

            contadorHerramientas.textContent =
                document.querySelectorAll(".fila-herramienta").length + " herramientas";

        }, 10);

    });

});