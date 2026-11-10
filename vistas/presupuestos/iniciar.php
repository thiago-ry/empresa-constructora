<?php

require_once __DIR__ . '/../../controladores/PresupuestoController.php';

$rutasConexion = [
    __DIR__ . '/../../config/Conexion.php',
    __DIR__ . '/../../modelos/Conexion.php'
];

if (!class_exists('Conexion')) {
    foreach ($rutasConexion as $ruta) {
        if (is_file($ruta)) {
            require_once $ruta;

            if (class_exists('Conexion')) {
                break;
            }
        }
    }
}

if (!class_exists('Conexion')) {
    throw new RuntimeException(
        'No se encontró la clase Conexion.'
    );
}

$conexion = new Conexion();
$db = $conexion->conectar();

$presupuestoController = new PresupuestoController($db);

if (!$db instanceof PDO) {
    throw new RuntimeException(
        'La conexión no devolvió un objeto PDO.'
    );
}

$presupuestoController = new PresupuestoController($db);