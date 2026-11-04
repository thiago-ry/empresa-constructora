<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET', 'POST']);

/** Estado "Devuelta" en la tabla estado_herramienta. */
const ESTADO_HERRAMIENTA_DEVUELTA = 5;

$usuario = usuario_actual();

/* ------------------------------ GET --------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idObra = (int) ($_GET['id_obra'] ?? 0);
    exigir_acceso_obra($usuario, $idObra);

    // ?historial=1 → últimas devoluciones registradas en la obra
    if (!empty($_GET['historial'])) {
        $limite = max(1, min(50, (int) ($_GET['limite'] ?? 10)));
        $devoluciones = consultar(
            "SELECT d.cantidad, d.fecha_devolucion AS fecha, d.observaciones, h.nombre,
                    TRIM(CONCAT(IFNULL(u.nombre, ''), ' ', IFNULL(u.apellido, ''))) AS usuario
             FROM devolucion_herramienta d
             INNER JOIN herramienta_obra ho ON ho.id_herramienta_obra = d.id_herramienta_obra
             INNER JOIN herramienta h ON h.id_herramienta = ho.id_herramienta
             LEFT JOIN usuario u ON u.id_usuario = d.id_usuario
             WHERE ho.id_obra = ?
             ORDER BY d.fecha_devolucion DESC, d.id_devolucion DESC
             LIMIT ?",
            'ii',
            [$idObra, $limite]
        );
        foreach ($devoluciones as &$dev) {
            $dev['cantidad'] = (int) $dev['cantidad'];
        }
        unset($dev);
        responder_ok(['devoluciones' => $devoluciones]);
    }

    // ?asignadas=1 → todo lo que se asignó alguna vez a la obra (lo que sigue acá y lo ya devuelto).
    if (!empty($_GET['asignadas'])) {
        $filas = consultar(
            "SELECT ho.id_herramienta_obra, h.nombre, h.tipo, h.marca, h.modelo,
                    ho.cantidad_asignada, ho.cantidad_devuelta, ho.fecha_asignacion, ho.fecha_devolucion,
                    IFNULL(e.nombre, '') AS estado
             FROM herramienta_obra ho
             INNER JOIN herramienta h ON h.id_herramienta = ho.id_herramienta
             LEFT JOIN estado_herramienta e ON e.id_estado_herramienta = ho.id_estado_herramienta
             WHERE ho.id_obra = ?
             ORDER BY (ho.cantidad_asignada > ho.cantidad_devuelta) DESC, ho.fecha_asignacion DESC, h.nombre ASC
             LIMIT 200",
            'i',
            [$idObra]
        );
        $asignadas = array_map(static function (array $f): array {
            $asig = (int) $f['cantidad_asignada'];
            $devueltas = (int) $f['cantidad_devuelta'];
            return [
                'id_herramienta_obra' => (int) $f['id_herramienta_obra'],
                'nombre' => $f['nombre'],
                'tipo' => $f['tipo'],
                'marca' => $f['marca'],
                'modelo' => $f['modelo'],
                'asignadas' => $asig,
                'devueltas' => $devueltas,
                'pendientes' => max(0, $asig - $devueltas),
                'fecha_asignacion' => $f['fecha_asignacion'],
                'fecha_devolucion' => $f['fecha_devolucion'],
                'estado' => $f['estado'],
            ];
        }, $filas);
        responder_ok(['herramientas' => $asignadas]);
    }

    // Herramientas asignadas a la obra que todavía no volvieron al pañol.
    $filas = consultar(
        'SELECT ho.id_herramienta_obra, h.nombre, h.tipo, h.marca, h.modelo,
                ho.cantidad_asignada, ho.cantidad_devuelta, ho.fecha_asignacion
         FROM herramienta_obra ho
         INNER JOIN herramienta h ON h.id_herramienta = ho.id_herramienta
         WHERE ho.id_obra = ? AND ho.cantidad_asignada > ho.cantidad_devuelta
         ORDER BY h.nombre ASC, ho.fecha_asignacion DESC',
        'i',
        [$idObra]
    );
    $herramientas = array_map(static fn(array $f): array => [
        'id_herramienta_obra' => (int) $f['id_herramienta_obra'],
        'nombre' => $f['nombre'],
        'tipo' => $f['tipo'],
        'marca' => $f['marca'],
        'modelo' => $f['modelo'],
        'asignadas' => (int) $f['cantidad_asignada'],
        'devueltas' => (int) $f['cantidad_devuelta'],
        'pendientes' => (int) $f['cantidad_asignada'] - (int) $f['cantidad_devuelta'],
        'fecha_asignacion' => $f['fecha_asignacion'],
    ], $filas);

    responder_ok(['herramientas' => $herramientas]);
}

/* ------------------------------ POST: devolución -------------------------- */
$datos = leer_json();
$idAsignacion = (int) ($datos['id_herramienta_obra'] ?? 0);
$cantidad = (int) ($datos['cantidad'] ?? 0);
$observaciones = mb_substr(trim((string) ($datos['observaciones'] ?? '')), 0, 500);

if ($idAsignacion <= 0 || $cantidad <= 0) {
    throw new ErrorValidacion('Elegí una herramienta e indicá una cantidad válida.');
}

try {
    db()->begin_transaction();

    // Se bloquea la fila para que dos devoluciones simultáneas no superen lo asignado.
    $asignacion = consultar_fila(
        'SELECT id_obra, cantidad_asignada, cantidad_devuelta FROM herramienta_obra WHERE id_herramienta_obra = ? FOR UPDATE',
        'i',
        [$idAsignacion]
    );
    if ($asignacion === null) {
        throw new ErrorValidacion('La herramienta no está asignada a ninguna obra.');
    }
    exigir_acceso_obra($usuario, (int) $asignacion['id_obra']);

    $pendientes = (int) $asignacion['cantidad_asignada'] - (int) $asignacion['cantidad_devuelta'];
    if ($cantidad > $pendientes) {
        throw new ErrorValidacion($pendientes > 0 ? "Solo quedan $pendientes por devolver." : 'Esta herramienta ya fue devuelta completa.');
    }

    // El trigger after_insert_devolucion_herramienta suma la cantidad a herramienta_obra.cantidad_devuelta.
    ejecutar(
        'INSERT INTO devolucion_herramienta (id_herramienta_obra, cantidad, fecha_devolucion, observaciones, id_usuario) VALUES (?, ?, ?, ?, ?)',
        'iissi',
        [$idAsignacion, $cantidad, date('Y-m-d H:i:s'), $observaciones, $usuario['id_usuario']]
    );

    // Si con esta devolución volvió todo, la asignación se marca como devuelta.
    if ($cantidad >= $pendientes) {
        ejecutar(
            'UPDATE herramienta_obra SET id_estado_herramienta = ?, fecha_devolucion = ? WHERE id_herramienta_obra = ?',
            'isi',
            [ESTADO_HERRAMIENTA_DEVUELTA, date('Y-m-d'), $idAsignacion]
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

responder_ok(['pendientes' => $pendientes - $cantidad], 'Devolución registrada.');
