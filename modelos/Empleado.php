<?php

require_once "Conexion.php";


class Empleado
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

        $sql = "SELECT *
                FROM empleado
                ORDER BY apellido,nombre";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }




    /*
    ==========================
        BUSCAR POR ID
    ==========================
    */

    public function buscarPorId($id)
    {

        $sql = "SELECT *
                FROM empleado
                WHERE id_empleado=?";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([$id]);


        return $stmt->fetch(PDO::FETCH_ASSOC);

    }




    /*
    ==========================
        ACTIVOS DISPONIBLES
        PARA UNA OBRA
    ==========================
    */

   public function obtenerDisponiblesPorObra($id_obra)
{

    $sql = "SELECT 
                e.*
            FROM empleado e
            WHERE e.estado = 1
            AND e.id_empleado NOT IN (

                SELECT eo.id_empleado
                FROM empleado_obra eo
                WHERE eo.id_obra = ?
                AND eo.estado = 1

            )
            ORDER BY e.apellido, e.nombre";


    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([$id_obra]);


    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}


}