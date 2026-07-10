<?php

require_once "Conexion.php";


class Reporte
{

    private $conexion;


    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }



    public function usuarios()
    {

        $sql = "SELECT 
                    u.id_usuario,
                    u.nombre,
                    u.apellido,
                    u.correo,
                    u.fecha_registro,
                    u.estado,
                    r.nombre_rol

                FROM usuario u

                INNER JOIN roles r
                ON u.id_rol = r.id_rol

                ORDER BY u.nombre";


        $consulta = $this->conexion->prepare($sql);

        $consulta->execute();


        return $consulta->fetchAll(PDO::FETCH_ASSOC);

    }


}