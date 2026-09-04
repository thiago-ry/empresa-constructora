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
$metodo = $_SERVER['REQUEST_METHOD'];

// 📥 GET: Obtener lista de materiales y lista de obras activas
if ($metodo === 'GET') {
    $sqlMat = "SELECT id_material, nombre_material, stock, unidad_medida FROM material WHERE estado = 1 ORDER BY nombre_material ASC";
    $resMat = $conexion->query($sqlMat);

    $sqlObras = "SELECT id_obra, nombre_obra FROM obra WHERE activo = 1 ORDER BY nombre_obra ASC";
    $resObras = $conexion->query($sqlObras);

    $materiales = [];
    while ($row = $resMat->fetch_assoc()) {
        $materiales[] = $row;
    }

    $obras = [];
    while ($row = $resObras->fetch_assoc()) {
        $obras[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "materiales" => $materiales,
        "obras" => $obras
    ]);
    exit();
}

// 📤 POST: Registrar egreso/consumo de material
if ($metodo === 'POST') {
    $inputJSON = file_get_contents("php://input");
    $datos = json_decode($inputJSON, true);

    $id_material = $datos['id_material'] ?? null;
    $id_usuario = $datos['id_usuario'] ?? 27; // ID de usuario por defecto o sesión
    $cantidad = $datos['cantidad'] ?? null;
    $observacion = $datos['observacion'] ?? '';

    if (!$id_material || !$cantidad || $cantidad <= 0) {
        echo json_encode(["status" => "error", "message" => "Datos de material o cantidad inválidos."]);
        exit();
    }

    // 1. Insertar el movimiento
    $stmtMov = $conexion->prepare("INSERT INTO movimiento_material (id_material, id_usuario, tipo, cantidad, observacion) VALUES (?, ?, 'EGRESO', ?, ?)");
    $stmtMov->bind_param("iids", $id_material, $id_usuario, $cantidad, $observacion);
    
    if ($stmtMov->execute()) {
        // 2. Descontar del stock actual
        $stmtStock = $conexion->prepare("UPDATE material SET stock = stock - ? WHERE id_material = ?");
        $stmtStock->bind_param("di", $cantidad, $id_material);
        $stmtStock->execute();
        $stmtStock->close();

        echo json_encode(["status" => "success", "message" => "Consumo de material registrado correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al registrar consumo: " . $stmtMov->error]);
    }
    $stmtMov->close();
    exit();
}
?>