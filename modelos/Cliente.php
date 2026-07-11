<?php

require_once "Conexion.php";

class Cliente
{
    private $conexion;


    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }



    // Obtener todos los clientes
    public function obtenerTodos()
    {
        $sql = "SELECT *
                FROM cliente
                ORDER BY nombre ASC";


        $consulta = $this->conexion->prepare($sql);

        $consulta->execute();


        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }



    // Buscar cliente por ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT *
                FROM cliente
                WHERE id_cliente = :id";


        $consulta = $this->conexion->prepare($sql);


        $consulta->bindParam(":id", $id);


        $consulta->execute();


        return $consulta->fetch(PDO::FETCH_ASSOC);
    }



    // Crear cliente
    public function crear($datos)
    {

        $sql = "INSERT INTO cliente
                (
                    nombre,
                    apellido,
                    telefono,
                    direccion,
                    correo,
                    id_usuario
                )
                VALUES
                (
                    :nombre,
                    :apellido,
                    :telefono,
                    :direccion,
                    :correo,
                    :id_usuario
                )";


        $consulta = $this->conexion->prepare($sql);


        return $consulta->execute([

            ":nombre" => $datos["nombre"],
            ":apellido" => $datos["apellido"],
            ":telefono" => $datos["telefono"],
            ":direccion" => $datos["direccion"],
            ":correo" => $datos["correo"],
            ":id_usuario" => $datos["id_usuario"]

        ]);

    }




    // Actualizar cliente
    public function actualizar($id,$datos)
    {

        $sql = "UPDATE cliente
                SET
                    nombre=:nombre,
                    apellido=:apellido,
                    telefono=:telefono,
                    direccion=:direccion,
                    correo=:correo,
                    id_usuario=:id_usuario
                WHERE id_cliente=:id";


        $consulta = $this->conexion->prepare($sql);


        return $consulta->execute([

            ":nombre"=>$datos["nombre"],
            ":apellido"=>$datos["apellido"],
            ":telefono"=>$datos["telefono"],
            ":direccion"=>$datos["direccion"],
            ":correo"=>$datos["correo"],
            ":id_usuario"=>$datos["id_usuario"],
            ":id"=>$id

        ]);

    }



    // Eliminar cliente
    public function eliminar($id)
    {

        $sql = "DELETE FROM cliente
                WHERE id_cliente=:id";


        $consulta = $this->conexion->prepare($sql);


        return $consulta->execute([

            ":id"=>$id

        ]);

    }

}

?>