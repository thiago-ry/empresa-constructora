<?php

require_once "Conexion.php";

class Permiso
{

    private $conexion;


    public function __construct()
    {

        $db = new Conexion();
        $this->conexion = $db->conectar();

    }


    public function tienePermiso($id_rol, $permiso)
    {

        $sql = "SELECT p.nombre_permiso
                FROM rol_permiso rp
                INNER JOIN permisos p
                ON rp.id_permiso = p.id_permiso
                WHERE rp.id_rol = :id_rol
                AND p.nombre_permiso = :permiso";


        $consulta = $this->conexion->prepare($sql);


        $consulta->execute([
            ":id_rol" => $id_rol,
            ":permiso" => $permiso
        ]);


        return $consulta->rowCount() > 0;

    }

}