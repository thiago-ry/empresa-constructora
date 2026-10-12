<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Conexión a la BD
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "constructora";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conn->connect_error]);
    exit();
}

// OBTENER MATERIALES (PETICIÓN GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id_material, nombre_material, unidad_medida, stock AS stock_actual FROM material WHERE estado = 1";
    $result = $conn->query($sql);

    $materiales = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $materiales[] = $row;
        }
    }

    echo json_encode(["status" => "success", "materiales" => $materiales]);
    exit();
}

// GUARDAR MOVIMIENTO (PETICIÓN POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!isset($data['id_material'], $data['cantidad'], $data['tipo'])) {
        echo json_encode(["status" => "error", "message" => "Faltan campos obligatorios."]);
        exit();
    }

    $id_material = intval($data['id_material']);
    $id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : 27;
    $tipo = $data['tipo']; // 'EGRESO' o 'INGRESO'
    $cantidad = floatval($data['cantidad']);
    $observacion = isset($data['observacion']) ? $conn->real_escape_string($data['observacion']) : '';

    // Validar Stock actual si es un EGRESO (Consumo)
    if ($tipo === 'EGRESO') {
        $checkStock = $conn->query("SELECT stock FROM material WHERE id_material = $id_material");
        if ($row = $checkStock->fetch_assoc()) {
            if ($row['stock'] < $cantidad) {
                echo json_encode(["status" => "error", "message" => "Stock insuficiente para registrar este consumo. Disponible: " . $row['stock']]);
                exit();
            }
        }
    }

    // Registrar en movimiento_material
    $stmt = $conn->prepare("INSERT INTO movimiento_material (id_material, id_usuario, tipo, cantidad, fecha, observacion) VALUES (?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param("iisds", $id_material, $id_usuario, $tipo, $cantidad, $observacion);

    if ($stmt->execute()) {
        // Actualizar el stock general en la tabla material
        $operador = ($tipo === 'INGRESO') ? '+' : '-';
        $updateStock = "UPDATE material SET stock = stock $operador $cantidad WHERE id_material = $id_material";
        $conn->query($updateStock);

        echo json_encode(["status" => "success", "message" => "Movimiento de $tipo registrado correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al insertar el movimiento: " . $stmt->error]);
    }
    
    $stmt->close();
    exit();
}

$conn->close();
?>