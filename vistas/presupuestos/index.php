
<?php

require_once __DIR__ . '/iniciar.php';

$presupuestos = [];
$error = '';

try {
    $presupuestos = $presupuestoController->listar();
} catch (Throwable $e) {
    error_log('Error al listar presupuestos: ' . $e->getMessage());
    $error = 'No se pudieron cargar los presupuestos.';
}

function ePresupuesto($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$totalPresupuestos = count($presupuestos);
$totalAprobados = 0;
$totalActivos = 0;

foreach ($presupuestos as $p) {
    if (($p['estado'] ?? '') === 'Aprobado') {
        $totalAprobados++;
    }

    if (($p['estado'] ?? '') === 'Activo') {
        $totalActivos++;
    }
}

require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';

?>

<main class="content presupuesto-listado">

    <!-- ENCABEZADO -->
    <div class="page-header presupuesto-header">

        <div>
            <span class="presupuesto-etiqueta">GESTIÓN ECONÓMICA</span>
            <h1>Presupuestos</h1>
            <p>Consultá, creá y administrá los presupuestos de tus obras.</p>
        </div>

        <a href="crear.php" class="btn btn-primary btn-nuevo-presupuesto">
            <span>+</span> Nuevo presupuesto
        </a>

    </div>

    <!-- MENSAJES -->
    <?php if (isset($_GET['guardado'])): ?>

        <div class="alert alert-success">
            El presupuesto se guardó correctamente.
        </div>

    <?php endif; ?>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= ePresupuesto($error) ?>
        </div>

    <?php endif; ?>

    <!-- INDICADORES -->
    <section class="presupuesto-indicadores">

        <div class="indicador-presupuesto">

            <div class="indicador-icono icono-total">📋</div>

            <div>
                <span>Total registrados</span>
                <strong><?= $totalPresupuestos ?></strong>
                <small>Presupuestos creados</small>
            </div>

        </div>

        <div class="indicador-presupuesto">

            <div class="indicador-icono icono-activo">📝</div>

            <div>
                <span>Activos</span>
                <strong><?= $totalActivos ?></strong>
                <small>En gestión</small>
            </div>

        </div>

        <div class="indicador-presupuesto">

            <div class="indicador-icono icono-aprobado">✓</div>

            <div>
                <span>Aprobados</span>
                <strong><?= $totalAprobados ?></strong>
                <small>Presupuestos aprobados</small>
            </div>

        </div>

    </section>

    <!-- TABLA -->
    <section class="form-card presupuesto-tabla-card">

        <div class="presupuesto-tabla-header">

            <div>
                <h2>Presupuestos registrados</h2>
                <p>Historial de presupuestos de las obras.</p>
            </div>

            <span class="contador-registros">
                <?= $totalPresupuestos ?> registros
            </span>

        </div>

        <div class="table-container">

            <table class="table tabla-presupuestos">

                <thead>
                    <tr>
                        <th>N.º</th>
                        <th>Obra</th>
                        <th>Fecha</th>
                        <th>Versión</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($presupuestos)): ?>

                        <tr>
                            <td colspan="7">

                                <div class="presupuesto-vacio">

                                    <div class="vacio-icono">📋</div>

                                    <h3>No hay presupuestos registrados</h3>

                                    <p>
                                        Cuando crees un presupuesto para una obra,
                                        aparecerá en este listado.
                                    </p>

                                    <a href="crear.php" class="btn btn-primary">
                                        + Crear presupuesto
                                    </a>

                                </div>

                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($presupuestos as $p): ?>

                            <?php
                            $estado = $p['estado'] ?? 'Activo';

                            $clases = [
                                'Aprobado' => 'estado-aprobado',
                                'Rechazado' => 'estado-rechazado',
                                'Inactivo' => 'estado-inactivo',
                                'Activo' => 'estado-activo'
                            ];
                            ?>

                            <tr>

                                <td class="numero-presupuesto">
                                    #<?= (int) $p['id_presupuesto'] ?>
                                </td>

                                <td class="nombre-obra">
                                    <?= ePresupuesto($p['nombre_obra'] ?? 'Sin obra') ?>
                                </td>

                                <td>
                                    <?= ePresupuesto($p['fecha'] ?? '') ?>
                                </td>

                                <td>
                                    <span class="version-presupuesto">
                                        V<?= (int) $p['version'] ?>
                                    </span>
                                </td>

                                <td class="precio-presupuesto">
                                    $<?= number_format(
                                        (float) $p['costo_total'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </td>

                                <td>
                                    <span class="estado-presupuesto <?= $clases[$estado] ?? 'estado-inactivo' ?>">
                                        <?= ePresupuesto($estado) ?>
                                    </span>
                                </td>

                             
<td>
    <div class="acciones-presupuesto-tabla">

        <a
            href="ticket.php?id=<?= (int) $p['id_presupuesto'] ?>"
            class="btn-ticket"
        >
            Ver ticket
        </a>

        <a
            href="ver.php?id=<?= (int) $p['id_presupuesto'] ?>"
            class="btn-ver-presupuesto"
        >
            Ver detalles
        </a>

    </div>
</td>


                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>
</html>
