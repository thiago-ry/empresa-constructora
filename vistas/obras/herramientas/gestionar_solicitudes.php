
<?php
require_once __DIR__ . "/../../../layouts/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rol = (int)($_SESSION["usuario"]["id_rol"] ?? 0);
$puedeGestionar = in_array($rol, [2, 3, 5], true);

if (!$puedeGestionar) {
    http_response_code(403);
    exit("No tenés permiso para ver estas solicitudes.");
}

if (!isset($solicitudes) || !is_array($solicitudes)) {
    $solicitudes = [];
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Solicitudes de herramientas</h2>
            <p class="text-muted mb-0">
                Revisá las solicitudes y gestioná su entrega.
            </p>
        </div>
        <a href="/empresa_constructora/vistas/herramientas/"
           class="btn btn-outline-secondary">
            Volver a herramientas
        </a>
    </div>

    <?php if (!empty($_SESSION["success"])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION["success"]) ?>
        </div>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION["error"])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION["error"]) ?>
        </div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($solicitudes)): ?>
                <p class="text-muted mb-0">
                    No hay solicitudes de herramientas registradas.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>N.º</th>
                                <th>Obra</th>
                                <th>Solicitante</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr>
                                <td>
                                    <?= (int)($s["id_solicitud"] ?? 0) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($s["nombre_obra"] ?? "Sin obra") ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($s["solicitante"] ?? "No disponible") ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($s["fecha"] ?? "") ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($s["estado"] ?? "") ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($s["observaciones"] ?? "-") ?>
                                </td>
                                <td>
                                    <?php if (($s["estado"] ?? "") === "Pendiente"): ?>
                                        <form method="POST"
                                              action="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=aprobar"
                                              class="d-inline">
                                            <input type="hidden" name="id_solicitud"
                                                   value="<?= (int)$s["id_solicitud"] ?>">
                                            <button type="submit"
                                                    class="btn btn-sm btn-success">
                                                Aprobar
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=rechazar"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Rechazar esta solicitud?')">
                                            <input type="hidden" name="id_solicitud"
                                                   value="<?= (int)$s["id_solicitud"] ?>">
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                Rechazar
                                            </button>
                                        </form>

                                    <?php elseif (($s["estado"] ?? "") === "Aprobada"): ?>
                                        <form method="POST"
                                              action="/empresa_constructora/controladores/SolicitudHerramientaController.php?accion=entregar"
                                              onsubmit="return confirm('¿Confirmás la entrega de estas herramientas?')">
                                            <input type="hidden" name="id_solicitud"
                                                   value="<?= (int)$s["id_solicitud"] ?>">
                                            <button type="submit"
                                                    class="btn btn-sm btn-primary">
                                                Registrar entrega
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Sin acciones</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>
