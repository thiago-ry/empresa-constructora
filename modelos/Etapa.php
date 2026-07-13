<?php

require_once "Conexion.php";


class Etapa
{

    private $conexion;


    public function __construct()
    {

        $db = new Conexion();

        $this->conexion = $db->conectar();

    }



    public function obtenerPorObra($id_obra)
    {

        $sql = "SELECT *
                FROM etapa_obra
                WHERE id_obra = :id_obra
                ORDER BY id_etapa ASC";


        $consulta = $this->conexion->prepare($sql);


        $consulta->bindParam(
            ":id_obra",
            $id_obra
        );


        $consulta->execute();


        return $consulta->fetchAll(PDO::FETCH_ASSOC);

    }




   public function crear($datos)
{

$sql="INSERT INTO etapa_obra
(
id_obra,
nombre_etapa,
descripcion,
fecha_inicio,
fecha_fin,
estado
)

VALUES
(?,?,?,?,?,?)";


$stmt=$this->conexion->prepare($sql);


return $stmt->execute([

$datos["id_obra"],
$datos["nombre_etapa"],
$datos["descripcion"],
$datos["fecha_inicio"],
$datos["fecha_fin"],
$datos["estado"]

]);

}

public function buscarPorId($id)
{

    $sql="SELECT *
          FROM etapa_obra
          WHERE id_etapa = ?";


    $stmt=$this->conexion->prepare($sql);

    $stmt->execute([$id]);


    return $stmt->fetch(PDO::FETCH_ASSOC);

}

public function actualizar($datos)
{

    $sql="UPDATE etapa_obra SET

        nombre_etapa=?,
        descripcion=?,
        fecha_inicio=?,
        fecha_fin=?,
        estado=?

        WHERE id_etapa=?";


    $stmt=$this->conexion->prepare($sql);


    return $stmt->execute([

        $datos["nombre_etapa"],
        $datos["descripcion"],
        $datos["fecha_inicio"],
        $datos["fecha_fin"],
        $datos["estado"],
        $datos["id_etapa"]

    ]);

}
public function calcularAvance($id_obra)
{

    $sql = "SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN estado='Finalizada' THEN 1 ELSE 0 END) AS finalizadas
            FROM etapa_obra
            WHERE id_obra = ?";


    $stmt = $this->conexion->prepare($sql);

    $stmt->execute([$id_obra]);


    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);


    if($resultado["total"] == 0){

        return 0;

    }


    return round(
        ($resultado["finalizadas"] / $resultado["total"]) * 100
    );

}


}