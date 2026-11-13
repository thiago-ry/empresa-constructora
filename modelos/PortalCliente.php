
<?php

require_once __DIR__ . "/Conexion.php";

class PortalCliente
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    // ==========================================
    // OBRAS DEL CLIENTE
    // ==========================================

    public function obtenerObrasCliente($idCliente)
    {
        $sql = "
            SELECT
                o.id_obra,
                o.nombre_obra,
                o.direccion,
                o.descripcion,
                o.fecha_inicio,
                o.fecha_fin,
                o.estado,
                o.activo
            FROM obra o
            WHERE o.id_usuario = :id_cliente
              AND o.activo = 1
            ORDER BY o.id_obra DESC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_cliente" => $idCliente
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerObraCliente($idObra, $idCliente)
    {
        $sql = "
            SELECT
                o.*,

                cliente.nombre AS nombre_cliente,
                cliente.apellido AS apellido_cliente,

                jefe.nombre AS nombre_jefe,
                jefe.apellido AS apellido_jefe,

                capataz.nombre AS nombre_capataz,
                capataz.apellido AS apellido_capataz

            FROM obra o

            INNER JOIN usuario cliente
                ON cliente.id_usuario = o.id_usuario

            LEFT JOIN usuario jefe
                ON jefe.id_usuario = o.id_jefe_obra

            LEFT JOIN usuario capataz
                ON capataz.id_usuario = o.id_capataz

            WHERE o.id_obra = :id_obra
              AND o.id_usuario = :id_cliente
              AND o.activo = 1

            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra,
            ":id_cliente" => $idCliente
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // AVANCES DIARIOS
    // ==========================================

    public function obtenerAvances($idObra)
    {
        $sql = "
            SELECT
                id_avance_diario,
                id_obra,
                fecha,
                descripcion
            FROM avance_diario
            WHERE id_obra = :id_obra
            ORDER BY fecha DESC, id_avance_diario DESC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // ETAPAS
    // ==========================================

    public function obtenerEtapas($idObra)
    {
        $sql = "
            SELECT
                id_etapa,
                id_obra,
                nombre_etapa,
                descripcion,
                fecha_inicio,
                fecha_fin,
                estado
            FROM etapa_obra
            WHERE id_obra = :id_obra
            ORDER BY id_etapa ASC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenEtapas($idObra)
    {
        $sql = "
            SELECT
                COUNT(*) AS total,

                SUM(
                    CASE WHEN estado = 'Finalizada'
                    THEN 1 ELSE 0 END
                ) AS finalizadas,

                SUM(
                    CASE WHEN estado = 'En Proceso'
                    THEN 1 ELSE 0 END
                ) AS en_proceso,

                SUM(
                    CASE WHEN estado = 'Pendiente'
                    THEN 1 ELSE 0 END
                ) AS pendientes,

                SUM(
                    CASE WHEN estado = 'Cancelada'
                    THEN 1 ELSE 0 END
                ) AS canceladas

            FROM etapa_obra
            WHERE id_obra = :id_obra
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = (int)($resumen["total"] ?? 0);
        $finalizadas = (int)($resumen["finalizadas"] ?? 0);

        $resumen["avance"] = $total > 0
            ? round(($finalizadas / $total) * 100)
            : 0;

        return $resumen;
    }

    // ==========================================
    // FOTOGRAFÍAS
    // ==========================================

    public function obtenerFotografias($idObra)
    {
        $sql = "
            SELECT
                id_foto,
                id_obra,
                ruta_imagen,
                descripcion,
                fecha
            FROM foto_obra
            WHERE id_obra = :id_obra
            ORDER BY fecha DESC, id_foto DESC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // DOCUMENTACIÓN
    // ==========================================

    public function obtenerDocumentos($idObra)
    {
        $sql = "
            SELECT
                id_documento,
                id_obra,
                tipo_documento,
                nombre_archivo,
                ruta_archivo,
                descripcion,
                fecha_subida
            FROM documento_obra
            WHERE id_obra = :id_obra
            ORDER BY fecha_subida DESC, id_documento DESC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // CAPATAZ
    // ==========================================

    public function obtenerCapataz($idObra)
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.nombre,
                u.apellido
            FROM obra o
            INNER JOIN usuario u
                ON u.id_usuario = o.id_capataz
            WHERE o.id_obra = :id_obra
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // JEFE DE OBRA
    // ==========================================

    public function obtenerJefeObra($idObra)
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.nombre,
                u.apellido
            FROM obra o
            INNER JOIN usuario u
                ON u.id_usuario = o.id_jefe_obra
            WHERE o.id_obra = :id_obra
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // EMPLEADOS DE LA OBRA
    // ==========================================

    public function obtenerEmpleados($idObra)
    {
        $sql = "
            SELECT
                eo.id_empleado_obra,
                eo.fecha_ingreso,
                u.nombre,
                u.apellido,
                c.nombre_cargo

            FROM empleado_obra eo

            INNER JOIN usuario u
                ON u.id_usuario = eo.id_usuario

            LEFT JOIN cargo c
                ON c.id_cargo = eo.id_cargo

            WHERE eo.id_obra = :id_obra
              AND eo.estado = 1
              AND u.estado = 1
              AND u.id_rol = 1

            ORDER BY u.apellido, u.nombre
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // PRESUPUESTOS
    // ==========================================

    public function obtenerPresupuestos($idObra)
    {
        $sql = "
            SELECT
                id_presupuesto,
                id_obra,
                nombre_presupuesto,
                fecha,
                version,
                costo_total,
                costo_final_con_impuestos,
                CASE
                    WHEN costo_final_con_impuestos > 0
                    THEN costo_final_con_impuestos
                    ELSE costo_total
                END AS monto_cliente,
                estado,
                detalle_general,
                fecha_aprobacion,
                validez_dias,
                forma_pago
            FROM presupuesto
            WHERE id_obra = :id_obra
              AND estado IN ('Activo', 'Aprobado')
            ORDER BY version DESC, fecha DESC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_obra" => $idObra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // DETALLE DEL PRESUPUESTO
    // ==========================================

    public function obtenerDetallePresupuesto($idPresupuesto, $idObra)
    {
        $sql = "
            SELECT
                dp.id_detalle,
                COALESCE(r.nombre_rubro, dp.descripcion) AS concepto,
                dp.descripcion,
                dp.unidad_medida,
                dp.cantidad,
                dp.subtotal

            FROM detalle_presupuesto dp

            INNER JOIN presupuesto p
                ON p.id_presupuesto = dp.id_presupuesto

            LEFT JOIN rubros_obra r
                ON r.id_rubro = dp.id_rubro

            WHERE dp.id_presupuesto = :id_presupuesto
              AND p.id_obra = :id_obra
              AND p.estado IN ('Activo', 'Aprobado')

            ORDER BY dp.id_detalle ASC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_presupuesto" => $idPresupuesto,
            ":id_obra" => $idObra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // MANO DE OBRA DEL PRESUPUESTO
    // ==========================================

    public function obtenerManoObraPresupuesto($idPresupuesto, $idObra)
    {
        $sql = "
            SELECT
                mo.id_mano_obra_pres,
                mo.descripcion,
                mo.cantidad_horas,
                mo.subtotal

            FROM mano_obra_presupuesto mo

            INNER JOIN presupuesto p
                ON p.id_presupuesto = mo.id_presupuesto

            WHERE mo.id_presupuesto = :id_presupuesto
              AND p.id_obra = :id_obra
              AND p.estado IN ('Activo', 'Aprobado')

            ORDER BY mo.id_mano_obra_pres ASC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ":id_presupuesto" => $idPresupuesto,
            ":id_obra" => $idObra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
