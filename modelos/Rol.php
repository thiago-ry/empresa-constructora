<?php

require_once "Conexion.php";

class Rol
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
                    id_rol,
                    nombre_rol
                FROM roles
                ORDER BY nombre_rol";


        $consulta = $this->conexion->prepare($sql);

        $consulta->execute();


        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT *
            FROM roles
            WHERE id_rol = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->bindParam(":id", $id);

        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}
