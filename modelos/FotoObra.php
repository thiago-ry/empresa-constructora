
<?php

require_once __DIR__ . "/Conexion.php";

class FotoObra
{
    private PDO $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    /**
     * Listar fotografías de una obra.
     */
    public function listarPorObra(int $id_obra): array
    {
        $sql = "SELECT
                    id_foto AS id_foto_obra,
                    id_obra,
                    ruta_imagen AS nombre_archivo,
                    descripcion,
                    fecha AS fecha_subida
                FROM foto_obra
                WHERE id_obra = :id_obra
                ORDER BY fecha DESC, id_foto DESC";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ':id_obra' => $id_obra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guardar una fotografía.
     */
    public function guardar(array $datos): int
    {
        $sql = "INSERT INTO foto_obra
                    (id_obra, ruta_imagen, descripcion, fecha)
                VALUES
                    (:id_obra, :ruta_imagen, :descripcion, CURDATE())";

        $stmt = $this->conexion->prepare($sql);

        $descripcion = trim((string)($datos['descripcion'] ?? ''));

        $stmt->execute([
            ':id_obra' => (int)$datos['id_obra'],
            ':ruta_imagen' => $datos['ruta_imagen'],
            ':descripcion' => $descripcion !== '' ? $descripcion : null
        ]);

        return (int)$this->conexion->lastInsertId();
    }

    /**
     * Obtener una fotografía por su ID.
     */
    public function obtenerPorId(int $id_foto): ?array
    {
        $sql = "SELECT
                    id_foto AS id_foto_obra,
                    id_obra,
                    ruta_imagen AS nombre_archivo,
                    descripcion,
                    fecha AS fecha_subida
                FROM foto_obra
                WHERE id_foto = :id_foto
                LIMIT 1";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ':id_foto' => $id_foto
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    /**
     * Actualizar descripción de una fotografía.
     */
    public function actualizarDescripcion(
        int $id_foto,
        string $descripcion
    ): bool {
        $sql = "UPDATE foto_obra
                SET descripcion = :descripcion
                WHERE id_foto = :id_foto";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            ':descripcion' => trim($descripcion) !== ''
                ? trim($descripcion)
                : null,
            ':id_foto' => $id_foto
        ]);
    }

    /**
     * Eliminar una fotografía de la base de datos.
     */
    public function eliminar(int $id_foto): bool
    {
        $sql = "DELETE FROM foto_obra
                WHERE id_foto = :id_foto";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            ':id_foto' => $id_foto
        ]);

        return $stmt->rowCount() > 0;
    }
}
