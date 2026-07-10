<?php

require_once __DIR__ . "/Conexion.php";

class Dashboard{

    private $conexion;

    public function __construct(){
        $db=new Conexion();
        $this->conexion=$db->conectar();
    }

    private function contar($tabla){
        $sql="SELECT COUNT(*) total FROM $tabla";
        $consulta=$this->conexion->query($sql);
        return $consulta->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function obtenerDatos(){
        return[
            "obras"=>$this->contar("obra"),
            "clientes"=>$this->contar("cliente"),
            "empleados"=>$this->contar("empleado"),
            "materiales"=>$this->contar("material")
        ];
    }

}