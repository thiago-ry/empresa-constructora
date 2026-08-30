<?php

require_once "Conexion.php";

class Obra
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    /*
    =====================================
        OBTENER TODAS LAS OBRAS
    =====================================
    */
    public function obtenerTodos()
    {
        $sql = "SELECT
                    o.id_obra,
                    o.nombre_obra,
                    o.direccion,
                    o.fecha_inicio,
                    o.fecha_fin,
                    o.estado,

                    -- Cliente
                    u.nombre AS nombre_cliente,
                    u.apellido AS apellido_cliente,

                    -- Jefe de Obra
                    j.nombre AS nombre_jefe_obra,
                    j.apellido AS apellido_jefe_obra

                FROM obra o

                INNER JOIN usuario u
                    ON o.id_usuario = u.id_usuario

                LEFT JOIN usuario j
                    ON o.id_jefe_obra = j.id_usuario

                WHERE o.activo = 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    =====================================
        OBTENER ESTADOS
    =====================================
    */
    public function obtenerEstados()
    {
        $sql = "SHOW COLUMNS FROM obra LIKE 'estado'";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        preg_match(
            "/^enum\\('(.*)'\\)$/",
            $resultado["Type"],
            $matches
        );

        return explode("','", $matches[1]);
    }

    /*
    =====================================
        OBTENER OBRA POR ID
    =====================================
    */
    public function buscarPorId($id)
    {
        $sql = "SELECT
                    o.*,

                    -- Cliente
                    u.nombre AS nombre_cliente,
                    u.apellido AS apellido_cliente,

                    -- Jefe de Obra
                    j.nombre AS nombre_jefe_obra,
                    j.apellido AS apellido_jefe_obra

                FROM obra o

                INNER JOIN usuario u
                    ON o.id_usuario = u.id_usuario

                LEFT JOIN usuario j
                    ON o.id_jefe_obra = j.id_usuario

                WHERE o.id_obra = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindParam(
            ":id",
            $id,
            PDO::PARAM_INT
        );

        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    /*
    =====================================
        OBTENER JEFES DE OBRA
    =====================================
    */
    public function obtenerJefesObra()
    {
        $sql = "SELECT
                    id_usuario,
                    nombre,
                    apellido,
                    documento
                FROM usuario
                WHERE id_rol = 4
                  AND estado = 1
                ORDER BY apellido ASC, nombre ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    =====================================
        AGREGAR OBRA
    =====================================
    */
    public function agregar($datos)
    {
        $sql = "INSERT INTO obra
                (
                    id_usuario,
                    id_jefe_obra,
                    nombre_obra,
                    direccion,
                    descripcion,
                    fecha_inicio,
                    fecha_fin,
                    estado,
                    activo
                )
                VALUES
                (
                    :id_usuario,
                    :id_jefe_obra,
                    :nombre_obra,
                    :direccion,
                    :descripcion,
                    :fecha_inicio,
                    :fecha_fin,
                    :estado,
                    1
                )";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ":id_usuario"   => $datos["id_usuario"],
            ":id_jefe_obra" => $datos["id_jefe_obra"],
            ":nombre_obra"  => $datos["nombre_obra"],
            ":direccion"    => $datos["direccion"],
            ":descripcion"  => $datos["descripcion"],
            ":fecha_inicio" => $datos["fecha_inicio"],
            ":fecha_fin"    => $datos["fecha_fin"],
            ":estado"       => $datos["estado"]
        ]);

        return $this->conexion->lastInsertId();
    }

    /*
    =====================================
        EDITAR OBRA
    =====================================
    */
    public function editar($datos)
    {
        $sql = "UPDATE obra
                SET
                    id_usuario = :id_usuario,
                    id_jefe_obra = :id_jefe_obra,
                    nombre_obra = :nombre_obra,
                    direccion = :direccion,
                    descripcion = :descripcion,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    estado = :estado

                WHERE id_obra = :id_obra";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ":id_usuario"   => $datos["id_usuario"],
            ":id_jefe_obra" => $datos["id_jefe_obra"],
            ":nombre_obra"  => $datos["nombre_obra"],
            ":direccion"    => $datos["direccion"],
            ":descripcion"  => $datos["descripcion"],
            ":fecha_inicio" => $datos["fecha_inicio"],
            ":fecha_fin"    => $datos["fecha_fin"],
            ":estado"       => $datos["estado"],
            ":id_obra"      => $datos["id_obra"]
        ]);
    }

    /*
    =====================================
        BAJA LÓGICA
    =====================================
    */
    public function bajaLogica($id)
    {
        $sql = "UPDATE obra
                SET activo = 0
                WHERE id_obra = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindParam(
            ":id",
            $id,
            PDO::PARAM_INT
        );

        return $consulta->execute();
    }

    /*
    =====================================
        ACTIVAR OBRA
    =====================================
    */
    public function activarObra($id)
    {
        $sql = "UPDATE obra
                SET activo = 1
                WHERE id_obra = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindParam(
            ":id",
            $id,
            PDO::PARAM_INT
        );

        return $consulta->execute();
    }

    /*
    =====================================
        OBTENER OBRAS ACTIVAS
    =====================================
    */
    public function obtenerActivas()
    {
        $sql = "SELECT
                    o.*,

                    u.nombre,
                    u.apellido,

                    j.nombre AS nombre_jefe_obra,
                    j.apellido AS apellido_jefe_obra

                FROM obra o

                INNER JOIN usuario u
                    ON o.id_usuario = u.id_usuario

                LEFT JOIN usuario j
                    ON o.id_jefe_obra = j.id_usuario

                WHERE o.activo = 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}