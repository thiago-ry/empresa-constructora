<?php

require_once "Conexion.php";

class EmpleadoObra
{

    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    public function obtenerPorObra($id_obra)
    {
        $sql = "SELECT
                    eo.*,
                    e.nombre,
                    e.apellido,
                    e.documento,
                    e.telefono
                FROM empleado_obra eo
                INNER JOIN empleado e
                    ON eo.id_empleado = e.id_empleado
                WHERE eo.id_obra = ?
                ORDER BY eo.estado ASC, e.apellido ASC, e.nombre ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obtenerActivos($id_obra)
    {
        $sql = "SELECT
                    eo.*,
                    e.nombre,
                    e.apellido,
                    e.documento,
                    e.telefono
                FROM empleado_obra eo
                INNER JOIN empleado e
                    ON eo.id_empleado = e.id_empleado
                WHERE eo.id_obra = ?
                AND eo.estado=1
                ORDER BY e.apellido,e.nombre";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obtenerRetirados($id_obra)
    {
        $sql = "SELECT
                    eo.*,
                    e.nombre,
                    e.apellido,
                    e.documento,
                    e.telefono
                FROM empleado_obra eo
                INNER JOIN empleado e
                    ON eo.id_empleado=e.id_empleado
                WHERE eo.id_obra=?
                AND eo.estado=0
                ORDER BY eo.fecha_egreso DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function buscarPorId($id)
    {
        $sql = "SELECT
                    eo.*,
                    e.nombre,
                    e.apellido,
                    e.documento
                FROM empleado_obra eo
                INNER JOIN empleado e
                    ON eo.id_empleado=e.id_empleado
                WHERE eo.id_empleado_obra=?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function existeEmpleadoActivo($idEmpleado, $idObra)
    {
        $sql = "SELECT COUNT(*)
                FROM empleado_obra
                WHERE id_empleado=?
                AND id_obra=?
                AND estado=1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idEmpleado, $idObra]);

        return $stmt->fetchColumn() > 0;
    }

    public function asignar($datos)
    {
        $sql = "INSERT INTO empleado_obra(

                    id_empleado,
                    id_obra,
                    fecha_ingreso,
                    estado,
                    observaciones,
                    id_usuario

                )VALUES(?,?,?,?,?,?)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            $datos["id_empleado"],
            $datos["id_obra"],
            $datos["fecha_ingreso"],
            1,
            $datos["observaciones"],
            $datos["id_usuario"]

        ]);
    }

    public function editar($datos)
    {
        $sql = "UPDATE empleado_obra SET

                    fecha_ingreso=?,
                    observaciones=?

                WHERE id_empleado_obra=?";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            $datos["fecha_ingreso"],
            $datos["observaciones"],
            $datos["id_empleado_obra"]

        ]);
    }

public function retirar($datos)
{
    $sql = "UPDATE empleado_obra SET

                fecha_egreso=?,
                motivo_egreso=?,
                estado=0,
                observaciones=?

            WHERE id_empleado_obra=?";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([

        $datos["fecha_egreso"],
        $datos["motivo_egreso"],
        $datos["observaciones"],
        $datos["id_empleado_obra"]

    ]);
}

public function obtenerResumen($idObra)
{
    $sql = "SELECT

            SUM(CASE WHEN estado=1 THEN 1 ELSE 0 END) activos,

            SUM(CASE WHEN estado=0 THEN 1 ELSE 0 END) retirados,

            COUNT(*) total

            FROM empleado_obra

            WHERE id_obra=?";


    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([$idObra]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
