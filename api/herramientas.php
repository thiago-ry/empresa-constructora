<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "constructora";

$conexion = new mysqli($host, $user, $pass, $dbname);

if ($conexion->connect_error) {
    echo json_encode(["status" => "error", "message" => "Error de conexión a la base de datos."]);
    exit();
}

$conexion->set_charset("utf8mb4");
$metodo = $_SERVER['REQUEST_METHOD'];

// 📥 GET: Listar herramientas con su stock
if ($metodo === 'GET') {
    $sql = "SELECT h.id_herramienta, h.nombre, h.tipo, h.marca, h.modelo, h.cantidad_total
            FROM herramienta h
            ORDER BY h.nombre ASC";

    $res = $conexion->query($sql);
    $herramientas = [];

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $herramientas[] = [
                'id_herramienta' => intval($row['id_herramienta']),
                'nombre' => $row['nombre'],
                'tipo' => $row['tipo'],
                'marca' => $row['marca'],
                'modelo' => $row['modelo'],
                'cantidad_total' => intval($row['cantidad_total'])
            ];
        }
    }

    echo json_encode([
        "status" => "success",
        "herramientas" => $herramientas
    ]);
    exit();
}

// 📤 POST: Registrar la Devolución de Herramienta
if ($metodo === 'POST') {
    $inputJSON = file_get_contents("php://input");
    $datos = json_decode($inputJSON, true);

    $id_herramienta = intval($datos['id_herramienta'] ?? 0);
    $cantidad = intval($datos['cantidad'] ?? 0);
    $observaciones = $datos['observaciones'] ?? '';
    $id_usuario = intval($datos['id_usuario'] ?? 27);

    if ($id_herramienta <= 0 || $cantidad <= 0) {
        echo json_encode(["status" => "error", "message" => "Seleccione una herramienta y especifique una cantidad válida."]);
        exit();
    }

    $conexion->begin_transaction();

    try {
        $fechaActual = date('Y-m-d H:i:s');

        // 1. Buscar si la herramienta existe en 'herramienta_obra'
        $stmtObra = $conexion->prepare("SELECT id_herramienta_obra FROM herramienta_obra WHERE id_herramienta = ? LIMIT 1");
        $stmtObra->bind_param("i", $id_herramienta);
        $stmtObra->execute();
        $resObra = $stmtObra->get_result();

        if ($resObra && $resObra->num_rows > 0) {
            $rowObra = $resObra->fetch_assoc();
            $id_herramienta_obra = $rowObra['id_herramienta_obra'];
        } else {
            // Si no existe asignada a una obra aún, creamos el registro base en 'herramienta_obra'
            $id_obra_defecto = 1; // Asignamos obra por defecto
            $stmtInsertHO = $conexion->prepare("INSERT INTO herramienta_obra (id_herramienta, id_obra, cantidad) VALUES (?, ?, ?)");
            $stmtInsertHO->bind_param("iii", $id_herramienta, $id_obra_defecto, $cantidad);
            $stmtInsertHO->execute();
            $id_herramienta_obra = $conexion->insert_id;
            $stmtInsertHO->close();
        }
        $stmtObra->close();

        // 2. Registrar la devolución usando el id_herramienta_obra válido
        $stmt = $conexion->prepare("INSERT INTO devolucion_herramienta (id_herramienta_obra, cantidad, fecha_devolucion, observaciones, id_usuario) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissi", $id_herramienta_obra, $cantidad, $fechaActual, $observaciones, $id_usuario);
        $stmt->execute();
        $stmt->close();

        $conexion->commit();
        echo json_encode(["status" => "success", "message" => "Devolución registrada con éxito."]);
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["status" => "error", "message" => "Error al guardar: " . $e->getMessage()]);
    }
    exit();
}

$conexion->close();
?>