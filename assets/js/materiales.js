    document.getElementById("buscarMaterial")
        .addEventListener("keyup", function() {

            let filtro = this.value.toLowerCase();

            document.querySelectorAll("#tablaMateriales tbody tr")
                .forEach(function(fila) {
                    fila.style.display =
                        fila.textContent.toLowerCase().includes(filtro) ?
                        "" :
                        "none";
                });
        });

const buscarMaterial = document.getElementById("buscarMaterial");
const filtroEstado = document.getElementById("filtroEstado");
const filtroStock = document.getElementById("filtroStock");


function filtrarMateriales(){


    let texto = buscarMaterial.value.toLowerCase();

    let estado = filtroEstado.value;

    let stock = filtroStock.value;



    document.querySelectorAll("#tablaMateriales tbody tr")
        .forEach(function(fila){


            let contenido = fila.textContent.toLowerCase();


            let estadoFila = fila.dataset.estado;

            let stockFila = fila.dataset.stock;



            let coincideTexto =
                contenido.includes(texto);



            let coincideEstado =
                estado == "" || estado == estadoFila;



            let coincideStock =
                stock == "" || stock == stockFila;



            if(
                coincideTexto &&
                coincideEstado &&
                coincideStock
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
    filtrarMateriales
);



filtroEstado.addEventListener(
    "change",
    filtrarMateriales
);



filtroStock.addEventListener(
    "change",
    filtrarMateriales
);