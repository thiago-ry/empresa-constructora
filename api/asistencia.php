<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET', 'POST']);

$usuario = usuario_actual();

/* ------------------------------ GET: lista -------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idObra = (int) ($_GET['id_obra'] ?? 0);
    exigir_acceso_obra($usuario, $idObra);
    $hoy = date('Y-m-d');
    $fecha = fecha_valida($_GET['fecha'] ?? null);

    $personal = personal_de_obra($idObra, $fecha);

    // Registros ya cargados para esa obra y día (la tabla guarda filas "Presente" y "Ausente").
    $registros = [];
    foreach (consultar('SELECT id_usuario, estado, hora_entrada, hora_salida, observacion FROM asistencia WHERE id_obra = ? AND fecha = ?', 'is', [$idObra, $fecha]) as $r) {
        $registros[(int) $r['id_usuario']] = $r;
    }

    $yaRegistrado = false;
    foreach ($personal as $p) {
        if (isset($registros[(int) $p['id_usuario']])) {
            $yaRegistrado = true;
            break;
        }
    }

    // Si hoy todavía no se cargó nada, todos arrancan presentes para que el parte sea rápido.
    // En días anteriores sin registros, nadie figura presente.
    $porDefecto = $fecha === $hoy;

    $empleados = array_map(static function (array $p) use ($registros, $yaRegistrado, $porDefecto): array {
        $id = (int) $p['id_usuario'];
        $registro = $registros[$id] ?? null;
        return [
            'id_usuario' => $id,
            'nombre' => trim($p['nombre'] . ' ' . $p['apellido']),
            'cargo' => $p['cargo'],
            'presente' => $yaRegistrado ? ($registro !== null && $registro['estado'] !== 'Ausente') : $porDefecto,
            'hora_entrada' => $registro['hora_entrada'] ?? null,
            'hora_salida' => $registro['hora_salida'] ?? null,
            'observacion' => $registro['observacion'] ?? null,
        ];
    }, $personal);

    responder_ok(['empleados' => $empleados, 'fecha' => $fecha, 'fecha_hoy' => $hoy, 'ya_registrado' => $yaRegistrado]);
}

/* ------------------------------ POST -------------------------------------- */
$datos = leer_json();
$idObra = (int) ($datos['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);

/** "7:30", "07:30" o "07:30:15" → "07:30:00" (o null si viene vacío). Corta si el formato no es válido. */
function hora_valida($valor, string $campo): ?string
{
    $v = trim((string) ($valor ?? ''));
    if ($v === '') {
        return null;
    }
    if (!preg_match('/^([01]?\d|2[0-3]):([0-5]\d)(:([0-5]\d))?$/', $v, $m)) {
        throw new ErrorValidacion("La $campo no es válida: usá el formato HH:MM (por ejemplo, 07:30).");
    }
    return sprintf('%02d:%s:%s', (int) $m[1], $m[2], $m[4] ?? '00');
}

/* ------------------------- POST: marcar hora de salida -------------------- */
// { accion: 'salida', id_obra, id_usuario? }  → sin id_usuario, marca la salida de todos los que siguen en obra.
if (($datos['accion'] ?? '') === 'salida') {
    $hoy = date('Y-m-d');
    if (($datos['fecha'] ?? $hoy) !== $hoy) {
        throw new ErrorValidacion('La hora de salida solo se puede marcar el mismo día.');
    }
    $ahora = date('H:i:s');
    $sql = "UPDATE asistencia SET hora_salida = ?
            WHERE id_obra = ? AND fecha = ? AND estado IN ('Presente', 'Tarde')
              AND hora_entrada IS NOT NULL AND hora_entrada <= ? AND hora_salida IS NULL";
    $tipos = 'siss';
    $params = [$ahora, $idObra, $hoy, $ahora];
    $idUsuario = (int) ($datos['id_usuario'] ?? 0);
    if ($idUsuario > 0) {
        $sql .= ' AND id_usuario = ?';
        $tipos .= 'i';
        $params[] = $idUsuario;
    }
    $marcados = ejecutar($sql, $tipos, $params);
    responder_ok(
        ['marcados' => $marcados, 'hora_salida' => $ahora],
        $marcados > 0 ? 'Salida registrada a las ' . substr($ahora, 0, 5) . '.' : 'No había nadie con la salida pendiente.'
    );
}

/* ------------------------------ POST: guardar parte ----------------------- */
$fecha = fecha_valida($datos['fecha'] ?? null);
$asistencias = $datos['asistencias'] ?? [];

if (!is_array($asistencias) || count($asistencias) === 0) {
    throw new ErrorValidacion('No se recibieron registros de asistencia.');
}

// Solo se aceptan personas que pertenecen a esta obra en esa fecha.
$permitidos = [];
foreach (personal_de_obra($idObra, $fecha) as $p) {
    $permitidos[(int) $p['id_usuario']] = true;
}

$horaActual = date('H:i:s');
$presentes = 0;
$ausentes = 0;

try {
    db()->begin_transaction();
    foreach ($asistencias as $registro) {
        $idUsuario = (int) ($registro['id_usuario'] ?? 0);
        if (!isset($permitidos[$idUsuario])) {
            continue;
        }
        $existente = consultar_fila(
            'SELECT id_asistencia, estado, hora_entrada, hora_salida, observacion FROM asistencia WHERE id_usuario = ? AND id_obra = ? AND fecha = ? LIMIT 1',
            'iis',
            [$idUsuario, $idObra, $fecha]
        );

        // Observación: si la app no la manda se conserva la que había.
        if (array_key_exists('observacion', $registro)) {
            $observacion = mb_substr(trim((string) ($registro['observacion'] ?? '')), 0, 500);
            $observacion = $observacion === '' ? null : $observacion;
        } else {
            $observacion = $existente['observacion'] ?? null;
        }

        if (!empty($registro['presente'])) {
            $estabaPresente = $existente !== null && $existente['estado'] !== 'Ausente';

            // Entrada: la que se escribió; si no hay, la que ya tenía; si tampoco, la hora actual.
            $entrada = array_key_exists('hora_entrada', $registro) ? hora_valida($registro['hora_entrada'], 'hora de entrada') : null;
            if ($entrada === null) {
                $entrada = $estabaPresente && $existente['hora_entrada'] !== null ? $existente['hora_entrada'] : $horaActual;
            }
            // Salida: la que se escribió (vacía = sin salida); si la app no la manda, se conserva.
            if (array_key_exists('hora_salida', $registro)) {
                $salida = hora_valida($registro['hora_salida'], 'hora de salida');
            } else {
                $salida = $estabaPresente ? $existente['hora_salida'] : null;
            }
            if ($salida !== null && $salida <= $entrada) {
                throw new ErrorValidacion('La hora de salida tiene que ser posterior a la de entrada (' . substr($entrada, 0, 5) . ').');
            }

            if ($existente === null) {
                ejecutar(
                    "INSERT INTO asistencia (id_usuario, id_obra, fecha, estado, hora_entrada, hora_salida, observacion) VALUES (?, ?, ?, 'Presente', ?, ?, ?)",
                    'iissss',
                    [$idUsuario, $idObra, $fecha, $entrada, $salida, $observacion]
                );
            } else {
                // Si ya estaba "Tarde" se conserva ese estado.
                ejecutar(
                    "UPDATE asistencia SET estado = IF(estado = 'Ausente', 'Presente', estado), hora_entrada = ?, hora_salida = ?, observacion = ? WHERE id_asistencia = ?",
                    'sssi',
                    [$entrada, $salida, $observacion, (int) $existente['id_asistencia']]
                );
            }
            $presentes++;
        } else {
            if ($existente === null) {
                ejecutar("INSERT INTO asistencia (id_usuario, id_obra, fecha, estado, observacion) VALUES (?, ?, ?, 'Ausente', ?)", 'iiss', [$idUsuario, $idObra, $fecha, $observacion]);
            } else {
                ejecutar(
                    "UPDATE asistencia SET estado = 'Ausente', hora_entrada = NULL, hora_salida = NULL, observacion = ? WHERE id_asistencia = ?",
                    'si',
                    [$observacion, (int) $existente['id_asistencia']]
                );
            }
            $ausentes++;
        }
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
    ['presentes' => $presentes, 'ausentes' => $ausentes],
    "Parte diario guardado: $presentes presentes, $ausentes ausentes."
);
