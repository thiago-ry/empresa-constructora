    document.getElementById("buscarHerramienta")
        .addEventListener("keyup", function() {
            let filtro = this.value.toLowerCase();

            document.querySelectorAll("#tablaHerramientas tbody tr")
                .forEach(function(fila) {
                    fila.style.display =
                        fila.textContent.toLowerCase().includes(filtro) ?
                        "" :
                        "none";
                });
        });

const buscarMaterial = document.getElementById("buscarHerramienta");
const filtroEstado = document.getElementById("filtroEstado");

function filtrarMateriales(){

    let texto = buscarHerramienta.value.toLowerCase();
    let estado = filtroEstado.value;

    document.querySelectorAll("#tablaHerramientas tbody tr")
        .forEach(function(fila){

            let contenido = fila.textContent.toLowerCase();
            let estadoFila = fila.dataset.estado;
            let coincideTexto =
                contenido.includes(texto);
            let coincideEstado =
                estado == "" || estado == estadoFila;

            if(
                coincideTexto &&
                coincideEstado
            ){
                fila.style.display = "";
            }
            else{
                fila.style.display = "none";
            }
        });
}

buscarMaterial.addEventListener(
    "keyup",
    filtrarHerramientas
);

filtroEstado.addEventListener(
    "change",
    filtrarHerramientas
);
