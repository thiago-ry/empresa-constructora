<?php

require_once __DIR__ . "/Conexion.php";

class Tarea
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    // Obras que tiene asignadas el capataz.
    public function obtenerObrasCapataz($id_usuario)
    {
        $sql = "SELECT id_obra, nombre_obra
                FROM obra
                WHERE id_capataz = ?
                ORDER BY nombre_obra";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([(int) $id_usuario]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Comprueba que el capataz tenga asignada la obra.
    public function capatazTieneObra($id_usuario, $id_obra)
    {
        $sql = "SELECT COUNT(*)
                FROM obra
                WHERE id_obra = ?
                  AND id_capataz = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            (int) $id_obra,
            (int) $id_usuario
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Empleados activos que pertenecen a la obra.
    public function obtenerEmpleadosPorObra($id_obra)
    {
        $sql = "SELECT DISTINCT
                    u.id_usuario,
                    u.nombre,
                    u.apellido
                FROM empleado_obra eo
                INNER JOIN usuario u
                    ON u.id_usuario = eo.id_usuario
                INNER JOIN roles r
                    ON r.id_rol = u.id_rol
                WHERE eo.id_obra = ?
                  AND eo.estado = 1
                  AND r.nombre_rol = 'Empleado'
                  AND u.estado = 1
                ORDER BY u.apellido, u.nombre";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([(int) $id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear una tarea y asignarla a varios empleados.
    public function crear($datos, $empleados)
    {
        $id_obra = (int) $datos["id_obra"];
        $id_capataz = (int) $datos["id_usuario"];

        if (
            $id_obra <= 0 ||
            $id_capataz <= 0 ||
            trim($datos["titulo"]) === "" ||
            trim($datos["descripcion"]) === "" ||
            !in_array(
                $datos["prioridad"],
                ["Baja", "Media", "Alta"],
                true
            ) ||
            empty($empleados)
        ) {
            throw new InvalidArgumentException(
                "Los datos de la tarea son inválidos."
            );
        }

        if (!$this->capatazTieneObra($id_capataz, $id_obra)) {
            throw new RuntimeException(
                "El capataz no tiene asignada esa obra."
            );
        }

        $empleadosObra = $this->obtenerEmpleadosPorObra($id_obra);

        $idsPermitidos = array_map(
            "intval",
            array_column($empleadosObra, "id_usuario")
        );

        $empleados = array_values(
            array_unique(array_map("intval", $empleados))
        );

        foreach ($empleados as $id_empleado) {
            if (
                $id_empleado <= 0 ||
                !in_array($id_empleado, $idsPermitidos, true)
            ) {
                throw new RuntimeException(
                    "Hay empleados que no pertenecen a la obra."
                );
            }
        }

        $this->conexion->beginTransaction();

        try {
            $sql = "INSERT INTO tarea
                        (
                            titulo,
                            id_usuario,
                            id_obra,
                            descripcion,
                            prioridad,
                            estado,
                            fecha,
                            fecha_limite
                        )
                    VALUES (?, ?, ?, ?, ?, 'Activa', CURDATE(), ?)";

            $stmt = $this->conexion->prepare($sql);

            $stmt->execute([
                trim($datos["titulo"]),
                $id_capataz,
                $id_obra,
                trim($datos["descripcion"]),
                $datos["prioridad"],
                !empty($datos["fecha_limite"])
                    ? $datos["fecha_limite"]
                    : null
            ]);

            $id_tarea = (int) $this->conexion->lastInsertId();

            $sql = "INSERT INTO tarea_empleado
                        (id_tarea, id_empleado, estado)
                    VALUES (?, ?, 'Pendiente')";

            $stmtAsignacion = $this->conexion->prepare($sql);

            foreach ($empleados as $id_empleado) {
                $stmtAsignacion->execute([
                    $id_tarea,
                    $id_empleado
                ]);
            }

            $this->conexion->commit();

            return $id_tarea;

        } catch (Exception $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $e;
        }
    }

    // Listado de tareas de las obras del capataz.
    public function listarCapataz($id_usuario)
    {
        $sql = "SELECT
                    t.id_tarea,
                    t.id_obra,
                    t.titulo,
                    t.descripcion,
                    t.fecha,
                    t.fecha_limite,
                    t.prioridad,
                    t.estado AS estado_tarea,
                    o.nombre_obra,
                    COUNT(te.id_tarea_empleado) AS total_asignados,
                    COALESCE(
                        SUM(te.estado = 'Pendiente'), 0
                    ) AS pendientes,
                    COALESCE(
                        SUM(te.estado = 'En proceso'), 0
                    ) AS en_proceso,
                    COALESCE(
                        SUM(te.estado = 'Por revisar'), 0
                    ) AS por_revisar,
                    COALESCE(
                        SUM(te.estado = 'Completada'), 0
                    ) AS completadas
                FROM tarea t
                INNER JOIN obra o
                    ON o.id_obra = t.id_obra
                LEFT JOIN tarea_empleado te
                    ON te.id_tarea = t.id_tarea
                WHERE o.id_capataz = ?
                GROUP BY
                    t.id_tarea,
                    t.id_obra,
                    t.titulo,
                    t.descripcion,
                    t.fecha,
                    t.fecha_limite,
                    t.prioridad,
                    t.estado,
                    o.nombre_obra
                ORDER BY t.fecha DESC, t.id_tarea DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([(int) $id_usuario]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tareas asignadas al empleado.
    public function listarEmpleado($id_usuario)
    {
        $sql = "SELECT
                    te.id_tarea_empleado,
                    te.estado AS estado_asignacion,
                    te.fecha_inicio,
                    te.fecha_finalizacion,
                    te.observacion_empleado,
                    te.observacion_capataz,
                    t.id_tarea,
                    t.titulo,
                    t.descripcion,
                    t.fecha,
                    t.fecha_limite,
                    t.prioridad,
                    o.nombre_obra
                FROM tarea_empleado te
                INNER JOIN tarea t
                    ON t.id_tarea = te.id_tarea
                INNER JOIN obra o
                    ON o.id_obra = t.id_obra
                WHERE te.id_empleado = ?
                ORDER BY
                    FIELD(
                        te.estado,
                        'Pendiente',
                        'En proceso',
                        'Por revisar',
                        'Completada'
                    ),
                    t.fecha_limite ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([(int) $id_usuario]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // El empleado actualiza su propia asignación.
    public function actualizarEstadoEmpleado(
        $id_tarea_empleado,
        $id_usuario,
        $estado,
        $observacion
    ) {
        if (
            !in_array(
                $estado,
                ["En proceso", "Por revisar"],
                true
            )
        ) {
            return false;
        }

        $sql = "UPDATE tarea_empleado
                SET estado = ?,
                    observacion_empleado = ?,
                    fecha_inicio = CASE
                        WHEN ? = 'En proceso'
                             AND fecha_inicio IS NULL
                        THEN NOW()
                        ELSE fecha_inicio
                    END,
                    fecha_finalizacion = CASE
                        WHEN ? = 'Por revisar'
                        THEN NOW()
                        ELSE NULL
                    END
                WHERE id_tarea_empleado = ?
                  AND id_empleado = ?
                  AND estado IN ('Pendiente', 'En proceso')";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $estado,
            $observacion,
            $estado,
            $estado,
            (int) $id_tarea_empleado,
            (int) $id_usuario
        ]);

        return $stmt->rowCount() > 0;
    }

    // Asignaciones de una tarea que puede revisar el capataz.
    public function obtenerAsignaciones($id_tarea, $id_capataz)
    {
        $sql = "SELECT
                    te.id_tarea_empleado,
                    te.estado,
                    te.observacion_empleado,
                    te.observacion_capataz,
                    te.fecha_inicio,
                    te.fecha_finalizacion,
                    u.nombre,
                    u.apellido
                FROM tarea_empleado te
                INNER JOIN tarea t
                    ON t.id_tarea = te.id_tarea
                INNER JOIN obra o
                    ON o.id_obra = t.id_obra
                INNER JOIN usuario u
                    ON u.id_usuario = te.id_empleado
                WHERE t.id_tarea = ?
                  AND o.id_capataz = ?
                ORDER BY u.apellido, u.nombre";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            (int) $id_tarea,
            (int) $id_capataz
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Confirmar o devolver una tarea para continuar.
    public function revisar(
        $id_tarea_empleado,
        $id_capataz,
        $estado,
        $observacion
    ) {
        if (
            !in_array(
                $estado,
                ["Completada", "En proceso"],
                true
            )
        ) {
            return false;
        }

        $sql = "UPDATE tarea_empleado te
                INNER JOIN tarea t
                    ON t.id_tarea = te.id_tarea
                INNER JOIN obra o
                    ON o.id_obra = t.id_obra
                SET te.estado = ?,
                    te.observacion_capataz = ?
                WHERE te.id_tarea_empleado = ?
                  AND o.id_capataz = ?
                  AND te.estado = 'Por revisar'";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $estado,
            $observacion,
            (int) $id_tarea_empleado,
            (int) $id_capataz
        ]);

        return $stmt->rowCount() > 0;
    }
}