<?php

require_once "Conexion.php";


class UnidadHerramienta
{

    private $conexion;



    public function __construct()
    {

        $db = new Conexion();

        $this->conexion = $db->conectar();

    }




    /*
    ==================================
        OBTENER TODAS LAS UNIDADES
    ==================================
    */


    public function obtenerTodas()
    {


        $sql = "

            SELECT

                u.id_unidad,

                u.numero_unidad,

                h.nombre AS herramienta,

                h.marca,

                e.nombre AS estado


            FROM unidad_herramienta u


            INNER JOIN herramienta h

            ON u.id_herramienta = h.id_herramienta


            INNER JOIN estado_herramienta e

            ON u.id_estado_herramienta = e.id_estado_herramienta



            ORDER BY h.nombre ASC, u.numero_unidad ASC


        ";


        $stmt = $this->conexion->prepare($sql);


        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }






    /*
    ==================================
        CREAR UNIDADES AL REGISTRAR
        UNA HERRAMIENTA
    ==================================
    */


    public function crearUnidades($id_herramienta, $cantidad)
    {


        $sql = "

            INSERT INTO unidad_herramienta

            (

                id_herramienta,

                numero_unidad,

                id_estado_herramienta

            )

            VALUES

            (

                :id_herramienta,

                :numero_unidad,

                1

            )

        ";



        $stmt = $this->conexion->prepare($sql);



        for($i = 1; $i <= $cantidad; $i++)
        {


            $stmt->execute([


                ":id_herramienta" => $id_herramienta,

                ":numero_unidad" => $i


            ]);


        }



        return true;


    }







    /*
    ==================================
        OBTENER DISPONIBLES
    ==================================
    */


    public function obtenerDisponibles($id_herramienta)
    {


        $sql = "

            SELECT *

            FROM unidad_herramienta

            WHERE id_herramienta = ?

            AND id_estado_herramienta = 1

        ";


        $stmt = $this->conexion->prepare($sql);


        $stmt->execute([$id_herramienta]);


        return $stmt->fetchAll(PDO::FETCH_ASSOC);


    }








    /*
    ==================================
        CAMBIAR ESTADO
    ==================================
    */


    public function cambiarEstado($id_unidad,$estado)
    {


        $sql = "

            UPDATE unidad_herramienta

            SET id_estado_herramienta = ?

            WHERE id_unidad = ?

        ";



        $stmt = $this->conexion->prepare($sql);



        return $stmt->execute([

            $estado,

            $id_unidad

        ]);


    }






    /*
    ==================================
        BUSCAR POR ID
    ==================================
    */


    public function buscarPorId($id)
    {


        $sql = "

            SELECT *

            FROM unidad_herramienta

            WHERE id_unidad = ?

        ";


        $stmt = $this->conexion->prepare($sql);


        $stmt->execute([$id]);


        return $stmt->fetch(PDO::FETCH_ASSOC);


    }



}