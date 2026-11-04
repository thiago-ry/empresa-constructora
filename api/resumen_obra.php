<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET']);

$usuario = usuario_actual();
$idObra = (int) ($_GET['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);

$hoy = date('Y-m-d');
$haceUnaSemana = date('Y-m-d', strtotime('-6 days'));

/** Cada dato se calcula por separado: si una consulta falla, el resto del resumen sigue funcionando. */
function intentar(callable $consulta)
{
    try {
        return $consulta();
    } catch (Throwable $e) {
        error_log('[constructora-api] resumen_obra: ' . $e->getMessage());
        return null;
    }
}

$totalPersonal = intentar(fn() => count(personal_de_obra($idObra, $hoy)));

$presentesHoy = intentar(fn() => (int) consultar_valor(
    "SELECT COUNT(DISTINCT id_usuario) FROM asistencia WHERE id_obra = ? AND fecha = ? AND estado IN ('Presente', 'Tarde')",
    'is',
    [$idObra, $hoy]
));

// Abiertas = Pendiente o En revisión (todo lo que no está Resuelta).
$incidenciasAbiertas = intentar(fn() => (int) consultar_valor(
    "SELECT COUNT(*) FROM incidencia WHERE id_obra = ? AND (estado IS NULL OR estado <> 'Resuelta')",
    'i',
    [$idObra]
));

$avancesSemana = intentar(fn() => (int) consultar_valor(
    'SELECT COUNT(*) FROM avance_diario WHERE id_obra = ? AND fecha >= ?',
    'is',
    [$idObra, $haceUnaSemana]
));

$ultimoAvance = intentar(fn() => consultar_valor('SELECT MAX(fecha) FROM avance_diario WHERE id_obra = ?', 'i', [$idObra]));

$avancesHoy = intentar(fn() => array_column(
    consultar('SELECT descripcion FROM avance_diario WHERE id_obra = ? AND fecha = ? ORDER BY id_avance_diario DESC LIMIT 10', 'is', [$idObra, $hoy]),
    'descripcion'
));

// Materiales por debajo de su stock mínimo (el stock es compartido entre obras).
$stockBajo = intentar(fn() => (int) consultar_valor('SELECT COUNT(*) FROM material WHERE estado = 1 AND stock_minimo > 0 AND stock <= stock_minimo'));

// Solicitudes (materiales y herramientas) que todavía esperan respuesta.
$solicitudesPendientes = intentar(function () use ($idObra) {
    $n = (int) consultar_valor("SELECT COUNT(*) FROM solicitud_material WHERE id_obra = ? AND estado = 'Pendiente'", 'i', [$idObra]);
    if (tabla_existe('solicitud_herramienta')) {
        $n += (int) consultar_valor("SELECT COUNT(*) FROM solicitud_herramienta WHERE id_obra = ? AND estado = 'Pendiente'", 'i', [$idObra]);
    }
    return $n;
});

/** Últimos movimientos de la obra: avances e incidencias mezclados, del más nuevo al más viejo. */
$actividad = intentar(function () use ($idObra) {
    $items = [];
    foreach (consultar('SELECT fecha, descripcion FROM avance_diario WHERE id_obra = ? ORDER BY fecha DESC, id_avance_diario DESC LIMIT 5', 'i', [$idObra]) as $f) {
        $items[] = ['tipo' => 'avance', 'titulo' => 'Avance de obra', 'detalle' => (string) $f['descripcion'], 'fecha' => $f['fecha']];
    }
    foreach (consultar('SELECT fecha, tipo_incidencia, descripcion FROM incidencia WHERE id_obra = ? ORDER BY fecha DESC, id_incidencia DESC LIMIT 5', 'i', [$idObra]) as $f) {
        $items[] = ['tipo' => 'incidencia', 'titulo' => 'Incidencia: ' . $f['tipo_incidencia'], 'detalle' => (string) $f['descripcion'], 'fecha' => $f['fecha']];
    }
    usort($items, static fn(array $a, array $b): int => strcmp((string) $b['fecha'], (string) $a['fecha']));
    return array_slice($items, 0, 6);
});

responder_ok(['resumen' => [
    'total_personal' => $totalPersonal,
    'presentes_hoy' => $presentesHoy,
    'incidencias_abiertas' => $incidenciasAbiertas,
    'avances_semana' => $avancesSemana,
    'ultimo_avance' => $ultimoAvance,
    'avances_hoy' => $avancesHoy ?? [],
    'stock_bajo' => $stockBajo,
    'solicitudes_pendientes' => $solicitudesPendientes,
    'actividad' => $actividad ?? [],
    'hoy' => $hoy,
]]);
