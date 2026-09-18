<?php

$materiales = $materiales ?? [];
$obra = $obra ?? [];

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Nueva solicitud de materiales
            </h1>

            <p>
                Obra:
                <strong>
                    <?= htmlspecialchars($obra["nombre_obra"] ?? "Sin nombre") ?>
                </strong>
            </p>

        </div>

        <a
            href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=listar&id_obra=<?= htmlspecialchars($obra["id_obra"] ?? 0) ?>"
            class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver

        </a>

    </div>


    <div class="form-card">


        <!-- ==================================================
             BUSCADOR Y FILTROS
        ================================================== -->

        <div class="form-group">

            <label for="buscarMaterial">

                <i class="fa-solid fa-magnifying-glass"></i>

                Buscar material

            </label>


            <div
                style="
                    display:flex;
                    gap:10px;
                    align-items:center;
                ">

                <div style="position:relative;flex:1;">

                    <input
                        type="text"
                        id="buscarMaterial"
                        class="input"
                        placeholder="Buscar por nombre de material..."
                        autocomplete="off">

                    <span
                        id="indicadorBusqueda"
                        style="
                            position:absolute;
                            right:15px;
                            top:50%;
                            transform:translateY(-50%);
                            display:none;
                        ">

                        <i class="fa-solid fa-spinner fa-spin"></i>

                    </span>

                </div>


                <button
                    type="button"
                    id="btnLimpiarBusqueda"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-xmark"></i>

                    Limpiar

                </button>

            </div>


            <small
                style="
                    display:block;
                    margin-top:8px;
                ">

                Busque el material que necesita y selecciónelo de la lista.

            </small>

        </div>


        <!-- ==================================================
             FILTRO
        ================================================== -->

        <div
            style="
                display:flex;
                gap:15px;
                flex-wrap:wrap;
                margin-top:20px;
            ">

            <div
                class="form-group"
                style="
                    margin:0;
                    min-width:220px;
                    flex:1;
                ">

                <label for="filtroUnidad">

                    <i class="fa-solid fa-filter"></i>

                    Filtrar por unidad

                </label>

                <select
                    id="filtroUnidad"
                    class="input">

                    <option value="">
                        Todas las unidades
                    </option>

                    <?php

                    $unidades = [];

                    foreach ($materiales as $material) {

                        $unidad = trim($material["unidad_medida"] ?? "");

                        if ($unidad !== "") {
                            $unidades[$unidad] = $unidad;
                        }
                    }

                    ksort($unidades);

                    foreach ($unidades as $unidad):

                    ?>

                        <option value="<?= htmlspecialchars($unidad) ?>">
                            <?= htmlspecialchars($unidad) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div
                style="
                    display:flex;
                    align-items:end;
                    padding-bottom:1px;
                ">

                <span
                    id="contadorMateriales"
                    style="
                        padding:10px 15px;
                        border-radius:8px;
                        background:rgba(255,255,255,.05);
                    ">

                    <?= count($materiales) ?> materiales disponibles

                </span>

            </div>

        </div>


        <!-- ==================================================
             TABLA DE MATERIALES
        ================================================== -->

        <div
            class="table-container"
            style="margin-top:25px;">

            <div class="table-header">

                <h2>

                    <i class="fa-solid fa-boxes-stacked"></i>

                    Materiales disponibles

                </h2>

                <span id="contadorResultados">
                    <?= count($materiales) ?> resultados
                </span>

            </div>


            <div style="overflow-x:auto;">

                <table class="table">

                    <thead>

                        <tr>

                            <th>
                                Material
                            </th>

                            <th>
                                Unidad
                            </th>

                            <th>
                                Stock disponible
                            </th>

                            <th class="no-print">
                                Acción
                            </th>

                        </tr>

                    </thead>


                    <tbody id="tablaMateriales">

                        <?php if (empty($materiales)): ?>

                            <tr>

                                <td
                                    colspan="4"
                                    style="
                                        text-align:center;
                                        padding:40px;
                                    ">

                                    <i class="fa-solid fa-box-open fa-2x"></i>

                                    <br><br>

                                    No hay materiales disponibles.

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($materiales as $material): ?>

                                <?php

                                $idMaterial = $material["id_material"] ?? 0;
                                $nombreMaterial = $material["nombre_material"] ?? "";
                                $stock = $material["stock"] ?? 0;
                                $unidad = $material["unidad_medida"] ?? "";

                                ?>

                                <tr
                                    class="material-item"
                                    data-id="<?= htmlspecialchars($idMaterial) ?>"
                                    data-nombre="<?= htmlspecialchars(mb_strtolower($nombreMaterial)) ?>"
                                    data-unidad="<?= htmlspecialchars(mb_strtolower($unidad)) ?>">

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($nombreMaterial) ?>
                                        </strong>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($unidad) ?>

                                    </td>


                                    <td>

                                        <span
                                            class="stock-material"
                                            data-stock="<?= htmlspecialchars($stock) ?>">

                                            <?= htmlspecialchars($stock) ?>

                                        </span>

                                        <?= htmlspecialchars($unidad) ?>

                                    </td>


                                    <td class="no-print">

                                        <button
                                            type="button"
                                            class="btn btn-primary btn-seleccionar-material"
                                            data-id="<?= htmlspecialchars($idMaterial) ?>"
                                            data-nombre="<?= htmlspecialchars($nombreMaterial) ?>"
                                            data-stock="<?= htmlspecialchars($stock) ?>"
                                            data-unidad="<?= htmlspecialchars($unidad) ?>">

                                            <i class="fa-solid fa-plus"></i>

                                            Seleccionar

                                        </button>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>


            <!-- SIN RESULTADOS -->

            <div
                id="sinResultados"
                style="
                    display:none;
                    text-align:center;
                    padding:35px;
                ">

                <i class="fa-solid fa-magnifying-glass fa-2x"></i>

                <br><br>

                No se encontraron materiales con esos filtros.

            </div>

        </div>


        <!-- ==================================================
             MATERIALES SELECCIONADOS
        ================================================== -->

        <div
            id="materialesSeleccionados"
            style="margin-top:30px;">

            <div class="table-header">

                <h2>

                    <i class="fa-solid fa-cart-shopping"></i>

                    Materiales a solicitar

                </h2>

                <span id="contadorSeleccionados">
                    0 materiales
                </span>

            </div>


            <div
                id="listaSeleccionados"
                style="
                    display:flex;
                    flex-direction:column;
                    gap:12px;
                    margin-top:15px;
                ">

                <div
                    id="sinSeleccionados"
                    style="
                        text-align:center;
                        padding:30px;
                        border-radius:10px;
                        border:1px dashed rgba(255,255,255,.15);
                    ">

                    <i class="fa-solid fa-box-open fa-2x"></i>

                    <br><br>

                    Todavía no seleccionó ningún material.

                    <br>

                    <small>
                        Seleccione materiales de la tabla superior.
                    </small>

                </div>

            </div>

        </div>


        <!-- ==================================================
             FORMULARIO
        ================================================== -->

        <form
            method="POST"
            action="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=guardar"
            id="formSolicitud"
            style="margin-top:30px;">

            <input
                type="hidden"
                name="id_obra"
                value="<?= htmlspecialchars($obra["id_obra"] ?? 0) ?>">


            <div
                id="inputsMateriales">
            </div>


            <!-- ==================================================
                 BOTONES
            ================================================== -->

            <div
                class="form-actions"
                style="
                    display:flex;
                    justify-content:flex-end;
                    gap:10px;
                    margin-top:25px;
                ">

                <a
                    href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=listar&id_obra=<?= htmlspecialchars($obra["id_obra"] ?? 0) ?>"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-xmark"></i>

                    Cancelar

                </a>


                <button
                    type="button"
                    id="btnLimpiarTodo"
                    class="btn btn-warning">

                    <i class="fa-solid fa-rotate-left"></i>

                    Limpiar

                </button>


                <button
                    type="submit"
                    id="btnEnviar"
                    class="btn btn-primary"
                    disabled>

                    <i class="fa-solid fa-paper-plane"></i>

                    Enviar solicitud

                </button>

            </div>

        </form>

    </div>

</main>


<script>
    const materiales = <?= json_encode($materiales, JSON_UNESCAPED_UNICODE) ?>;

    const materialesSeleccionados = new Map();

    const buscarMaterial = document.getElementById("buscarMaterial");
    const filtroUnidad = document.getElementById("filtroUnidad");
    const tablaMateriales = document.getElementById("tablaMateriales");
    const contadorResultados = document.getElementById("contadorResultados");
    const contadorSeleccionados = document.getElementById("contadorSeleccionados");
    const listaSeleccionados = document.getElementById("listaSeleccionados");
    const sinSeleccionados = document.getElementById("sinSeleccionados");
    const inputsMateriales = document.getElementById("inputsMateriales");
    const btnEnviar = document.getElementById("btnEnviar");
    const btnLimpiarTodo = document.getElementById("btnLimpiarTodo");
    const btnLimpiarBusqueda = document.getElementById("btnLimpiarBusqueda");
    const sinResultados = document.getElementById("sinResultados");


    /* ==========================================================
       BUSCAR Y FILTRAR
    ========================================================== */

    function filtrarMateriales() {

        const texto = buscarMaterial.value
            .trim()
            .toLowerCase();

        const unidad = filtroUnidad.value
            .trim()
            .toLowerCase();

        const filas = tablaMateriales.querySelectorAll(".material-item");

        let encontrados = 0;


        filas.forEach(function(fila) {

            const nombre = fila.dataset.nombre || "";
            const unidadMaterial = fila.dataset.unidad || "";

            const coincideNombre =
                nombre.includes(texto);

            const coincideUnidad =
                unidad === "" ||
                unidadMaterial === unidad;


            if (coincideNombre && coincideUnidad) {

                fila.style.display = "";
                encontrados++;

            } else {

                fila.style.display = "none";

            }

        });


        contadorResultados.textContent =
            encontrados + (encontrados === 1 ? " resultado" : " resultados");


        if (encontrados === 0) {

            sinResultados.style.display = "block";

        } else {

            sinResultados.style.display = "none";

        }

    }


    /* ==========================================================
       SELECCIONAR MATERIAL
    ========================================================== */

    document.querySelectorAll(".btn-seleccionar-material")
        .forEach(function(boton) {

            boton.addEventListener("click", function() {

                const id = String(this.dataset.id);

                const nombre = this.dataset.nombre;

                const stock = parseFloat(this.dataset.stock) || 0;

                const unidad = this.dataset.unidad;


                if (materialesSeleccionados.has(id)) {

                    alert("Este material ya fue seleccionado.");

                    return;

                }


                materialesSeleccionados.set(id, {

                    id: id,
                    nombre: nombre,
                    stock: stock,
                    unidad: unidad,
                    cantidad: ""

                });


                actualizarSeleccionados();

                actualizarTabla();

            });

        });


    /* ==========================================================
       MOSTRAR SELECCIONADOS
    ========================================================== */

    function actualizarSeleccionados() {

        listaSeleccionados.innerHTML = "";

        inputsMateriales.innerHTML = "";


        if (materialesSeleccionados.size === 0) {

            listaSeleccionados.innerHTML = `
            <div
                id="sinSeleccionados"
                style="
                    text-align:center;
                    padding:30px;
                    border-radius:10px;
                    border:1px dashed rgba(255,255,255,.15);
                ">

                <i class="fa-solid fa-box-open fa-2x"></i>

                <br><br>

                Todavía no seleccionó ningún material.

                <br>

                <small>
                    Seleccione materiales de la tabla superior.
                </small>

            </div>
        `;

            contadorSeleccionados.textContent = "0 materiales";

            btnEnviar.disabled = true;

            return;

        }


        contadorSeleccionados.textContent =
            materialesSeleccionados.size +
            (
                materialesSeleccionados.size === 1 ?
                " material" :
                " materiales"
            );


        materialesSeleccionados.forEach(function(material) {

            const fila = document.createElement("div");

            fila.className = "material-seleccionado";

            fila.style.cssText = `
            display:flex;
            align-items:center;
            gap:15px;
            padding:18px;
            border-radius:10px;
            border:1px solid rgba(255,255,255,.1);
            flex-wrap:wrap;
        `;


            fila.innerHTML = `

            <div style="flex:1;min-width:200px;">

                <strong>
                    ${escapeHtml(material.nombre)}
                </strong>

                <div style="margin-top:5px;">

                    <small>
                        Stock disponible:
                        <strong>
                            ${material.stock}
                        </strong>
                        ${escapeHtml(material.unidad)}
                    </small>

                </div>

            </div>


            <div
                class="form-group"
                style="
                    margin:0;
                    min-width:180px;
                ">

                <label>
                    Cantidad a solicitar
                </label>

                <input
                    type="number"
                    class="input cantidad-material"
                    data-id="${material.id}"
                    min="0.01"
                    max="${material.stock}"
                    step="0.01"
                    value="${material.cantidad}"
                    placeholder="Ej: 10"
                    required>

            </div>


            <button
                type="button"
                class="btn btn-danger btn-quitar-material"
                data-id="${material.id}">

                <i class="fa-solid fa-trash"></i>

                Quitar

            </button>

        `;


            listaSeleccionados.appendChild(fila);


            /* INPUTS OCULTOS */

            const inputMaterial = document.createElement("input");

            inputMaterial.type = "hidden";
            inputMaterial.name = "material[]";
            inputMaterial.value = material.id;

            inputsMateriales.appendChild(inputMaterial);


            const inputCantidad = document.createElement("input");

            inputCantidad.type = "hidden";
            inputCantidad.name = "cantidad[]";
            inputCantidad.dataset.id = material.id;
            inputCantidad.className = "input-cantidad-hidden";
            inputCantidad.value = material.cantidad;

            inputsMateriales.appendChild(inputCantidad);

        });


        /* ======================================================
           EVENTOS CANTIDAD
        ====================================================== */

        document.querySelectorAll(".cantidad-material")
            .forEach(function(input) {

                input.addEventListener("input", function() {

                    const id = String(this.dataset.id);

                    const cantidad =
                        parseFloat(this.value) || 0;

                    const material =
                        materialesSeleccionados.get(id);

                    if (!material) {
                        return;
                    }


                    material.cantidad = this.value;


                    const hidden =
                        document.querySelector(
                            `.input-cantidad-hidden[data-id="${id}"]`
                        );


                    if (hidden) {

                        hidden.value = this.value;

                    }


                    validarFormulario();

                });

            });


        /* ======================================================
           EVENTOS QUITAR
        ====================================================== */

        document.querySelectorAll(".btn-quitar-material")
            .forEach(function(boton) {

                boton.addEventListener("click", function() {

                    const id = String(this.dataset.id);

                    materialesSeleccionados.delete(id);

                    actualizarSeleccionados();

                    actualizarTabla();

                });

            });


        validarFormulario();

    }


    /* ==========================================================
       ACTUALIZAR TABLA
    ========================================================== */

    function actualizarTabla() {

        document
            .querySelectorAll(".btn-seleccionar-material")
            .forEach(function(boton) {

                const id = String(boton.dataset.id);

                if (materialesSeleccionados.has(id)) {

                    boton.disabled = true;

                    boton.innerHTML = `
                    <i class="fa-solid fa-check"></i>
                    Seleccionado
                `;

                } else {

                    boton.disabled = false;

                    boton.innerHTML = `
                    <i class="fa-solid fa-plus"></i>
                    Seleccionar
                `;

                }

            });

    }


    /* ==========================================================
       VALIDAR FORMULARIO
    ========================================================== */

    function validarFormulario() {

        if (materialesSeleccionados.size === 0) {

            btnEnviar.disabled = true;

            return;

        }


        let valido = true;


        materialesSeleccionados.forEach(function(material) {

            const cantidad =
                parseFloat(material.cantidad);


            if (
                !cantidad ||
                cantidad <= 0 ||
                cantidad > material.stock
            ) {

                valido = false;

            }

        });


        btnEnviar.disabled = !valido;

    }


    /* ==========================================================
       LIMPIAR TODO
    ========================================================== */

    btnLimpiarTodo.addEventListener("click", function() {

        if (materialesSeleccionados.size > 0) {

            if (
                !confirm(
                    "¿Está seguro de quitar todos los materiales seleccionados?"
                )
            ) {

                return;

            }

        }


        materialesSeleccionados.clear();

        actualizarSeleccionados();

        actualizarTabla();

    });


    /* ==========================================================
       LIMPIAR BUSCADOR
    ========================================================== */

    btnLimpiarBusqueda.addEventListener("click", function() {

        buscarMaterial.value = "";

        filtroUnidad.value = "";

        filtrarMateriales();

    });


    /* ==========================================================
       EVENTOS DE FILTRO
    ========================================================== */

    buscarMaterial.addEventListener(
        "input",
        filtrarMateriales
    );

    filtroUnidad.addEventListener(
        "change",
        filtrarMateriales
    );


    /* ==========================================================
       EVITAR CANTIDADES MAYORES AL STOCK
    ========================================================== */

    document
        .getElementById("formSolicitud")
        .addEventListener("submit", function(e) {

            let valido = true;

            materialesSeleccionados.forEach(function(material) {

                const cantidad =
                    parseFloat(material.cantidad);


                if (
                    !cantidad ||
                    cantidad <= 0 ||
                    cantidad > material.stock
                ) {

                    valido = false;

                }

            });


            if (!valido) {

                e.preventDefault();

                alert(
                    "Revise las cantidades solicitadas. No puede solicitar una cantidad mayor al stock disponible."
                );

            }

        });


    /* ==========================================================
       ESCAPAR HTML
    ========================================================== */

    function escapeHtml(texto) {

        const div = document.createElement("div");

        div.textContent = texto;

        return div.innerHTML;

    }


    /* ==========================================================
       INICIALIZAR
    ========================================================== */

    filtrarMateriales();

    actualizarSeleccionados();
</script>


<?php

$script = "solicitud-material";

require_once __DIR__ . "/../../../layouts/footer.php";

?>