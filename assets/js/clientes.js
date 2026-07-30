
document.addEventListener("DOMContentLoaded", () => {

    const buscar = document.getElementById("buscarCliente");
    const filtroEstado = document.getElementById("filtroEstado");

    function filtrarClientes()
    {
        const texto = buscar.value.toLowerCase();
        const estado = filtroEstado.value.toLowerCase();

        document.querySelectorAll("#tablaClientes tbody tr").forEach(fila => {

            const contenido = fila.textContent.toLowerCase();

            const badge = fila.querySelector(".badge");
            const estadoFila = badge
                ? badge.textContent.trim().toLowerCase()
                : "";

            const coincideTexto =
                contenido.includes(texto);

            const coincideEstado =
                estado === "" ||
                estadoFila === estado;

            fila.style.display =
                coincideTexto && coincideEstado
                    ? ""
                    : "none";
        });
    }

    buscar.addEventListener("keyup", filtrarClientes);
    filtroEstado.addEventListener("change", filtrarClientes);

});
