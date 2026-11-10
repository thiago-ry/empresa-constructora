
<?php
$presupuestos = $presupuestos ?? [];
$urlCrear = $urlCrear ?? '';
$urlVer = $urlVer ?? '';
?>

<div class="presupuesto-contenedor">
    <header class="presupuesto-header">
        <div>
            <h1>Presupuestos</h1>
            <p>Consultá los presupuestos registrados para las obras.</p>
        </div>

        <a class="btn-guardar"
           style="text-decoration:none"
           href="<?= htmlspecialchars($urlCrear, ENT_QUOTES, 'UTF-8') ?>">
            + Nuevo presupuesto
        </a>
    </header>

    <section class="panel-presupuesto">
        <div class="tabla-scroll">
            <table>
                <thead>
                    <tr>
                        <th>N.º</th>
                        <th>Obra</th>
                        <th>Fecha</th>
                        <th>Versión</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$presupuestos): ?>
                    <tr>
                        <td colspan="7">Todavía no hay presupuestos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($presupuestos as $p): ?>
                        <tr>
                            <td><?= (int) $p['id_presupuesto'] ?></td>
                            <td><?= htmlspecialchars($p['nombre_obra'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($p['fecha'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int) $p['version'] ?></td>
                            <td>
                                $<?= number_format((float) $p['costo_total'], 2, ',', '.') ?>
                            </td>
                           <td><?= htmlspecialchars($p['estado'], ENT_QUOTES, 'UTF-8') ?></td>

<td>
    <a
        href="ver.php?id=<?= (int) $p['id_presupuesto'] ?>"
        class="btn btn-outline"
    >
        Ver detalles
    </a>
</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
