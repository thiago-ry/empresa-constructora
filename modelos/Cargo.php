<?php

require_once "Conexion.php";

class Cargo
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
                FROM cargo
                ORDER BY nombre_cargo";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM cargo
                WHERE id_cargo = :id";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ":id" => $id
        ]);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}