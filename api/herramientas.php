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

// 📥 GET: Obtener lista de herramientas y empleados
if ($metodo === 'GET') {
    // Lista de herramientas (si usas la tabla `herramienta` o `maquinaria`)
    $sqlHerramientas = "SELECT id_herramienta, nombre, codigo, estado, ubicacion FROM herramienta ORDER BY nombre ASC";
    $resHerr = $conexion->query($sqlHerramientas);

    // Fallback si la tabla es distinta
    if (!$resHerr) {
        $sqlHerramientas = "SELECT id_equipo AS id_herramienta, nombre, codigo, estado FROM equipo ORDER BY nombre ASC";
        $resHerr = $conexion->query($sqlHerramientas);
    }

    // Lista de empleados para asignar
    $sqlEmp = "SELECT id_empleado, nombre, apellido FROM empleado WHERE activo = 1 ORDER BY apellido ASC";
    $resEmp = $conexion->query($sqlEmp);

    if (!$resEmp) {
        $sqlEmp = "SELECT id_usuario AS id_empleado, nombre, apellido FROM usuario WHERE activo = 1 ORDER BY apellido ASC";
        $resEmp = $conexion->query($sqlEmp);
    }

    $herramientas = [];
    if ($resHerr) {
        while ($row = $resHerr->fetch_assoc()) {
            $herramientas[] = $row;
        }
    }

    $empleados = [];
    if ($resEmp) {
        while ($row = $resEmp->fetch_assoc()) {
            $empleados[] = $row;
        }
    }

    echo json_encode([
        "status" => "success",
        "herramientas" => $herramientas,
        "empleados" => $empleados
    ]);
    exit();
}

// 📤 POST: Asignar o devolver herramienta
if ($metodo === 'POST') {
    $inputJSON = file_get_contents("php://input");
    $datos = json_decode($inputJSON, true);

    $id_herramienta = $datos['id_herramienta'] ?? null;
    $accion = $datos['accion'] ?? 'asignar'; // 'asignar' o 'devolver'
    $id_empleado = $datos['id_empleado'] ?? null;
    $observacion = $datos['observacion'] ?? '';

    if (!$id_herramienta) {
        echo json_encode(["status" => "error", "message" => "ID de herramienta requerido."]);
        exit();
    }

    if ($accion === 'asignar') {
        $nuevo_estado = 'En uso';
        $detalleObs = "Asignado a Empleado ID: " . $id_empleado . " - " . $observacion;
    } else {
        $nuevo_estado = 'Disponible';
        $detalleObs = "Devuelto a pañol - " . $observacion;
    }

    // Actualizamos el estado de la herramienta
    $stmt = $conexion->prepare("UPDATE herramienta SET estado = ? WHERE id_herramienta = ?");
    $stmt->bind_param("si", $nuevo_estado, $id_herramienta);

    if ($stmt->execute()) {
        // Registrar en historial si existe la tabla
        $conexion->query("INSERT INTO historial_herramienta (id_herramienta, id_empleado, accion, fecha, observacion) 
                          VALUES ($id_herramienta, " . ($id_empleado ? $id_empleado : "NULL") . ", '$accion', NOW(), '$observacion')");

        echo json_encode(["status" => "success", "message" => "Estado de la herramienta actualizado."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al actualizar."]);
    }
    $stmt->close();
    exit();
}
?>