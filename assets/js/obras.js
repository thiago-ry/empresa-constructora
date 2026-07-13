const buscar = document.getElementById("buscarObra");
const estado = document.getElementById("filtroEstado");
const filas = document.querySelectorAll("#tablaObras tbody tr");

function filtrar() {

    const texto = buscar.value.toLowerCase().trim();
    const estadoSeleccionado = estado.value.toLowerCase();

    filas.forEach(fila => {

        const nombre = fila.cells[0].textContent.toLowerCase();
        const cliente = fila.cells[1].textContent.toLowerCase();
        const direccion = fila.cells[2].textContent.toLowerCase();
        const estadoTexto = fila.cells[6].textContent.trim().toLowerCase();
        const estadoSeleccionado = estado.value.trim().toLowerCase();

        const coincideTexto =
            nombre.includes(texto) ||
            cliente.includes(texto) ||
            direccion.includes(texto);

        const coincideEstado =
            estadoSeleccionado === "" ||
            estadoTexto === estadoSeleccionado;

        fila.style.display = (coincideTexto && coincideEstado)
            ? ""
            : "none";

    });

}

buscar.addEventListener("input", filtrar);
estado.addEventListener("change", filtrar);