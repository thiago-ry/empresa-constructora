document.addEventListener("DOMContentLoaded", function () {


const buscador = document.querySelector(
    'input[name="busqueda"]'
);

const filtroEstado = document.querySelector(
    'select[name="estado"]'
);

const filtroCargo = document.querySelector(
    'select[name="id_cargo"]'
);

const tabla = document.getElementById(
    "tablaEmpleados"
);

if (!tabla) {
    return;
}


const filas = tabla.querySelectorAll(
    "tbody tr"
);


function normalizarTexto(texto) {

    return texto
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();

}


function filtrarEmpleados() {

    const textoBusqueda =
        normalizarTexto(
            buscador ? buscador.value : ""
        );


    const estadoSeleccionado =
        filtroEstado
            ? filtroEstado.value
            : "";


    const cargoSeleccionado =
        filtroCargo
            ? filtroCargo.value
            : "";


    let encontrados = 0;


    filas.forEach(function (fila) {


        const celdas =
            fila.querySelectorAll("td");


        if (celdas.length < 7) {
            return;
        }


        const textoFila =
            normalizarTexto(
                fila.textContent
            );


        const coincideBusqueda =
            textoBusqueda === "" ||
            textoFila.includes(
                textoBusqueda
            );


        const textoEstado =
            normalizarTexto(
                celdas[5].textContent
            );


        let coincideEstado = true;


        if (
            estadoSeleccionado === "1"
        ) {

            coincideEstado =
                textoEstado === "activo";

        }


        if (
            estadoSeleccionado === "0"
        ) {

            coincideEstado =
                textoEstado === "inactivo";

        }



        const textoCargo =
            normalizarTexto(
                celdas[3].textContent
            );


        let coincideCargo = true;


        if (
            cargoSeleccionado !== "" &&
            filtroCargo
        ) {

            const opcionSeleccionada =
                filtroCargo.options[
                    filtroCargo.selectedIndex
                ];


            const nombreCargo =
                normalizarTexto(
                    opcionSeleccionada.textContent
                );


            coincideCargo =
                textoCargo.includes(
                    nombreCargo
                );

        }


        const mostrar =
            coincideBusqueda &&
            coincideEstado &&
            coincideCargo;


        if (mostrar) {

            fila.style.display = "";

            encontrados++;

        } else {

            fila.style.display = "none";

        }

    });


    actualizarContador(
        encontrados
    );


    mostrarMensajeSinResultados(
        encontrados
    );

}


function actualizarContador(cantidad) {

    const contador =
        document.querySelector(
            ".contador-resultados"
        );


    if (contador) {

        contador.textContent =
            "Mostrando " +
            cantidad +
            " empleado(s).";

    }

}



function mostrarMensajeSinResultados(
    cantidad
) {

    let mensaje =
        document.getElementById(
            "sinResultadosJS"
        );


    if (cantidad === 0) {

        if (!mensaje) {

            mensaje =
                document.createElement("div");

            mensaje.id =
                "sinResultadosJS";

            mensaje.style.textAlign =
                "center";

            mensaje.style.padding =
                "25px";

            mensaje.style.fontWeight =
                "500";

            mensaje.innerHTML =
                '<i class="fa-solid fa-filter"></i>' +
                '<br>' +
                'No se encontraron empleados con los filtros seleccionados.';


            tabla.parentNode.insertBefore(
                mensaje,
                tabla
            );

        }

    } else {

        if (mensaje) {

            mensaje.remove();

        }

    }

}


if (buscador) {

    buscador.addEventListener(
        "input",
        filtrarEmpleados
    );

}


if (filtroEstado) {

    filtroEstado.addEventListener(
        "change",
        filtrarEmpleados
    );

}


if (filtroCargo) {

    filtroCargo.addEventListener(
        "change",
        filtrarEmpleados
    );

}


filtrarEmpleados();


});
