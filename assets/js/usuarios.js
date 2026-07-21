const buscar = document.getElementById("buscarUsuario");
const rol = document.getElementById("filtroRol");
const estado = document.getElementById("filtroEstado");
const filas = document.querySelectorAll("#tablaUsuarios tbody tr");

function filtrar() {

    const texto = buscar.value.trim().toLowerCase();
    const rolSeleccionado = rol.value.toLowerCase();
    const estadoSeleccionado = estado.value.toLowerCase();

    filas.forEach(fila => {

        const nombre = fila.cells[0].innerText.trim().toLowerCase();
        const documento = fila.cells[1].innerText.trim().toLowerCase();
        const telefono = fila.cells[2].innerText.trim().toLowerCase();
        const correo = fila.cells[3].innerText.trim().toLowerCase();
        const rolTexto = fila.cells[4].innerText.trim().toLowerCase();
        const estadoTexto = fila.cells[5].innerText.trim().toLowerCase();

        const coincideTexto =
            nombre.includes(texto) ||
            documento.includes(texto) ||
            telefono.includes(texto) ||
            correo.includes(texto) ||
            rolTexto.includes(texto);

        const coincideRol =
            rolSeleccionado === "" ||
            rolTexto === rolSeleccionado;

        const coincideEstado =
            estadoSeleccionado === "" ||
            estadoTexto === estadoSeleccionado;

        fila.style.display =
            coincideTexto && coincideRol && coincideEstado
                ? ""
                : "none";

    });

}

if (buscar) {
    buscar.addEventListener("keyup", filtrar);
}

if (rol) {
    rol.addEventListener("change", filtrar);
}

if (estado) {
    estado.addEventListener("change", filtrar);
}