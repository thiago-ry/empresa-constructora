<?php

class Conexion {

    private $host = "localhost";
    private $usuario = "root";
    private $password = "";
    private $baseDatos = "constructora";

    public function conectar(){

        try {

            $conexion = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->baseDatos.";charset=utf8",
                $this->usuario,
                $this->password
            );

            $conexion->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $conexion;

        } catch(PDOException $e){

            die("Error de conexión: ".$e->getMessage());

        }

    }

}

?>