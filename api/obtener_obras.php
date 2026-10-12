<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';

if (!$conexion) {
    echo json_encode(["status" => "error", "message" => "Error de conexión."]);
    exit();
}

$conexion->set_charset("utf8mb4");

$sql = "SELECT id_obra, nombre_obra FROM obra ORDER BY nombre_obra ASC";
$resultado = $conexion->query($sql);

$obras = [];
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $obras[] = [
            'id_obra' => (int)$row['id_obra'],
            'nombre_obra' => $row['nombre_obra'] ?? ''
        ];
    }
}

echo json_encode(["status" => "success", "obras" => $obras]);
?>