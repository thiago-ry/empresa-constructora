<?php

require_once "Conexion.php";

class HerramientaObra
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    // =========================================================
    // OBTENER TODAS LAS ASIGNACIONES
    // =========================================================

    public function obtenerTodos()
    {
        $sql = "
            SELECT

                ho.id_herramienta_obra,
                ho.id_herramienta,
                ho.id_obra,

                h.nombre AS herramienta,
                o.nombre_obra AS obra,

                ho.cantidad_asignada,
                ho.cantidad_devuelta,

                (
                    ho.cantidad_asignada -
                    ho.cantidad_devuelta
                ) AS cantidad_pendiente,

                ho.fecha_asignacion,
                ho.observaciones,

                eh.nombre AS estado

            FROM herramienta_obra ho

            INNER JOIN herramienta h
                ON ho.id_herramienta = h.id_herramienta

            INNER JOIN obra o
                ON ho.id_obra = o.id_obra

            INNER JOIN estado_herramienta eh
                ON ho.id_estado_herramienta =
                   eh.id_estado_herramienta

            ORDER BY ho.id_herramienta_obra DESC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // OBTENER ASIGNACIÓN POR ID
    // =========================================================

    public function obtenerPorId($id)
    {
        $sql = "
            SELECT

                ho.id_herramienta_obra,
                ho.id_herramienta,
                ho.id_obra,

                h.nombre AS herramienta,

                ho.cantidad_asignada,
                ho.cantidad_devuelta,

                (
                    ho.cantidad_asignada -
                    ho.cantidad_devuelta
                ) AS cantidad_pendiente,

                ho.fecha_asignacion,
                ho.observaciones,

                eh.nombre AS estado

            FROM herramienta_obra ho

            INNER JOIN herramienta h
                ON ho.id_herramienta = h.id_herramienta

            INNER JOIN estado_herramienta eh
                ON ho.id_estado_herramienta =
                   eh.id_estado_herramienta

            WHERE ho.id_herramienta_obra = ?
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // OBTENER ASIGNACIÓN SIMPLE
    // =========================================================

    public function obtenerAsignacion($id)
    {
        $sql = "
            SELECT *
            FROM herramienta_obra
            WHERE id_herramienta_obra = ?
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // AGREGAR ASIGNACIÓN
    // =========================================================

    public function agregar($datos)
    {
        $sql = "
            INSERT INTO herramienta_obra
            (
                id_herramienta,
                id_obra,
                cantidad_asignada,
                cantidad_devuelta,
                fecha_asignacion,
                observaciones,
                id_estado_herramienta
            )
            VALUES
            (
                ?, ?, ?, 0, ?, ?, ?
            )
        ";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            $datos["id_herramienta"],
            $datos["id_obra"],
            $datos["cantidad"],
            $datos["fecha_asignacion"],
            $datos["observaciones"],
            $datos["id_estado_herramienta"]

        ]);
    }

    // =========================================================
    // EDITAR ASIGNACIÓN
    // =========================================================

    public function editar($datos)
    {
        $sql = "
            UPDATE herramienta_obra

            SET

                cantidad_asignada = ?,
                observaciones = ?

            WHERE id_herramienta_obra = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            $datos["cantidad_asignada"],
            $datos["observaciones"],
            $datos["id_herramienta_obra"]

        ]);
    }
        // =========================================================
    // ELIMINAR ASIGNACIÓN
    // =========================================================

    public function eliminar($id)
    {
        $sql = "
            DELETE FROM herramienta_obra
            WHERE id_herramienta_obra = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([$id]);
    }

    // =========================================================
    // OBTENER HERRAMIENTAS DE UNA OBRA
    // =========================================================

    public function obtenerPorObra($id_obra)
    {
        $sql = "
            SELECT

                ho.id_herramienta_obra,
                ho.id_herramienta,

                h.nombre AS herramienta,

                ho.cantidad_asignada,
                ho.cantidad_devuelta,

                (
                    ho.cantidad_asignada -
                    ho.cantidad_devuelta
                ) AS cantidad_pendiente,

                ho.fecha_asignacion,
                ho.observaciones,

                eh.nombre AS estado

            FROM herramienta_obra ho

            INNER JOIN herramienta h
                ON ho.id_herramienta = h.id_herramienta

            INNER JOIN estado_herramienta eh
                ON ho.id_estado_herramienta =
                   eh.id_estado_herramienta

            WHERE ho.id_obra = ?

            ORDER BY ho.id_herramienta_obra DESC
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // REGISTRAR DEVOLUCIÓN
    // =========================================================

    public function registrarDevolucion($datos)
    {
        $sql = "
            INSERT INTO devolucion_herramienta
            (
                id_herramienta_obra,
                cantidad,
                fecha_devolucion,
                observaciones,
                id_usuario
            )
            VALUES
            (
                ?, ?, ?, ?, ?
            )
        ";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            $datos["id_herramienta_obra"],
            $datos["cantidad"],
            $datos["fecha_devolucion"],
            $datos["observaciones"],
            $datos["id_usuario"]

        ]);
    }

    // =========================================================
    // OBTENER HISTORIAL DE DEVOLUCIONES
    // =========================================================

    public function obtenerDevoluciones($id_herramienta_obra)
    {
        $sql = "
            SELECT

                d.id_devolucion,
                d.cantidad,
                d.fecha_devolucion,
                d.observaciones,

                CONCAT(
                    u.nombre,
                    ' ',
                    u.apellido
                ) AS usuario

            FROM devolucion_herramienta d

            LEFT JOIN usuario u
                ON d.id_usuario = u.id_usuario

            WHERE d.id_herramienta_obra = ?

            ORDER BY d.fecha_devolucion DESC
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_herramienta_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // OBTENER CANTIDAD PENDIENTE
    // =========================================================

    public function obtenerCantidadPendiente($id_herramienta_obra)
    {
        $sql = "
            SELECT

                (
                    cantidad_asignada -
                    cantidad_devuelta
                ) AS cantidad_pendiente

            FROM herramienta_obra

            WHERE id_herramienta_obra = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_herramienta_obra]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado
            ? (int)$resultado["cantidad_pendiente"]
            : 0;
    }

    // =========================================================
    // ACTUALIZAR CANTIDAD DEVUELTA
    // =========================================================

    public function actualizarCantidadDevuelta(
        $id_herramienta_obra,
        $cantidad
    )
    {
        $sql = "
            UPDATE herramienta_obra

            SET
                cantidad_devuelta =
                cantidad_devuelta + ?

            WHERE id_herramienta_obra = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $cantidad,
            $id_herramienta_obra
        ]);
    }

    // =========================================================
    // MARCAR COMO DEVUELTA
    // =========================================================

    public function marcarComoDevuelta($id_herramienta_obra)
    {
        $sql = "
            UPDATE herramienta_obra

            SET id_estado_herramienta = 5

            WHERE id_herramienta_obra = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $id_herramienta_obra
        ]);
    }
}