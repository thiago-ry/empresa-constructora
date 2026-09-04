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

// 📥 GET: Traer la lista de obreros/empleados y obras activas
if ($metodo === 'GET') {
    // Intentamos obtener los empleados de la tabla empleado (o usuario según tu estructura)
    $sqlEmpleados = "SELECT id_empleado, nombre, apellido, cargo FROM empleado WHERE activo = 1 ORDER BY apellido, nombre ASC";
    $resEmp = $conexion->query($sqlEmpleados);

    // Si la tabla se llama distinto, adaptamos el fallback
    if (!$resEmp) {
        $sqlEmpleados = "SELECT id_usuario AS id_empleado, nombre, apellido, rol AS cargo FROM usuario WHERE activo = 1 ORDER BY apellido, nombre ASC";
        $resEmp = $conexion->query($sqlEmpleados);
    }

    $sqlObras = "SELECT id_obra, nombre_obra FROM obra WHERE activo = 1 ORDER BY nombre_obra ASC";
    $resObras = $conexion->query($sqlObras);

    $empleados = [];
    if ($resEmp) {
        while ($row = $resEmp->fetch_assoc()) {
            $empleados[] = $row;
        }
    }

    $obras = [];
    if ($resObras) {
        while ($row = $resObras->fetch_assoc()) {
            $obras[] = $row;
        }
    }

    echo json_encode([
        "status" => "success",
        "empleados" => $empleados,
        "obras" => $obras,
        "fecha_hoy" => date('Y-m-d')
    ]);
    exit();
}

// 📤 POST: Registrar o actualizar la asistencia del día
if ($metodo === 'POST') {
    $inputJSON = file_get_contents("php://input");
    $datos = json_decode($inputJSON, true);

    $id_obra = $datos['id_obra'] ?? null;
    $fecha = $datos['fecha'] ?? date('Y-m-d');
    $asistencias = $datos['asistencias'] ?? []; // Array: [{id_empleado: 1, estado: 'Presente'}, ...]

    if (!$id_obra || empty($asistencias)) {
        echo json_encode(["status" => "error", "message" => "Faltan datos requeridos (obra o asistencias)."]);
        exit();
    }

    $conexion->begin_transaction();

    try {
        foreach ($asistencias as $ast) {
            $id_emp = $ast['id_empleado'];
            $estado = $ast['estado']; // 'Presente', 'Ausente', 'Justificado', 'Tarde'
            $obs = $ast['observacion'] ?? '';

            // Borramos registro previo si existía para evitar duplicados en el mismo día/obra
            $stmtDel = $conexion->prepare("DELETE FROM asistencia WHERE id_empleado = ? AND id_obra = ? AND fecha = ?");
            $stmtDel->bind_param("iis", $id_emp, $id_obra, $fecha);
            $stmtDel->execute();
            $stmtDel->close();

            // Insertamos la nueva asistencia
            $stmtIns = $conexion->prepare("INSERT INTO asistencia (id_empleado, id_obra, fecha, estado, observacion) VALUES (?, ?, ?, ?, ?)");
            $stmtIns->bind_param("iisss", $id_emp, $id_obra, $fecha, $estado, $obs);
            $stmtIns->execute();
            $stmtIns->close();
        }

        $conexion->commit();
        echo json_encode(["status" => "success", "message" => "Asistencia registrada correctamente."]);
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["status" => "error", "message" => "Error al guardar asistencia: " . $e->getMessage()]);
    }
    exit();
}
?>