<?php

require_once __DIR__ . "/../../../config/permisos.php";
verificarPermiso("materiales");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$entregas = $entregas ?? [];

?>

<main class="content">

    <div class="page-header">

        <div>
            <h1 class="page-title">
                Historial de entregas
            </h1>

            <p class="page-subtitle">
                Registro histórico de materiales entregados
            </p>
        </div>

        <a
            href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=listar"
            class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver a solicitudes

        </a>

    </div>


    <div class="card">

        <?php if (empty($entregas)): ?>

            <div class="empty-state">

                <i class="fa-solid fa-truck"></i>

                <h3>No hay entregas registradas</h3>

                <p>
                    Todavía no se registraron entregas de materiales.
                </p>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>
                            <th>Entrega</th>
                            <th>Solicitud</th>
                            <th>Obra</th>
                            <th>Fecha</th>
                            <th>Responsable</th>
                            <th>Materiales</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($entregas as $entrega): ?>

                            <tr>

                                <td>

                                    <strong>
                                        #<?= htmlspecialchars($entrega["id_entrega"]) ?>
                                    </strong>

                                </td>


                                <td>

                                    #<?= htmlspecialchars($entrega["id_solicitud"]) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars($entrega["nombre_obra"]) ?>

                                </td>


                                <td>

                                    <div class="fecha">

                                        <strong>
                                            <?= date(
                                                "d/m/Y",
                                                strtotime($entrega["fecha"])
                                            ) ?>
                                        </strong>

                                        <span>
                                            <?= date(
                                                "H:i",
                                                strtotime($entrega["fecha"])
                                            ) ?>
                                        </span>

                                    </div>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $entrega["nombre"] . " " . $entrega["apellido"]
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $entrega["cantidad_materiales"]
                                    ) ?>

                                    <?= $entrega["cantidad_materiales"] == 1
                                        ? "material"
                                        : "materiales"
                                    ?>

                                </td>


                                <td>

                                    <a
                                        href="/empresa_constructora/controladores/EntregaMaterialController.php?accion=detalleEntrega&id=<?= $entrega["id_entrega"] ?>"
                                        class="btn btn-secondary btn-sm">

                                        <i class="fa-solid fa-eye"></i>
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


<style>

.table-responsive {
    overflow-x:auto;
}

.fecha {
    display:flex;
    flex-direction:column;
    gap:2px;
}

.fecha span {
    color:#8b98a8;
    font-size:12px;
}

.btn-sm {
    padding:7px 12px;
    font-size:13px;
}

.empty-state {
    padding:60px 30px;
    text-align:center;
    color:#888;
}

.empty-state i {
    font-size:42px;
    margin-bottom:15px;
}

.empty-state h3 {
    margin-bottom:6px;
}

.empty-state p {
    margin:0;
}

</style>


<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>