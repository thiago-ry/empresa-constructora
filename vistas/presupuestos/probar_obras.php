
<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/iniciar.php';

    $obras = $presupuestoController->obras();

    echo '<h2>Prueba de carga de obras</h2>';
    echo '<pre>';
    var_dump($obras);
    echo '</pre>';

} catch (Throwable $e) {
    echo '<h2>Error al cargar obras</h2>';
    echo '<pre>';
    echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    echo '</pre>';
}
