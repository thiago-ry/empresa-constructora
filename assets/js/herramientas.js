
document.addEventListener("DOMContentLoaded", function () {

    const buscar = document.getElementById("buscarHerramienta");
    const tipo = document.getElementById("filtroTipo");
    const cantidad = document.getElementById("filtroCantidad");
    const limpiar = document.getElementById("limpiarFiltros");
    const tabla = document.getElementById("tablaHerramientas");

    // Comprobar elementos
    if (!buscar || !tipo || !cantidad || !limpiar || !tabla) {
        console.error("Error: faltan elementos HTML para los filtros.");
        return;
    }

    function filtrarHerramientas() {

        const texto = buscar.value.toLowerCase().trim();
        const tipoSeleccionado = tipo.value.toLowerCase().trim();
        const cantidadSeleccionada = cantidad.value;

        const filas = tabla.querySelectorAll("tbody tr");

        filas.forEach(function (fila) {

            const contenido = fila.textContent.toLowerCase();

            const tipoFila = fila.cells[2].textContent
                .toLowerCase().trim();

            const cantidadTexto = fila.cells[3].textContent
                .replace(/\s+/g, "")
                .trim();

            const cantidadFila = parseInt(cantidadTexto, 10) || 0;

            const coincideTexto = contenido.includes(texto);

            const coincideTipo =
                tipoSeleccionado === "" ||
                tipoFila === tipoSeleccionado;

            let coincideCantidad = true;

            switch (cantidadSeleccionada) {

                case "bajo":
                    coincideCantidad = cantidadFila <= 2;
                    break;

                case "disponible":
                    coincideCantidad = cantidadFila > 2;
                    break;

            }

            fila.style.display =
                coincideTexto && coincideTipo && coincideCantidad
                    ? ""
                    : "none";

        });

    }

    // Eventos de filtrado automático
    buscar.addEventListener("input", filtrarHerramientas);
    tipo.addEventListener("change", filtrarHerramientas);
    cantidad.addEventListener("change", filtrarHerramientas);

    // Restablecer filtros
    limpiar.addEventListener("click", function () {

        buscar.value = "";
        tipo.value = "";
        cantidad.value = "";

        filtrarHerramientas();

    });

});
