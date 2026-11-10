
<?php

require_once "Conexion.php";


class SolicitudMaterial
{
    private $db;

    public function __construct()
    {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    public function crear($id_obra)
    {
        $sql = "INSERT INTO solicitud_material
                (id_obra, fecha, estado)
                VALUES
                (:id_obra, CURDATE(), 'Pendiente')";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":id_obra", $id_obra, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    public function agregarDetalle($id_solicitud, $id_material, $cantidad)
    {
        $sql = "INSERT INTO detalle_solicitud_material
                (id_solicitud, id_material, cantidad)
                VALUES
                (:id_solicitud, :id_material, :cantidad)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":id_solicitud", $id_solicitud, PDO::PARAM_INT);
        $stmt->bindParam(":id_material", $id_material, PDO::PARAM_INT);
        $stmt->bindParam(":cantidad", $cantidad);

        return $stmt->execute();
    }

    public function obtenerTodas()
    {
        $sql = "SELECT
                sm.id_solicitud,
                sm.id_obra,
                sm.fecha,
                sm.estado,
                o.nombre_obra,
                COUNT(dsm.id_detalle) AS cantidad_materiales
            FROM solicitud_material sm
            INNER JOIN obra o
                ON o.id_obra = sm.id_obra
            LEFT JOIN detalle_solicitud_material dsm
                ON dsm.id_solicitud = sm.id_solicitud
            GROUP BY
                sm.id_solicitud,
                sm.id_obra,
                sm.fecha,
                sm.estado,
                o.nombre_obra
            ORDER BY sm.id_solicitud DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


/*
==================================================
    HISTORIAL GENERAL DE MATERIALES SOLICITADOS
==================================================
*/

public function obtenerHistorialMateriales($id_obra = null)
{
    $sql = "
        SELECT
            sm.id_solicitud,
            sm.fecha,
            sm.estado,

            o.id_obra,
            o.nombre_obra,

            m.id_material,
            m.nombre_material,
            m.unidad_medida,

            dsm.cantidad AS cantidad_solicitada,

            COALESCE(
                entregas.cantidad_entregada,
                0
            ) AS cantidad_entregada

        FROM solicitud_material sm

        INNER JOIN obra o
            ON sm.id_obra = o.id_obra

        INNER JOIN detalle_solicitud_material dsm
            ON sm.id_solicitud = dsm.id_solicitud

        INNER JOIN material m
            ON dsm.id_material = m.id_material

        LEFT JOIN (
            SELECT
                em.id_solicitud,
                dem.id_material,
                SUM(dem.cantidad_entregada)
                    AS cantidad_entregada

            FROM entrega_material em

            INNER JOIN detalle_entrega_material dem
                ON em.id_entrega = dem.id_entrega

            GROUP BY
                em.id_solicitud,
                dem.id_material

        ) entregas
            ON entregas.id_solicitud = sm.id_solicitud
            AND entregas.id_material = dsm.id_material
    ";

    if ($id_obra !== null) {
        $sql .= " WHERE sm.id_obra = :id_obra ";
    }

    $sql .= "
        ORDER BY
            sm.fecha DESC,
            sm.id_solicitud DESC,
            m.nombre_material ASC
    ";

    $stmt = $this->db->prepare($sql);

    if ($id_obra !== null) {
        $stmt->bindValue(
            ":id_obra",
            $id_obra,
            PDO::PARAM_INT
        );
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function obtenerPorObra($id_obra)
    {
        $sql = "SELECT
                    sm.id_solicitud,
                    sm.id_obra,
                    sm.fecha,
                    sm.estado,
                    COUNT(dsm.id_detalle) AS cantidad_materiales
                FROM solicitud_material sm
                LEFT JOIN detalle_solicitud_material dsm
                    ON dsm.id_solicitud = sm.id_solicitud
                WHERE sm.id_obra = :id_obra
                GROUP BY
                    sm.id_solicitud,
                    sm.id_obra,
                    sm.fecha,
                    sm.estado
                ORDER BY sm.id_solicitud DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":id_obra", $id_obra, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una solicitud específica.
     */
    public function obtenerPorId($id_solicitud)
    {
        $sql = "SELECT
                    sm.id_solicitud,
                    sm.id_obra,
                    sm.fecha,
                    sm.estado
                FROM solicitud_material sm
                WHERE sm.id_solicitud = :id_solicitud";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(
            ":id_solicitud",
            $id_solicitud,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener los materiales de una solicitud.
     */
    public function obtenerDetalle($id_solicitud)
    {
        $sql = "SELECT
                    dsm.id_detalle,
                    dsm.id_solicitud,
                    dsm.id_material,
                    dsm.cantidad,
                    m.nombre_material,
                    m.unidad_medida,
                    m.stock
                FROM detalle_solicitud_material dsm
                INNER JOIN material m
                    ON m.id_material = dsm.id_material
                WHERE dsm.id_solicitud = :id_solicitud
                ORDER BY dsm.id_detalle ASC";

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
     * Cambiar el estado de una solicitud.
     */
    public function cambiarEstado($id_solicitud, $estado)
    {
        $estadosPermitidos = [
            "Pendiente",
            "Aprobada",
            "Rechazada",
            "Entregada"
        ];

        if (!in_array($estado, $estadosPermitidos, true)) {
            return false;
        }

        $sql = "UPDATE solicitud_material
                SET estado = :estado
                WHERE id_solicitud = :id_solicitud";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(
            ":id_solicitud",
            $id_solicitud,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    /**
     * Eliminar una solicitud.
     * Los detalles se eliminan automáticamente
     * gracias al ON DELETE CASCADE.
     */
    public function eliminar($id_solicitud)
    {
        $sql = "DELETE FROM solicitud_material
                WHERE id_solicitud = :id_solicitud";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(
            ":id_solicitud",
            $id_solicitud,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }
}
