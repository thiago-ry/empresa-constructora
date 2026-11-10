<?php
/**
 * Materiales asignados a la obra: lo que el depósito le entregó, lo que ya se consumió
 * (consumos cargados desde la app) y lo que queda disponible en obra.
 *
 * GET ?id_obra=1
 */
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET']);

$usuario = usuario_actual();
$idObra = (int) ($_GET['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);

$materiales = [];

// Entregado por el depósito (entregas hechas a solicitudes de esta obra).
foreach (consultar(
    "SELECT m.id_material, m.nombre_material, m.unidad_medida, SUM(de.cantidad_entregada) AS entregado,
            MAX(e.fecha) AS ultima_entrega
     FROM entrega_material e
     INNER JOIN solicitud_material s ON s.id_solicitud = e.id_solicitud
     INNER JOIN detalle_entrega_material de ON de.id_entrega = e.id_entrega
     INNER JOIN material m ON m.id_material = de.id_material
     WHERE s.id_obra = ?
     GROUP BY m.id_material, m.nombre_material, m.unidad_medida",
    'i',
    [$idObra]
) as $f) {
    $materiales[(int) $f['id_material']] = [
        'id_material' => (int) $f['id_material'],
        'nombre' => $f['nombre_material'],
        'unidad' => (string) $f['unidad_medida'],
        'entregado' => (float) $f['entregado'],
        'consumido' => 0.0,
        'ultima_entrega' => $f['ultima_entrega'],
    ];
}

// Consumido en la obra (solo se puede saber si la migración agregó movimiento_material.id_obra).
$consumoRegistrado = columna_existe('movimiento_material', 'id_obra');
if ($consumoRegistrado) {
    foreach (consultar(
        "SELECT m.id_material, m.nombre_material, m.unidad_medida, SUM(mm.cantidad) AS consumido
         FROM movimiento_material mm
         INNER JOIN material m ON m.id_material = mm.id_material
         WHERE mm.id_obra = ? AND mm.tipo = 'EGRESO'
         GROUP BY m.id_material, m.nombre_material, m.unidad_medida",
        'i',
        [$idObra]
    ) as $f) {
        $id = (int) $f['id_material'];
        if (!isset($materiales[$id])) {
            $materiales[$id] = [
                'id_material' => $id,
                'nombre' => $f['nombre_material'],
                'unidad' => (string) $f['unidad_medida'],
                'entregado' => 0.0,
                'consumido' => 0.0,
                'ultima_entrega' => null,
            ];
        }
        $materiales[$id]['consumido'] = (float) $f['consumido'];
    }
}

$lista = array_values($materiales);
foreach ($lista as &$m) {
    $m['disponible'] = max(0.0, $m['entregado'] - $m['consumido']);
}
unset($m);
usort($lista, static fn(array $a, array $b): int => strcasecmp($a['nombre'], $b['nombre']));

responder_ok(['materiales' => $lista, 'consumo_registrado' => $consumoRegistrado]);
