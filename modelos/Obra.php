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
                c.nombre AS nombre_cliente,
                c.apellido AS apellido_cliente
            FROM obra o
            INNER JOIN cliente c
                ON o.id_cliente = c.id_cliente
            ORDER BY o.nombre_obra ASC";


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
                c.nombre AS nombre_cliente,
                c.apellido AS apellido_cliente
            FROM obra o
            INNER JOIN cliente c
                ON o.id_cliente = c.id_cliente
            WHERE o.id_obra = :id";

    $consulta = $this->conexion->prepare($sql);

    $consulta->bindParam(":id",$id);

    $consulta->execute();

    return $consulta->fetch(PDO::FETCH_ASSOC);
}

  public function agregar($datos)
{
    $sql = "INSERT INTO obra
            (
                id_cliente,
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
                :id_cliente,
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

        ":id_cliente" => $datos["id_cliente"],

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
                    id_cliente=:id_cliente,
                    nombre_obra=:nombre_obra,
                    direccion=:direccion,
                    descripcion=:descripcion,
                    fecha_inicio=:fecha_inicio,
                    fecha_fin=:fecha_fin,
                    estado=:estado
                WHERE id_obra=:id_obra";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([

            ":id_cliente"=>$datos["id_cliente"],
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
                    c.nombre,
                    c.apellido
                FROM obra o
                INNER JOIN cliente c
                    ON o.id_cliente=c.id_cliente
                WHERE o.activo=1
                ORDER BY o.nombre_obra";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}