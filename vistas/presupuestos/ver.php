<?php

require_once __DIR__ . '/iniciar.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

$presupuesto = $presupuestoController->obtener($id);

if (!$presupuesto) {
    header('Location: index.php?error=no_encontrado');
    exit;
}

function eVer($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';

?>

<main class="content">

    <div class="page-header">

        <div>
            <h1>Presupuesto #<?= (int) $presupuesto['id_presupuesto'] ?></h1>
            <p>Detalle de los costos registrados.</p>
        </div>

        <a href="index.php" class="btn btn-outline">
            Volver
        </a>

    </div>

    <section class="form-card">

        <h2>Datos generales</h2>

        <p>
            <strong>Obra:</strong>
            <?= eVer($presupuesto['nombre_obra']) ?>
        </p>

        <p>
            <strong>Fecha:</strong>
            <?= eVer($presupuesto['fecha']) ?>
        </p>

        <p>
            <strong>Versión:</strong>
            V<?= (int) $presupuesto['version'] ?>
        </p>

        <p>
            <strong>Estado:</strong>
            <?= eVer($presupuesto['estado']) ?>
        </p>

        <p>
            <strong>Observaciones:</strong>
            <?= nl2br(eVer($presupuesto['detalle_general'] ?? 'Sin observaciones')) ?>
        </p>

    </section>

    <section class="form-card">

        <h2>Materiales y conceptos adicionales</h2>

        <div class="table-container">

            <table class="table">

                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($presupuesto['materiales'] as $item): ?>

                        <tr>

                            <td><?= eVer($item['descripcion']) ?></td>

                            <td><?= eVer($item['cantidad']) ?></td>

                            <td>
                                $<?= number_format((float) $item['costo_unitario'], 2, ',', '.') ?>
                            </td>

                            <td>
                                $<?= number_format((float) $item['subtotal'], 2, ',', '.') ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

    <section class="form-card">

        <h2>Mano de obra y servicios</h2>

        <div class="table-container">

            <table class="table">

                <thead>
                    <tr>
                        <th>Trabajo</th>
                        <th>Horas</th>
                        <th>Costo por hora</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($presupuesto['mano_obra'] as $trabajo): ?>

                        <tr>

                            <td><?= eVer($trabajo['descripcion']) ?></td>

                            <td><?= eVer($trabajo['cantidad_horas']) ?></td>

                            <td>
                                $<?= number_format((float) $trabajo['costo_hora'], 2, ',', '.') ?>
                            </td>

                            <td>
                                $<?= number_format((float) $trabajo['subtotal'], 2, ',', '.') ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

    <section class="form-card">

        <h2>Resumen económico</h2>

        <?php
        $base = 0;

        foreach ($presupuesto['materiales'] as $item) {
            $base += (float) $item['subtotal'];
        }

        foreach ($presupuesto['mano_obra'] as $trabajo) {
            $base += (float) $trabajo['subtotal'];
        }

        $gastos = $base * (float) ($presupuesto['porcentaje_gastos'] ?? 0) / 100;
        $imprevistos = $base * (float) ($presupuesto['porcentaje_imprevistos'] ?? 0) / 100;
        $gananciaBase = $base + $gastos + $imprevistos;
        $ganancia = $gananciaBase * (float) ($presupuesto['porcentaje_ganancia'] ?? 0) / 100;
        ?>

        <p>
            Costo base:
            <strong>$<?= number_format($base, 2, ',', '.') ?></strong>
        </p>

        <p>
            Gastos generales:
            <strong>$<?= number_format($gastos, 2, ',', '.') ?></strong>
        </p>

        <p>
            Imprevistos:
            <strong>$<?= number_format($imprevistos, 2, ',', '.') ?></strong>
        </p>

        <p>
            Ganancia:
            <strong>$<?= number_format($ganancia, 2, ',', '.') ?></strong>
        </p>

        <hr>

        <h2>
            Total:
            $<?= number_format((float) $presupuesto['costo_total'], 2, ',', '.') ?>
        </h2>

    </section>

</main>

</body>
</html>