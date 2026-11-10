/*
 * Filtros y exportación de los reportes.
 *
 * Uso en las vistas:
 *   <input  data-buscar="tablaObras">                 buscador de texto
 *   <select data-filtro="tablaObras" data-campo="estado">  filtra por <tr data-estado="...">
 *   <span   data-contador="tablaObras"></span>         muestra cuántas filas se ven
 *   <a data-exportar data-tabla="tablaObras" href="...">  se le agregan buscar y estado
 */
(function () {

    function filtrarTabla(idTabla) {
        const tabla = document.getElementById(idTabla);
        if (!tabla) return;

        const buscador = document.querySelector('[data-buscar="' + idTabla + '"]');
        const texto = buscador ? buscador.value.toLowerCase().trim() : "";
        const filtros = document.querySelectorAll('[data-filtro="' + idTabla + '"]');

        let visibles = 0;

        tabla.querySelectorAll("tbody tr").forEach(function (fila) {
            if (fila.classList.contains("fila-vacia")) return;

            let mostrar = texto === "" || fila.textContent.toLowerCase().includes(texto);

            filtros.forEach(function (filtro) {
                if (!mostrar || filtro.value === "") return;
                const valorFila = fila.dataset[filtro.dataset.campo] || "";
                // data-modo="lista": la fila puede tener varios valores separados por "|"
                const coincide = filtro.dataset.modo === "lista"
                    ? valorFila.split("|").includes(filtro.value)
                    : valorFila === filtro.value;
                if (!coincide) mostrar = false;
            });

            fila.style.display = mostrar ? "" : "none";
            if (mostrar) visibles++;
        });

        document.querySelectorAll('[data-contador="' + idTabla + '"]').forEach(function (el) {
            el.textContent = visibles;
        });

        actualizarExportacion(idTabla, texto);
    }

    function actualizarExportacion(idTabla, texto) {
        const estadoSel = document.querySelector('[data-filtro="' + idTabla + '"][data-exportar-estado]');

        document.querySelectorAll('a[data-exportar][data-tabla="' + idTabla + '"]').forEach(function (enlace) {
            const url = new URL(enlace.getAttribute("href"), window.location.origin);
            if (texto) {
                url.searchParams.set("buscar", texto);
            } else {
                url.searchParams.delete("buscar");
            }
            if (estadoSel && estadoSel.value) {
                url.searchParams.set("estado", estadoSel.value);
            } else {
                url.searchParams.delete("estado");
            }
            enlace.setAttribute("href", url.pathname + url.search);
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        const tablas = new Set();

        document.querySelectorAll("[data-buscar]").forEach(function (el) {
            tablas.add(el.dataset.buscar);
            el.addEventListener("input", function () { filtrarTabla(el.dataset.buscar); });
        });

        document.querySelectorAll("[data-filtro]").forEach(function (el) {
            tablas.add(el.dataset.filtro);
            el.addEventListener("change", function () { filtrarTabla(el.dataset.filtro); });
        });

        tablas.forEach(filtrarTabla);

        // Pestañas (reporte de asignaciones)
        document.querySelectorAll("[data-pestana]").forEach(function (boton) {
            boton.addEventListener("click", function () {
                const grupo = boton.closest("[data-pestanas]");
                grupo.querySelectorAll("[data-pestana]").forEach(function (b) { b.classList.remove("activa"); });
                boton.classList.add("activa");
                document.querySelectorAll(".panel-pestana").forEach(function (panel) {
                    panel.hidden = panel.id !== boton.dataset.pestana;
                });
            });
        });
    });

})();