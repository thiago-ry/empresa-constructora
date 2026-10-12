<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';

if (!$conexion) {
    echo json_encode(["status" => "error", "message" => "Error de conexión con la BD."]);
    exit();
}

$conexion->set_charset("utf8mb4");

$id_obra = isset($_GET['id_obra']) ? (int)$_GET['id_obra'] : 9;

// Seleccionamos id_avance_diario en lugar de id_avance
$sql = "SELECT id_avance_diario, id_obra, fecha, descripcion FROM avance_diario WHERE id_obra = ? ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_obra);
$stmt->execute();
$resultado = $stmt->get_result();

$avances = [];
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        $avances[] = [
            'id_avance' => (int)$row['id_avance_diario'],
            'id_obra' => (int)$row['id_obra'],
            'descripcion' => $row['descripcion'],
            'fecha' => $row['fecha'],
            'foto' => null // Por si las fotos están en otra tabla
        ];
    }
}

echo json_encode(["status" => "success", "avances" => $avances]);
?>