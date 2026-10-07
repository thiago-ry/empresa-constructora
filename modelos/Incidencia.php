<?php

require_once __DIR__ . "/Conexion.php";

class Incidencia
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    /* =========================
       OBTENER INCIDENCIAS POR OBRA
       ========================= */

    public function obtenerPorObra($id_obra)
    {
        $sql = "SELECT *
                FROM incidencia
                WHERE id_obra = ?
                ORDER BY fecha DESC, id_incidencia DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_obra]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* =========================
       CREAR INCIDENCIA
       ========================= */

    public function crear($datos)
    {
        $sql = "INSERT INTO incidencia
                (
                    id_obra,
                    fecha,
                    tipo_incidencia,
                    descripcion,
                    estado,
                    solucion
                )
                VALUES
                (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $datos["id_obra"],
            $datos["fecha"],
            $datos["tipo_incidencia"],
            $datos["descripcion"],
            $datos["estado"],
            $datos["solucion"]
        ]);
    }


    /* =========================
       OBTENER POR ID
       ========================= */

    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM incidencia
                WHERE id_incidencia = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /* =========================
       ACTUALIZAR
       ========================= */

    public function actualizar($datos)
    {
        $sql = "UPDATE incidencia SET
                fecha = ?,
                tipo_incidencia = ?,
                descripcion = ?,
                estado = ?,
                solucion = ?
                WHERE id_incidencia = ?";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $datos["fecha"],
            $datos["tipo_incidencia"],
            $datos["descripcion"],
            $datos["estado"],
            $datos["solucion"],
            $datos["id_incidencia"]
        ]);
    }


    /* =========================
       ELIMINAR INCIDENCIA
       ========================= */

    public function eliminar($id)
    {
        $sql = "DELETE FROM incidencia
                WHERE id_incidencia = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }


    /* =========================
       RESUMEN
       ========================= */

    public function obtenerResumen($id_obra)
    {
        $sql = "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN estado = 'En revisión' THEN 1 ELSE 0 END) AS revision,
                SUM(CASE WHEN estado = 'Resuelta' THEN 1 ELSE 0 END) AS resueltas
                FROM incidencia
                WHERE id_obra = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_obra]);

        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            "total" => (int)$datos["total"],
            "pendientes" => (int)$datos["pendientes"],
            "revision" => (int)$datos["revision"],
            "resueltas" => (int)$datos["resueltas"]
        ];
    }


    /* =========================
       OBTENER FOTOS
       ========================= */

    public function obtenerFotos($id_incidencia)
    {
        $sql = "SELECT *
                FROM foto_incidencia
                WHERE id_incidencia = ?
                ORDER BY fecha DESC, id_foto_incidencia DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_incidencia]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* =========================
       AGREGAR FOTO
       ========================= */

    public function agregarFoto($id_incidencia, $ruta_foto)
    {
        $sql = "INSERT INTO foto_incidencia
                (
                    id_incidencia,
                    ruta_foto,
                    fecha
                )
                VALUES
                (?, ?, NOW())";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $id_incidencia,
            $ruta_foto
        ]);
    }


    /* =========================
       OBTENER FOTO POR ID
       ========================= */

    public function obtenerFoto($id_foto)
    {
        $sql = "SELECT *
                FROM foto_incidencia
                WHERE id_foto_incidencia = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_foto]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /* =========================
       ELIMINAR FOTO
       ========================= */

    public function eliminarFoto($id_foto)
    {
        $sql = "DELETE FROM foto_incidencia
                WHERE id_foto_incidencia = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_foto]);

        return $stmt->rowCount() > 0;
    }


    /* =========================
       ÚLTIMO ID
       ========================= */

    public function obtenerUltimoId()
    {
        return $this->conexion->lastInsertId();
    }
}