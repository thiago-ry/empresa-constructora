<?php

require_once "Conexion.php";

class Cliente
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    public function obtenerTodos()
    {
        $sql = "
            SELECT
                u.*,
                (
                    SELECT COUNT(*)
                    FROM obra o
                    WHERE o.id_usuario = u.id_usuario
                ) AS total_obras
            FROM usuario u
            WHERE u.id_rol = 6
            ORDER BY u.apellido, u.nombre
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $sql = "
            SELECT *
            FROM usuario
            WHERE id_usuario = ?
            AND id_rol = 6
            LIMIT 1
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerActivos()
    {
        $sql = "
            SELECT *
            FROM usuario
            WHERE id_rol = 6
            AND estado = 1
            ORDER BY apellido, nombre
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($texto)
    {
        $texto = "%{$texto}%";

        $sql = "
            SELECT *
            FROM usuario
            WHERE id_rol = 6
            AND (
                nombre LIKE ?
                OR apellido LIKE ?
                OR documento LIKE ?
                OR correo LIKE ?
            )
            ORDER BY apellido, nombre
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $texto,
            $texto,
            $texto,
            $texto
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarClientes()
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM usuario
            WHERE id_rol = 6
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function obtenerObras($idCliente)
    {
        $sql = "
            SELECT
                id_obra,
                nombre_obra,
                direccion,
                fecha_inicio,
                fecha_fin,
                estado,
                activo
            FROM obra
            WHERE id_usuario = ?
            ORDER BY id_obra DESC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idCliente]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarObras($idCliente)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM obra
            WHERE id_usuario = ?
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idCliente]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function obtenerEstadisticas()
    {
        $sql = "
            SELECT
                COUNT(*) AS total_clientes,
                SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) AS activos,
                SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) AS inactivos
            FROM usuario
            WHERE id_rol = 6
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerResumen($idCliente)
    {
        $sql = "
        SELECT
            COUNT(*) AS total_obras,
            SUM(CASE WHEN estado = 'En ejecución' THEN 1 ELSE 0 END) AS obras_activas,
            SUM(CASE WHEN estado = 'Finalizada' THEN 1 ELSE 0 END) AS obras_finalizadas
        FROM obra
        WHERE id_usuario = ?
    ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idCliente]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function tieneObras($id_usuario)
    {

        $sql = "SELECT COUNT(*) AS cantidad

                FROM obra

                WHERE id_usuario = :id_usuario";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":id_usuario" => $id_usuario

        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC)["cantidad"] > 0;
    }

    public function bajaLogica($id)
    {

        $sql = "UPDATE usuario

                SET estado = 0

                WHERE id_usuario = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id" => $id

        ]);
    }


    public function activarUsuario($id)
    {

        $sql = "UPDATE usuario

                SET estado = 1

                WHERE id_usuario = :id";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id" => $id

        ]);
    }

       public function existeCorreo($correo)
    {

        $sql = "SELECT id_usuario
                FROM usuario
                WHERE correo = :correo";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([

            ":correo" => $correo

        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

     public function agregar($datos)
    {

        $sql = "INSERT INTO usuario
                (
                    id_rol,
                    nombre,
                    apellido,
                    documento,
                    telefono,
                    correo,
                    contraseña,
                    estado
                )
                VALUES
                (
                    6,
                    :nombre,
                    :apellido,
                    :documento,
                    :telefono,
                    :correo,
                    :clave,
                    1

                )";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ":nombre"      => ucwords(strtolower($datos["nombre"])),
            ":apellido"    => ucwords(strtolower($datos["apellido"])),
            ":documento"   => $datos["documento"],
            ":telefono"    => $datos["telefono"],
            ":correo"      => $datos["correo"],
            ":clave"       => $datos["contraseña"]
        ]);

        return $this->conexion->lastInsertId();
    }
}
