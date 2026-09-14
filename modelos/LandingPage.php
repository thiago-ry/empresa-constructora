<?php

require_once __DIR__ . "/Conexion.php";

class LandingPage
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
                FROM foto_obra
                WHERE descripcion LIKE '%Finalizado%'";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}