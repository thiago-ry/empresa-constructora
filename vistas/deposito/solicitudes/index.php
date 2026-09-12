<?php

require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("materiales");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$solicitudes = $solicitudes ?? [];

?>

<main class="content">

    <div class="page-header">

        <div>
            <h1 class="page-title">
                Solicitudes de materiales
            </h1>

            <p class="page-subtitle">
                Gestión de solicitudes del depósito
            </p>
        </div>

        <div style="display:flex;gap:10px;">

    <a
        href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=historial"
        class="btn btn-secondary">

        <i class="fa-solid fa-clock-rotate-left"></i>
        Historial de entregas

    </a>

</div>

    </div>


    <div class="card">

        <?php if (empty($solicitudes)): ?>

            <div style="
                padding:50px;
                text-align:center;
                color:#888;
            ">

                <i
                    class="fa-solid fa-box-open"
                    style="font-size:40px; margin-bottom:15px;">
                </i>

                <h3>No hay solicitudes</h3>

                <p>
                    No existen solicitudes de materiales registradas.
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

                            <?php

                            $estado = $solicitud["estado"];

                            switch ($estado) {

                                case "Pendiente":
                                    $claseEstado = "badge-warning";
                                    break;

                                case "Aprobada":
                                    $claseEstado = "badge-success";
                                    break;

                                case "Rechazada":
                                    $claseEstado = "badge-danger";
                                    break;

                                case "Entregada":
                                    $claseEstado = "badge-info";
                                    break;

                                default:
                                    $claseEstado = "badge-secondary";
                                    break;
                            }

                            ?>

                            <tr>

                                <td>
                                    <strong>
                                        #<?= htmlspecialchars(
                                            $solicitud["id_solicitud"]
                                        ) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $solicitud["nombre_obra"]
                                    ) ?>
                                </td>

                                <td>
                                    <?= date(
                                        "d/m/Y",
                                        strtotime($solicitud["fecha"])
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $solicitud["cantidad_materiales"]
                                    ) ?>

                                    <?= $solicitud["cantidad_materiales"] == 1
                                        ? "material"
                                        : "materiales"
                                    ?>
                                </td>

                                <td>

                                    <span class="badge <?= $claseEstado ?>">
                                        <?= htmlspecialchars($estado) ?>
                                    </span>

                                </td>

                                <td>

                                    <div
                                        style="
                                            display:flex;
                                            gap:8px;
                                            flex-wrap:wrap;
                                        ">

                                        <a
                                            href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=detalle&id=<?= $solicitud["id_solicitud"] ?>"
                                            class="btn btn-secondary btn-sm">

                                            <i class="fa-solid fa-eye"></i>
                                            Ver detalle

                                        </a>


                                        <?php if ($estado === "Pendiente"): ?>

                                            <a
                                                href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=aprobar&id=<?= $solicitud["id_solicitud"] ?>"
                                                class="btn btn-primary btn-sm"
                                                onclick="return confirm('¿Aprobar esta solicitud?');">

                                                <i class="fa-solid fa-check"></i>
                                                Aprobar

                                            </a>


                                            <a
                                                href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=rechazar&id=<?= $solicitud["id_solicitud"] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Rechazar esta solicitud?');">

                                                <i class="fa-solid fa-xmark"></i>
                                                Rechazar

                                            </a>

                                        <?php elseif ($estado === "Aprobada"): ?>

                                            <a
                                                href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=entregar&id=<?= $solicitud["id_solicitud"] ?>"
                                                class="btn btn-primary btn-sm">

                                                <i class="fa-solid fa-truck"></i>
                                                Registrar entrega

                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</main>


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

</style>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>