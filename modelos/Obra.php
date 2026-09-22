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

    public function buscarPorId($id)
    {
        $sql = "SELECT
                    o.*,

                    -- Cliente
                    u.nombre AS nombre_cliente,
                    u.apellido AS apellido_cliente,

                    -- Jefe de Obra
                    j.nombre AS nombre_jefe_obra,
                    j.apellido AS apellido_jefe_obra,

                    -- Capataz
                    c.nombre AS nombre_capataz,
                    c.apellido AS apellido_capataz

                FROM obra o

                INNER JOIN usuario u
                    ON o.id_usuario = u.id_usuario

                LEFT JOIN usuario j
                    ON o.id_jefe_obra = j.id_usuario

                LEFT JOIN usuario c
                    ON o.id_capataz = c.id_usuario

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


    public function agregar($datos)
    {
        $sql = "INSERT INTO obra
                (
                    id_usuario,
                    id_jefe_obra,
                    id_capataz,
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
                    :id_capataz,
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
            ":id_usuario"    => $datos["id_usuario"],
            ":id_jefe_obra"  => $datos["id_jefe_obra"],
            ":id_capataz"    => $datos["id_capataz"],
            ":nombre_obra"   => $datos["nombre_obra"],
            ":direccion"     => $datos["direccion"],
            ":descripcion"   => $datos["descripcion"],
            ":fecha_inicio"  => $datos["fecha_inicio"],
            ":fecha_fin"     => $datos["fecha_fin"],
            ":estado"        => $datos["estado"]
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
                    id_capataz = :id_capataz,
                    nombre_obra = :nombre_obra,
                    direccion = :direccion,
                    descripcion = :descripcion,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    estado = :estado

                WHERE id_obra = :id_obra";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ":id_usuario"    => $datos["id_usuario"],
            ":id_jefe_obra"  => $datos["id_jefe_obra"],
            ":id_capataz"    => $datos["id_capataz"],
            ":nombre_obra"   => $datos["nombre_obra"],
            ":direccion"     => $datos["direccion"],
            ":descripcion"   => $datos["descripcion"],
            ":fecha_inicio"  => $datos["fecha_inicio"],
            ":fecha_fin"     => $datos["fecha_fin"],
            ":estado"        => $datos["estado"],
            ":id_obra"       => $datos["id_obra"]
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


    public function usuarioPuedeVerObra($id_obra, $id_usuario, $id_rol)
    {
        $sql = "SELECT o.id_obra
                FROM obra o
                WHERE o.id_obra = :id_obra
                  AND o.activo = 1";

        if ($id_rol == 2) {

            // Gerente → todas

        } elseif ($id_rol == 4) {

            // Jefe de Obra → sus obras
            $sql .= " AND o.id_jefe_obra = :id_usuario";
        } elseif ($id_rol == 7) {

            // Capataz → su única obra
            $sql .= " AND o.id_capataz = :id_usuario";
        } elseif ($id_rol == 6) {

            // Cliente → sus obras
            $sql .= " AND o.id_usuario = :id_usuario";
        } else {

            return false;
        }

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindParam(
            ":id_obra",
            $id_obra,
            PDO::PARAM_INT
        );

        if ($id_rol != 2) {

            $consulta->bindParam(
                ":id_usuario",
                $id_usuario,
                PDO::PARAM_INT
            );
        }

        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC) !== false;
    }


    public function obtenerObrasSegunUsuario($id_usuario, $id_rol)
    {
        $sql = "SELECT
                    o.id_obra,
                    o.nombre_obra,
                    o.direccion,
                    o.fecha_inicio,
                    o.fecha_fin,
                    o.estado,
                    o.id_usuario,
                    o.id_jefe_obra,
                    o.id_capataz,

                    -- Cliente
                    u.nombre AS nombre_cliente,
                    u.apellido AS apellido_cliente,

                    -- Jefe de Obra
                    j.nombre AS nombre_jefe_obra,
                    j.apellido AS apellido_jefe_obra,

                    -- Capataz
                    c.nombre AS nombre_capataz,
                    c.apellido AS apellido_capataz

                FROM obra o

                INNER JOIN usuario u
                    ON o.id_usuario = u.id_usuario

                LEFT JOIN usuario j
                    ON o.id_jefe_obra = j.id_usuario

                LEFT JOIN usuario c
                    ON o.id_capataz = c.id_usuario

                WHERE o.activo = 1";

        /*
        =====================================
            GERENTE
            Puede ver todas las obras
        =====================================
        */

        if ($id_rol == 2) {

            $sql .= "
                ORDER BY o.id_obra DESC
            ";

            $consulta = $this->conexion->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        /*
        =====================================
            JEFE DE OBRA
            Solo sus obras
        =====================================
        */

        if ($id_rol == 4) {

            $sql .= "
                AND o.id_jefe_obra = :id_usuario
                ORDER BY o.id_obra DESC
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bindParam(
                ":id_usuario",
                $id_usuario,
                PDO::PARAM_INT
            );

            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        /*
        =====================================
            CAPATAZ
            Solo su obra
        =====================================
        */

        if ($id_rol == 7) {

            $sql .= "
                AND o.id_capataz = :id_usuario
                ORDER BY o.id_obra DESC
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bindParam(
                ":id_usuario",
                $id_usuario,
                PDO::PARAM_INT
            );

            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        /*
        =====================================
            CLIENTE
            Solo sus obras
        =====================================
        */

        if ($id_rol == 6) {

            $sql .= "
                AND o.id_usuario = :id_usuario
                ORDER BY o.id_obra DESC
            ";

            $consulta = $this->conexion->prepare($sql);

            $consulta->bindParam(
                ":id_usuario",
                $id_usuario,
                PDO::PARAM_INT
            );

            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        }

        /*
        =====================================
            OTROS ROLES
            No tienen obras visibles
        =====================================
        */

        return [];
    }



    public function obtenerCapataces()
    {
        $sql = "SELECT
                    id_usuario,
                    nombre,
                    apellido,
                    documento
                FROM usuario
                WHERE id_rol = 7
                  AND estado = 1
                ORDER BY apellido ASC, nombre ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerCapatacesDisponibles()
    {
        $sql = "SELECT
u.id_usuario,
u.nombre,
u.apellido,
u.documento
FROM usuario u
LEFT JOIN obra o
ON o.id_capataz = u.id_usuario
WHERE u.id_rol = 7
AND u.estado = 1
AND o.id_capataz IS NULL
ORDER BY u.apellido ASC, u.nombre ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerCapatacesDisponiblesParaEditar($id_obra)
    {
        $sql = "SELECT
u.id_usuario,
u.nombre,
u.apellido,
u.documento
FROM usuario u
LEFT JOIN obra o
ON o.id_capataz = u.id_usuario
AND o.id_obra != :id_obra
WHERE u.id_rol = 7
AND u.estado = 1
AND o.id_capataz IS NULL
ORDER BY u.apellido ASC, u.nombre ASC";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindParam(
            ":id_obra",
            $id_obra,
            PDO::PARAM_INT
        );

        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    public function capatazDisponible($id_capataz, $id_obra = null)
    {
        $sql = "SELECT id_obra
FROM obra
WHERE id_capataz = :id_capataz";

        if ($id_obra !== null) {
            $sql .= " AND id_obra != :id_obra";
        }

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindValue(
            ":id_capataz",
            $id_capataz,
            PDO::PARAM_INT
        );

        if ($id_obra !== null) {
            $consulta->bindValue(
                ":id_obra",
                $id_obra,
                PDO::PARAM_INT
            );
        }

        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC) === false;
    }
}