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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo_incidencia = trim($_POST['tipo_incidencia'] ?? 'Material');
    $id_obra = intval($_POST['id_obra'] ?? 9);
    $estado = 'Pendiente';

    // Lista de tipos válidos según la base de datos
    $tiposValidos = [
        'Material', 'Seguridad', 'Clima', 'Herramientas', 
        'Personal', 'Cliente', 'Diseño/Planos', 'Retraso'
    ];

    if (!in_array($tipo_incidencia, $tiposValidos)) {
        $tipo_incidencia = 'Material';
    }

    if (empty($descripcion)) {
        echo json_encode(["status" => "error", "message" => "Debe ingresar una descripción de la incidencia."]);
        exit();
    }

    $conexion->begin_transaction();

    try {
        $fechaActual = date('Y-m-d H:i:s');

        // 1. Insertar la incidencia principal
        $stmt = $conexion->prepare("INSERT INTO incidencia (id_obra, fecha, tipo_incidencia, descripcion, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_obra, $fechaActual, $tipo_incidencia, $descripcion, $estado);
        $stmt->execute();

        $id_incidencia = $conexion->insert_id;
        $stmt->close();

        $dirSubida = "uploads/incidencias/";
        if (!file_exists($dirSubida)) {
            mkdir($dirSubida, 0777, true);
        }

        $rutaFotoFinal = null;

        // 2A. Opción Celular (Mobile): Envío por $_FILES
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $nombreFoto = "incidencia_" . $id_incidencia . "_" . time() . "." . ($ext ?: "jpg");
            $rutaFotoFinal = $dirSubida . $nombreFoto;
            move_uploaded_file($_FILES['foto']['tmp_name'], $rutaFotoFinal);
        }
        // 2B. Opción Web: Envío en Base64
        else if (!empty($_POST['foto_base64'])) {
            $base64Data = $_POST['foto_base64'];
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $ext = strtolower($type[1]);
            } else {
                $ext = 'jpg';
            }
            $base64Data = base64_decode($base64Data);
            if ($base64Data !== false) {
                $nombreFoto = "incidencia_" . $id_incidencia . "_" . time() . "." . $ext;
                $rutaFotoFinal = $dirSubida . $nombreFoto;
                file_put_contents($rutaFotoFinal, $base64Data);
            }
        }

        // 3. Si se adjuntó foto, insertamos el registro
        if ($rutaFotoFinal) {
            $stmtFoto = $conexion->prepare("INSERT INTO foto_incidencia (id_incidencia, ruta_foto, fecha) VALUES (?, ?, ?)");
            $stmtFoto->bind_param("iss", $id_incidencia, $rutaFotoFinal, $fechaActual);
            $stmtFoto->execute();
            $stmtFoto->close();
        }

        $conexion->commit();
        echo json_encode([
            "status" => "success",
            "message" => "Incidencia guardada con éxito."
        ]);

    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["status" => "error", "message" => "Error al guardar: " . $e->getMessage()]);
    }
    exit();
}

$conexion->close();
?>