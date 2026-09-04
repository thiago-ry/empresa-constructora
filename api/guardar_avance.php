<?php
// Reportar todos los errores de PHP para diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de memoria e subida
ini_set('memory_limit', '256M');
ini_set('post_max_size', '20M');
ini_set('upload_max_filesize', '20M');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Conexión a la base de datos
require_once 'conexion.php';

if (!isset($conexion) || $conexion->connect_error) {
    echo json_encode([
        "status" => "error", 
        "message" => "Error de conexión a la BD: " . ($conexion->connect_error ?? "Conexión no definida")
    ]);
    exit();
}

$conexion->set_charset("utf8mb4");

// 2. Leer datos recibidos del cuerpo de la petición
$inputJSON = file_get_contents("php://input");
$datos = json_decode($inputJSON, true);

if (!$datos) {
    echo json_encode([
        "status" => "error", 
        "message" => "No se recibieron datos JSON válidos o el archivo enviado es muy pesado."
    ]);
    exit();
}

$id_obra     = isset($datos['id_obra']) ? intval($datos['id_obra']) : 9;
$descripcion = trim($datos['descripcion'] ?? '');
$fotoBase64  = $datos['foto'] ?? null;
$fechaActual = date('Y-m-d');

if (empty($descripcion)) {
    echo json_encode(["status" => "error", "message" => "La descripción es requerida."]);
    exit();
}

// 3. Guardar el avance diario
$stmt = $conexion->prepare("INSERT INTO avance_diario (id_obra, fecha, descripcion) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode([
        "status" => "error", 
        "message" => "Error al preparar la consulta de avance: " . $conexion->error
    ]);
    exit();
}

$stmt->bind_param("iss", $id_obra, $fechaActual, $descripcion);

if ($stmt->execute()) {
    $id_avance = $stmt->insert_id;
    $stmt->close();

    $fotoMensaje = "Sin foto";

    // 4. Guardar la fotografía si fue adjuntada
    if (!empty($fotoBase64)) {
        $dirUploads = __DIR__ . '/uploads/obras/';
        if (!file_exists($dirUploads)) {
            mkdir($dirUploads, 0777, true);
        }

        $fotoLimpia = preg_replace('#^data:image/\w+;base64,#i', '', $fotoBase64);
        $nombreArchivo = 'obra_' . $id_obra . '_' . time() . '.jpg';
        $rutaCompleta = $dirUploads . $nombreArchivo;
        $rutaBD = 'uploads/obras/' . $nombreArchivo;

        if (file_put_contents($rutaCompleta, base64_decode($fotoLimpia))) {
            $stmtFoto = $conexion->prepare("INSERT INTO foto_obra (id_obra, ruta_imagen, descripcion, fecha) VALUES (?, ?, ?, ?)");
            if ($stmtFoto) {
                $stmtFoto->bind_param("isss", $id_obra, $rutaBD, $descripcion, $fechaActual);
                if ($stmtFoto->execute()) {
                    $fotoMensaje = "Foto guardada correctamente.";
                } else {
                    $fotoMensaje = "Error al registrar la foto en BD: " . $stmtFoto->error;
                }
                $stmtFoto->close();
            } else {
                $fotoMensaje = "Error al preparar inserción de foto: " . $conexion->error;
            }
        } else {
            $fotoMensaje = "Error al guardar la foto en disco local.";
        }
    }

    echo json_encode([
        "status" => "success", 
        "message" => "¡Avance guardado con éxito! (ID Registro: $id_avance)",
        "foto_status" => $fotoMensaje
    ]);
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Error al insertar el avance: " . $stmt->error
    ]);
}
?>