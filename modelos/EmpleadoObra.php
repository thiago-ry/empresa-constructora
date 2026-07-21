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
    ==========================
        OBTENER POR OBRA
    ==========================
    */

    public function obtenerPorObra($id_obra)
    {
        $sql = "SELECT

                    eo.*,

                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono

                FROM empleado_obra eo

                INNER JOIN usuario u
                    ON eo.id_usuario = u.id_usuario

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                WHERE eo.id_obra = ?
                AND r.nombre_rol = 'Empleado'

                ORDER BY eo.estado DESC,
                         u.apellido ASC,
                         u.nombre ASC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    ==========================
        EMPLEADOS ACTIVOS
    ==========================
    */

    public function obtenerActivos($id_obra)
    {
        $sql = "SELECT

                    eo.*,

                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono

                FROM empleado_obra eo

                INNER JOIN usuario u
                    ON eo.id_usuario = u.id_usuario

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                WHERE eo.id_obra = ?
                AND eo.estado = 1
                AND r.nombre_rol = 'Empleado'

                ORDER BY u.apellido,
                         u.nombre";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    ==========================
        EMPLEADOS RETIRADOS
    ==========================
    */

    public function obtenerRetirados($id_obra)
    {
        $sql = "SELECT

                    eo.*,

                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono

                FROM empleado_obra eo

                INNER JOIN usuario u
                    ON eo.id_usuario = u.id_usuario

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                WHERE eo.id_obra = ?
                AND eo.estado = 0
                AND r.nombre_rol = 'Empleado'

                ORDER BY eo.fecha_egreso DESC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    ==========================
        BUSCAR POR ID
    ==========================
    */

    public function buscarPorId($id)
    {
        $sql = "SELECT

                    eo.*,

                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono

                FROM empleado_obra eo

                INNER JOIN usuario u
                    ON eo.id_usuario = u.id_usuario

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                WHERE eo.id_empleado_obra = ?
                AND r.nombre_rol = 'Empleado'";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
    ==========================
        VALIDAR DUPLICADO
    ==========================
    */

    public function existeEmpleadoActivo($idUsuario, $idObra)
    {
        $sql = "SELECT COUNT(*)

                FROM empleado_obra

                WHERE id_usuario = ?
                AND id_obra = ?
                AND estado = 1";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$idUsuario, $idObra]);

        return $stmt->fetchColumn() > 0;
    }

        /*
    ==========================
        ASIGNAR EMPLEADO
    ==========================
    */

    public function asignar($datos)
    {

        $sql = "INSERT INTO empleado_obra
                (
                    id_usuario,
                    id_obra,
                    fecha_ingreso,
                    estado,
                    observaciones
                )
                VALUES
                (
                    :id_usuario,
                    :id_obra,
                    :fecha_ingreso,
                    1,
                    :observaciones
                )";


        $stmt = $this->conexion->prepare($sql);


        return $stmt->execute([

            ":id_usuario" => $datos["id_usuario"],

            ":id_obra" => $datos["id_obra"],

            ":fecha_ingreso" => $datos["fecha_ingreso"],

            ":observaciones" => $datos["observaciones"]

        ]);

    }




    /*
    ==========================
        EDITAR ASIGNACIÓN
    ==========================
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
    ==========================
        RETIRAR EMPLEADO
    ==========================
    */

    public function retirar($datos)
    {

        $sql = "UPDATE empleado_obra

                SET

                    fecha_egreso = :fecha_egreso,

                    motivo_egreso = :motivo_egreso,

                    observaciones = :observaciones,

                    estado = 0

                WHERE id_empleado_obra = :id";


        $stmt = $this->conexion->prepare($sql);


        return $stmt->execute([

            ":fecha_egreso" => $datos["fecha_egreso"],

            ":motivo_egreso" => $datos["motivo_egreso"],

            ":observaciones" => $datos["observaciones"],

            ":id" => $datos["id_empleado_obra"]

        ]);

    }




    /*
    ==========================
        REACTIVAR EMPLEADO
    ==========================
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
    ==========================
        RESUMEN DE EMPLEADOS
    ==========================
    */

    public function obtenerResumen($id_obra)
    {

        $sql = "SELECT

                    COUNT(*) AS total,

                    SUM(CASE 
                        WHEN estado = 1 
                        THEN 1 
                        ELSE 0 
                    END) AS activos,

                    SUM(CASE 
                        WHEN estado = 0 
                        THEN 1 
                        ELSE 0 
                    END) AS retirados

                FROM empleado_obra

                WHERE id_obra = :id_obra";


        $stmt = $this->conexion->prepare($sql);


        $stmt->execute([

            ":id_obra" => $id_obra

        ]);


        return $stmt->fetch(PDO::FETCH_ASSOC);

    }




}