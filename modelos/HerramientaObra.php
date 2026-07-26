<?php

require_once "Conexion.php";


class HerramientaObra
{

    private $conexion;


    public function __construct()
    {

        $db = new Conexion();

        $this->conexion = $db->conectar();
    }



    // Obtener todas las asignaciones

    public function obtenerTodos()
    {

        $sql = "
 SELECT
    ho.id_herramienta_obra,
    h.nombre AS herramienta,
    o.nombre_obra AS obra,
    ho.cantidad,
    ho.fecha_asignacion,
    ho.fecha_devolucion,
    ho.observaciones

        FROM herramienta_obra ho

        INNER JOIN herramienta h
        ON ho.id_herramienta = h.id_herramienta

        INNER JOIN obra o
        ON ho.id_obra = o.id_obra

        INNER JOIN estado_herramienta eh
ON ho.id_estado_herramienta = eh.id_estado_herramienta

        ORDER BY ho.id_herramienta_obra DESC
        ";


        $stmt = $this->conexion->prepare($sql);

        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // Buscar una asignación

    public function obtenerPorId($id)
    {


        $sql = "

    SELECT

        ho.id_herramienta_obra,

        ho.id_herramienta,

        ho.id_obra,

        ho.cantidad,

        ho.fecha_asignacion,

        ho.fecha_devolucion,

        ho.observaciones,

        h.nombre AS herramienta


    FROM herramienta_obra ho


    INNER JOIN herramienta h

    ON ho.id_herramienta = h.id_herramienta


    WHERE ho.id_herramienta_obra = ?

    ";



        $stmt = $this->conexion->prepare($sql);


        $stmt->execute([$id]);


        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

public function obtenerAsignacion($id)
{
    $sql = "
        SELECT *
        FROM herramienta_obra
        WHERE id_herramienta_obra = ?
    ";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    // Agregar asignación

    public function agregar($datos)
    {

        $sql = "
        INSERT INTO herramienta_obra
        (
            id_herramienta,
            id_obra,
            cantidad,
            fecha_asignacion,
            observaciones
        )

        VALUES
        (?,?,?,?,?)
        ";


        $stmt = $this->conexion->prepare($sql);


        return $stmt->execute([

            $datos['id_herramienta'],
            $datos['id_obra'],
            $datos['cantidad'],
            $datos['fecha_asignacion'],
            $datos['observaciones']

        ]);
    }




    // Editar asignación

 public function editar($datos)
{

    $sql = "
    UPDATE herramienta_obra

    SET

    cantidad = ?,

    fecha_devolucion = ?,

    id_estado_herramienta = ?,

    observaciones = ?

    WHERE id_herramienta_obra = ?

    ";


    $stmt = $this->conexion->prepare($sql);


    return $stmt->execute([

        $datos["cantidad"],

        $datos["fecha_devolucion"],

        $datos["id_estado_herramienta"],

        $datos["observaciones"],

        $datos["id_herramienta_obra"]

    ]);

}




    // Eliminar asignación

    public function eliminar($id)
    {

        $sql = "
        DELETE FROM herramienta_obra
        WHERE id_herramienta_obra=?
        ";


        $stmt = $this->conexion->prepare($sql);


        return $stmt->execute([$id]);
    }

 public function obtenerPorObra($id_obra)
{

    $sql = "
    SELECT 

        ho.id_herramienta_obra,

        h.nombre AS herramienta,

        ho.cantidad,

        ho.fecha_asignacion,

        ho.fecha_devolucion,

        eh.nombre AS estado


    FROM herramienta_obra ho


    INNER JOIN herramienta h

    ON ho.id_herramienta = h.id_herramienta


    INNER JOIN estado_herramienta eh

    ON ho.id_estado_herramienta = eh.id_estado_herramienta


    WHERE ho.id_obra = ?


    ORDER BY ho.id_herramienta_obra DESC
    ";


    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([$id_obra]);


    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}
    
}
