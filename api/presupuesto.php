<?php
/**
 * Cálculo y guardado de presupuestos de obra.
 *  GET  ?id_obra=1&catalogo=1                 → materiales con precio de referencia (cualquier rol)
 *  GET  ?id_obra=1                            → presupuestos guardados de la obra (Jefe de Obra / Gerente)
 *  GET  ?id_obra=1&id_presupuesto=5           → un presupuesto con su detalle (Jefe de Obra / Gerente)
 *  POST { id_obra, items, mano_obra, imprevistos_pct, detalle_general }  → guarda uno nuevo (Jefe de Obra / Gerente)
 *
 * El servidor recalcula todos los subtotales: nunca confía en los totales que manda la app.
 * Usa las tablas del panel web: presupuesto, detalle_presupuesto y mano_obra_presupuesto.
 */
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET', 'POST']);

$usuario = usuario_actual();

/* ------------------------------ GET --------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idObra = (int) ($_GET['id_obra'] ?? 0);
    exigir_acceso_obra($usuario, $idObra);

    // Catálogo con precio de referencia: el más bajo que tiene cargado cualquiera de los proveedores.
    if (!empty($_GET['catalogo'])) {
        $filas = consultar(
            'SELECT m.id_material, m.nombre_material, m.unidad_medida, MIN(p.precio) AS precio
             FROM material m
             LEFT JOIN precio_material p ON p.id_material = m.id_material
             WHERE m.estado = 1
             GROUP BY m.id_material, m.nombre_material, m.unidad_medida
             ORDER BY m.nombre_material ASC'
        );
        $materiales = array_map(static fn(array $f): array => [
            'id_material' => (int) $f['id_material'],
            'nombre' => $f['nombre_material'],
            'unidad' => (string) $f['unidad_medida'],
            'precio' => $f['precio'] !== null ? (float) $f['precio'] : null,
        ], $filas);
        responder_ok(['materiales' => $materiales]);
    }

    // Los presupuestos guardados son datos de gestión.
    exigir_gestion($usuario);

    $idPresupuesto = (int) ($_GET['id_presupuesto'] ?? 0);
    if ($idPresupuesto > 0) {
        $p = consultar_fila(
            'SELECT id_presupuesto, fecha, version, costo_total, estado, detalle_general
             FROM presupuesto WHERE id_presupuesto = ? AND id_obra = ?',
            'ii',
            [$idPresupuesto, $idObra]
        );
        if ($p === null) {
            responder_error('El presupuesto no existe.', 404);
        }
        $materiales = consultar(
            'SELECT d.descripcion, d.cantidad, d.costo_unitario, d.subtotal, m.nombre_material, m.unidad_medida
             FROM detalle_presupuesto d
             LEFT JOIN material m ON m.id_material = d.id_material
             WHERE d.id_presupuesto = ?
             ORDER BY d.id_detalle',
            'i',
            [$idPresupuesto]
        );
        $manoObra = consultar(
            'SELECT descripcion, cantidad_horas, costo_hora, subtotal
             FROM mano_obra_presupuesto WHERE id_presupuesto = ? ORDER BY id_mano_obra_pres',
            'i',
            [$idPresupuesto]
        );
        responder_ok([
            'presupuesto' => [
                'id_presupuesto' => (int) $p['id_presupuesto'],
                'fecha' => $p['fecha'],
                'version' => (int) $p['version'],
                'costo_total' => (float) $p['costo_total'],
                'estado' => $p['estado'],
                'detalle_general' => $p['detalle_general'],
                'materiales' => array_map(static fn(array $d): array => [
                    'descripcion' => $d['nombre_material'] !== null && trim((string) $d['descripcion']) === '' ? $d['nombre_material'] : (string) $d['descripcion'],
                    'unidad' => (string) $d['unidad_medida'],
                    'cantidad' => (float) $d['cantidad'],
                    'costo_unitario' => (float) $d['costo_unitario'],
                    'subtotal' => (float) $d['subtotal'],
                ], $materiales),
                'mano_obra' => array_map(static fn(array $d): array => [
                    'descripcion' => $d['descripcion'],
                    'horas' => (float) $d['cantidad_horas'],
                    'costo_hora' => (float) $d['costo_hora'],
                    'subtotal' => (float) $d['subtotal'],
                ], $manoObra),
            ],
        ]);
    }

    $filas = consultar(
        'SELECT id_presupuesto, fecha, version, costo_total, estado
         FROM presupuesto WHERE id_obra = ?
         ORDER BY version DESC, id_presupuesto DESC LIMIT 20',
        'i',
        [$idObra]
    );
    responder_ok(['presupuestos' => array_map(static fn(array $f): array => [
        'id_presupuesto' => (int) $f['id_presupuesto'],
        'fecha' => $f['fecha'],
        'version' => (int) $f['version'],
        'costo_total' => (float) $f['costo_total'],
        'estado' => $f['estado'],
    ], $filas)]);
}

/* ------------------------------ POST: guardar ----------------------------- */
exigir_gestion($usuario);

$datos = leer_json();
$idObra = (int) ($datos['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);

$crudosItems = is_array($datos['items'] ?? null) ? $datos['items'] : [];
$crudosMano = is_array($datos['mano_obra'] ?? null) ? $datos['mano_obra'] : [];
if (count($crudosItems) + count($crudosMano) === 0) {
    throw new ErrorValidacion('El presupuesto está vacío: agregá materiales o mano de obra.');
}
if (count($crudosItems) > 100 || count($crudosMano) > 50) {
    throw new ErrorValidacion('El presupuesto tiene demasiadas líneas.');
}

$imprevistosPct = (float) ($datos['imprevistos_pct'] ?? 0);
if (!is_finite($imprevistosPct) || $imprevistosPct < 0 || $imprevistosPct > 100) {
    throw new ErrorValidacion('El porcentaje de imprevistos tiene que estar entre 0 y 100.');
}
$notas = mb_substr(trim((string) ($datos['detalle_general'] ?? '')), 0, 1000);

/** Costo unitario: puede ser 0 (material sin precio cargado) pero no negativo. */
function costo_valido($valor, string $nombreCampo): float
{
    $n = is_numeric($valor) ? (float) $valor : -1.0;
    if (!is_finite($n) || $n < 0) {
        throw new ErrorValidacion("$nombreCampo no es válido.");
    }
    if ($n > 1000000000) {
        throw new ErrorValidacion("$nombreCampo es demasiado grande.");
    }
    return $n;
}

// Nombres de los materiales elegidos (si la línea no trae descripción propia).
$nombres = [];
$idsMaterial = [];
foreach ($crudosItems as $c) {
    $idM = (int) (is_array($c) ? ($c['id_material'] ?? 0) : 0);
    if ($idM > 0) {
        $idsMaterial[$idM] = true;
    }
}
if ($idsMaterial) {
    [$marcas, $tipos] = placeholders_in(count($idsMaterial));
    foreach (consultar("SELECT id_material, nombre_material FROM material WHERE id_material IN ($marcas)", $tipos, array_keys($idsMaterial)) as $m) {
        $nombres[(int) $m['id_material']] = $m['nombre_material'];
    }
}

$lineasMateriales = [];
$totalMateriales = 0.0;
foreach ($crudosItems as $c) {
    if (!is_array($c)) {
        throw new ErrorValidacion('Hay una línea de material inválida.');
    }
    $idM = (int) ($c['id_material'] ?? 0);
    if ($idM > 0 && !isset($nombres[$idM])) {
        throw new ErrorValidacion('Algún material ya no existe en el catálogo.');
    }
    $descripcion = mb_substr(trim((string) ($c['descripcion'] ?? '')), 0, 255);
    if ($descripcion === '' && $idM > 0) {
        $descripcion = $nombres[$idM];
    }
    if ($descripcion === '') {
        throw new ErrorValidacion('Cada línea de material necesita una descripción.');
    }
    $cant = cantidad_valida($c['cantidad'] ?? 0, 'La cantidad de «' . $descripcion . '»', 100000000);
    $costo = costo_valido($c['costo_unitario'] ?? 0, 'El costo de «' . $descripcion . '»');
    $subtotal = round($cant * $costo, 2);
    $totalMateriales += $subtotal;
    $lineasMateriales[] = ['id_material' => $idM > 0 ? $idM : null, 'descripcion' => $descripcion, 'cantidad' => $cant, 'costo' => $costo, 'subtotal' => $subtotal];
}

$lineasMano = [];
$totalMano = 0.0;
foreach ($crudosMano as $c) {
    if (!is_array($c)) {
        throw new ErrorValidacion('Hay una línea de mano de obra inválida.');
    }
    $descripcion = mb_substr(trim((string) ($c['descripcion'] ?? '')), 0, 255);
    if ($descripcion === '') {
        throw new ErrorValidacion('Cada línea de mano de obra necesita una descripción (por ejemplo, "Albañil").');
    }
    $horas = cantidad_valida($c['horas'] ?? 0, 'Las horas de «' . $descripcion . '»', 1000000);
    $costoHora = costo_valido($c['costo_hora'] ?? 0, 'El costo por hora de «' . $descripcion . '»');
    $subtotal = round($horas * $costoHora, 2);
    $totalMano += $subtotal;
    $lineasMano[] = ['descripcion' => $descripcion, 'horas' => $horas, 'costo_hora' => $costoHora, 'subtotal' => $subtotal];
}

// Los imprevistos se guardan como una línea más del detalle, así el total siempre coincide con la suma de las líneas.
$imprevistos = round(($totalMateriales + $totalMano) * $imprevistosPct / 100, 2);
$total = round($totalMateriales + $totalMano + $imprevistos, 2);

try {
    db()->begin_transaction();

    $version = (int) consultar_valor('SELECT IFNULL(MAX(version), 0) + 1 FROM presupuesto WHERE id_obra = ? FOR UPDATE', 'i', [$idObra]);
    $nota = trim('Calculado desde la app. ' . $notas);

    ejecutar(
        "INSERT INTO presupuesto (id_obra, fecha, version, costo_total, estado, detalle_general) VALUES (?, NOW(), ?, ?, 'Activo', ?)",
        'iids',
        [$idObra, $version, $total, $nota]
    );
    $idPresupuesto = (int) db()->insert_id;

    foreach ($lineasMateriales as $l) {
        ejecutar(
            'INSERT INTO detalle_presupuesto (id_presupuesto, id_material, descripcion, cantidad, costo_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)',
            'iisddd',
            [$idPresupuesto, $l['id_material'], $l['descripcion'], $l['cantidad'], $l['costo'], $l['subtotal']]
        );
    }
    if ($imprevistos > 0) {
        ejecutar(
            'INSERT INTO detalle_presupuesto (id_presupuesto, id_material, descripcion, cantidad, costo_unitario, subtotal) VALUES (?, NULL, ?, 1, ?, ?)',
            'isdd',
            [$idPresupuesto, 'Imprevistos (' . rtrim(rtrim(number_format($imprevistosPct, 2, '.', ''), '0'), '.') . '%)', $imprevistos, $imprevistos]
        );
    }
    foreach ($lineasMano as $l) {
        ejecutar(
            'INSERT INTO mano_obra_presupuesto (id_presupuesto, descripcion, cantidad_horas, costo_hora, subtotal) VALUES (?, ?, ?, ?, ?)',
            'isddd',
            [$idPresupuesto, $l['descripcion'], $l['horas'], $l['costo_hora'], $l['subtotal']]
        );
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
    ['id_presupuesto' => $idPresupuesto, 'version' => $version, 'costo_total' => $total, 'imprevistos' => $imprevistos],
    'Presupuesto guardado (versión ' . $version . ').'
);
