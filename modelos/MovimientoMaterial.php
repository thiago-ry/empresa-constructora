<?php

require_once "Conexion.php";

class MovimientoMaterial
{

    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    /*
    ==========================
        OBTENER TODOS
    ==========================
    */

    public function obtenerTodos()
    {

        $sql = "SELECT
                    mm.*,
                    m.nombre_material,
                    u.nombre,
                    u.apellido
                FROM movimiento_material mm
                INNER JOIN material m
                    ON mm.id_material = m.id_material
                INNER JOIN usuario u
                    ON mm.id_usuario = u.id_usuario
                ORDER BY mm.fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    /*
    ==========================
        OBTENER POR MATERIAL
    ==========================
    */

    public function obtenerPorMaterial($id_material)
    {

        $sql = "SELECT
                    mm.*,
                    u.nombre,
                    u.apellido
                FROM movimiento_material mm
                INNER JOIN usuario u
                    ON mm.id_usuario = u.id_usuario
                WHERE mm.id_material = :id_material
                ORDER BY mm.fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_material", $id_material, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    /*
    ==========================
        AGREGAR MOVIMIENTO
    ==========================
    */

    public function agregar($datos)
    {

        $sql = "INSERT INTO movimiento_material
                (
                    id_material,
                    id_usuario,
                    tipo,
                    cantidad,
                    observacion
                )
                VALUES
                (
                    :id_material,
                    :id_usuario,
                    :tipo,
                    :cantidad,
                    :observacion
                )";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            ":id_material" => $datos["id_material"],
            ":id_usuario" => $datos["id_usuario"],
            ":tipo" => $datos["tipo"],
            ":cantidad" => $datos["cantidad"],
            ":observacion" => $datos["observacion"]

        ]);

    }

    /*
    ==========================
        BUSCAR POR ID
    ==========================
    */

    public function buscar($id)
    {

        $sql = "SELECT *
                FROM movimiento_material
                WHERE id_movimiento = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /*
    ==========================
        ACTUALIZAR STOCK
    ==========================
    */

    public function actualizarStock($id_material, $stock)
    {

        $sql = "UPDATE material
                SET stock = :stock
                WHERE id_material = :id_material";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([

            ":stock" => $stock,
            ":id_material" => $id_material

        ]);

    }

}