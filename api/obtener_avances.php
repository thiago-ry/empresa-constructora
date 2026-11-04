<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET']);

$usuario = usuario_actual();
$idObra = (int) ($_GET['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);
$limite = max(1, min(100, (int) ($_GET['limite'] ?? 30)));

// La foto de un avance se enlaza por obra + fecha + descripción (así se guarda en foto_obra).
$filas = consultar(
    'SELECT a.id_avance_diario, a.id_obra, a.fecha, a.descripcion,
            (SELECT f.ruta_imagen FROM foto_obra f
              WHERE f.id_obra = a.id_obra AND f.fecha = a.fecha AND f.descripcion = a.descripcion
              LIMIT 1) AS foto
     FROM avance_diario a
     WHERE a.id_obra = ?
     ORDER BY a.fecha DESC, a.id_avance_diario DESC
     LIMIT ?',
    'ii',
    [$idObra, $limite]
);

$avances = array_map(static fn(array $f): array => [
    'id_avance' => (int) $f['id_avance_diario'],
    'id_obra' => (int) $f['id_obra'],
    'fecha' => $f['fecha'],
    'descripcion' => $f['descripcion'],
    'foto' => $f['foto'],
], $filas);

responder_ok(['avances' => $avances]);
