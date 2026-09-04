<?php
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

require_once 'conexion.php';

if (!$conexion) {
    echo json_encode(["status" => "error", "message" => "Error de conexión a la Base de Datos"]);
    exit();
}

$conexion->set_charset("utf8mb4");

$inputJSON = file_get_contents("php://input");
$datos = json_decode($inputJSON, true);

if (!$datos) {
    echo json_encode(["status" => "error", "message" => "JSON no válido o datos demasiado grandes."]);
    exit();
}

$id_obra     = isset($datos['id_obra']) ? intval($datos['id_obra']) : 9;
$descripcion = trim($datos['descripcion'] ?? '');
$fotoBase64  = $datos['foto'] ?? null;
$fechaActual = date('Y-m-d');

if (empty($descripcion)) {
    echo json_encode(["status" => "error", "message" => "La descripción no puede estar vacía."]);
    exit();
}

// Inserción en avance_diario
$sqlAvance = "INSERT INTO avance_diario (id_obra, fecha, descripcion) VALUES ($id_obra, '$fechaActual', '$descripcion')";

if ($conexion->query($sqlAvance) === TRUE) {
    
    // Si hay foto, guardar en disco y registrar
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
            $sqlFoto = "INSERT INTO foto_obra (id_obra, ruta_imagen, descripcion, fecha) VALUES ($id_obra, '$rutaBD', '$descripcion', '$fechaActual')";
            $conexion->query($sqlFoto);
        }
    }

    echo json_encode(["status" => "success", "message" => "¡Guardado con éxito en avance_diario!"]);
} else {
    // Si falla MySQL, devolvemos el error exacto
    echo json_encode(["status" => "error", "message" => "Error de MySQL: " . $conexion->error]);
}
?>