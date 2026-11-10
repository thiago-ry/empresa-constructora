
<?php

require_once __DIR__ . '/iniciar.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    http_response_code(400);
    exit('Identificador de presupuesto inválido.');
}

try {
    $presupuesto = $presupuestoController->obtener($id);
} catch (Throwable $e) {
    error_log('Error al generar ticket: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo cargar el comprobante.');
}

if (!$presupuesto) {
    http_response_code(404);
    exit('El presupuesto solicitado no existe.');
}

// ESCAPAR TEXTO
function eTicket($valor): string
{
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// FORMATO DE DINERO
function dineroTicket($valor): string
{
    return '$ ' . number_format(
        (float) $valor,
        2,
        ',',
        '.'
    );
}

// DATOS PRINCIPALES
$idPresupuesto = (int) $presupuesto['id_presupuesto'];

$materiales = $presupuesto['materiales'] ?? [];
$manoObra = $presupuesto['mano_obra'] ?? [];

$fecha = !empty($presupuesto['fecha'])
    ? date('d/m/Y', strtotime($presupuesto['fecha']))
    : 'No especificada';

$version = (int) ($presupuesto['version'] ?? 1);

// TOTALES
$totalMateriales = 0;
$totalManoObra = 0;

foreach ($materiales as $material) {
    $totalMateriales += (float) ($material['subtotal'] ?? 0);
}

foreach ($manoObra as $trabajo) {
    $totalManoObra += (float) ($trabajo['subtotal'] ?? 0);
}

$costoDirecto = (float) (
    $presupuesto['costo_directo']
    ?? ($totalMateriales + $totalManoObra)
);

$porcGastosObra = (float) ($presupuesto['porc_gastos_obra'] ?? 0);
$porcGastosOficina = (float) ($presupuesto['porc_gastos_oficina'] ?? 0);
$porcImprevistos = (float) ($presupuesto['porc_imprevistos'] ?? 0);
$porcGanancia = (float) ($presupuesto['porc_ganancia'] ?? 0);

$gastosObra = $costoDirecto * ($porcGastosObra / 100);
$gastosOficina = $costoDirecto * ($porcGastosOficina / 100);
$imprevistos = $costoDirecto * ($porcImprevistos / 100);

$baseGanancia = $costoDirecto
    + $gastosObra
    + $gastosOficina
    + $imprevistos;

$ganancia = $baseGanancia * ($porcGanancia / 100);

// TOTAL GUARDADO EN LA BASE DE DATOS
$totalFinal = (float) $presupuesto['costo_total'];

require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/sidebar.php';

?>

<style>

/* ==================================
   BUILDPRO - TICKET PRESUPUESTO
================================== */

.ticket-pagina {
    padding: 25px;
    color: #f1f5f9;
    font-family: 'Poppins', Arial, sans-serif;
}

.ticket-acciones {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 22px;
}

.ticket-acciones h1 {
    margin: 0;
    font-size: 25px;
}

.ticket-botones {
    display: flex;
    gap: 10px;
}

.btn-imprimir-ticket {
    padding: 12px 20px;
    color: #111827;
    background: #f4b400;
    border: none;
    border-radius: 9px;
    font-weight: 700;
    cursor: pointer;
}

.btn-volver-ticket {
    display: inline-block;
    padding: 12px 18px;
    color: #e2e8f0;
    background: #24384d;
    border-radius: 9px;
    text-decoration: none;
}

/* DOCUMENTO */

.ticket-documento {
    width: 100%;
    max-width: 850px;
    margin: 0 auto;
    padding: 40px;
    color: #1e293b;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(0,0,0,.25);
    font-family: Arial, sans-serif;
}

.ticket-cabecera {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    padding-bottom: 22px;
    border-bottom: 3px solid #f4b400;
}

.ticket-logo h2 {
    margin: 0;
    color: #172536;
    font-size: 30px;
    font-weight: 800;
}

.ticket-logo p {
    margin: 5px 0;
    color: #64748b;
    font-size: 11px;
    letter-spacing: 2px;
}

.ticket-numero {
    text-align: right;
}

.ticket-numero h3 {
    margin: 0;
    color: #172536;
    font-size: 21px;
}

.ticket-numero p {
    margin: 5px 0;
    color: #64748b;
    font-size: 12px;
}

.ticket-titulo {
    margin: 25px 0;
    text-align: center;
}

.ticket-titulo h2 {
    margin: 0 0 6px;
    color: #172536;
    font-size: 22px;
}

.ticket-titulo p {
    margin: 0;
    color: #64748b;
    font-size: 12px;
}

/* INFORMACIÓN */

.ticket-datos {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 25px;
    padding: 18px;
    background: #f1f5f9;
    border-radius: 8px;
}

.ticket-dato small {
    display: block;
    margin-bottom: 5px;
    color: #64748b;
    font-size: 11px;
}

.ticket-dato strong {
    color: #172536;
    font-size: 13px;
}

/* TABLAS */

.ticket-documento h4 {
    margin: 22px 0 12px;
    color: #172536;
    font-size: 14px;
}

.ticket-tabla {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.ticket-tabla th {
    padding: 11px 8px;
    color: #ffffff;
    background: #172536;
    font-size: 11px;
    text-align: left;
}

.ticket-tabla td {
    padding: 11px 8px;
    color: #334155;
    border-bottom: 1px solid #e2e8f0;
    font-size: 12px;
}

.ticket-tabla th:last-child,
.ticket-tabla td:last-child {
    text-align: right;
}

.ticket-vacio {
    padding: 15px;
    color: #64748b;
    text-align: center;
    font-size: 12px;
}

/* RESUMEN */

.ticket-resumen {
    margin-top: 20px;
    padding: 18px;
    background: #f1f5f9;
    border-radius: 8px;
}

.ticket-resumen .linea {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    padding: 8px 0;
    color: #475569;
    font-size: 12px;
}

.ticket-resumen .total {
    margin-top: 12px;
    padding-top: 16px;
    color: #172536;
    border-top: 2px solid #f4b400;
    font-size: 17px;
    font-weight: 800;
}

.ticket-resumen .total strong {
    color: #a87900;
    font-size: 21px;
}

/* CONDICIONES Y FIRMAS */

.ticket-condiciones {
    margin-top: 22px;
    padding: 15px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    font-size: 12px;
    line-height: 1.7;
}

.ticket-firmas {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    margin-top: 65px;
}

.ticket-firma {
    padding-top: 10px;
    text-align: center;
    border-top: 1px solid #64748b;
    color: #64748b;
    font-size: 11px;
}

.ticket-pie {
    margin-top: 35px;
    padding-top: 15px;
    color: #94a3b8;
    border-top: 1px solid #e2e8f0;
    text-align: center;
    font-size: 10px;
}

/* IMPRESIÓN A4 */

@page {
    size: A4;
    margin: 12mm;
}

@media print {

    body {
        background: #ffffff !important;
        color: #000000 !important;
    }

    .ticket-acciones,
    .sidebar,
    aside,
    .topbar,
    .navbar,
    header {
        display: none !important;
    }

    .ticket-pagina,
    .content {
        display: block !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }

    .ticket-documento {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
        box-shadow: none;
        border-radius: 0;
    }

    .ticket-documento,
    .ticket-tabla,
    .ticket-resumen {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    .ticket-tabla tr,
    .ticket-resumen,
    .ticket-firmas {
        break-inside: avoid;
    }
}

@media (max-width: 650px) {

    .ticket-pagina {
        padding: 12px;
    }

    .ticket-acciones {
        align-items: flex-start;
        flex-direction: column;
    }

    .ticket-documento {
        padding: 20px;
    }

    .ticket-datos {
        grid-template-columns: 1fr;
    }

    .ticket-firmas {
        gap: 25px;
    }
}

</style>

<main class="content ticket-pagina">

    <div class="ticket-acciones no-print">

        <div>
            <h1>Comprobante de presupuesto</h1>
            <p>Visualización e impresión del ticket.</p>
        </div>

        <div class="ticket-botones">

            <a href="index.php" class="btn-volver-ticket">
                Volver
            </a>

            <button
                type="button"
                class="btn-imprimir-ticket"
                onclick="window.print()"
            >
                Imprimir ticket
            </button>

        </div>

    </div>

    <section class="ticket-documento">

        <!-- CABECERA -->

        <div class="ticket-cabecera">

            <div class="ticket-logo">
                <h2>BUILDPRO</h2>
                <p>EMPRESA CONSTRUCTORA</p>
            </div>

            <div class="ticket-numero">

                <h3>PRESUPUESTO</h3>

                <p>
                    N.º <?= str_pad($idPresupuesto, 5, '0', STR_PAD_LEFT) ?>
                </p>

                <p>
                    Versión <?= $version ?>
                </p>

            </div>

        </div>

        <div class="ticket-titulo">

            <h2>COMPROBANTE DE PRESUPUESTO</h2>

            <p>
                Documento correspondiente al presupuesto registrado
                en BUILDPRO.
            </p>

        </div>

        <!-- DATOS -->

        <div class="ticket-datos">

            <div class="ticket-dato">
                <small>OBRA</small>
                <strong>
                    <?= eTicket($presupuesto['nombre_obra'] ?? '') ?>
                </strong>
            </div>

            <div class="ticket-dato">
                <small>FECHA DE EMISIÓN</small>
                <strong><?= eTicket($fecha) ?></strong>
            </div>

            <div class="ticket-dato">
                <small>NOMBRE DEL PRESUPUESTO</small>
                <strong>
                    <?= eTicket($presupuesto['nombre_presupuesto'] ?? 'Presupuesto General') ?>
                </strong>
            </div>

            <div class="ticket-dato">
                <small>ESTADO</small>
                <strong>
                    <?= eTicket($presupuesto['estado'] ?? 'Activo') ?>
                </strong>
            </div>

        </div>

        <!-- MATERIALES -->

        <h4>1. DETALLE DE MATERIALES</h4>

        <table class="ticket-tabla">

            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($materiales)): ?>

                    <tr>
                        <td colspan="5" class="ticket-vacio">
                            No hay materiales registrados.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($materiales as $m): ?>

                        <tr>

                            <td><?= eTicket($m['descripcion'] ?? '') ?></td>

                            <td>
                                <?= number_format(
                                    (float) $m['cantidad'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td><?= eTicket($m['unidad_medida'] ?? '') ?></td>

                            <td><?= dineroTicket($m['costo_unitario'] ?? 0) ?></td>

                            <td><?= dineroTicket($m['subtotal'] ?? 0) ?></td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

        <!-- MANO DE OBRA -->

        <h4>2. MANO DE OBRA Y TRABAJOS</h4>

        <table class="ticket-tabla">

            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Horas</th>
                    <th>Costo por hora</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($manoObra)): ?>

                    <tr>
                        <td colspan="4" class="ticket-vacio">
                            No hay trabajos registrados.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($manoObra as $trabajo): ?>

                        <tr>

                            <td>
                                <?= eTicket($trabajo['descripcion'] ?? '') ?>
                            </td>

                            <td>
                                <?= number_format(
                                    (float) $trabajo['cantidad_horas'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <?= dineroTicket($trabajo['costo_hora'] ?? 0) ?>
                            </td>

                            <td>
                                <?= dineroTicket($trabajo['subtotal'] ?? 0) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

        <!-- RESUMEN -->

        <h4>3. RESUMEN DE COSTOS</h4>

        <div class="ticket-resumen">

            <div class="linea">
                <span>Total materiales</span>
                <strong><?= dineroTicket($totalMateriales) ?></strong>
            </div>

            <div class="linea">
                <span>Total mano de obra</span>
                <strong><?= dineroTicket($totalManoObra) ?></strong>
            </div>

            <div class="linea">
                <span>Costo directo</span>
                <strong><?= dineroTicket($costoDirecto) ?></strong>
            </div>

            <div class="linea">
                <span>
                    Gastos de obra (<?= $porcGastosObra ?>%)
                </span>
                <strong><?= dineroTicket($gastosObra) ?></strong>
            </div>

            <div class="linea">
                <span>
                    Gastos de oficina (<?= $porcGastosOficina ?>%)
                </span>
                <strong><?= dineroTicket($gastosOficina) ?></strong>
            </div>

            <div class="linea">
                <span>
                    Imprevistos (<?= $porcImprevistos ?>%)
                </span>
                <strong><?= dineroTicket($imprevistos) ?></strong>
            </div>

            <div class="linea">
                <span>
                    Ganancia (<?= $porcGanancia ?>%)
                </span>
                <strong><?= dineroTicket($ganancia) ?></strong>
            </div>

            <div class="linea total">
                <span>TOTAL DEL PRESUPUESTO</span>
                <strong><?= dineroTicket($totalFinal) ?></strong>
            </div>

        </div>

        <!-- CONDICIONES -->

        <div class="ticket-condiciones">

            <strong>Condiciones del presupuesto</strong>

            <p>
                Validez:
                <?= (int) ($presupuesto['validez_dias'] ?? 15) ?>
                días.
            </p>

            <p>
                Forma de pago:
                <?= eTicket($presupuesto['forma_pago'] ?? 'A convenir') ?>
            </p>

            <p>
                Observaciones:
                <?= nl2br(eTicket($presupuesto['detalle_general'] ?? 'Sin observaciones.')) ?>
            </p>

            <?php if (!empty($presupuesto['clausula_redeterminacion'])): ?>

                <p>
                    Redeterminación:
                    <?= eTicket($presupuesto['clausula_redeterminacion']) ?>
                </p>

            <?php endif; ?>

        </div>

        <!-- FIRMAS -->

        <div class="ticket-firmas">

            <div class="ticket-firma">
                Firma y aclaración de la empresa
            </div>

            <div class="ticket-firma">
                Firma y aclaración del cliente
            </div>

        </div>

        <div class="ticket-pie">

            BUILDPRO - Sistema de Gestión Integral para Empresa Constructora

            <br>

            Comprobante de presupuesto N.º
            <?= str_pad($idPresupuesto, 5, '0', STR_PAD_LEFT) ?>

        </div>

    </section>

</main>

</body>
</html>
