<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';

if (!$conexion) {
    echo json_encode([
        "status" => "error",
        "message" => "Error de conexión a la base de datos."
    ]);
    exit();
}

// Forzar UTF-8 en la conexión
$conexion->set_charset("utf8mb4");

$inputJSON = file_get_contents("php://input");
$datos = json_decode($inputJSON, true);

$correo   = trim($datos['correo'] ?? $_POST['correo'] ?? '');
$password = trim($datos['password'] ?? $_POST['password'] ?? '');

if (empty($correo) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "Por favor ingresa tu correo y contraseña."
    ]);
    exit();
}

try {
    // Traemos id_rol para verificar los permisos
    $stmt = $conexion->prepare("SELECT id_usuario, nombre, apellido, correo, id_rol FROM usuario WHERE correo = ? AND `contraseña` = ?");
    
    if (!$stmt) {
        throw new Exception("Error en la consulta SQL: " . $conexion->error);
    }

    $stmt->bind_param("ss", $correo, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $user = $resultado->fetch_assoc();

        // VALIDACIÓN DE ROL: id_rol = 4 es Jefe de Obra
        if ((int)$user['id_rol'] !== 4) {
            $stmt->close();
            echo json_encode([
                "status" => "error",
                "message" => "Acceso denegado. Esta aplicación es únicamente para Jefes de Obra."
            ]);
            exit();
        }

        // Registrar acceso SOLO si es Jefe de Obra
        $stmtAcceso = $conexion->prepare("INSERT INTO acceso_usuario (id_usuario, fecha_hora_ingreso) VALUES (?, NOW())");
        if ($stmtAcceso) {
            $stmtAcceso->bind_param("i", $user['id_usuario']);
            $stmtAcceso->execute();
            $stmtAcceso->close();
        }

        $stmt->close();

        echo json_encode([
            "status" => "success",
            "message" => "¡Inicio de sesión exitoso!",
            "user" => [
                "id_usuario" => $user['id_usuario'],
                "nombre"     => $user['nombre'],
                "apellido"   => $user['apellido'],
                "correo"     => $user['correo'],
                "id_rol"     => $user['id_rol']
            ]
        ]);
        exit();

    } else {
        $stmt->close();
        echo json_encode([
            "status" => "error",
            "message" => "Correo o contraseña incorrectos."
        ]);
        exit();
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error en el servidor: " . $e->getMessage()
    ]);
    exit();
}
?>