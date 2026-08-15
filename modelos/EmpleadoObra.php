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


    /*
    ==========================================================
        LISTAR EMPLEADOS DE UNA OBRA
    ==========================================================
    */

   public function obtenerPorObra($id_obra)
{
    $sql = "SELECT
                eo.*,
                u.nombre,
                u.apellido,
                u.documento,
                u.telefono,
                c.nombre_cargo

            FROM empleado_obra eo

            INNER JOIN usuario u
                ON eo.id_usuario = u.id_usuario

            INNER JOIN roles r
                ON u.id_rol = r.id_rol

            LEFT JOIN cargo c
                ON eo.id_cargo = c.id_cargo

            WHERE eo.id_obra = ?

            AND r.nombre_rol = 'Empleado'

            ORDER BY
                eo.estado DESC,
                u.apellido ASC,
                u.nombre ASC";

    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([$id_obra]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /*
    ==========================================================
        EMPLEADOS ACTIVOS
    ==========================================================
    */

    public function obtenerActivos($id_obra)
    {
        $sql = "SELECT
                    eo.*,
                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono,
                    c.nombre_cargo
                FROM empleado_obra eo

                INNER JOIN usuario u
                    ON eo.id_usuario = u.id_usuario

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                LEFT JOIN cargo c
                    ON eo.id_cargo = c.id_cargo

                WHERE eo.id_obra = ?

                AND eo.estado = 1

                AND r.nombre_rol = 'Empleado'

                ORDER BY
                    u.apellido,
                    u.nombre";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        EMPLEADOS RETIRADOS
    ==========================================================
    */

    public function obtenerRetirados($id_obra)
    {
        $sql = "SELECT
                    eo.*,
                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono,
                    c.nombre_cargo
                FROM empleado_obra eo

                INNER JOIN usuario u
                    ON eo.id_usuario = u.id_usuario

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                LEFT JOIN cargo c
                    ON eo.id_cargo = c.id_cargo

                WHERE eo.id_obra = ?

                AND eo.estado = 0

                AND r.nombre_rol = 'Empleado'

                ORDER BY
                    eo.fecha_egreso DESC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        BUSCAR ASIGNACIÓN
    ==========================================================
    */
public function buscarPorId($id)
{
    $sql = "SELECT
                eo.*,
                u.nombre,
                u.apellido,
                u.documento,
                u.telefono,
                c.nombre_cargo
            FROM empleado_obra eo

            INNER JOIN usuario u
                ON eo.id_usuario = u.id_usuario

            INNER JOIN roles r
                ON u.id_rol = r.id_rol

            LEFT JOIN cargo c
                ON eo.id_cargo = c.id_cargo

            WHERE eo.id_empleado_obra = ?

            AND r.nombre_rol = 'Empleado'";

    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    /*
    ==========================================================
        VERIFICAR SI YA ESTÁ EN UNA OBRA
    ==========================================================
    */

    public function existeEmpleadoActivo($idUsuario, $idObra)
    {
        $sql = "SELECT COUNT(*)
                FROM empleado_obra
                WHERE id_usuario = ?
                AND id_obra = ?
                AND estado = 1";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $idUsuario,
            $idObra
        ]);

        return $stmt->fetchColumn() > 0;
    }


    /*
    ==========================================================
        ASIGNAR
    ==========================================================
    */

    public function asignar($datos)
    {
        $sql = "INSERT INTO empleado_obra
                (
                    id_usuario,
                    id_obra,
                    id_cargo,
                    fecha_ingreso,
                    estado,
                    observaciones
                )
                VALUES
                (
                    :id_usuario,
                    :id_obra,
                    :id_cargo,
                    :fecha_ingreso,
                    1,
                    :observaciones
                )";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":id_usuario" => $datos["id_usuario"],
            ":id_obra" => $datos["id_obra"],
            ":id_cargo" => $datos["id_cargo"],
            ":fecha_ingreso" => $datos["fecha_ingreso"],
            ":observaciones" => $datos["observaciones"]
        ]);
    }


    /*
    ==========================================================
        EDITAR
    ==========================================================
    */

    public function editar($datos)
    {
        $sql = "UPDATE empleado_obra
                SET
                    fecha_ingreso = :fecha_ingreso,
                    observaciones = :observaciones
                WHERE id_empleado_obra = :id";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":fecha_ingreso" => $datos["fecha_ingreso"],
            ":observaciones" => $datos["observaciones"],
            ":id" => $datos["id_empleado_obra"]
        ]);
    }


    /*
    ==========================================================
        RETIRAR DE UNA OBRA
    ==========================================================
    */

    public function retirar($datos)
    {
        $sql = "UPDATE empleado_obra
                SET
                    fecha_egreso = :fecha_egreso,
                    motivo_egreso = :motivo_egreso,
                    observaciones = :observaciones,
                    estado = 0
                WHERE id_empleado_obra = :id
                AND estado = 1";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":fecha_egreso" => $datos["fecha_egreso"],
            ":motivo_egreso" => $datos["motivo_egreso"],
            ":observaciones" => $datos["observaciones"],
            ":id" => $datos["id_empleado_obra"]
        ]);
    }


    /*
    ==========================================================
        ACTIVAR
    ==========================================================
    */

public function activar($id)
{
    $sql = "UPDATE empleado_obra
            SET
                estado = 1,
                fecha_egreso = NULL,
                motivo_egreso = NULL
            WHERE id_empleado_obra = :id";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([
        ":id" => $id
    ]);
}


    /*
    ==========================================================
        RESUMEN
    ==========================================================
    */

    public function obtenerResumen($id_obra)
    {
        $sql = "SELECT
                    COUNT(*) AS total,

                    SUM(
                        CASE
                            WHEN estado = 1 THEN 1
                            ELSE 0
                        END
                    ) AS activos,

                    SUM(
                        CASE
                            WHEN estado = 0 THEN 1
                            ELSE 0
                        END
                    ) AS retirados

                FROM empleado_obra

                WHERE id_obra = :id_obra";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ":id_obra" => $id_obra
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        CARGOS DEL EMPLEADO
    ==========================================================
    */

    public function obtenerCargosEmpleado($id_usuario)
    {
        $sql = "SELECT
                    ec.id_cargo,
                    c.nombre_cargo

                FROM empleado_cargo ec

                INNER JOIN cargo c
                    ON ec.id_cargo = c.id_cargo

                WHERE ec.id_usuario = ?

                ORDER BY c.nombre_cargo ASC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $id_usuario
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        OTRAS OBRAS ACTIVAS DEL EMPLEADO
    ==========================================================
    */

    public function obtenerOtrasObrasActivas(
        $id_usuario,
        $id_obra_actual
    ) {

        $sql = "SELECT
                    eo.id_empleado_obra,
                    eo.id_obra,
                    eo.id_cargo,
                    eo.fecha_ingreso,

                    o.nombre_obra,

                    c.nombre_cargo

                FROM empleado_obra eo

                INNER JOIN obra o
                    ON eo.id_obra = o.id_obra

                LEFT JOIN cargo c
                    ON eo.id_cargo = c.id_cargo

                WHERE eo.id_usuario = ?

                AND eo.id_obra <> ?

                AND eo.estado = 1

                ORDER BY o.nombre_obra ASC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $id_usuario,
            $id_obra_actual
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        RETIRAR DE TODAS LAS OBRAS ACTIVAS
    ==========================================================
    */

    public function retirarDeTodasLasObras(
        $id_usuario,
        $fecha_egreso,
        $motivo_egreso,
        $observaciones
    ) {

        $sql = "UPDATE empleado_obra

                SET
                    fecha_egreso = :fecha_egreso,
                    motivo_egreso = :motivo_egreso,
                    observaciones = :observaciones,
                    estado = 0

                WHERE id_usuario = :id_usuario

                AND estado = 1";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":fecha_egreso" => $fecha_egreso,
            ":motivo_egreso" => $motivo_egreso,
            ":observaciones" => $observaciones,
            ":id_usuario" => $id_usuario
        ]);
    }


    /*
    ==========================================================
        RETIRAR DE OBRAS SELECCIONADAS
    ==========================================================
    */

    public function retirarDeObrasSeleccionadas(
        $id_usuario,
        $ids_obras,
        $fecha_egreso,
        $motivo_egreso,
        $observaciones
    ) {

        if (empty($ids_obras)) {
            return false;
        }


        $placeholders = implode(
            ",",
            array_fill(
                0,
                count($ids_obras),
                "?"
            )
        );


        $sql = "UPDATE empleado_obra

                SET
                    fecha_egreso = ?,
                    motivo_egreso = ?,
                    observaciones = ?,
                    estado = 0

                WHERE id_usuario = ?

                AND id_obra IN ($placeholders)

                AND estado = 1";


        $stmt = $this->conexion->prepare($sql);


        $parametros = [
            $fecha_egreso,
            $motivo_egreso,
            $observaciones,
            $id_usuario
        ];


        foreach ($ids_obras as $id_obra) {

            $parametros[] = $id_obra;

        }


        return $stmt->execute($parametros);
    }
    public function getConexion()
{
    return $this->conexion;
}
}