<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET', 'POST']);

$usuario = usuario_actual();
$tieneObra = columna_existe('movimiento_material', 'id_obra');

/* ------------------------------ GET: catálogo ----------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ?historial=1&id_obra=1 → últimos movimientos de stock cargados en esa obra
    if (!empty($_GET['historial'])) {
        $idObra = (int) ($_GET['id_obra'] ?? 0);
        exigir_acceso_obra($usuario, $idObra);
        $limite = max(1, min(50, (int) ($_GET['limite'] ?? 10)));

        // Sin la columna id_obra (migración pendiente) no hay forma de saber de qué obra es cada movimiento:
        // es mejor no mostrar nada que mostrar movimientos de otras obras.
        $movimientos = [];
        if ($tieneObra) {
            $movimientos = consultar(
                "SELECT mm.tipo, mm.cantidad, mm.fecha, mm.observacion, m.nombre_material, m.unidad_medida,
                        TRIM(CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellido, ''))) AS usuario
                 FROM movimiento_material mm
                 INNER JOIN material m ON m.id_material = mm.id_material
                 LEFT JOIN usuario u ON u.id_usuario = mm.id_usuario
                 WHERE mm.id_obra = ?
                 ORDER BY mm.fecha DESC, mm.id_movimiento DESC
                 LIMIT ?",
                'ii',
                [$idObra, $limite]
            );
        }
        foreach ($movimientos as &$mov) {
            $mov['cantidad'] = (float) $mov['cantidad'];
        }
        unset($mov);
        responder_ok(['movimientos' => $movimientos]);
    }

    $materiales = consultar(
        'SELECT id_material, nombre_material, unidad_medida, stock AS stock_actual, stock_minimo
         FROM material WHERE estado = 1 ORDER BY nombre_material ASC'
    );
    foreach ($materiales as &$m) {
        $m['id_material'] = (int) $m['id_material'];
        $m['stock_actual'] = (float) $m['stock_actual'];
        $m['stock_minimo'] = (float) $m['stock_minimo'];
    }
    unset($m);
    responder_ok(['materiales' => $materiales]);
}

/* ------------------------------ POST: movimiento -------------------------- */
$datos = leer_json();
$idObra = (int) ($datos['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);
$idMaterial = (int) ($datos['id_material'] ?? 0);
$tipo = (string) ($datos['tipo'] ?? '');
$cantidad = (float) ($datos['cantidad'] ?? 0);
$observacion = mb_substr(trim((string) ($datos['observacion'] ?? '')), 0, 500);

if ($idMaterial <= 0) {
    throw new ErrorValidacion('Elegí un material.');
}
if (!in_array($tipo, ['EGRESO', 'INGRESO'], true)) {
    throw new ErrorValidacion('El tipo de movimiento no es válido.');
}
if (!is_finite($cantidad) || $cantidad <= 0) {
    throw new ErrorValidacion('Ingresá una cantidad mayor a cero.');
}

/*
 * 'consumo_descuenta_deposito' (config.php):
 *   true  = el consumo en obra también resta del stock del depósito (comportamiento histórico de la app).
 *   false = el consumo solo resta de lo que la obra recibió: el stock del depósito ya bajó cuando
 *           el depósito hizo la entrega, así que restarlo de nuevo lo contaría dos veces.
 */
$descuentaDeposito = (bool) config('consumo_descuenta_deposito', true);
$esConsumo = $tipo === 'EGRESO';
if ($esConsumo && !$descuentaDeposito && !$tieneObra) {
    throw new ErrorValidacion('Falta ejecutar api/migracion_v3.sql para registrar consumos por obra.');
}

try {
    db()->begin_transaction();

    // FOR UPDATE bloquea la fila: dos consumos simultáneos no pueden pasarse del stock.
    $material = consultar_fila('SELECT stock FROM material WHERE id_material = ? AND estado = 1 FOR UPDATE', 'i', [$idMaterial]);
    if ($material === null) {
        throw new ErrorValidacion('El material no existe o está dado de baja.');
    }
    $stockActual = (float) $material['stock'];

    if ($esConsumo) {
        if ($descuentaDeposito) {
            if ($stockActual < $cantidad) {
                throw new ErrorValidacion('Stock insuficiente. Disponible: ' . rtrim(rtrim(number_format($stockActual, 2, '.', ''), '0'), '.'));
            }
        } else {
            $enObra = material_disponible_en_obra($idObra, $idMaterial);
            if ($enObra < $cantidad) {
                throw new ErrorValidacion('La obra no tiene tanto material. Disponible en obra: ' . rtrim(rtrim(number_format(max(0, $enObra), 2, '.', ''), '0'), '.'));
            }
        }
    }

    if ($tieneObra) {
        ejecutar(
            'INSERT INTO movimiento_material (id_material, id_usuario, id_obra, tipo, cantidad, fecha, observacion) VALUES (?, ?, ?, ?, ?, NOW(), ?)',
            'iiisds',
            [$idMaterial, $usuario['id_usuario'], $idObra, $tipo, $cantidad, $observacion]
        );
    } else {
        ejecutar(
            'INSERT INTO movimiento_material (id_material, id_usuario, tipo, cantidad, fecha, observacion) VALUES (?, ?, ?, ?, NOW(), ?)',
            'iisds',
            [$idMaterial, $usuario['id_usuario'], $tipo, $cantidad, $observacion]
        );
    }

    $nuevoStock = $stockActual;
    if ($tipo === 'INGRESO') {
        $nuevoStock = $stockActual + $cantidad;
    } elseif ($descuentaDeposito) {
        $nuevoStock = $stockActual - $cantidad;
    }
    if ($nuevoStock !== $stockActual) {
        ejecutar('UPDATE material SET stock = ? WHERE id_material = ?', 'di', [$nuevoStock, $idMaterial]);
    }

    db()->commit();
} catch (Throwable $e) {
    try {
        db()->rollback();
    } catch (Throwable $ignorado) {
    }
    throw $e;
}

responder_ok(
    ['stock_actual' => $nuevoStock],
    $tipo === 'INGRESO' ? 'Ingreso registrado.' : 'Consumo registrado.'
);
