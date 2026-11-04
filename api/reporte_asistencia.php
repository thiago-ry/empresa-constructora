<?php
/**
 * Reporte de asistencia de una obra entre dos fechas: por cada trabajador, cuántos días
 * trabajó y cuántas horas (hora_salida - hora_entrada). La app lo convierte en PDF.
 *
 * GET ?id_obra=1&desde=2026-10-01&hasta=2026-10-31   (por defecto: del 1º del mes a hoy)
 */
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET']);

$usuario = usuario_actual();
$idObra = (int) ($_GET['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);

$hoy = date('Y-m-d');
$desde = fecha_valida(($_GET['desde'] ?? '') !== '' ? $_GET['desde'] : date('Y-m-01'));
$hasta = fecha_valida(($_GET['hasta'] ?? '') !== '' ? $_GET['hasta'] : $hoy);
if ($desde > $hasta) {
    throw new ErrorValidacion('La fecha "desde" no puede ser posterior a "hasta".');
}
if ((strtotime($hasta) - strtotime($desde)) / 86400 > 366) {
    throw new ErrorValidacion('El período no puede superar un año.');
}

/** "08:15:30" → segundos desde la medianoche. */
function segundos_del_dia(string $hora): int
{
    $p = explode(':', $hora);
    return ((int) $p[0]) * 3600 + ((int) ($p[1] ?? 0)) * 60 + (int) ($p[2] ?? 0);
}

/** Horas entre entrada y salida, o null si falta alguna o la salida es anterior a la entrada. */
function horas_entre(?string $entrada, ?string $salida): ?float
{
    if ($entrada === null || $entrada === '' || $salida === null || $salida === '') {
        return null;
    }
    $dif = segundos_del_dia($salida) - segundos_del_dia($entrada);
    return $dif > 0 ? $dif / 3600 : null;
}

$obra = consultar_fila('SELECT nombre_obra FROM obra WHERE id_obra = ?', 'i', [$idObra]);

$cargos = [];
foreach (consultar(
    'SELECT eo.id_usuario, MAX(c.nombre_cargo) AS cargo
     FROM empleado_obra eo LEFT JOIN cargo c ON c.id_cargo = eo.id_cargo
     WHERE eo.id_obra = ? GROUP BY eo.id_usuario',
    'i',
    [$idObra]
) as $c) {
    $cargos[(int) $c['id_usuario']] = trim((string) $c['cargo']);
}

$filas = consultar(
    "SELECT a.id_usuario, a.fecha, a.estado, a.hora_entrada, a.hora_salida, a.observacion, u.nombre, u.apellido
     FROM asistencia a
     INNER JOIN usuario u ON u.id_usuario = a.id_usuario
     WHERE a.id_obra = ? AND a.fecha BETWEEN ? AND ?
     ORDER BY u.apellido, u.nombre, a.fecha, a.id_asistencia",
    'iss',
    [$idObra, $desde, $hasta]
);

// Un registro por persona y día. Si hubiera duplicados, gana el que indica presencia.
$personas = [];
foreach ($filas as $f) {
    $id = (int) $f['id_usuario'];
    if (!isset($personas[$id])) {
        $personas[$id] = [
            'id_usuario' => $id,
            'nombre' => trim($f['apellido'] . ' ' . $f['nombre']),
            'dias' => [],
        ];
    }
    $dia = $f['fecha'];
    $actual = $personas[$id]['dias'][$dia] ?? null;
    $nuevoPresente = $f['estado'] !== 'Ausente';
    if ($actual === null || ($nuevoPresente && $actual['estado'] === 'Ausente')) {
        $personas[$id]['dias'][$dia] = $f;
    } elseif ($nuevoPresente && $actual['estado'] !== 'Ausente' && $actual['hora_salida'] === null && $f['hora_salida'] !== null) {
        $personas[$id]['dias'][$dia] = $f;
    }
}

$trabajadores = [];
$totales = ['personas' => 0, 'dias_trabajados' => 0, 'ausencias' => 0, 'tardanzas' => 0, 'horas' => 0.0, 'dias_sin_salida' => 0];

foreach ($personas as $id => $p) {
    $diasPresente = 0;
    $diasTarde = 0;
    $diasAusente = 0;
    $diasSinSalida = 0;
    $diasConHoras = 0;
    $horas = 0.0;
    $detalle = [];

    foreach ($p['dias'] as $fecha => $d) {
        $h = null;
        if ($d['estado'] === 'Ausente') {
            $diasAusente++;
        } else {
            if ($d['estado'] === 'Tarde') {
                $diasTarde++;
            } else {
                $diasPresente++;
            }
            $h = horas_entre($d['hora_entrada'], $d['hora_salida']);
            if ($h === null) {
                $diasSinSalida++;
            } else {
                $horas += $h;
                $diasConHoras++;
            }
        }
        $detalle[] = [
            'fecha' => $fecha,
            'estado' => $d['estado'],
            'entrada' => $d['hora_entrada'] !== null ? substr($d['hora_entrada'], 0, 5) : null,
            'salida' => $d['hora_salida'] !== null ? substr($d['hora_salida'], 0, 5) : null,
            'horas' => $h !== null ? round($h, 2) : null,
            'observacion' => $d['observacion'] !== null && trim((string) $d['observacion']) !== '' ? trim((string) $d['observacion']) : null,
        ];
    }

    $trabajados = $diasPresente + $diasTarde;
    $trabajadores[] = [
        'id_usuario' => $id,
        'nombre' => $p['nombre'],
        'cargo' => $cargos[$id] ?? '',
        'dias_trabajados' => $trabajados,
        'dias_presente' => $diasPresente,
        'dias_tarde' => $diasTarde,
        'dias_ausente' => $diasAusente,
        'dias_sin_salida' => $diasSinSalida,
        'horas' => round($horas, 2),
        'promedio_horas' => $diasConHoras > 0 ? round($horas / $diasConHoras, 2) : null,
        'detalle' => $detalle,
    ];

    $totales['personas']++;
    $totales['dias_trabajados'] += $trabajados;
    $totales['ausencias'] += $diasAusente;
    $totales['tardanzas'] += $diasTarde;
    $totales['horas'] += $horas;
    $totales['dias_sin_salida'] += $diasSinSalida;
}
$totales['horas'] = round($totales['horas'], 2);

responder_ok([
    'reporte' => [
        'obra' => $obra['nombre_obra'] ?? '',
        'desde' => $desde,
        'hasta' => $hasta,
        'generado' => date('Y-m-d H:i'),
        'trabajadores' => $trabajadores,
        'totales' => $totales,
    ],
]);
