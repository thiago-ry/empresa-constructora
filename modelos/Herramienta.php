<?php

require_once "Conexion.php";

class Herramienta
{

    private $conexion;


    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }



    /*
    ==================================
        OBTENER TODAS
    ==================================
    */

    public function obtenerTodos()
    {

        $sql = "
            SELECT

                h.id_herramienta,
                h.nombre,
                h.tipo,
                h.marca,
                h.modelo,
                h.cantidad_total,
                h.fecha_adquisicion,
                h.costo

            FROM herramienta h

            ORDER BY h.nombre ASC
        ";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /*
    ==================================
        BUSCAR POR ID
    ==================================
    */

    public function buscarPorId($id)
    {

        $sql = "

            SELECT

                h.id_herramienta,
                h.nombre,
                h.tipo,
                h.marca,
                h.modelo,
                h.cantidad_total,
                h.fecha_adquisicion,
                h.costo

            FROM herramienta h

            WHERE h.id_herramienta = ?

        ";


        $stmt = $this->conexion->prepare($sql);


        $stmt->execute([$id]);


        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




    /*
    ==================================
        BUSCAR
    ==================================
    */

    public function buscar($id)
    {

        $sql = "

            SELECT *

            FROM herramienta

            WHERE id_herramienta = :id

        ";


        $stmt = $this->conexion->prepare($sql);


        $stmt->bindParam(":id", $id, PDO::PARAM_INT);


        $stmt->execute();


        return $stmt->fetch(PDO::FETCH_ASSOC);
    }





    /*
    ==================================
        AGREGAR HERRAMIENTA
    ==================================
    */


    public function agregar($datos)
    {


        $sql = "

            INSERT INTO herramienta
            (

                nombre,
                tipo,
                marca,
                modelo,
                cantidad_total,
                fecha_adquisicion,
                costo

            )

            VALUES
            (

                :nombre,
                :tipo,
                :marca,
                :modelo,
                :cantidad_total,
                :fecha_adquisicion,
                :costo

            )

        ";


        $stmt = $this->conexion->prepare($sql);



        $resultado = $stmt->execute([


            ":nombre" => $datos["nombre"],

            ":tipo" => $datos["tipo"],

            ":marca" => $datos["marca"],

            ":modelo" => $datos["modelo"],

            ":cantidad_total" => $datos["cantidad_total"],

            ":fecha_adquisicion" => $datos["fecha_adquisicion"],

            ":costo" => $datos["costo"]


        ]);



        if ($resultado) {

            return $this->conexion->lastInsertId();
        }


        return false;
    }


public function editar($datos)
{
    $sql = "
    UPDATE herramienta SET

        nombre = ?,
        tipo = ?,
        marca = ?,
        modelo = ?,
        cantidad_total = ?,
        fecha_adquisicion = ?,
        costo = ?

    WHERE id_herramienta = ?
    ";

    $stmt = $this->conexion->prepare($sql);

    return $stmt->execute([

        $datos["nombre"],
        $datos["tipo"],
        $datos["marca"],
        $datos["modelo"],
        $datos["cantidad_total"],
        $datos["fecha_adquisicion"],
        $datos["costo"],
        $datos["id_herramienta"]

    ]);
}

    /*
    ==================================
        ELIMINAR
    ==================================
    */


    public function eliminar($id)
    {


        $sql = "

            DELETE FROM herramienta

            WHERE id_herramienta = ?

        ";


        $stmt = $this->conexion->prepare($sql);


        return $stmt->execute([$id]);
    }

    public function obtenerDisponibles()
    {

        $herramientas = $this->obtenerTodos();

        $disponibles = [];

        foreach ($herramientas as $herramienta) {

            $stockDisponible = $this->obtenerCantidadDisponible($herramienta["id_herramienta"]);

            if ($stockDisponible > 0) {

                $herramienta["disponibles"] = $stockDisponible;

                $disponibles[] = $herramienta;
            }
        }

        return $disponibles;
    }

    public function obtenerCantidadTotal($id_herramienta)
    {

        $sql = "
        SELECT cantidad_total
        FROM herramienta
        WHERE id_herramienta = ?
    ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_herramienta]);

        return (int)$stmt->fetchColumn();
    }

    public function obtenerCantidadAsignada($id_herramienta)
    {

        $sql = "
        SELECT COALESCE(SUM(cantidad),0)

        FROM herramienta_obra

        WHERE id_herramienta = ?

        AND id_estado_herramienta = 2
    ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_herramienta]);

        return (int)$stmt->fetchColumn();
    }

    public function obtenerCantidadDisponible($id_herramienta)
    {

        $total = $this->obtenerCantidadTotal($id_herramienta);

        $asignadas = $this->obtenerCantidadAsignada($id_herramienta);

        return $total - $asignadas;
    }

    /*
==================================
    OBTENER DETALLE
==================================
*/

public function obtenerDetalle($id_herramienta)
{

    $herramienta = $this->buscarPorId($id_herramienta);

    if (!$herramienta) {

        return false;

    }

    $herramienta["cantidad_asignada"] =
        $this->obtenerCantidadAsignada($id_herramienta);

    $herramienta["cantidad_disponible"] =
        $this->obtenerCantidadDisponible($id_herramienta);

    return $herramienta;

}
}
