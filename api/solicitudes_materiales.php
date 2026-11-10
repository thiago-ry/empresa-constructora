<?php
/**
 * Solicitudes de materiales al depósito.
 *  GET  ?id_obra=1            → solicitudes de la obra con su estado, sus ítems y lo ya entregado
 *  POST { id_obra, items: [{ id_material, cantidad }], observacion }  → crea una solicitud "Pendiente"
 *
 * Usa las tablas del panel web (solicitud_material, detalle_solicitud_material, entrega_material):
 * el depósito aprueba, rechaza y entrega desde el panel, y acá se ve el resultado.
 */
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET', 'POST']);

$usuario = usuario_actual();

/* ------------------------------ GET: lista -------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idObra = (int) ($_GET['id_obra'] ?? 0);
    exigir_acceso_obra($usuario, $idObra);
    $limite = max(1, min(60, (int) ($_GET['limite'] ?? 30)));

    $conObservacion = columna_existe('solicitud_material', 'observacion');
    $conSolicitante = columna_existe('solicitud_material', 'id_usuario');

    $sql = 'SELECT s.id_solicitud, s.fecha, s.estado, '
        . ($conObservacion ? 's.observacion' : 'NULL') . ' AS observacion, '
        . ($conSolicitante ? "TRIM(CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellido, '')))" : "''") . ' AS solicitante
         FROM solicitud_material s '
        . ($conSolicitante ? 'LEFT JOIN usuario u ON u.id_usuario = s.id_usuario ' : '')
        . 'WHERE s.id_obra = ?
         ORDER BY s.fecha DESC, s.id_solicitud DESC
         LIMIT ?';
    $solicitudes = consultar($sql, 'ii', [$idObra, $limite]);

    $porId = [];
    foreach ($solicitudes as $s) {
        $porId[(int) $s['id_solicitud']] = [
            'id' => (int) $s['id_solicitud'],
            'tipo' => 'materiales',
            'fecha' => $s['fecha'],
            'estado' => $s['estado'] ?? 'Pendiente',
            'observacion' => $s['observacion'],
            'respuesta' => null,
            'solicitante' => (string) $s['solicitante'],
            'items' => [],
        ];
    }

    if ($porId) {
        $ids = array_keys($porId);
        [$marcas, $tipos] = placeholders_in(count($ids));

        $items = consultar(
            "SELECT d.id_solicitud, d.id_material, m.nombre_material, m.unidad_medida, d.cantidad
             FROM detalle_solicitud_material d
             INNER JOIN material m ON m.id_material = d.id_material
             WHERE d.id_solicitud IN ($marcas)
             ORDER BY m.nombre_material",
            $tipos,
            $ids
        );

        // Lo que el depósito ya entregó, por solicitud y material.
        $entregado = [];
        foreach (consultar(
            "SELECT e.id_solicitud, de.id_material, SUM(de.cantidad_entregada) AS total
             FROM entrega_material e
             INNER JOIN detalle_entrega_material de ON de.id_entrega = e.id_entrega
             WHERE e.id_solicitud IN ($marcas)
             GROUP BY e.id_solicitud, de.id_material",
            $tipos,
            $ids
        ) as $e) {
            $entregado[(int) $e['id_solicitud'] . '-' . (int) $e['id_material']] = (float) $e['total'];
        }

        foreach ($items as $i) {
            $clave = (int) $i['id_solicitud'] . '-' . (int) $i['id_material'];
            $porId[(int) $i['id_solicitud']]['items'][] = [
                'id_material' => (int) $i['id_material'],
                'nombre' => $i['nombre_material'],
                'unidad' => (string) $i['unidad_medida'],
                'cantidad' => (float) $i['cantidad'],
                'entregada' => $entregado[$clave] ?? 0.0,
            ];
        }
    }

    responder_ok(['solicitudes' => array_values($porId)]);
}

/* ------------------------------ POST: crear ------------------------------- */
$datos = leer_json();
$idObra = (int) ($datos['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);
$observacion = mb_substr(trim((string) ($datos['observacion'] ?? '')), 0, 500);

$crudos = $datos['items'] ?? [];
if (!is_array($crudos) || count($crudos) === 0) {
    throw new ErrorValidacion('Agregá al menos un material a la solicitud.');
}
if (count($crudos) > 40) {
    throw new ErrorValidacion('Una solicitud puede tener hasta 40 materiales.');
}

// Se juntan los repetidos y se valida cada cantidad.
$items = [];
foreach ($crudos as $c) {
    $idMaterial = (int) (is_array($c) ? ($c['id_material'] ?? 0) : 0);
    if ($idMaterial <= 0) {
        throw new ErrorValidacion('Hay un material que no es válido.');
    }
    $cantidad = cantidad_valida(is_array($c) ? ($c['cantidad'] ?? 0) : 0, 'La cantidad');
    $items[$idMaterial] = ($items[$idMaterial] ?? 0.0) + $cantidad;
}

[$marcas, $tipos] = placeholders_in(count($items));
$existentes = consultar("SELECT id_material FROM material WHERE estado = 1 AND id_material IN ($marcas)", $tipos, array_keys($items));
if (count($existentes) !== count($items)) {
    throw new ErrorValidacion('Algún material ya no está disponible en el catálogo. Actualizá la lista e intentá de nuevo.');
}

$campos = [
    'id_obra' => ['i', $idObra],
    'fecha' => ['s', date('Y-m-d')],
    'estado' => ['s', 'Pendiente'],
];
if (columna_existe('solicitud_material', 'id_usuario')) {
    $campos['id_usuario'] = ['i', $usuario['id_usuario']];
}
if (columna_existe('solicitud_material', 'observacion')) {
    $campos['observacion'] = ['s', $observacion];
}

try {
    db()->begin_transaction();

    ejecutar(
        'INSERT INTO solicitud_material (' . implode(', ', array_keys($campos)) . ') VALUES (' . implode(', ', array_fill(0, count($campos), '?')) . ')',
        implode('', array_column($campos, 0)),
        array_column($campos, 1)
    );
    $idSolicitud = (int) db()->insert_id;

    foreach ($items as $idMaterial => $cantidad) {
        ejecutar(
            'INSERT INTO detalle_solicitud_material (id_solicitud, id_material, cantidad) VALUES (?, ?, ?)',
            'iid',
            [$idSolicitud, $idMaterial, $cantidad]
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

responder_ok(['id_solicitud' => $idSolicitud], 'Solicitud enviada. Queda pendiente de aprobación del depósito.');
