<?php

require_once "Conexion.php";

class Asistencia
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    public function obtenerPorUsuarioFecha($id_usuario, $id_obra, $fecha)
    {
        $sql = "SELECT
                id_asistencia,
                id_usuario,
                id_obra,
                fecha,
                estado,
                hora_entrada,
                hora_salida,
                observacion
            FROM asistencia
            WHERE id_usuario = :id_usuario
              AND id_obra = :id_obra
              AND fecha = :fecha
            LIMIT 1";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id_usuario" => $id_usuario,
            ":id_obra" => $id_obra,
            ":fecha" => $fecha
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function empleadoPerteneceObraCapataz(
        $id_usuario_empleado,
        $id_usuario_capataz,
        $id_obra = 0
    ) {
        $sql = "SELECT
                    u.id_usuario,
                    u.nombre,
                    u.apellido,
                    eo.id_obra,
                    eo.id_cargo,
                    c.nombre_cargo,
                    o.nombre_obra
                FROM empleado_obra eo
                INNER JOIN usuario u
                    ON u.id_usuario = eo.id_usuario
                INNER JOIN obra o
                    ON o.id_obra = eo.id_obra
                LEFT JOIN cargo c
                    ON c.id_cargo = eo.id_cargo
                WHERE eo.id_usuario = :id_usuario_empleado
                  AND o.id_capataz = :id_usuario_capataz
                  AND o.activo = 1
                  AND u.estado = 1";

        $params = [
            ":id_usuario_empleado" => $id_usuario_empleado,
            ":id_usuario_capataz" => $id_usuario_capataz
        ];

        if ($id_obra > 0) {
            $sql .= " AND eo.id_obra = :id_obra";
            $params[":id_obra"] = $id_obra;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function marcarEntrada($id_usuario, $id_obra, $fecha, $hora)
    {
        $asistencia = $this->obtenerPorUsuarioFecha(
            $id_usuario,
            $id_obra,
            $fecha
        );

        if ($asistencia) {

            if ($asistencia["estado"] === "Ausente") {

                $sql = "UPDATE asistencia
                        SET estado = 'Presente',
                            hora_entrada = :hora_entrada,
                            hora_salida = NULL
                        WHERE id_asistencia = :id_asistencia";

                $stmt = $this->conexion->prepare($sql);

                $stmt->execute([
                    ":hora_entrada" => $hora,
                    ":id_asistencia" => $asistencia["id_asistencia"]
                ]);

                return [
                    "success" => true,
                    "mensaje" => "Entrada registrada correctamente."
                ];
            }

            return [
                "success" => false,
                "mensaje" => "El empleado ya tiene una asistencia registrada hoy en esta obra."
            ];
        }

        $sql = "INSERT INTO asistencia
                    (
                        id_usuario,
                        id_obra,
                        fecha,
                        estado,
                        hora_entrada
                    )
                VALUES
                    (
                        :id_usuario,
                        :id_obra,
                        :fecha,
                        'Presente',
                        :hora_entrada
                    )";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id_usuario" => $id_usuario,
            ":id_obra" => $id_obra,
            ":fecha" => $fecha,
            ":hora_entrada" => $hora
        ]);

        return [
            "success" => true,
            "mensaje" => "Entrada registrada correctamente."
        ];
    }

    public function marcarAusencia($id_usuario, $id_obra, $fecha, $observacion = null)
    {
        $asistencia = $this->obtenerPorUsuarioFecha(
            $id_usuario,
            $id_obra,
            $fecha
        );

        if ($asistencia) {

            if ($asistencia["estado"] === "Ausente") {
                return [
                    "success" => false,
                    "mensaje" => "El empleado ya está marcado como ausente."
                ];
            }

            return [
                "success" => false,
                "mensaje" => "El empleado ya tiene una asistencia registrada hoy en esta obra."
            ];
        }

        $sql = "INSERT INTO asistencia
                    (
                        id_usuario,
                        id_obra,
                        fecha,
                        hora_entrada,
                        hora_salida,
                        estado,
                        observacion
                    )
                VALUES
                    (
                        :id_usuario,
                        :id_obra,
                        :fecha,
                        NULL,
                        NULL,
                        'Ausente',
                        :observacion
                    )";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id_usuario" => $id_usuario,
            ":id_obra" => $id_obra,
            ":fecha" => $fecha,
            ":observacion" => $observacion
        ]);

        return [
            "success" => true,
            "mensaje" => "Ausencia registrada correctamente."
        ];
    }

    public function marcarSalida($id_usuario, $id_obra, $fecha, $hora)
    {
        $asistencia = $this->obtenerPorUsuarioFecha(
            $id_usuario,
            $id_obra,
            $fecha
        );

        if (!$asistencia) {
            return [
                "success" => false,
                "mensaje" => "El empleado todavía no tiene una entrada registrada en esta obra."
            ];
        }

        if ($asistencia["estado"] === "Ausente") {
            return [
                "success" => false,
                "mensaje" => "El empleado está marcado como ausente."
            ];
        }

        if (empty($asistencia["hora_entrada"])) {
            return [
                "success" => false,
                "mensaje" => "El empleado todavía no tiene una entrada registrada."
            ];
        }

        if (!empty($asistencia["hora_salida"])) {
            return [
                "success" => false,
                "mensaje" => "La salida ya fue registrada."
            ];
        }

        $sql = "UPDATE asistencia
                SET hora_salida = :hora_salida
                WHERE id_asistencia = :id_asistencia";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":hora_salida" => $hora,
            ":id_asistencia" => $asistencia["id_asistencia"]
        ]);

        return [
            "success" => true,
            "mensaje" => "Salida registrada correctamente."
        ];
    }

    public function obtenerHistorialPorCapataz(
        $id_usuario_capataz,
        $id_usuario_empleado = "",
        $id_obra = "",
        $fecha_desde = "",
        $fecha_hasta = ""
    ) {
        $sql = "SELECT
                    a.id_asistencia,
                    a.id_usuario,
                    a.id_obra,
                    a.fecha,
                    a.estado,
                    a.hora_entrada,
                    a.hora_salida,
                    a.observacion,
                    u.nombre,
                    u.apellido,
                    eo.id_cargo,
                    c.nombre_cargo,
                    o.nombre_obra
                FROM asistencia a
                INNER JOIN usuario u
                    ON u.id_usuario = a.id_usuario
                INNER JOIN empleado_obra eo
                    ON eo.id_usuario = a.id_usuario
                    AND eo.id_obra = a.id_obra
                INNER JOIN obra o
                    ON o.id_obra = a.id_obra
                LEFT JOIN cargo c
                    ON c.id_cargo = eo.id_cargo
                WHERE o.id_capataz = :id_capataz
                  AND o.activo = 1";

        $params = [
            ":id_capataz" => $id_usuario_capataz
        ];

        if ($id_usuario_empleado !== "") {
            $sql .= " AND a.id_usuario = :id_usuario";
            $params[":id_usuario"] = $id_usuario_empleado;
        }

        if ($id_obra !== "") {
            $sql .= " AND a.id_obra = :id_obra";
            $params[":id_obra"] = $id_obra;
        }

        if ($fecha_desde !== "") {
            $sql .= " AND a.fecha >= :fecha_desde";
            $params[":fecha_desde"] = $fecha_desde;
        }

        if ($fecha_hasta !== "") {
            $sql .= " AND a.fecha <= :fecha_hasta";
            $params[":fecha_hasta"] = $fecha_hasta;
        }

        $sql .= " ORDER BY
                    a.fecha DESC,
                    a.hora_entrada DESC,
                    u.apellido ASC,
                    u.nombre ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerHistorialPorEmpleado(
        $id_usuario_empleado,
        $id_obra = "",
        $fecha_desde = "",
        $fecha_hasta = ""
    ) {
        $sql = "SELECT
                a.id_asistencia,
                a.id_usuario,
                a.id_obra,
                a.fecha,
                a.estado,
                a.hora_entrada,
                a.hora_salida,
                a.observacion,
                o.nombre_obra
            FROM asistencia a
            INNER JOIN obra o
                ON o.id_obra = a.id_obra
            WHERE a.id_usuario = :id_usuario";

        $params = [
            ":id_usuario" => $id_usuario_empleado
        ];

        if (!empty($id_obra)) {
            $sql .= " AND a.id_obra = :id_obra";
            $params[":id_obra"] = $id_obra;
        }

        if (!empty($fecha_desde)) {
            $sql .= " AND a.fecha >= :fecha_desde";
            $params[":fecha_desde"] = $fecha_desde;
        }

        if (!empty($fecha_hasta)) {
            $sql .= " AND a.fecha <= :fecha_hasta";
            $params[":fecha_hasta"] = $fecha_hasta;
        }

        $sql .= " ORDER BY a.fecha DESC, a.hora_entrada DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obtenerObrasPorEmpleado($id_usuario_empleado)
    {
        $sql = "SELECT DISTINCT
                o.id_obra,
                o.nombre_obra
            FROM empleado_obra eo
            INNER JOIN obra o
                ON o.id_obra = eo.id_obra
            WHERE eo.id_usuario = :id_usuario
              AND o.activo = 1
            ORDER BY o.nombre_obra ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_usuario" => $id_usuario_empleado
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerEmpleadosCapataz($id_usuario_capataz)
    {
        $sql = "SELECT DISTINCT
                    u.id_usuario,
                    u.nombre,
                    u.apellido
                FROM empleado_obra eo
                INNER JOIN usuario u
                    ON u.id_usuario = eo.id_usuario
                INNER JOIN obra o
                    ON o.id_obra = eo.id_obra
                WHERE o.id_capataz = :id_capataz
                  AND o.activo = 1
                  AND u.estado = 1
                ORDER BY
                    u.apellido ASC,
                    u.nombre ASC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id_capataz" => $id_usuario_capataz
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerObrasCapataz($id_usuario_capataz)
    {
        $sql = "SELECT
                    o.id_obra,
                    o.nombre_obra
                FROM obra o
                WHERE o.id_capataz = :id_capataz
                  AND o.activo = 1
                ORDER BY o.nombre_obra ASC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id_capataz" => $id_usuario_capataz
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenPorCapataz(
        $id_usuario_capataz,
        $id_usuario_empleado = "",
        $id_obra = "",
        $fecha_desde = "",
        $fecha_hasta = ""
    ) {
        $sql = "SELECT
                    COUNT(*) AS total,

                    SUM(
                        CASE
                            WHEN a.estado = 'Presente'
                             AND a.hora_entrada IS NOT NULL
                             AND a.hora_salida IS NOT NULL
                            THEN 1
                            ELSE 0
                        END
                    ) AS completas,

                    SUM(
                        CASE
                            WHEN a.estado = 'Presente'
                             AND a.hora_entrada IS NOT NULL
                             AND a.hora_salida IS NULL
                            THEN 1
                            ELSE 0
                        END
                    ) AS sin_salida,

                    SUM(
                        CASE
                            WHEN a.estado = 'Ausente'
                            THEN 1
                            ELSE 0
                        END
                    ) AS ausentes,

                    SUM(
                        CASE
                            WHEN a.estado = 'Tarde'
                            THEN 1
                            ELSE 0
                        END
                    ) AS tardes

                FROM asistencia a

                INNER JOIN empleado_obra eo
                    ON eo.id_usuario = a.id_usuario
                    AND eo.id_obra = a.id_obra

                INNER JOIN obra o
                    ON o.id_obra = a.id_obra

                WHERE o.id_capataz = :id_capataz
                  AND o.activo = 1";

        $params = [
            ":id_capataz" => $id_usuario_capataz
        ];

        if ($id_usuario_empleado !== "") {
            $sql .= " AND a.id_usuario = :id_usuario";
            $params[":id_usuario"] = $id_usuario_empleado;
        }

        if ($id_obra !== "") {
            $sql .= " AND a.id_obra = :id_obra";
            $params[":id_obra"] = $id_obra;
        }

        if ($fecha_desde !== "") {
            $sql .= " AND a.fecha >= :fecha_desde";
            $params[":fecha_desde"] = $fecha_desde;
        }

        if ($fecha_hasta !== "") {
            $sql .= " AND a.fecha <= :fecha_hasta";
            $params[":fecha_hasta"] = $fecha_hasta;
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
