<?php

require_once "Conexion.php";

class Empleado
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    public function obtenerTodos($busqueda = "", $estado = "", $id_cargo = "")
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.id_rol,
                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono,
                    u.direccion,
                    u.salario,
                    u.correo,
                    u.fecha_registro,
                    u.estado,

                    GROUP_CONCAT(
                        DISTINCT c.nombre_cargo
                        ORDER BY c.nombre_cargo
                        SEPARATOR ', '
                    ) AS cargos

                FROM usuario u

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                LEFT JOIN empleado_cargo ec
                    ON u.id_usuario = ec.id_usuario

                LEFT JOIN cargo c
                    ON ec.id_cargo = c.id_cargo

                WHERE r.nombre_rol = 'Empleado'";

        if ($busqueda !== "") {
            $sql .= " AND (
                        u.nombre LIKE :busqueda
                        OR u.apellido LIKE :busqueda
                        OR u.documento LIKE :busqueda
                        OR u.telefono LIKE :busqueda
                        OR u.correo LIKE :busqueda
                        OR CONCAT(u.nombre, ' ', u.apellido)
                            LIKE :busqueda_completo
                        OR CONCAT(u.apellido, ' ', u.nombre)
                            LIKE :busqueda_completo2
                    )";
        }

        if (
            $estado !== "" &&
            ($estado === "1" || $estado === "0")
        ) {
            $sql .= " AND u.estado = :estado";
        }

        if (
            $id_cargo !== "" &&
            is_numeric($id_cargo)
        ) {
            $sql .= " AND EXISTS (
                        SELECT 1
                        FROM empleado_cargo ec2
                        WHERE ec2.id_usuario = u.id_usuario
                        AND ec2.id_cargo = :id_cargo
                    )";
        }

        $sql .= "
                GROUP BY
                    u.id_usuario,
                    u.id_rol,
                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.telefono,
                    u.direccion,
                    u.salario,
                    u.correo,
                    u.fecha_registro,
                    u.estado

                ORDER BY
                    u.apellido ASC,
                    u.nombre ASC
        ";

        $stmt = $this->conexion->prepare($sql);

        if ($busqueda !== "") {
            $texto = "%" . $busqueda . "%";

            $stmt->bindValue(
                ":busqueda",
                $texto,
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ":busqueda_completo",
                $texto,
                PDO::PARAM_STR
            );

            $stmt->bindValue(
                ":busqueda_completo2",
                $texto,
                PDO::PARAM_STR
            );
        }

        if (
            $estado !== "" &&
            ($estado === "1" || $estado === "0")
        ) {
            $stmt->bindValue(
                ":estado",
                (int) $estado,
                PDO::PARAM_INT
            );
        }

        if (
            $id_cargo !== "" &&
            is_numeric($id_cargo)
        ) {
            $stmt->bindValue(
                ":id_cargo",
                (int) $id_cargo,
                PDO::PARAM_INT
            );
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.id_rol,
                    u.nombre,
                    u.apellido,
                    u.documento,
                    u.correo,
                    u.telefono,
                    u.direccion,
                    u.salario,
                    u.fecha_registro,
                    u.estado,
                    r.nombre_rol

                FROM usuario u

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                WHERE u.id_usuario = ?
                AND r.nombre_rol = 'Empleado'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar($texto)
    {
        return $this->obtenerTodos(
            trim($texto),
            "",
            ""
        );
    }

    public function obtenerTodosLosCargos()
    {
        $sql = "SELECT
                    id_cargo,
                    nombre_cargo,
                    descripcion

                FROM cargo

                ORDER BY nombre_cargo ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCargos($id_usuario)
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
        $stmt->execute([$id_usuario]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEstadisticas()
    {
        $sql = "SELECT
                    COUNT(*) AS total,

                    SUM(
                        CASE
                            WHEN u.estado = 1
                            THEN 1
                            ELSE 0
                        END
                    ) AS activos,

                    SUM(
                        CASE
                            WHEN u.estado = 0
                            THEN 1
                            ELSE 0
                        END
                    ) AS inactivos

                FROM usuario u

                INNER JOIN roles r
                    ON u.id_rol = r.id_rol

                WHERE r.nombre_rol = 'Empleado'";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerObras($id_usuario)
    {
        $sql = "SELECT
                    eo.id_empleado_obra,
                    eo.id_obra,
                    eo.fecha_ingreso,
                    eo.fecha_egreso,
                    eo.motivo_egreso,
                    eo.observaciones,
                    eo.estado,
                    o.nombre_obra,
                    c.nombre_cargo

                FROM empleado_obra eo

                INNER JOIN obra o
                    ON eo.id_obra = o.id_obra

                LEFT JOIN cargo c
                    ON eo.id_cargo = c.id_cargo

                WHERE eo.id_usuario = ?

                ORDER BY
                    eo.estado DESC,
                    eo.fecha_ingreso DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_usuario]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarObras($id_usuario)
    {
        $sql = "SELECT COUNT(*)
                FROM empleado_obra
                WHERE id_usuario = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_usuario]);

        return $stmt->fetchColumn();
    }

    public function getConexion()
    {
        return $this->conexion;
    }
}