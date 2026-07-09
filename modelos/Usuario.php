<?php

require_once "Conexion.php";

class Usuario
{

    private $conexion;

    public function __construct()
    {

        $db = new Conexion();
        $this->conexion = $db->conectar();
    }



    public function buscarPorCorreo($correo)
    {

        $sql = "SELECT 
                    u.id_usuario,
                    u.id_rol,
                    u.nombre,
                    u.apellido,
                    u.correo,
                    u.contraseña,
                    u.estado,
                    r.nombre_rol
                FROM usuario u
                INNER JOIN roles r 
                ON u.id_rol = r.id_rol
                WHERE u.correo = :correo";


        $consulta = $this->conexion->prepare($sql);

        $consulta->bindParam(":correo", $correo);

        $consulta->execute();


        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function registrarIngreso($id_usuario)
    {

        $sql = "INSERT INTO acceso_usuario (id_usuario, fecha_hora_ingreso) VALUES (:id_usuario, NOW())";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id_usuario", $id_usuario);

        return $consulta->execute();
    }

    public function registrarSalida($id_usuario)
    {

        $sql = "UPDATE acceso_usuario 
            SET fecha_hora_salida = NOW()
            WHERE id_usuario = :id_usuario
            AND fecha_hora_salida IS NULL
            ORDER BY id_acceso DESC
            LIMIT 1";

        $consulta = $this->conexion->prepare($sql);
        $consulta->bindParam(":id_usuario", $id_usuario);

        return $consulta->execute();
    }
}
