<?php

require_once "Conexion.php";


class Avance
{

    private $conexion;



    public function __construct()
    {

        $db = new Conexion();

        $this->conexion = $db->conectar();

    }





    public function obtenerPorObra($id_obra)
    {

        $sql = "SELECT * 
                FROM avance_diario
                WHERE id_obra = ?
                ORDER BY fecha DESC";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);


        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }







    public function contar($id_obra)
    {

        $sql = "SELECT COUNT(*) 
                FROM avance_diario
                WHERE id_obra = ?";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);


        return $stmt->fetchColumn();

    }







    public function primero($id_obra)
    {

        $sql = "SELECT fecha
                FROM avance_diario
                WHERE id_obra = ?
                ORDER BY fecha ASC
                LIMIT 1";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);


        return $stmt->fetch(PDO::FETCH_ASSOC);

    }







    public function ultimo($id_obra)
    {

        $sql = "SELECT fecha
                FROM avance_diario
                WHERE id_obra = ?
                ORDER BY fecha DESC
                LIMIT 1";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id_obra]);


        return $stmt->fetch(PDO::FETCH_ASSOC);

    }








    public function buscarPorId($id)
    {

        $sql = "SELECT *
                FROM avance_diario
                WHERE id_avance_diario = ?";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id]);


        return $stmt->fetch(PDO::FETCH_ASSOC);

    }








    public function guardar($datos)
    {

        $sql = "INSERT INTO avance_diario
                (
                    id_obra,
                    fecha,
                    descripcion
                )
                VALUES
                (
                    ?,
                    ?,
                    ?
                )";


        $stmt = $this->conexion->prepare($sql);


        return $stmt->execute([

            $datos["id_obra"],

            $datos["fecha"],

            $datos["descripcion"]

        ]);

    }







    public function ultimoInsertado()
    {

        return $this->conexion->lastInsertId();

    }








    public function actualizar($datos)
    {

        $sql = "UPDATE avance_diario
                SET
                    fecha = ?,
                    descripcion = ?
                WHERE id_avance_diario = ?";


        $stmt = $this->conexion->prepare($sql);


        return $stmt->execute([

            $datos["fecha"],

            $datos["descripcion"],

            $datos["id_avance_diario"]

        ]);

    }








    public function eliminar($id)
    {

        $sql = "DELETE FROM avance_diario
                WHERE id_avance_diario = ?";


        $stmt = $this->conexion->prepare($sql);


        return $stmt->execute([$id]);

    }


}