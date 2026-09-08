<?php
/** @var array $herramienta */
/** @var array $historial */
require_once __DIR__ . "/../../config/permisos.php";

verificarPermiso("herramientas");

require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";

?>

<main class="content">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="page-header">

        <div>

            <h1 class="page-title">
                HISTORIAL DE ASIGNACIONES
            </h1>

            <p class="page-subtitle">

                <?= htmlspecialchars(
                    $herramienta["nombre"]
                ) ?>

                <?php if (!empty($herramienta["marca"])): ?>

                    · <?= htmlspecialchars(
                            $herramienta["marca"]
                        ) ?>

                <?php endif; ?>

                <?php if (!empty($herramienta["modelo"])): ?>

                    · <?= htmlspecialchars(
                            $herramienta["modelo"]
                        ) ?>

                <?php endif; ?>

            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="/empresa_constructora/controladores/HerramientaController.php?accion=ver&id=<?= (int) $herramienta["id_herramienta"] ?>"
                class="btn btn-secondary">

                <i class="fa-solid fa-arrow-left"></i>

                Volver

            </a>

        </div>

    </div>


    <!-- =====================================================
         INFORMACIÓN DE LA HERRAMIENTA
    ====================================================== -->

    <div class="form-card historial-card">

        <div class="form-card-header">

            <div>

                <h2>

                    <i class="fa-solid fa-toolbox"></i>

                    <?= htmlspecialchars(
                        $herramienta["nombre"]
                    ) ?>

                </h2>

                <p>
                    Registro histórico de las asignaciones
                    realizadas.
                </p>

            </div>

        </div>


        <!-- =================================================
             RESUMEN
        ================================================== -->

        <div class="historial-resumen">

            <div class="historial-resumen-item">

                <span>
                    Asignaciones
                </span>

                <strong>
                    <?= count($historial) ?>
                </strong>

            </div>

            <div class="historial-resumen-item">

                <span>
                    Unidades actuales asignadas
                </span>

                <strong>
                    <?= (int) $herramienta["cantidad_asignada"] ?>
                </strong>

            </div>

            <div class="historial-resumen-item">

                <span>
                    Unidades disponibles
                </span>

                <strong>
                    <?= (int) $herramienta["cantidad_disponible"] ?>
                </strong>

            </div>

        </div>


        <!-- =================================================
             HISTORIAL
        ================================================== -->

        <?php if (empty($historial)): ?>

            <div class="historial-vacio">

                <div class="historial-vacio-icon">

                    <i class="fa-solid fa-clock-rotate-left"></i>

                </div>

                <strong>
                    Sin historial de asignaciones
                </strong>

                <p>
                    Esta herramienta todavía no fue asignada
                    a ninguna obra.
                </p>

            </div>

        <?php else: ?>

            <div class="tabla-historial-container">

                <table class="tabla-historial">

                    <thead>

                        <tr>

                            <th>
                                Obra
                            </th>

                            <th>
                                Fecha de asignación
                            </th>

                            <th>
                                Asignadas
                            </th>

                            <th>
                                Devueltas
                            </th>

                            <th>
                                Pendientes
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Última devolución
                            </th>

                            <th>
                                Acción
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($historial as $registro): ?>

                            <?php

                            $pendiente =
                                (int) $registro["cantidad_pendiente"];

                            $devuelta =
                                (int) $registro["cantidad_devuelta"];

                            $asignada =
                                (int) $registro["cantidad_asignada"];

                            /*
                             * El estado visual se determina
                             * principalmente por las cantidades.
                             */

                            if ($pendiente <= 0) {

                                $estadoTexto = "Devuelta";
                                $estadoClase = "historial-devuelta";
                            } elseif ($devuelta > 0) {

                                $estadoTexto =
                                    "Parcialmente devuelta";

                                $estadoClase =
                                    "historial-parcial";
                            } else {

                                $estadoTexto = "Asignada";
                                $estadoClase = "historial-asignada";
                            }

                            ?>

                            <tr>

                                <!-- OBRA -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $registro["obra"]
                                        ) ?>

                                    </strong>

                                </td>


                                <!-- FECHA ASIGNACIÓN -->

                                <td>

                                    <?= !empty($registro["fecha_asignacion"])
                                        ? date(
                                            "d/m/Y",
                                            strtotime(
                                                $registro["fecha_asignacion"]
                                            )
                                        )
                                        : "-"
                                    ?>

                                </td>


                                <!-- ASIGNADAS -->

                                <td>

                                    <span class="cantidad-historial">

                                        <?= $asignada ?>

                                    </span>

                                </td>


                                <!-- DEVUELTAS -->

                                <td>

                                    <span class="cantidad-historial">

                                        <?= $devuelta ?>

                                    </span>

                                </td>


                                <!-- PENDIENTES -->

                                <td>

                                    <span class="cantidad-historial">

                                        <?= $pendiente ?>

                                    </span>

                                </td>


                                <!-- ESTADO -->

                                <td>

                                    <span
                                        class="estado-historial <?= $estadoClase ?>">

                                        <?= $estadoTexto ?>

                                    </span>

                                </td>


                                <!-- ÚLTIMA DEVOLUCIÓN -->

                                <td>

                                    <?php if (
                                        !empty($registro["fecha_ultima_devolucion"])
                                    ): ?>

                                        <?= date(
                                            "d/m/Y",
                                            strtotime(
                                                $registro["fecha_ultima_devolucion"]
                                            )
                                        ) ?>

                                    <?php else: ?>

                                        —

                                    <?php endif; ?>

                                </td>


                                <!-- ACCIÓN -->

                                <td>

                                    <a
                                        href="/empresa_constructora/controladores/HerramientaObraController.php?accion=devolver&id=<?= (int) $registro["id_herramienta_obra"] ?>"
                                        class="btn btn-secondary btn-historial">

                                        <i class="fa-solid fa-eye"></i>

                                        Ver

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
    .historial-card {
        margin-top: 20px;
        overflow: hidden;
    }

    .historial-resumen {
        display: grid;
        grid-template-columns:
            repeat(3, minmax(0, 1fr));

        gap: 14px;

        margin-bottom: 24px;
    }

    .historial-resumen-item {
        padding: 18px;

        border:
            1px solid rgba(128, 128, 128, 0.18);

        border-radius: 10px;

        background:
            rgba(128, 128, 128, 0.035);
    }

    .historial-resumen-item span {
        display: block;

        margin-bottom: 6px;

        font-size: 12px;

        opacity: 0.62;
    }

    .historial-resumen-item strong {
        font-size: 24px;
    }

    .tabla-historial-container {
        width: 100%;

        overflow-x: auto;
    }

    .tabla-historial {
        width: 100%;

        border-collapse: collapse;

        min-width: 900px;
    }

    .tabla-historial th,
    .tabla-historial td {
        padding: 14px 15px;

        text-align: left;

        border-bottom:
            1px solid rgba(128, 128, 128, 0.14);

        white-space: nowrap;
    }

    .tabla-historial th {
        font-size: 11px;

        text-transform: uppercase;

        letter-spacing: 0.5px;

        opacity: 0.60;
    }

    .tabla-historial td {
        font-size: 13px;
    }

    .tabla-historial tbody tr {
        transition:
            background 0.15s ease;
    }

    .tabla-historial tbody tr:hover {
        background:
            rgba(128, 128, 128, 0.045);
    }

    .cantidad-historial {
        font-weight: 600;
    }

    .estado-historial {
        display: inline-flex;

        align-items: center;

        padding: 5px 10px;

        border-radius: 6px;

        font-size: 11px;

        font-weight: 600;

        border: 1px solid transparent;
    }

    .historial-asignada {
        background:
            rgba(59, 130, 246, 0.10);

        color: #2563eb;

        border-color:
            rgba(59, 130, 246, 0.20);
    }

    .historial-parcial {
        background:
            rgba(245, 158, 11, 0.10);

        color: #d97706;

        border-color:
            rgba(245, 158, 11, 0.20);
    }

    .historial-devuelta {
        background:
            rgba(34, 197, 94, 0.10);

        color: #16a34a;

        border-color:
            rgba(34, 197, 94, 0.20);
    }

    .btn-historial {
        padding: 7px 10px;

        font-size: 12px;
    }

    .historial-vacio {
        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        min-height: 260px;

        text-align: center;
    }

    .historial-vacio-icon {
        width: 56px;
        height: 56px;

        display: flex;

        align-items: center;
        justify-content: center;

        margin-bottom: 14px;

        border-radius: 10px;

        background:
            rgba(128, 128, 128, 0.08);

        font-size: 22px;

        opacity: 0.60;
    }

    .historial-vacio strong {
        margin-bottom: 6px;

        font-size: 16px;
    }

    .historial-vacio p {
        margin: 0;

        font-size: 13px;

        opacity: 0.60;
    }

    @media (max-width: 750px) {

        .historial-resumen {
            grid-template-columns: 1fr;
        }

    }
</style>


<?php

require_once __DIR__ . "/../../layouts/footer.php";

?>