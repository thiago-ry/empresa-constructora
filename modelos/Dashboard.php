<?php

require_once __DIR__ . "/Conexion.php";

class Dashboard
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    private function contarObras()
    {
        try {

            $sql = "
                SELECT COUNT(*) AS total
                FROM obra
            ";

            $consulta = $this->conexion->query($sql);

            return (int)$consulta->fetch(PDO::FETCH_ASSOC)["total"];

        } catch (Exception $e) {
            return 0;
        }
    }


    private function contarClientes()
    {
        try {

            $sql = "
                SELECT COUNT(*) AS total
                FROM usuario u
                INNER JOIN roles r
                    ON u.id_rol = r.id_rol
                WHERE u.estado = 1
                AND r.nombre_rol = 'Cliente';
            ";

            $consulta = $this->conexion->query($sql);

            return (int)$consulta->fetch(PDO::FETCH_ASSOC)["total"];

        } catch (Exception $e) {
            return 0;
        }
    }


    private function contarEmpleados()
    {
        try {

            $sql = "
                SELECT COUNT(*) AS total
                FROM usuario u
                INNER JOIN roles r
                    ON u.id_rol = r.id_rol
                WHERE u.estado = 1
                AND r.nombre_rol = 'Empleado'
            ";

            $consulta = $this->conexion->query($sql);

            return (int)$consulta->fetch(PDO::FETCH_ASSOC)["total"];

        } catch (Exception $e) {
            return 0;
        }
    }


    private function contarMateriales()
    {
        try {

            $sql = "
                SELECT COUNT(*) AS total
                FROM material
            ";

            $consulta = $this->conexion->query($sql);

            return (int)$consulta->fetch(PDO::FETCH_ASSOC)["total"];

        } catch (Exception $e) {
            return 0;
        }
    }


    private function estadisticasHerramientas()
    {
        $resultado = [
            "total" => 0,
            "disponibles" => 0,
            "asignadas" => 0,
            "reparacion" => 0,
            "fuera_servicio" => 0
        ];

        try {

            $sql = "
                SELECT
                    COUNT(*) AS total,
                    SUM(
                        CASE
                            WHEN LOWER(e.nombre) = 'disponible'
                            THEN 1
                            ELSE 0
                        END
                    ) AS disponibles,

                    SUM(
                        CASE
                            WHEN LOWER(e.nombre) = 'asignada'
                            THEN 1
                            ELSE 0
                        END
                    ) AS asignadas,

                    SUM(
                        CASE
                            WHEN LOWER(e.nombre) IN (
                                'en reparación',
                                'en reparacion'
                            )
                            THEN 1
                            ELSE 0
                        END
                    ) AS reparacion,

                    SUM(
                        CASE
                            WHEN LOWER(e.nombre) = 'fuera de servicio'
                            THEN 1
                            ELSE 0
                        END
                    ) AS fuera_servicio

                FROM unidad_herramienta u

                INNER JOIN estado_herramienta e
                    ON u.id_estado_herramienta =
                       e.id_estado_herramienta
            ";

            $consulta = $this->conexion->query($sql);

            $datos = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($datos) {

                $resultado["total"] =
                    (int)($datos["total"] ?? 0);

                $resultado["disponibles"] =
                    (int)($datos["disponibles"] ?? 0);

                $resultado["asignadas"] =
                    (int)($datos["asignadas"] ?? 0);

                $resultado["reparacion"] =
                    (int)($datos["reparacion"] ?? 0);

                $resultado["fuera_servicio"] =
                    (int)($datos["fuera_servicio"] ?? 0);
            }

        } catch (Exception $e) {
            
        }

        return $resultado;
    }


    public function obtenerActividadReciente($limite = 8)
    {
        try {

            $limite = (int)$limite;

            if ($limite <= 0) {
                $limite = 8;
            }

            $sql = "
                SELECT
                    a.id_auditoria,
                    a.accion,
                    a.tabla_afectada,
                    a.id_registro,
                    a.fecha,
                    a.descripcion,
                    u.nombre,
                    u.apellido

                FROM auditoria a

                LEFT JOIN usuario u
                    ON a.id_usuario = u.id_usuario

                ORDER BY a.fecha DESC

                LIMIT $limite
            ";

            $consulta = $this->conexion->query($sql);

            return $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {

            return [];
        }
    }


    public function obtenerEstadisticasAuditoria()
    {
        $resultado = [
            "total" => 0,
            "insertar" => 0,
            "editar" => 0,
            "eliminar" => 0
        ];

        try {

            $sql = "
                SELECT COUNT(*) AS total
                FROM auditoria
            ";

            $consulta = $this->conexion->query($sql);

            $resultado["total"] =
                (int)$consulta
                ->fetch(PDO::FETCH_ASSOC)["total"];


            $sql = "
                SELECT
                    LOWER(accion) AS accion,
                    COUNT(*) AS cantidad
                FROM auditoria
                GROUP BY LOWER(accion)
            ";

            $consulta = $this->conexion->query($sql);

            while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {

                $accion = $fila["accion"];
                $cantidad = (int)$fila["cantidad"];

                if ($accion === "insertar") {
                    $resultado["insertar"] = $cantidad;
                }

                if ($accion === "editar") {
                    $resultado["editar"] = $cantidad;
                }

                if ($accion === "eliminar") {
                    $resultado["eliminar"] = $cantidad;
                }
            }

        } catch (Exception $e) {
        }

        return $resultado;
    }


    public function obtenerDatos()
    {
        $herramientas =
            $this->estadisticasHerramientas();

        $auditoria =
            $this->obtenerEstadisticasAuditoria();

        return [
            "obras" =>
                $this->contarObras(),

            "clientes" =>
                $this->contarClientes(),

            "empleados" =>
                $this->contarEmpleados(),

            "materiales" =>
                $this->contarMateriales(),

            "herramientas" =>
                $herramientas,

            "auditoria" =>
                $auditoria,

            "actividad" =>
                $this->obtenerActividadReciente(8)
        ];
    }
}