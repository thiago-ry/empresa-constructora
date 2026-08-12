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
    public function obras()
    {
        $sql = "SELECT o.*,
                   CONCAT(u.nombre,' ',u.apellido) AS responsable
            FROM obra o
            INNER JOIN usuario u
                ON o.id_usuario = u.id_usuario
            ORDER BY o.fecha_inicio DESC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function promedioAvance()
    {
        require_once "Etapa.php";

        $etapa = new Etapa();

        $obras = $this->obras();

        $suma = 0;

        foreach ($obras as $obra) {
            $suma += $etapa->calcularAvance($obra["id_obra"]);
        }

        return count($obras) > 0
            ? round($suma / count($obras))
            : 0;
    }

public function empleados()
{
    $sql = "
        SELECT
            u.id_usuario,
            u.nombre,
            u.apellido,
            u.documento,
            u.correo,
            u.telefono,
            u.direccion,
            u.salario,
            u.fecha_registro,
            u.estado,

            COALESCE(
                GROUP_CONCAT(
                    DISTINCT c.nombre_cargo
                    ORDER BY c.nombre_cargo
                    SEPARATOR ', '
                ),
                'Sin cargo'
            ) AS nombre_cargo,

            GROUP_CONCAT(
                DISTINCT o.nombre_obra
                ORDER BY o.nombre_obra
                SEPARATOR ', '
            ) AS obras,

            COUNT(
                DISTINCT CASE
                    WHEN eo.estado = 1 THEN eo.id_obra
                END
            ) AS cantidad_obras

        FROM usuario u

        LEFT JOIN empleado_cargo uc
            ON uc.id_usuario = u.id_usuario

        LEFT JOIN cargo c
            ON c.id_cargo = uc.id_cargo

        LEFT JOIN empleado_obra eo
            ON eo.id_usuario = u.id_usuario

        LEFT JOIN obra o
            ON o.id_obra = eo.id_obra

        WHERE u.id_rol = 1

        GROUP BY
            u.id_usuario,
            u.nombre,
            u.apellido,
            u.documento,
            u.correo,
            u.telefono,
            u.direccion,
            u.salario,
            u.fecha_registro,
            u.estado

        ORDER BY
            u.apellido ASC,
            u.nombre ASC
    ";

    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
