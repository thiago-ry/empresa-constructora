
document.addEventListener("DOMContentLoaded", function () {

    const buscar = document.getElementById("buscarObra");
    const estado = document.getElementById("filtroEstado");
    const tabla = document.getElementById("tablaObras");

    if (!buscar || !estado || !tabla) {
        console.error("No se encontraron los elementos del filtro de obras.");
        return;
    }

    const filas = tabla.querySelectorAll("tbody tr");

    function normalizar(texto) {
        return texto
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    function filtrar() {

        const texto = normalizar(buscar.value);
        const estadoSeleccionado = normalizar(estado.value);

        filas.forEach(function (fila) {

            const celdas = fila.cells;

            // Evitar procesar filas sin las columnas esperadas
            if (celdas.length < 8) {
                return;
            }

            const nombre = normalizar(celdas[0].textContent);
            const cliente = normalizar(celdas[1].textContent);
            const jefeObra = normalizar(celdas[2].textContent);
            const capataz = normalizar(celdas[3].textContent);
            const direccion = normalizar(celdas[4].textContent);
            const fechaInicio = normalizar(celdas[5].textContent);
            const fechaFin = normalizar(celdas[6].textContent);
            const estadoTexto = normalizar(celdas[7].textContent);

            // Búsqueda general
            const coincideTexto =
                nombre.includes(texto) ||
                cliente.includes(texto) ||
                jefeObra.includes(texto) ||
                capataz.includes(texto) ||
                direccion.includes(texto) ||
                fechaInicio.includes(texto) ||
                fechaFin.includes(texto);

            // Filtro por estado
            const coincideEstado =
                estadoSeleccionado === "" ||
                estadoTexto === estadoSeleccionado;

            // Mostrar u ocultar fila
            fila.style.display =
                (coincideTexto && coincideEstado) ? "" : "none";

        });

    }

    // Eventos
    buscar.addEventListener("input", filtrar);
    estado.addEventListener("change", filtrar);

});
