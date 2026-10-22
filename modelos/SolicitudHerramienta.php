
<?php

require_once __DIR__ . "/Conexion.php";

class SolicitudHerramienta
{
    private $conexion;

    public function __construct($conexion = null)
    {
        if ($conexion instanceof PDO) {
            $this->conexion = $conexion;
        } else {
            $db = new Conexion();
            $this->conexion = $db->conectar();
        }
    }

    public function crear($id_obra, $id_usuario, $observaciones = null)
    {
        $sql = "INSERT INTO solicitud_herramienta
                (id_obra, id_usuario, observaciones)
                VALUES (?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $id_obra,
            $id_usuario,
            $observaciones
        ]);

        return $this->conexion->lastInsertId();
    }
    public function marcarEntregada($id_solicitud)
    {
        $sql = "UPDATE solicitud_herramienta
            SET estado = 'Entregada'
            WHERE id_solicitud = ?
              AND estado = 'Aprobada'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([(int)$id_solicitud]);

        return $stmt->rowCount() > 0;
    }

    public function agregarDetalle($id_solicitud, $id_herramienta, $cantidad)
    {
        if ((int)$cantidad <= 0) {
            return false;
        }

        $sql = "INSERT INTO detalle_solicitud_herramienta
                (id_solicitud, id_herramienta, cantidad)
                VALUES (?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $id_solicitud,
            $id_herramienta,
            $cantidad
        ]);
    }

    public function obtenerTodas()
    {
        $sql = "SELECT
                    s.id_solicitud,
                    s.id_obra,
                    s.id_usuario,
                    s.fecha,
                    s.estado,
                    s.observaciones,
                    o.nombre_obra AS obra,
                    CONCAT(u.nombre, ' ', u.apellido) AS solicitante
                FROM solicitud_herramienta s
                INNER JOIN obra o ON o.id_obra = s.id_obra
                INNER JOIN usuario u ON u.id_usuario = s.id_usuario
                ORDER BY s.fecha DESC";

        return $this->conexion
            ->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorObra($id_obra)
    {
        $sql = "SELECT
                    s.id_solicitud,
                    s.id_obra,
                    s.id_usuario,
                    s.fecha,
                    s.estado,
                    s.observaciones,
                    CONCAT(u.nombre, ' ', u.apellido) AS solicitante
                FROM solicitud_herramienta s
                INNER JOIN usuario u ON u.id_usuario = s.id_usuario
                WHERE s.id_obra = ?
                ORDER BY s.fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_solicitud)
    {
        $sql = "SELECT
                    s.*,
                    o.nombre_obra AS obra,
                    CONCAT(u.nombre, ' ', u.apellido) AS solicitante
                FROM solicitud_herramienta s
                INNER JOIN obra o ON o.id_obra = s.id_obra
                INNER JOIN usuario u ON u.id_usuario = s.id_usuario
                WHERE s.id_solicitud = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_solicitud]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalle($id_solicitud)
    {
        $sql = "SELECT
                    d.id_detalle,
                    d.id_herramienta,
                    d.cantidad,
                    h.nombre AS herramienta
                FROM detalle_solicitud_herramienta d
                INNER JOIN herramienta h
                    ON h.id_herramienta = d.id_herramienta
                WHERE d.id_solicitud = ?
                ORDER BY d.id_detalle";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_solicitud]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarEstado($id_solicitud, $estado)
    {
        $estados = [
            'Pendiente',
            'Aprobada',
            'Rechazada',
            'Entregada'
        ];

        if (!in_array($estado, $estados, true)) {
            return false;
        }

        $sql = "UPDATE solicitud_herramienta
                SET estado = ?
                WHERE id_solicitud = ?
                AND estado = 'Pendiente'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$estado, $id_solicitud]);

        return $stmt->rowCount() > 0;
    }

    public function cambiarEstadoPendiente($id_solicitud, $estado)
    {
        $estadosPermitidos = ["Aprobada", "Rechazada"];

        if (!in_array($estado, $estadosPermitidos, true)) {
            return false;
        }

        $sql = "UPDATE solicitud_herramienta
            SET estado = ?
            WHERE id_solicitud = ?
              AND estado = 'Pendiente'";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $estado,
            (int)$id_solicitud
        ]);

        return $stmt->rowCount() > 0;
    }
}
