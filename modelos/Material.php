<?php

require_once "Conexion.php";

class Material
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }


    public function obtenerTodos()
    {
        $sql = "SELECT *
                FROM material
                ORDER BY nombre_material ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($id)
    {
        $sql = "SELECT *
                FROM material
                WHERE id_material = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function agregar($datos)
    {
        $sql = "INSERT INTO material (
                    nombre_material,
                    descripcion,
                    stock,
                    stock_minimo,
                    unidad_medida,
                    estado
                ) VALUES (
                    :nombre_material,
                    :descripcion,
                    :stock,
                    :stock_minimo,
                    :unidad_medida,
                    :estado
                )";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":nombre_material" => $datos["nombre_material"],
            ":descripcion" => $datos["descripcion"],
            ":stock" => $datos["stock"],
            ":stock_minimo" => $datos["stock_minimo"],
            ":unidad_medida" => $datos["unidad_medida"],
            ":estado" => $datos["estado"]
        ]);
    }


    public function editar($datos)
    {
        $sql = "UPDATE material SET
                    nombre_material = :nombre_material,
                    descripcion = :descripcion,
                    stock = :stock,
                    stock_minimo = :stock_minimo,
                    unidad_medida = :unidad_medida,
                    estado = :estado
                WHERE id_material = :id_material";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ":nombre_material" => $datos["nombre_material"],
            ":descripcion" => $datos["descripcion"],
            ":stock" => $datos["stock"],
            ":stock_minimo" => $datos["stock_minimo"],
            ":unidad_medida" => $datos["unidad_medida"],
            ":estado" => $datos["estado"],
            ":id_material" => $datos["id_material"]
        ]);
    }


    public function eliminar($id)
    {
        $sql = "UPDATE material
                SET estado = 0
                WHERE id_material = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function activar($id)
    {
        $sql = "UPDATE material
                SET estado = 1
                WHERE id_material = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function obtenerPorId($id)
    {

        $sql = "SELECT *
            FROM material
            WHERE id_material = ?";


        $stmt = $this->conexion->prepare($sql);


        $stmt->execute([$id]);


        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
