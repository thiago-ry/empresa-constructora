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


    /*
    ==========================================================
        OBTENER TODOS LOS EMPLEADOS
    ==========================================================
    */

    public function obtenerTodos()
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

                WHERE r.id_rol = 1

                ORDER BY
                    u.apellido ASC,
                    u.nombre ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        OBTENER EMPLEADO POR ID
    ==========================================================
    */

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

                AND r.id_rol = 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        BUSCAR EMPLEADOS
    ==========================================================
    */

    public function buscar($texto)
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

                WHERE r.id_rol = 1

                AND (
                    u.nombre LIKE ?
                    OR u.apellido LIKE ?
                    OR u.documento LIKE ?
                    OR u.correo LIKE ?
                    OR CONCAT(u.nombre, ' ', u.apellido) LIKE ?
                )

                ORDER BY
                    u.apellido ASC,
                    u.nombre ASC";

        $buscar = "%" . $texto . "%";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $buscar,
            $buscar,
            $buscar,
            $buscar,
            $buscar
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        ESTADÍSTICAS
    ==========================================================
    */

    public function obtenerEstadisticas()
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
                    ) AS inactivos

                FROM usuario

                WHERE id_rol = 1";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        VERIFICAR CORREO
    ==========================================================
    */

    public function existeCorreo($correo, $idExcluir = null)
    {
        if ($idExcluir !== null) {

            $sql = "SELECT COUNT(*)
                    FROM usuario

                    WHERE correo = ?

                    AND id_usuario <> ?";

            $stmt = $this->conexion->prepare($sql);

            $stmt->execute([
                $correo,
                $idExcluir
            ]);

        } else {

            $sql = "SELECT COUNT(*)
                    FROM usuario
                    WHERE correo = ?";

            $stmt = $this->conexion->prepare($sql);

            $stmt->execute([
                $correo
            ]);
        }

        return $stmt->fetchColumn() > 0;
    }


    /*
    ==========================================================
        AGREGAR EMPLEADO
    ==========================================================
    */

    public function agregar($datos)
    {
        $sql = "INSERT INTO usuario
                (
                    id_rol,
                    nombre,
                    apellido,
                    documento,
                    correo,
                    contraseña,
                    telefono,
                    direccion,
                    salario,
                    estado
                )

                VALUES
                (
                    1,
                    :nombre,
                    :apellido,
                    :documento,
                    :correo,
                    :contraseña,
                    :telefono,
                    :direccion,
                    :salario,
                    1
                )";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([

            ":nombre" => $datos["nombre"],

            ":apellido" => $datos["apellido"],

            ":documento" => $datos["documento"] ?: null,

            ":correo" => $datos["correo"],

            ":contraseña" => password_hash(
                $datos["contraseña"],
                PASSWORD_DEFAULT
            ),

            ":telefono" => $datos["telefono"] ?: null,

            ":direccion" => $datos["direccion"] ?: null,

            ":salario" =>
                $datos["salario"] !== ""
                    ? $datos["salario"]
                    : null

        ]);

        return $this->conexion->lastInsertId();
    }


    /*
    ==========================================================
        EDITAR EMPLEADO
    ==========================================================
    */

    public function editar($datos)
    {
        $sql = "UPDATE usuario

                SET
                    nombre = :nombre,
                    apellido = :apellido,
                    documento = :documento,
                    correo = :correo,
                    telefono = :telefono,
                    direccion = :direccion,
                    salario = :salario

                WHERE id_usuario = :id_usuario

                AND id_rol = 1";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            ":nombre" => $datos["nombre"],

            ":apellido" => $datos["apellido"],

            ":documento" => $datos["documento"] ?: null,

            ":correo" => $datos["correo"],

            ":telefono" => $datos["telefono"] ?: null,

            ":direccion" => $datos["direccion"] ?: null,

            ":salario" =>
                $datos["salario"] !== ""
                    ? $datos["salario"]
                    : null,

            ":id_usuario" => $datos["id_usuario"]

        ]);
    }


    /*
    ==========================================================
        DAR DE BAJA
    ==========================================================
    */

    public function bajaLogica($id)
    {
        $sql = "UPDATE usuario

                SET estado = 0

                WHERE id_usuario = ?

                AND id_rol = 1";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([$id]);
    }


    /*
    ==========================================================
        ACTIVAR
    ==========================================================
    */

    public function activar($id)
    {
        $sql = "UPDATE usuario

                SET estado = 1

                WHERE id_usuario = ?

                AND id_rol = 1";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([$id]);
    }


    /*
    ==========================================================
        OBTENER CARGOS
    ==========================================================
    */

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

        $stmt->execute([
            $id_usuario
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        OBTENER TODOS LOS CARGOS
    ==========================================================
    */

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


    /*
    ==========================================================
        GUARDAR CARGOS
    ==========================================================
    */

    public function guardarCargos($id_usuario, $cargos)
    {
        $this->conexion->beginTransaction();

        try {

            $sqlEliminar = "DELETE FROM empleado_cargo
                            WHERE id_usuario = ?";

            $stmtEliminar =
                $this->conexion->prepare($sqlEliminar);

            $stmtEliminar->execute([
                $id_usuario
            ]);


            if (!empty($cargos)) {

                $sqlInsertar =
                    "INSERT INTO empleado_cargo
                    (
                        id_usuario,
                        id_cargo
                    )

                    VALUES
                    (
                        ?,
                        ?
                    )";

                $stmtInsertar =
                    $this->conexion->prepare($sqlInsertar);


                foreach ($cargos as $id_cargo) {

                    $stmtInsertar->execute([
                        $id_usuario,
                        intval($id_cargo)
                    ]);
                }
            }


            $this->conexion->commit();

            return true;


        } catch (PDOException $e) {

            $this->conexion->rollBack();

            throw $e;
        }
    }


    /*
    ==========================================================
        OBRAS DEL EMPLEADO
    ==========================================================
    */

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

        $stmt->execute([
            $id_usuario
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
    ==========================================================
        CONTAR OBRAS
    ==========================================================
    */

    public function contarObras($id_usuario)
    {
        $sql = "SELECT COUNT(*)

                FROM empleado_obra

                WHERE id_usuario = ?";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $id_usuario
        ]);

        return $stmt->fetchColumn();
    }


    /*
    ==========================================================
        CONEXIÓN
    ==========================================================
    */

    public function getConexion()
    {
        return $this->conexion;
    }
}