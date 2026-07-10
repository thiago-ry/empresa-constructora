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

}

?>