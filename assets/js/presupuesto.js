document.addEventListener("DOMContentLoaded", () => {

    const formulario = document.getElementById("formPresupuesto");
    const selectorMaterial = document.getElementById("selectorMaterial");
    const btnAgregarMaterial = document.getElementById("agregarMaterial");
    const btnAgregarTrabajo = document.getElementById("agregarTrabajo");

    const tbodyMateriales = document.getElementById("materiales");
    const tbodyTrabajos = document.getElementById("trabajos");

    if (
        !formulario ||
        !selectorMaterial ||
        !btnAgregarMaterial ||
        !btnAgregarTrabajo ||
        !tbodyMateriales ||
        !tbodyTrabajos
    ) {
        console.error("No se encontraron los elementos del formulario.");
        return;
    }

    let catalogoMateriales = [];
    let contadorMateriales = 0;
    let contadorTrabajos = 0;

    const formatoMoneda = new Intl.NumberFormat("es-AR", {
        style: "currency",
        currency: "ARS",
        minimumFractionDigits: 2
    });

    function numero(valor) {
        const resultado = Number(valor);
        return Number.isFinite(resultado) ? resultado : 0;
    }

    function dinero(valor) {
        return formatoMoneda.format(valor);
    }

    function crearCelda(elemento) {
        const td = document.createElement("td");
        td.appendChild(elemento);
        return td;
    }

    function crearInput(nombre, tipo, valor, placeholder = "") {
        const input = document.createElement("input");

        input.type = tipo;
        input.name = nombre;
        input.value = valor;
        input.placeholder = placeholder;
        input.required = true;
        input.className = "form-control";

        if (tipo === "number") {
            input.min = "0";
            input.step = "0.01";
        }

        return input;
    }

    function crearBotonEliminar() {
        const boton = document.createElement("button");

        boton.type = "button";
        boton.textContent = "Eliminar";
        boton.className = "btn-eliminar";

        boton.addEventListener("click", () => {
            const fila = boton.closest("tr");

            if (fila) {
                fila.remove();
                actualizarIndices();
                calcularTotales();
            }
        });

        return boton;
    }

    // CARGAR TODOS LOS MATERIALES ACTIVOS
    async function cargarMateriales() {
        const estado = document.getElementById("estadoMateriales");

        selectorMaterial.innerHTML =
            '<option value="">Cargando materiales...</option>';

        btnAgregarMaterial.disabled = true;

        try {
            const respuesta = await fetch(
                "crear.php?ajax=materiales",
                {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                }
            );

            if (!respuesta.ok) {
                throw new Error("Error al consultar los materiales.");
            }

            const datos = await respuesta.json();

            if (!Array.isArray(datos)) {
                throw new Error(datos.error || "Respuesta incorrecta.");
            }

            catalogoMateriales = datos;

            selectorMaterial.innerHTML =
                '<option value="">Seleccioná un material</option>';

            catalogoMateriales.forEach(material => {
                const opcion = document.createElement("option");

                opcion.value = material.id_material;
                opcion.textContent =
                    material.nombre_material +
                    (material.unidad_medida
                        ? " — " + material.unidad_medida
                        : "");

                selectorMaterial.appendChild(opcion);
            });

            if (catalogoMateriales.length === 0) {
                selectorMaterial.innerHTML =
                    '<option value="">No hay materiales activos</option>';

                if (estado) {
                    estado.textContent =
                        "No se encontraron materiales activos en el catálogo.";
                }
            } else {
                if (estado) {
                    estado.textContent =
                        catalogoMateriales.length +
                        " materiales disponibles.";
                }

                btnAgregarMaterial.disabled = false;
            }

        } catch (error) {
            console.error(error);

            selectorMaterial.innerHTML =
                '<option value="">No se pudieron cargar los materiales</option>';

            if (estado) {
                estado.textContent =
                    "Revisá la conexión o la ruta del archivo.";
            }
        }
    }

    // AGREGAR MATERIAL SELECCIONADO
    function agregarMaterial() {
        const idMaterial = selectorMaterial.value;

        if (!idMaterial) {
            alert("Seleccioná un material del catálogo.");
            selectorMaterial.focus();
            return;
        }

        const repetido = [...tbodyMateriales.querySelectorAll(
            'input[name$="[id_material]"]'
        )].some(input => input.value === idMaterial);

        if (repetido) {
            alert("Ese material ya está agregado al presupuesto.");
            selectorMaterial.value = "";
            return;
        }

        const material = catalogoMateriales.find(
            item => String(item.id_material) === String(idMaterial)
        );

        if (!material) {
            alert("No se encontró el material seleccionado.");
            return;
        }

        const fila = document.createElement("tr");
        const indice = contadorMateriales++;

        const id = document.createElement("input");
        id.type = "hidden";
        id.name = `materiales[${indice}][id_material]`;
        id.value = material.id_material;

        const nombre = document.createElement("strong");
        nombre.textContent = material.nombre_material;

        const unidad = document.createElement("span");
        unidad.textContent = material.unidad_medida || "unidad";

        const cantidad = crearInput(
            `materiales[${indice}][cantidad]`,
            "number",
            "1"
        );

        cantidad.min = "0.01";
        cantidad.classList.add("cantidad-material");

        const precio = crearInput(
            `materiales[${indice}][costo_unitario]`,
            "number",
            "0"
        );

        precio.min = "0";
        precio.classList.add("precio-material");

        const subtotal = document.createElement("strong");
        subtotal.className = "subtotal-material";
        subtotal.textContent = dinero(0);

        const celdaMaterial = document.createElement("td");
        celdaMaterial.append(id, nombre);

        fila.append(
            celdaMaterial,
            crearCelda(unidad),
            crearCelda(cantidad),
            crearCelda(precio),
            crearCelda(subtotal),
            crearCelda(crearBotonEliminar())
        );

        tbodyMateriales.appendChild(fila);

        selectorMaterial.value = "";

        calcularTotales();
    }

    // AGREGAR MANO DE OBRA O TRABAJO EXTRA
    function agregarTrabajo() {
        const indice = contadorTrabajos++;
        const fila = document.createElement("tr");

        const descripcion = crearInput(
            `trabajos[${indice}][descripcion]`,
            "text",
            "",
            "Ej.: Albañil, plomero, electricista"
        );

        const horas = crearInput(
            `trabajos[${indice}][cantidad_horas]`,
            "number",
            "1"
        );

        horas.min = "0.01";

        const costoHora = crearInput(
            `trabajos[${indice}][costo_hora]`,
            "number",
            "0"
        );

        costoHora.min = "0";

        const subtotal = document.createElement("strong");
        subtotal.className = "subtotal-trabajo";
        subtotal.textContent = dinero(0);

        fila.append(
            crearCelda(descripcion),
            crearCelda(horas),
            crearCelda(costoHora),
            crearCelda(subtotal),
            crearCelda(crearBotonEliminar())
        );

        tbodyTrabajos.appendChild(fila);

        calcularTotales();
    }

    // ACTUALIZAR ÍNDICES DESPUÉS DE ELIMINAR FILAS
    function actualizarIndices() {
        [...tbodyMateriales.querySelectorAll("tr")].forEach((fila, indice) => {
            fila.querySelector('input[name$="[id_material]"]').name =
                `materiales[${indice}][id_material]`;

            fila.querySelector(".cantidad-material").name =
                `materiales[${indice}][cantidad]`;

            fila.querySelector(".precio-material").name =
                `materiales[${indice}][costo_unitario]`;
        });

        [...tbodyTrabajos.querySelectorAll("tr")].forEach((fila, indice) => {
            const inputs = fila.querySelectorAll("input");

            inputs[0].name = `trabajos[${indice}][descripcion]`;
            inputs[1].name = `trabajos[${indice}][cantidad_horas]`;
            inputs[2].name = `trabajos[${indice}][costo_hora]`;
        });

        contadorMateriales = tbodyMateriales.querySelectorAll("tr").length;
        contadorTrabajos = tbodyTrabajos.querySelectorAll("tr").length;
    }

    // OBTENER PORCENTAJE
    function porcentaje(id) {
        const campo = document.getElementById(id);
        return campo ? numero(campo.value) : 0;
    }

    // ACTUALIZAR RESUMEN Y TOTALES
    function calcularTotales() {
        let totalMateriales = 0;
        let totalManoObra = 0;

        tbodyMateriales.querySelectorAll("tr").forEach(fila => {
            const cantidad = numero(
                fila.querySelector(".cantidad-material").value
            );

            const precio = numero(
                fila.querySelector(".precio-material").value
            );

            const subtotal = cantidad * precio;

            fila.querySelector(".subtotal-material").textContent =
                dinero(subtotal);

            totalMateriales += subtotal;
        });

        tbodyTrabajos.querySelectorAll("tr").forEach(fila => {
            const horas = numero(
                fila.querySelector('input[name$="[cantidad_horas]"]').value
            );

            const costoHora = numero(
                fila.querySelector('input[name$="[costo_hora]"]').value
            );

            const subtotal = horas * costoHora;

            fila.querySelector(".subtotal-trabajo").textContent =
                dinero(subtotal);

            totalManoObra += subtotal;
        });

        const costoDirecto = totalMateriales + totalManoObra;

        const gastosObra =
            costoDirecto * porcentaje("porc_gastos_obra") / 100;

        const gastosOficina =
            costoDirecto * porcentaje("porc_gastos_oficina") / 100;

        const imprevistos =
            costoDirecto * porcentaje("porc_imprevistos") / 100;

        const baseGanancia =
            costoDirecto + gastosObra + gastosOficina + imprevistos;

        const ganancia =
            baseGanancia * porcentaje("porc_ganancia") / 100;

        const totalFinal = baseGanancia + ganancia;

        document.getElementById("totalMateriales").textContent =
            dinero(totalMateriales);

        document.getElementById("totalManoObra").textContent =
            dinero(totalManoObra);

        document.getElementById("resumenMateriales").textContent =
            dinero(totalMateriales);

        document.getElementById("resumenManoObra").textContent =
            dinero(totalManoObra);

        document.getElementById("costoBase").textContent =
            dinero(costoDirecto);

        document.getElementById("totalGastosObra").textContent =
            dinero(gastosObra);

        document.getElementById("totalGastosOficina").textContent =
            dinero(gastosOficina);

        document.getElementById("totalImprevistos").textContent =
            dinero(imprevistos);

        document.getElementById("totalGanancia").textContent =
            dinero(ganancia);

        document.getElementById("totalFinal").textContent =
            dinero(totalFinal);
    }

    // EVENTOS
    btnAgregarMaterial.addEventListener("click", agregarMaterial);
    btnAgregarTrabajo.addEventListener("click", agregarTrabajo);

    tbodyMateriales.addEventListener("input", calcularTotales);
    tbodyTrabajos.addEventListener("input", calcularTotales);

    document.querySelectorAll(".porcentaje").forEach(campo => {
        campo.addEventListener("input", calcularTotales);
    });

    // VALIDAR FORMULARIO ANTES DE GUARDAR
    formulario.addEventListener("submit", evento => {
        const obra = document.getElementById("id_obra");

        if (!obra.value) {
            evento.preventDefault();
            alert("Seleccioná una obra.");
            obra.focus();
            return;
        }

        const filasMateriales = [...tbodyMateriales.querySelectorAll("tr")];
        const filasTrabajos = [...tbodyTrabajos.querySelectorAll("tr")];

        if (filasMateriales.length === 0 && filasTrabajos.length === 0) {
            evento.preventDefault();
            alert("Agregá al menos un material o un trabajo.");
            return;
        }

        for (const fila of filasMateriales) {
            const cantidad = fila.querySelector(".cantidad-material");
            const precio = fila.querySelector(".precio-material");

            if (
                cantidad.value === "" ||
                Number(cantidad.value) <= 0 ||
                precio.value === "" ||
                Number(precio.value) < 0
            ) {
                evento.preventDefault();
                alert("Completá correctamente la cantidad y el precio de los materiales.");
                return;
            }
        }

        for (const fila of filasTrabajos) {
            const descripcion = fila.querySelector('input[type="text"]');
            const horas = fila.querySelector('input[name$="[cantidad_horas]"]');
            const costoHora = fila.querySelector('input[name$="[costo_hora]"]');

            if (
                !descripcion.value.trim() ||
                horas.value === "" ||
                Number(horas.value) <= 0 ||
                costoHora.value === "" ||
                Number(costoHora.value) < 0
            ) {
                evento.preventDefault();
                alert("Completá correctamente la descripción, las horas y el costo de cada trabajo.");
                return;
            }
        }

        const camposPorcentajes = [
            "porc_gastos_obra",
            "porc_gastos_oficina",
            "porc_imprevistos",
            "porc_ganancia"
        ];

        for (const id of camposPorcentajes) {
            const campo = document.getElementById(id);
            const valor = Number(campo.value);

            if (
                campo.value === "" ||
                !Number.isFinite(valor) ||
                valor < 0 ||
                valor > 100
            ) {
                evento.preventDefault();
                alert("Los porcentajes deben estar entre 0 y 100.");
                campo.focus();
                return;
            }
        }
    });

    cargarMateriales();
    calcularTotales();
});