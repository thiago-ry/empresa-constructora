<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['POST']);

$usuario = usuario_actual();

// Llega como formulario (multipart): descripcion, id_obra y, opcionalmente, el archivo "foto".
$idObra = (int) ($_POST['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);
$descripcion = texto_requerido($_POST['descripcion'] ?? '', 'La descripción');
$hoy = date('Y-m-d');

$ruta = null;
try {
    $ruta = guardar_foto('foto', 'obras', 'obra_' . $idObra);

    db()->begin_transaction();
    ejecutar(
        'INSERT INTO avance_diario (id_obra, fecha, descripcion) VALUES (?, ?, ?)',
        'iss',
        [$idObra, $hoy, $descripcion]
    );
    $idAvance = db()->insert_id;

    if ($ruta !== null) {
        ejecutar(
            'INSERT INTO foto_obra (id_obra, ruta_imagen, descripcion, fecha) VALUES (?, ?, ?, ?)',
            'isss',
            [$idObra, $ruta, $descripcion, $hoy]
        );
    }
    db()->commit();
} catch (Throwable $e) {
    try {
        db()->rollback();
    } catch (Throwable $ignorado) {
    }
    borrar_foto($ruta);
    throw $e;
}

responder_ok(
    ['id_avance' => $idAvance, 'foto' => $ruta],
    $ruta !== null ? 'Avance guardado con su foto.' : 'Avance guardado.'
);
