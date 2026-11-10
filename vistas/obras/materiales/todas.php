
<?php

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitudes = $solicitudes ?? [];
$historialMateriales = $historialMateriales ?? [];

?>

<main class="content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Solicitudes de materiales</h1>
            <p class="page-subtitle">
                Supervisión general de solicitudes de todas las obras.
            </p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button
                type="button"
                class="btn btn-primary"
                onclick="abrirModalMateriales()">
                <i class="fa-solid fa-boxes-stacked"></i>
                Listar materiales
            </button>
        </div>
    </div>

    <!-- TABLA GENERAL DE SOLICITUDES -->

    <div class="card">

        <?php if (empty($solicitudes)): ?>

            <div style="padding:50px; text-align:center; color:#888;">
                <h3>No hay solicitudes</h3>
                <p>
                    Todavía no existen solicitudes de materiales registradas.
                </p>
            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Solicitud</th>
                            <th>Obra</th>
                            <th>Fecha</th>
                            <th>Materiales</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>

                                <td>
                                    <strong>
                                        #<?= htmlspecialchars($solicitud["id_solicitud"]) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($solicitud["nombre_obra"]) ?>
                                </td>

                                <td>
                                    <?= date("d/m/Y", strtotime($solicitud["fecha"])) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($solicitud["cantidad_materiales"]) ?>
                                    <?= $solicitud["cantidad_materiales"] == 1
                                        ? "material"
                                        : "materiales" ?>
                                </td>

                                <td>
                                    <?php
                                    $estado = $solicitud["estado"];

                                    $clases = [
                                        "Pendiente" => "badge-warning",
                                        "Aprobada" => "badge-success",
                                        "Rechazada" => "badge-danger",
                                        "Entregada" => "badge-info"
                                    ];

                                    $claseEstado = $clases[$estado] ?? "badge-secondary";
                                    ?>

                                    <span class="badge <?= $claseEstado ?>">
                                        <?= htmlspecialchars($estado) ?>
                                    </span>
                                </td>

                                <td>
                                    <a
                                        href="/empresa_constructora/controladores/SolicitudMaterialController.php?accion=detalle&id=<?= (int)$solicitud["id_solicitud"] ?>"
                                        class="btn btn-secondary btn-sm">
                                        Ver detalle
                                    </a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>

        <?php endif; ?>

    </div>

</main>


<!-- MODAL: HISTORIAL GENERAL DE MATERIALES -->

<div id="modalMateriales" class="modal-materiales">

    <div class="modal-materiales-content">

        <div class="modal-materiales-header">

            <div>
                <h2>Listado general de materiales</h2>
                <p>
                    Materiales incluidos en las solicitudes de todas las obras.
                </p>
            </div>

            <button
                type="button"
                class="modal-cerrar"
                onclick="cerrarModalMateriales()">
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <div class="modal-buscador">
            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                id="buscarMaterialSolicitado"
                placeholder="Buscar por material, obra o solicitud...">
        </div>

        <div class="modal-tabla-container">

            <table class="table" id="tablaHistorialMateriales">

                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Obra</th>
                        <th>Solicitud</th>
                        <th>Fecha</th>
                        <th>Solicitado</th>
                        <th>Entregado</th>
                        <th>Estado</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($historialMateriales)): ?>

                        <tr>
                            <td colspan="7" style="text-align:center;">
                                No hay materiales registrados.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($historialMateriales as $item): ?>

                            <?php
                            $estado = $item["estado"];

                            $clases = [
                                "Pendiente" => "badge-warning",
                                "Aprobada" => "badge-info",
                                "Rechazada" => "badge-danger",
                                "Entregada" => "badge-success"
                            ];

                            $claseEstado = $clases[$estado] ?? "badge-secondary";
                            ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($item["nombre_material"]) ?>
                                    </strong>

                                    <small>
                                        <?= htmlspecialchars($item["unidad_medida"] ?? "") ?>
                                    </small>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item["nombre_obra"]) ?>
                                </td>

                                <td>
                                    #<?= (int)$item["id_solicitud"] ?>
                                </td>

                                <td>
                                    <?= date("d/m/Y", strtotime($item["fecha"])) ?>
                                </td>

                                <td>
                                    <?= number_format((float)$item["cantidad_solicitada"], 2, ",", ".") ?>
                                </td>

                                <td>
                                    <?= number_format((float)$item["cantidad_entregada"], 2, ",", ".") ?>
                                </td>

                                <td>
                                    <span class="badge <?= $claseEstado ?>">
                                        <?= htmlspecialchars($estado) ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

            <div
                id="sinResultadosMateriales"
                style="display:none; text-align:center; padding:25px;">
                No se encontraron materiales.
            </div>

        </div>

        <div class="modal-materiales-footer">

            <span>
                Registros: <?= count($historialMateriales) ?>
            </span>

            <button
                type="button"
                class="btn btn-secondary"
                onclick="cerrarModalMateriales()">
                Cerrar
            </button>

        </div>

    </div>

</div>


<style>

.table-responsive {
    overflow-x: auto;
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.badge-warning {
    background: rgba(245, 158, 11, .15);
    color: #f59e0b;
}

.badge-success {
    background: rgba(34, 197, 94, .15);
    color: #22c55e;
}

.badge-danger {
    background: rgba(239, 68, 68, .15);
    color: #ef4444;
}

.badge-info {
    background: rgba(59, 130, 246, .15);
    color: #3b82f6;
}

.badge-secondary {
    background: rgba(148, 163, 184, .15);
    color: #94a3b8;
}

.btn-sm {
    padding: 7px 12px;
    font-size: 13px;
}

.modal-materiales {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0, 0, 0, .75);
    backdrop-filter: blur(5px);
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.modal-materiales.active {
    display: flex;
}

.modal-materiales-content {
    background: #111c2a;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 18px;
    width: 100%;
    max-width: 1250px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
}

.modal-materiales-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 22px 25px;
    border-bottom: 1px solid rgba(255,255,255,.08);
    gap: 15px;
}

.modal-materiales-header h2 {
    margin: 0;
    color: #f4b400;
}

.modal-materiales-header p {
    margin: 6px 0 0;
    color: #94a3b8;
}

.modal-cerrar {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 25px;
    cursor: pointer;
}

.modal-buscador {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 25px;
    padding: 12px 16px;
    background: #0b1320;
    border: 1px solid #26364a;
    border-radius: 10px;
    color: #f4b400;
}

.modal-buscador input {
    width: 100%;
    background: transparent;
    border: none;
    outline: none;
    color: white;
    font-size: 14px;
}

.modal-buscador input::placeholder {
    color: #8190a5;
}

.modal-tabla-container {
    overflow: auto;
    padding: 0 25px;
    flex: 1;
}

.modal-tabla-container table {
    width: 100%;
    min-width: 850px;
    border-collapse: collapse;
}

.modal-tabla-container th {
    position: sticky;
    top: 0;
    background: #172638;
    z-index: 2;
}

.modal-tabla-container td small {
    display: block;
    color: #94a3b8;
    margin-top: 4px;
}

.modal-materiales-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 25px;
    border-top: 1px solid rgba(255,255,255,.08);
    color: #94a3b8;
}

@media(max-width: 600px) {
    .modal-materiales {
        padding: 8px;
    }

    .modal-materiales-content {
        max-height: 95vh;
    }

    .modal-materiales-header {
        padding: 16px;
    }

    .modal-tabla-container {
        padding: 0 12px;
    }
}

</style>


<script>

function abrirModalMateriales() {
    document.getElementById("modalMateriales").classList.add("active");
}

function cerrarModalMateriales() {
    document.getElementById("modalMateriales").classList.remove("active");
}

document.addEventListener("DOMContentLoaded", function() {

    const buscador = document.getElementById("buscarMaterialSolicitado");
    const tabla = document.getElementById("tablaHistorialMateriales");
    const sinResultados = document.getElementById("sinResultadosMateriales");

    if (!buscador || !tabla) {
        return;
    }

    buscador.addEventListener("input", function() {

        const texto = this.value.toLowerCase().trim();
        const filas = tabla.querySelectorAll("tbody tr");

        let visibles = 0;

        filas.forEach(function(fila) {

            const coincide = fila.textContent.toLowerCase().includes(texto);

            fila.style.display = coincide ? "" : "none";

            if (coincide) {
                visibles++;
            }

        });

        if (sinResultados) {
            sinResultados.style.display = visibles === 0 ? "block" : "none";
        }

    });

});

</script>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>
