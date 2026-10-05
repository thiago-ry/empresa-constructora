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

    public function obtenerReporte($filtros = [])
    {
        $sql = "
        SELECT *
        FROM (
            SELECT
                a.fecha AS fecha_hora,
                NULL AS fecha_egreso,
                CONCAT(u.nombre, ' ', u.apellido) AS usuario,
                'AUDITORIA' AS tipo,
                a.accion AS accion,
                a.tabla_afectada AS modulo,
                a.id_registro AS id_registro,
                a.descripcion AS descripcion
            FROM auditoria a
            INNER JOIN usuario u ON u.id_usuario = a.id_usuario

            UNION ALL

            SELECT
                ac.fecha_hora_ingreso AS fecha_hora,
                ac.fecha_hora_salida AS fecha_egreso,
                CONCAT(u.nombre, ' ', u.apellido) AS usuario,
                'ACCESO' AS tipo,
                'INGRESO' AS accion,
                'SISTEMA' AS modulo,
                NULL AS id_registro,
                'Ingreso al sistema' AS descripcion
            FROM acceso_usuario ac
            INNER JOIN usuario u ON u.id_usuario = ac.id_usuario
        ) AS reporte
        WHERE 1 = 1
    ";

        $parametros = [];

        if (!empty($filtros["buscar"])) {
            $sql .= " AND (
            usuario LIKE :buscar
            OR descripcion LIKE :buscar
        )";
            $parametros[":buscar"] = "%" . $filtros["buscar"] . "%";
        }

        if (!empty($filtros["tipo"])) {
            $sql .= " AND tipo = :tipo";
            $parametros[":tipo"] = $filtros["tipo"];
        }

        if (!empty($filtros["accion"])) {
            $sql .= " AND accion = :accion";
            $parametros[":accion"] = $filtros["accion"];
        }

        if (!empty($filtros["modulo"])) {
            $sql .= " AND modulo = :modulo";
            $parametros[":modulo"] = $filtros["modulo"];
        }

        if (!empty($filtros["fecha_desde"])) {
            $sql .= " AND DATE(fecha_hora) >= :fecha_desde";
            $parametros[":fecha_desde"] = $filtros["fecha_desde"];
        }

        if (!empty($filtros["fecha_hasta"])) {
            $sql .= " AND DATE(fecha_hora) <= :fecha_hasta";
            $parametros[":fecha_hasta"] = $filtros["fecha_hasta"];
        }

        $sql .= " ORDER BY fecha_hora DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
