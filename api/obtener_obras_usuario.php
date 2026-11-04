<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET']);

$usuario = usuario_actual();

$obras = array_map(static fn(array $f): array => [
    'id_obra' => (int) $f['id_obra'],
    'nombre_obra' => $f['nombre_obra'],
    'direccion' => $f['direccion'],
], obras_del_usuario($usuario));

responder_ok(['obras' => $obras]);
