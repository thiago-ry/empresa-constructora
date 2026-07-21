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

  public function obtenerTodos()
{
    $sql = "SELECT
    o.id_obra,
    o.nombre_obra,
    o.direccion,
    o.fecha_inicio,
    o.fecha_fin,
    o.estado,
    u.nombre AS nombre_cliente,
    u.apellido AS apellido_cliente
FROM obra o
INNER JOIN usuario u
    ON o.id_usuario = u.id_usuario";


    $consulta = $this->conexion->prepare($sql);

    $consulta->execute();


    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

public function obtenerEstados()
{
    $sql = "SHOW COLUMNS FROM obra LIKE 'estado'";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute();

    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

    preg_match("/^enum\(\'(.*)\'\)$/", $resultado["Type"], $matches);

    return explode("','", $matches[1]);
}
    public function buscarPorId($id)
{
    $sql = "SELECT
    o.*,
    u.nombre AS nombre_cliente,
    u.apellido AS apellido_cliente
FROM obra o
INNER JOIN usuario u
    ON o.id_usuario = u.id_usuario";

    $consulta = $this->conexion->prepare($sql);

    $consulta->bindParam(":id",$id);

    $consulta->execute();

    return $consulta->fetch(PDO::FETCH_ASSOC);
}

  public function agregar($datos)
{
    $sql = "INSERT INTO obra
            (
                id_usuario,
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

        ":id_usuario" => $datos["id_usuario"],

        ":nombre_obra" => $datos["nombre_obra"],

        ":direccion" => $datos["direccion"],

        ":descripcion" => $datos["descripcion"],

        ":fecha_inicio" => $datos["fecha_inicio"],

        ":fecha_fin" => $datos["fecha_fin"],

        ":estado" => $datos["estado"]

    ]);


    return $this->conexion->lastInsertId();
}

    public function editar($datos)
    {
        $sql = "UPDATE obra
                SET
                    id_usuario=:id_usuario,
                    nombre_obra=:nombre_obra,
                    direccion=:direccion,
                    descripcion=:descripcion,
                    fecha_inicio=:fecha_inicio,
                    fecha_fin=:fecha_fin,
                    estado=:estado
                WHERE id_obra=:id_obra";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id_usuario"=>$datos["id_usuario"],
            ":nombre_obra"=>$datos["nombre_obra"],
            ":direccion"=>$datos["direccion"],
            ":descripcion"=>$datos["descripcion"],
            ":fecha_inicio"=>$datos["fecha_inicio"],
            ":fecha_fin"=>$datos["fecha_fin"],
            ":estado"=>$datos["estado"],
            ":id_obra"=>$datos["id_obra"]

        ]);
    }

    public function bajaLogica($id)
    {
        $sql = "UPDATE obra
                SET activo = 0
                WHERE id_obra = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id",$id);

        return $consulta->execute();
    }

    public function activarObra($id)
    {
        $sql = "UPDATE obra
                SET activo = 1
                WHERE id_obra = :id";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id",$id);

        return $consulta->execute();
    }

    public function obtenerActivas()
    {
        $sql = "SELECT
    o.*,
    u.nombre,
    u.apellido
FROM obra o
INNER JOIN usuario u
    ON o.id_usuario = u.id_usuario";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}