<?php

require_once "Conexion.php";

class Auditoria
{

    private $conexion;


    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }


    public function registrar($datos)
    {

        $sql = "INSERT INTO auditoria
        (
            id_usuario,
            accion,
            tabla_afectada,
            id_registro,
            descripcion
        )
        VALUES
        (
            :id_usuario,
            :accion,
            :tabla_afectada,
            :id_registro,
            :descripcion
        )";


        $consulta = $this->conexion->prepare($sql);


        return $consulta->execute([

            ":id_usuario" => $datos["id_usuario"],
            ":accion" => $datos["accion"],
            ":tabla_afectada" => $datos["tabla_afectada"],
            ":id_registro" => $datos["id_registro"],
            ":descripcion" => $datos["descripcion"]

        ]);

    }

}