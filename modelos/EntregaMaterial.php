<?php

require_once "Conexion.php";

class EntregaMaterial
{
    private $db;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    /**
     * Crear cabecera de entrega
     */
    public function crear($id_solicitud, $id_usuario, $observaciones = null)
    {
        $sql = "INSERT INTO entrega_material
                (
                    id_solicitud,
                    id_usuario,
                    observaciones
                )
                VALUES
                (
                    :id_solicitud,
                    :id_usuario,
                    :observaciones
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(
            ":id_solicitud",
            $id_solicitud,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ":id_usuario",
            $id_usuario,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ":observaciones",
            $observaciones
        );

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Agregar material a una entrega
     */
    public function agregarDetalle(
        $id_entrega,
        $id_material,
        $cantidad_entregada
    ) {
        $sql = "INSERT INTO detalle_entrega_material
                (
                    id_entrega,
                    id_material,
                    cantidad_entregada
                )
                VALUES
                (
                    :id_entrega,
                    :id_material,
                    :cantidad_entregada
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(
            ":id_entrega",
            $id_entrega,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ":id_material",
            $id_material,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ":cantidad_entregada",
            $cantidad_entregada
        );

        return $stmt->execute();
    }

    /**
     * Obtener entrega por solicitud
     */
    public function obtenerPorSolicitud($id_solicitud)
    {
        $sql = "SELECT
                    em.id_entrega,
                    em.id_solicitud,
                    em.fecha,
                    em.id_usuario,
                    em.observaciones,
                    u.nombre,
                    u.apellido
                FROM entrega_material em

                INNER JOIN usuario u
                    ON u.id_usuario = em.id_usuario

                WHERE em.id_solicitud = :id_solicitud

                ORDER BY em.fecha DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(
            ":id_solicitud",
            $id_solicitud,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener detalle de una entrega
     */
    public function obtenerDetalle($id_entrega)
    {
        $sql = "SELECT
                    dem.id_detalle_entrega,
                    dem.id_entrega,
                    dem.id_material,
                    dem.cantidad_entregada,
                    m.nombre_material,
                    m.unidad_medida
                FROM detalle_entrega_material dem

                INNER JOIN material m
                    ON m.id_material = dem.id_material

                WHERE dem.id_entrega = :id_entrega

                ORDER BY dem.id_detalle_entrega ASC";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(
            ":id_entrega",
            $id_entrega,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una entrega
     */
    public function obtenerPorId($id_entrega)
    {
        $sql = "SELECT
                    em.id_entrega,
                    em.id_solicitud,
                    em.fecha,
                    em.id_usuario,
                    em.observaciones,
                    u.nombre,
                    u.apellido
                FROM entrega_material em

                INNER JOIN usuario u
                    ON u.id_usuario = em.id_usuario

                WHERE em.id_entrega = :id_entrega";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(
            ":id_entrega",
            $id_entrega,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerHistorial()
    {
        $sql = "SELECT
                em.id_entrega,
                em.id_solicitud,
                em.fecha,
                em.id_usuario,
                em.observaciones,
                o.nombre_obra,
                u.nombre,
                u.apellido,
                COUNT(dem.id_detalle_entrega) AS cantidad_materiales
            FROM entrega_material em
            INNER JOIN solicitud_material sm
                ON sm.id_solicitud = em.id_solicitud
            INNER JOIN obra o
                ON o.id_obra = sm.id_obra
            INNER JOIN usuario u
                ON u.id_usuario = em.id_usuario
            LEFT JOIN detalle_entrega_material dem
                ON dem.id_entrega = em.id_entrega
            GROUP BY
                em.id_entrega,
                em.id_solicitud,
                em.fecha,
                em.id_usuario,
                em.observaciones,
                o.nombre_obra,
                u.nombre,
                u.apellido
            ORDER BY em.fecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
