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

$id_usuario = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;

if ($id_usuario <= 0) {
    echo json_encode(["status" => "error", "message" => "ID de usuario inválido."]);
    exit();
}

// Consultar las obras asociadas al usuario a través de la tabla empleado_obra u obra
$sql = "SELECT o.id_obra, o.nombre_obra, o.direccion, o.estado 
        FROM obra o 
        INNER JOIN empleado_obra eo ON o.id_obra = eo.id_obra 
        WHERE eo.id_usuario = ? AND o.estado = 1";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$obras = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $obras[] = [
            'id_obra' => (int)$row['id_obra'],
            'nombre_obra' => $row['nombre_obra'],
            'direccion' => $row['direccion']
        ];
    }
} else {
    // Si no hay vinculación en empleado_obra, listar todas las obras activas como fallback
    $sql_all = "SELECT id_obra, nombre_obra, direccion FROM obra WHERE estado = 1";
    $res_all = $conexion->query($sql_all);
    if ($res_all) {
        while ($row = $res_all->fetch_assoc()) {
            $obras[] = [
                'id_obra' => (int)$row['id_obra'],
                'nombre_obra' => $row['nombre_obra'],
                'direccion' => $row['direccion']
            ];
        }
    }
}

echo json_encode(["status" => "success", "obras" => $obras]);
?>