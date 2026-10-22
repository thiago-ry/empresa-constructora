
<?php

/**
 * @var array $herramientas
 * @var array $obra
 */

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

$titulo = "Solicitar herramientas";

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$herramientas = $herramientas ?? [];
$obra = $obra ?? [];

/*
 * Preparamos los datos para el buscador.
 * Se utiliza el nombre de herramienta devuelto por el modelo.
 */
$herramientasBuscador = [];

foreach ($herramientas as $herramienta) {
    $herramientasBuscador[] = [
        "id" => (int) ($herramienta["id_herramienta"] ?? 0),
        "nombre" => (string) (
            $herramienta["nombre"]
            ?? $herramienta["nombre_herramienta"]
            ?? ""
        )
    ];
}

$herramientasBuscador = array_values(array_filter(
    $herramientasBuscador,
    function ($herramienta) {
        return $herramienta["id"] > 0
            && trim($herramienta["nombre"]) !== "";
    }
));
?>

<main class="content fade-up">

    <div class="page-header">
        <div>
            <span class="badge-id">GESTIÓN DE HERRAMIENTAS</span>
            <h1 style="margin-top: 12px;">Solicitar herramientas</h1>
            <p>
                Registrá las herramientas que necesitás para realizar los trabajos de la obra.
            </p>
        </div>

        <a
            href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=listar"
            class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Mis solicitudes
        </a>
    </div>

    <?php if (!empty($_SESSION["error"])): ?>
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <h3>
                <i class="fa-solid fa-circle-exclamation"></i>
                No se pudo completar la operación
            </h3>
            <p>
                <?= htmlspecialchars($_SESSION["error"], ENT_QUOTES, "UTF-8") ?>
            </p>
        </div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION["success"])): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <h3>
                <i class="fa-solid fa-circle-check"></i>
                Operación realizada
            </h3>
            <p>
                <?= htmlspecialchars($_SESSION["success"], ENT_QUOTES, "UTF-8") ?>
            </p>
        </div>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>

    <div class="info-box" style="margin-bottom: 25px;">
        <i class="fa-solid fa-helmet-safety"></i>
        <div>
            <h4>Obra asignada</h4>
            <p>
                <?= htmlspecialchars(
                    $obra["nombre_obra"] ?? "Obra asignada",
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </p>
        </div>
    </div>

    <div class="form-card">

        <div class="card-header-clean" style="padding: 0 0 22px; margin-bottom: 25px;">
            <div class="title-with-icon">
                <i class="fa-solid fa-screwdriver-wrench text-primary"></i>
                <div>
                    <h2>Detalle de la solicitud</h2>
                    <p style="margin-top: 5px;">
                        Buscá las herramientas por nombre e indicá cuántas necesitás.
                    </p>
                </div>
            </div>
        </div>

        <?php if (empty($herramientasBuscador)): ?>

            <div class="empty-state">
                <i class="fa-solid fa-toolbox"></i>
                <h3>No hay herramientas disponibles</h3>
                <p>
                    Por el momento no hay herramientas habilitadas para solicitar.
                </p>
            </div>

        <?php else: ?>

            <form
                method="POST"
                action="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=guardar"
                class="form"
                id="formSolicitud">

                <div class="form-group">
                    <label>Herramientas solicitadas</label>

                    <div id="herramientas" class="solicitud-lista">

                        <div class="fila-herramienta">

                            <div class="grupo-buscador">
                                <label>Buscar herramienta</label>

                                <input
                                    type="text"
                                    class="input buscador-herramienta"
                                    placeholder="Escribí el nombre de la herramienta..."
                                    autocomplete="off"
                                    aria-label="Buscar herramienta">

                                <div
                                    class="resultados-herramientas"
                                    role="listbox">
                                </div>

                                <div
                                    class="herramienta-seleccionada"
                                    style="display: none;">

                                    <span class="nombre-herramienta"></span>

                                    <button
                                        type="button"
                                        class="btn-quitar-herramienta">
                                        Quitar
                                    </button>
                                </div>

                                <input
                                    type="hidden"
                                    name="id_herramienta[]"
                                    class="id-herramienta"
                                    value="">

                                <small class="text-secondary ayuda-buscador">
                                    Escribí para buscar y seleccioná una herramienta.
                                </small>
                            </div>

                            <div class="form-group cantidad-group">
                                <label>Cantidad solicitada</label>

                                <input
                                    type="number"
                                    name="cantidad[]"
                                    class="input cantidad-herramienta"
                                    min="1"
                                    step="1"
                                    value="1"
                                    required>
                            </div>

                            <button
                                type="button"
                                class="btn btn-danger btn-eliminar"
                                title="Eliminar herramienta"
                                aria-label="Eliminar herramienta"
                                disabled>
                                <i class="fa-solid fa-trash"></i>
                            </button>

                        </div>

                    </div>
                </div>

                <button
                    type="button"
                    id="agregarHerramienta"
                    class="btn btn-secondary">
                    <i class="fa-solid fa-plus"></i>
                    Agregar otra herramienta
                </button>

                <div class="divider"></div>

                <div class="form-group">
                    <label for="observaciones">Observaciones</label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                        class="textarea"
                        placeholder="Indicá para qué trabajo necesitás las herramientas o agregá algún detalle..."
                        maxlength="1000"></textarea>

                    <small class="text-secondary">
                        Campo opcional. Máximo 1000 caracteres.
                    </small>
                </div>

                <div class="form-actions">
                    <a
                        href="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=listar"
                        class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i>
                        Enviar solicitud
                    </button>
                </div>

            </form>

        <?php endif; ?>

    </div>

</main>

<style>
    .solicitud-lista {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .fila-herramienta {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 150px 42px;
        align-items: end;
        gap: 15px;
        padding: 18px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 14px;
        transition: border-color 0.2s ease;
    }

    .fila-herramienta:hover {
        border-color: #38526d;
    }

    .fila-herramienta .grupo-buscador,
    .fila-herramienta .cantidad-group {
        min-width: 0;
        margin-bottom: 0;
    }

    .fila-herramienta .grupo-buscador > label,
    .fila-herramienta .cantidad-group > label {
        display: block;
        margin-bottom: 8px;
    }

    .grupo-buscador {
        position: relative;
    }

    .buscador-herramienta {
        width: 100%;
    }

    .resultados-herramientas {
        display: none;
        position: absolute;
        z-index: 100;
        top: calc(100% - 26px);
        left: 0;
        right: 0;
        margin-top: 26px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        max-height: 240px;
        overflow-y: auto;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.30);
    }

    .resultado-herramienta {
        display: block;
        width: 100%;
        padding: 12px 14px;
        background: transparent;
        border: 0;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        text-align: left;
        cursor: pointer;
        font: inherit;
    }

    .resultado-herramienta:last-child {
        border-bottom: 0;
    }

    .resultado-herramienta:hover,
    .resultado-herramienta:focus {
        background: rgba(244, 180, 0, 0.10);
        color: var(--primary);
        outline: none;
    }

    .resultado-vacio {
        padding: 12px 14px;
        color: var(--text-secondary);
        font-size: 13px;
    }

    .herramienta-seleccionada {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 8px;
        padding: 12px 14px;
        background: rgba(244, 180, 0, 0.08);
        border: 1px solid var(--primary);
        border-radius: 12px;
    }

    .nombre-herramienta {
        font-weight: 600;
        color: var(--text);
        overflow-wrap: anywhere;
    }

    .btn-quitar-herramienta {
        flex-shrink: 0;
        background: transparent;
        border: 0;
        color: #ff7777;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-quitar-herramienta:hover {
        text-decoration: underline;
    }

    .btn-eliminar {
        width: 42px;
        height: 42px;
        padding: 0;
        margin-bottom: 3px;
    }

    .btn-eliminar:disabled {
        opacity: 0.35;
        cursor: not-allowed;
        transform: none;
    }

    .form-card .card-header-clean h2 {
        color: var(--white);
        font-size: 19px;
        margin: 0;
    }

    .form-card .card-header-clean p {
        color: var(--text-secondary);
        font-size: 13px;
    }

    .form-actions {
        flex-wrap: wrap;
    }

    @media (max-width: 700px) {
        .fila-herramienta {
            grid-template-columns: minmax(0, 1fr) 42px;
            gap: 14px;
            padding: 14px;
        }

        .fila-herramienta .grupo-buscador {
            grid-column: 1 / -1;
        }

        .fila-herramienta .cantidad-group {
            grid-column: 1;
        }

        .fila-herramienta .btn-eliminar {
            grid-column: 2;
            grid-row: 2;
            width: 42px;
            margin-bottom: 3px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .form-actions .btn,
        #agregarHerramienta {
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const contenedor = document.getElementById("herramientas");
    const botonAgregar = document.getElementById("agregarHerramienta");
    const formulario = document.getElementById("formSolicitud");

    if (!contenedor || !botonAgregar || !formulario) {
        return;
    }

    const herramientas = <?= json_encode(
        $herramientasBuscador,
        JSON_UNESCAPED_UNICODE |
        JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP
    ) ?>;

    function normalizar(texto) {
        return String(texto)
            .toLocaleLowerCase("es")
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
    }

    function actualizarBotonesEliminar() {
        const filas = contenedor.querySelectorAll(".fila-herramienta");

        filas.forEach(function (fila) {
            const boton = fila.querySelector(".btn-eliminar");
            boton.disabled = filas.length === 1;
        });
    }

    function obtenerIdsSeleccionados(filaActual) {
        const ids = new Set();

        contenedor.querySelectorAll(".fila-herramienta").forEach(function (fila) {
            if (fila === filaActual) {
                return;
            }

            const campo = fila.querySelector(".id-herramienta");

            if (campo && campo.value) {
                ids.add(String(campo.value));
            }
        });

        return ids;
    }

    function cerrarResultados(fila) {
        const resultados = fila.querySelector(".resultados-herramientas");

        resultados.style.display = "none";
        resultados.innerHTML = "";
    }

    function mostrarResultados(fila) {
        const buscador = fila.querySelector(".buscador-herramienta");
        const resultados = fila.querySelector(".resultados-herramientas");
        const campoId = fila.querySelector(".id-herramienta");
        const busqueda = normalizar(buscador.value.trim());

        resultados.innerHTML = "";

        if (!busqueda) {
            cerrarResultados(fila);
            return;
        }

        const idsSeleccionados = obtenerIdsSeleccionados(fila);

        const coincidencias = herramientas.filter(function (herramienta) {
            return normalizar(herramienta.nombre).includes(busqueda)
                && String(herramienta.id) !== campoId.value
                && !idsSeleccionados.has(String(herramienta.id));
        });

        if (coincidencias.length === 0) {
            const mensaje = document.createElement("div");
            mensaje.className = "resultado-vacio";
            mensaje.textContent = "No se encontraron herramientas disponibles en los resultados.";
            resultados.appendChild(mensaje);
            resultados.style.display = "block";
            return;
        }

        coincidencias.forEach(function (herramienta) {
            const opcion = document.createElement("button");

            opcion.type = "button";
            opcion.className = "resultado-herramienta";
            opcion.setAttribute("role", "option");
            opcion.textContent = herramienta.nombre;

            opcion.addEventListener("click", function () {
                campoId.value = herramienta.id;

                fila.querySelector(".nombre-herramienta").textContent =
                    herramienta.nombre;

                fila.querySelector(".herramienta-seleccionada").style.display =
                    "flex";

                buscador.style.display = "none";
                fila.querySelector(".ayuda-buscador").style.display = "none";

                cerrarResultados(fila);
            });

            resultados.appendChild(opcion);
        });

        resultados.style.display = "block";
    }

    function configurarFila(fila) {
        const buscador = fila.querySelector(".buscador-herramienta");
        const botonQuitar = fila.querySelector(".btn-quitar-herramienta");
        const botonEliminar = fila.querySelector(".btn-eliminar");
        const resultados = fila.querySelector(".resultados-herramientas");

        buscador.addEventListener("input", function () {
            mostrarResultados(fila);
        });

        buscador.addEventListener("focus", function () {
            if (buscador.value.trim() !== "") {
                mostrarResultados(fila);
            }
        });

        botonQuitar.addEventListener("click", function () {
            fila.querySelector(".id-herramienta").value = "";

            fila.querySelector(".nombre-herramienta").textContent = "";
            fila.querySelector(".herramienta-seleccionada").style.display =
                "none";

            buscador.style.display = "block";
            fila.querySelector(".ayuda-buscador").style.display = "block";

            buscador.value = "";
            cerrarResultados(fila);
            buscador.focus();
        });

        botonEliminar.addEventListener("click", function () {
            const filas = contenedor.querySelectorAll(".fila-herramienta");

            if (filas.length <= 1) {
                return;
            }

            fila.remove();
            actualizarBotonesEliminar();
        });

        document.addEventListener("click", function (evento) {
            if (!fila.contains(evento.target)) {
                cerrarResultados(fila);
            }
        });

        resultados.addEventListener("keydown", function (evento) {
            if (evento.key === "Escape") {
                cerrarResultados(fila);
                buscador.focus();
            }
        });
    }

    configurarFila(contenedor.querySelector(".fila-herramienta"));
    actualizarBotonesEliminar();

    botonAgregar.addEventListener("click", function () {
        const original = contenedor.querySelector(".fila-herramienta");
        const nueva = original.cloneNode(true);

        nueva.querySelector(".buscador-herramienta").value = "";
        nueva.querySelector(".buscador-herramienta").style.display = "block";

        nueva.querySelector(".id-herramienta").value = "";
        nueva.querySelector(".nombre-herramienta").textContent = "";

        nueva.querySelector(".resultados-herramientas").innerHTML = "";
        nueva.querySelector(".resultados-herramientas").style.display = "none";

        nueva.querySelector(".herramienta-seleccionada").style.display = "none";
        nueva.querySelector(".ayuda-buscador").style.display = "block";

        nueva.querySelector(".cantidad-herramienta").value = "1";

        contenedor.appendChild(nueva);

        configurarFila(nueva);
        actualizarBotonesEliminar();

        nueva.querySelector(".buscador-herramienta").focus();
    });

    formulario.addEventListener("submit", function (evento) {
        const filas = contenedor.querySelectorAll(".fila-herramienta");
        const idsSeleccionados = new Set();

        for (const fila of filas) {
            const campoId = fila.querySelector(".id-herramienta");
            const cantidad = fila.querySelector(".cantidad-herramienta");

            const id = campoId.value.trim();
            const valorCantidad = cantidad.value.trim();
            const numeroCantidad = Number(valorCantidad);

            if (
                !id ||
                !valorCantidad ||
                !Number.isInteger(numeroCantidad) ||
                numeroCantidad < 1
            ) {
                evento.preventDefault();
                alert("Seleccioná una herramienta e ingresá una cantidad entera mayor que cero.");
                fila.querySelector(".buscador-herramienta").focus();
                return;
            }

            if (idsSeleccionados.has(id)) {
                evento.preventDefault();
                alert("No repitas la misma herramienta. Si necesitás más unidades, modificá la cantidad.");
                fila.querySelector(".buscador-herramienta").focus();
                return;
            }

            idsSeleccionados.add(id);
        }
    });
});
</script>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>