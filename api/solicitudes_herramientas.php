<?php
/**
 * Solicitudes de herramientas al pañol.
 *  GET  ?catalogo=1           → herramientas con cuántas hay disponibles
 *  GET  ?id_obra=1            → solicitudes de la obra con su estado
 *  POST { id_obra, items: [{ id_herramienta, cantidad }], observacion }          → crea una solicitud
 *  POST { accion: 'responder', id_solicitud, estado, respuesta? }  (Jefe de Obra / Gerente)
 *       estado = Aprobada | Rechazada | Entregada. Al marcarla "Entregada" las herramientas
 *       quedan asignadas a la obra (tabla herramienta_obra) y aparecen en "Asignado a la obra".
 *
 * Requiere haber ejecutado api/migracion_v3.sql (crea las tablas solicitud_herramienta).
 */
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET', 'POST']);

$usuario = usuario_actual();
exigir_tabla('solicitud_herramienta');
exigir_tabla('detalle_solicitud_herramienta');

/** Unidades de una herramienta que están asignadas a alguna obra y todavía no volvieron. */
const SQL_EN_USO = 'SELECT IFNULL(SUM(GREATEST(cantidad_asignada - cantidad_devuelta, 0)), 0)
                    FROM herramienta_obra WHERE id_herramienta = ?';

/* ------------------------------ GET --------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Catálogo para elegir qué pedir.
    if (!empty($_GET['catalogo'])) {
        $filas = consultar(
            'SELECT h.id_herramienta, h.nombre, h.tipo, h.marca, h.modelo, h.cantidad_total,
                    IFNULL((SELECT SUM(GREATEST(ho.cantidad_asignada - ho.cantidad_devuelta, 0))
                            FROM herramienta_obra ho WHERE ho.id_herramienta = h.id_herramienta), 0) AS en_uso
             FROM herramienta h
             ORDER BY h.nombre ASC, h.marca ASC'
        );
        $herramientas = array_map(static function (array $f): array {
            $total = (int) $f['cantidad_total'];
            return [
                'id_herramienta' => (int) $f['id_herramienta'],
                'nombre' => $f['nombre'],
                'tipo' => $f['tipo'],
                'marca' => $f['marca'],
                'modelo' => $f['modelo'],
                'total' => $total,
                'disponibles' => max(0, $total - (int) $f['en_uso']),
            ];
        }, $filas);
        responder_ok(['herramientas' => $herramientas]);
    }

    $idObra = (int) ($_GET['id_obra'] ?? 0);
    exigir_acceso_obra($usuario, $idObra);
    $limite = max(1, min(60, (int) ($_GET['limite'] ?? 30)));

    // "respuesta" es opcional: se usa solo si la tabla tiene esa columna (ver migracion_v3.sql).
    $conRespuesta = columna_existe('solicitud_herramienta', 'respuesta');
    $solicitudes = consultar(
        "SELECT s.id_solicitud AS id, s.fecha, s.estado, s.observaciones AS observacion, "
        . ($conRespuesta ? 's.respuesta' : 'NULL') . " AS respuesta,
                TRIM(CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellido, ''))) AS solicitante
         FROM solicitud_herramienta s
         LEFT JOIN usuario u ON u.id_usuario = s.id_usuario
         WHERE s.id_obra = ?
         ORDER BY s.fecha DESC, s.id_solicitud DESC
         LIMIT ?",
        'ii',
        [$idObra, $limite]
    );

    $porId = [];
    foreach ($solicitudes as $s) {
        $porId[(int) $s['id']] = [
            'id' => (int) $s['id'],
            'tipo' => 'herramientas',
            'fecha' => $s['fecha'],
            'estado' => $s['estado'],
            'observacion' => $s['observacion'],
            'respuesta' => $s['respuesta'],
            'solicitante' => (string) $s['solicitante'],
            'items' => [],
        ];
    }

    if ($porId) {
        $ids = array_keys($porId);
        [$marcas, $tipos] = placeholders_in(count($ids));
        $items = consultar(
            "SELECT d.id_solicitud AS id, d.id_herramienta, h.nombre, h.marca, h.modelo, d.cantidad
             FROM detalle_solicitud_herramienta d
             INNER JOIN herramienta h ON h.id_herramienta = d.id_herramienta
             WHERE d.id_solicitud IN ($marcas)
             ORDER BY h.nombre",
            $tipos,
            $ids
        );
        foreach ($items as $i) {
            $detalle = trim(((string) $i['marca']) . ' ' . ((string) $i['modelo']));
            $porId[(int) $i['id']]['items'][] = [
                'id_herramienta' => (int) $i['id_herramienta'],
                'nombre' => $i['nombre'],
                'detalle' => $detalle,
                'unidad' => 'u.',
                'cantidad' => (float) $i['cantidad'],
                // Las herramientas se entregan completas o no se entregan: si ya está entregada (o devuelta después), llegó todo.
                'entregada' => in_array($porId[(int) $i['id']]['estado'], ['Entregada', 'Parcialmente devuelta', 'Devuelta'], true) ? (float) $i['cantidad'] : 0.0,
            ];
        }
    }

    responder_ok(['solicitudes' => array_values($porId)]);
}

/* ------------------------------ POST -------------------------------------- */
$datos = leer_json();

/* ---------------- Responder una solicitud (solo Jefe de Obra / Gerente) --- */
if (($datos['accion'] ?? '') === 'responder') {
    exigir_gestion($usuario);

    $idSolicitud = (int) ($datos['id_solicitud'] ?? 0);
    $estadoNuevo = (string) ($datos['estado'] ?? '');
    $respuesta = mb_substr(trim((string) ($datos['respuesta'] ?? '')), 0, 255);
    if ($idSolicitud <= 0 || !in_array($estadoNuevo, ['Aprobada', 'Rechazada', 'Entregada'], true)) {
        throw new ErrorValidacion('La respuesta no es válida.');
    }

    try {
        db()->begin_transaction();

        $sol = consultar_fila(
            'SELECT id_obra, estado FROM solicitud_herramienta WHERE id_solicitud = ? FOR UPDATE',
            'i',
            [$idSolicitud]
        );
        if ($sol === null) {
            throw new ErrorValidacion('La solicitud no existe.');
        }
        exigir_acceso_obra($usuario, (int) $sol['id_obra']);

        $estadoActual = (string) $sol['estado'];
        $permitidos = [
            'Pendiente' => ['Aprobada', 'Rechazada', 'Entregada'],
            'Aprobada' => ['Entregada', 'Rechazada'],
        ];
        if (!isset($permitidos[$estadoActual]) || !in_array($estadoNuevo, $permitidos[$estadoActual], true)) {
            throw new ErrorValidacion("Esta solicitud ya está $estadoActual y no se puede cambiar a $estadoNuevo.");
        }

        if ($estadoNuevo === 'Entregada') {
            $lineas = consultar(
                'SELECT d.id_herramienta, d.cantidad, h.nombre, h.cantidad_total
                 FROM detalle_solicitud_herramienta d
                 INNER JOIN herramienta h ON h.id_herramienta = d.id_herramienta
                 WHERE d.id_solicitud = ?
                 FOR UPDATE',
                'i',
                [$idSolicitud]
            );
            foreach ($lineas as $l) {
                $enUso = (int) consultar_valor(SQL_EN_USO, 'i', [(int) $l['id_herramienta']]);
                $disponibles = max(0, (int) $l['cantidad_total'] - $enUso);
                if ((int) $l['cantidad'] > $disponibles) {
                    throw new ErrorValidacion('No alcanzan las unidades de «' . $l['nombre'] . '»: se piden ' . (int) $l['cantidad'] . ' y hay ' . $disponibles . ' disponibles.');
                }
            }
            foreach ($lineas as $l) {
                ejecutar(
                    'INSERT INTO herramienta_obra (id_herramienta, cantidad_asignada, cantidad_devuelta, id_obra, fecha_asignacion, observaciones)
                     VALUES (?, ?, 0, ?, ?, ?)',
                    'iiiss',
                    [(int) $l['id_herramienta'], (int) $l['cantidad'], (int) $sol['id_obra'], date('Y-m-d'), 'Solicitud #' . $idSolicitud]
                );
            }
        }

        if (columna_existe('solicitud_herramienta', 'respuesta')) {
            ejecutar(
                'UPDATE solicitud_herramienta SET estado = ?, respuesta = ? WHERE id_solicitud = ?',
                'ssi',
                [$estadoNuevo, $respuesta !== '' ? $respuesta : null, $idSolicitud]
            );
        } else {
            ejecutar('UPDATE solicitud_herramienta SET estado = ? WHERE id_solicitud = ?', 'si', [$estadoNuevo, $idSolicitud]);
        }

        db()->commit();
    } catch (Throwable $e) {
        try {
            db()->rollback();
        } catch (Throwable $ignorado) {
        }
        throw $e;
    }

    responder_ok(['estado' => $estadoNuevo], $estadoNuevo === 'Entregada' ? 'Entrega registrada: las herramientas quedaron asignadas a la obra.' : "Solicitud $estadoNuevo.");
}

/* ---------------- Crear una solicitud ------------------------------------- */
$idObra = (int) ($datos['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);
$observacion = mb_substr(trim((string) ($datos['observacion'] ?? '')), 0, 500);

$crudos = $datos['items'] ?? [];
if (!is_array($crudos) || count($crudos) === 0) {
    throw new ErrorValidacion('Agregá al menos una herramienta a la solicitud.');
}
if (count($crudos) > 40) {
    throw new ErrorValidacion('Una solicitud puede tener hasta 40 herramientas.');
}

$items = [];
foreach ($crudos as $c) {
    $idHerramienta = (int) (is_array($c) ? ($c['id_herramienta'] ?? 0) : 0);
    if ($idHerramienta <= 0) {
        throw new ErrorValidacion('Hay una herramienta que no es válida.');
    }
    $cantidad = (int) cantidad_valida(is_array($c) ? ($c['cantidad'] ?? 0) : 0, 'La cantidad', 10000);
    if ($cantidad < 1) {
        throw new ErrorValidacion('La cantidad tiene que ser al menos 1.');
    }
    $items[$idHerramienta] = ($items[$idHerramienta] ?? 0) + $cantidad;
}

[$marcas, $tipos] = placeholders_in(count($items));
$existentes = [];
foreach (consultar("SELECT id_herramienta, nombre, cantidad_total FROM herramienta WHERE id_herramienta IN ($marcas)", $tipos, array_keys($items)) as $h) {
    $existentes[(int) $h['id_herramienta']] = $h;
}
foreach ($items as $idHerramienta => $cantidad) {
    if (!isset($existentes[$idHerramienta])) {
        throw new ErrorValidacion('Alguna herramienta ya no está en el catálogo. Actualizá la lista e intentá de nuevo.');
    }
    if ($cantidad > (int) $existentes[$idHerramienta]['cantidad_total']) {
        throw new ErrorValidacion('Pediste más unidades de «' . $existentes[$idHerramienta]['nombre'] . '» de las que existen (' . (int) $existentes[$idHerramienta]['cantidad_total'] . ').');
    }
}

try {
    db()->begin_transaction();

    ejecutar(
        "INSERT INTO solicitud_herramienta (id_obra, id_usuario, fecha, estado, observaciones) VALUES (?, ?, ?, 'Pendiente', ?)",
        'iiss',
        [$idObra, $usuario['id_usuario'], date('Y-m-d H:i:s'), $observacion !== '' ? $observacion : null]
    );
    $idSolicitud = (int) db()->insert_id;

    foreach ($items as $idHerramienta => $cantidad) {
        ejecutar(
            'INSERT INTO detalle_solicitud_herramienta (id_solicitud, id_herramienta, cantidad) VALUES (?, ?, ?)',
            'iii',
            [$idSolicitud, $idHerramienta, $cantidad]
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

responder_ok(['id_solicitud' => $idSolicitud], 'Solicitud enviada. Queda pendiente de aprobación.');
