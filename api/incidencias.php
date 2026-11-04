<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET', 'POST']);

const TIPOS_INCIDENCIA = ['Material', 'Seguridad', 'Clima', 'Herramientas', 'Personal', 'Cliente', 'Diseño/Planos', 'Retraso'];

$usuario = usuario_actual();

/* ----------------------------- GET: historial ----------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idObra = (int) ($_GET['id_obra'] ?? 0);
    exigir_acceso_obra($usuario, $idObra);
    $limite = max(1, min(100, (int) ($_GET['limite'] ?? 30)));

    $filas = consultar(
        'SELECT id_incidencia, fecha, tipo_incidencia, descripcion, estado, solucion, foto
         FROM incidencia WHERE id_obra = ?
         ORDER BY fecha DESC, id_incidencia DESC LIMIT ?',
        'ii',
        [$idObra, $limite]
    );

    $incidencias = array_map(static fn(array $f): array => [
        'id_incidencia' => (int) $f['id_incidencia'],
        'fecha' => $f['fecha'],
        'tipo' => $f['tipo_incidencia'],
        'descripcion' => $f['descripcion'],
        'estado' => $f['estado'],
        'solucion' => $f['solucion'],
        'foto' => $f['foto'],
    ], $filas);

    responder_ok(['incidencias' => $incidencias]);
}

/* ----------------------------- POST: resolver ----------------------------- */
if (($_POST['accion'] ?? '') === 'resolver') {
    exigir_gestion($usuario);
    $idIncidencia = (int) ($_POST['id_incidencia'] ?? 0);
    $fila = consultar_fila('SELECT id_obra FROM incidencia WHERE id_incidencia = ?', 'i', [$idIncidencia]);
    if ($fila === null) {
        throw new ErrorValidacion('La incidencia no existe.');
    }
    exigir_acceso_obra($usuario, (int) $fila['id_obra']);

    $solucion = mb_substr(trim((string) ($_POST['solucion'] ?? '')), 0, 2000);
    ejecutar(
        "UPDATE incidencia SET estado = 'Resuelta', solucion = ? WHERE id_incidencia = ?",
        'si',
        [$solucion !== '' ? $solucion : null, $idIncidencia]
    );
    responder_ok([], 'Incidencia marcada como resuelta.');
}

/* ----------------------------- POST: registrar ---------------------------- */
$idObra = (int) ($_POST['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);

$descripcion = texto_requerido($_POST['descripcion'] ?? '', 'La descripción');
$tipo = trim((string) ($_POST['tipo_incidencia'] ?? ''));
if (!in_array($tipo, TIPOS_INCIDENCIA, true)) {
    throw new ErrorValidacion('Elegí un tipo de incidencia válido.');
}

$ruta = null;
try {
    $ruta = guardar_foto('foto', 'incidencias', 'incidencia_' . $idObra);
    ejecutar(
        "INSERT INTO incidencia (id_obra, fecha, tipo_incidencia, descripcion, estado, foto) VALUES (?, ?, ?, ?, 'Pendiente', ?)",
        'issss',
        [$idObra, date('Y-m-d'), $tipo, $descripcion, $ruta]
    );
    $idIncidencia = db()->insert_id;
} catch (Throwable $e) {
    borrar_foto($ruta);
    throw $e;
}

responder_ok(['id_incidencia' => $idIncidencia], 'Incidencia registrada.');
