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
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $conexion->connect_error]);
    exit();
}

$conexion->set_charset("utf8mb4");
$metodo = $_SERVER['REQUEST_METHOD'];

// 📥 GET: Cargar la lista de usuarios/empleados reales
if ($metodo === 'GET') {
    // Obtenemos los usuarios con su rol traducido
    $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.id_rol, 
                   IFNULL(c.nombre_cargo, CONCAT('Rol ID ', u.id_rol)) AS cargo
            FROM usuario u
            LEFT JOIN cargo c ON u.id_rol = c.id_cargo
            ORDER BY u.apellido, u.nombre ASC";
            
    $res = $conexion->query($sql);

    $empleados = [];
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $empleados[] = [
                'id_usuario' => intval($row['id_usuario']),
                'nombre' => $row['nombre'] . ' ' . $row['apellido'],
                'cargo' => $row['cargo'],
                'presente' => true // Por defecto lo dejamos marcado como presente
            ];
        }
    }

    echo json_encode([
        "status" => "success",
        "empleados" => $empleados,
        "fecha_hoy" => date('Y-m-d')
    ]);
    exit();
}

// 📤 POST: Guardar asistencia en la tabla 'asistencia'
if ($metodo === 'POST') {
    $inputJSON = file_get_contents("php://input");
    $datos = json_decode($inputJSON, true);

    $fecha = $datos['fecha'] ?? date('Y-m-d');
    $asistencias = $datos['asistencias'] ?? []; // Array: [{id_usuario: 8, presente: true}, ...]

    if (empty($asistencias)) {
        echo json_encode(["status" => "error", "message" => "No se recibieron registros de asistencia."]);
        exit();
    }

    $conexion->begin_transaction();

    try {
        $horaActual = date('H:i:s');

        foreach ($asistencias as $ast) {
            $id_usuario = intval($ast['id_usuario']);
            $presente = $ast['presente'];

            // Eliminamos registro previo del usuario para el mismo día (evita duplicados)
            $stmtDel = $conexion->prepare("DELETE FROM asistencia WHERE id_usuario = ? AND fecha = ?");
            $stmtDel->bind_param("is", $id_usuario, $fecha);
            $stmtDel->execute();
            $stmtDel->close();

            // Si el empleado asistió, insertamos en la tabla 'asistencia'
            if ($presente) {
                $stmtIns = $conexion->prepare("INSERT INTO asistencia (id_usuario, fecha, hora_entrada) VALUES (?, ?, ?)");
                $stmtIns->bind_param("iss", $id_usuario, $fecha, $horaActual);
                $stmtIns->execute();
                $stmtIns->close();
            }
        }

        $conexion->commit();
        echo json_encode(["status" => "success", "message" => "Control de asistencia registrado en la base de datos."]);
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["status" => "error", "message" => "Error al registrar asistencia: " . $e->getMessage()]);
    }
    exit();
}

$conexion->close();
?>