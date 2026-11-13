
<?php

require_once 'Conexion.php';

class DocumentoObra
{
    private PDO $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR DOCUMENTOS DE UNA OBRA
    |--------------------------------------------------------------------------
    */

    public function listarPorObra(int $id_obra): array
    {
        $sql = "SELECT
                    id_documento,
                    id_obra,
                    tipo_documento,
                    nombre_archivo,
                    ruta_archivo,
                    descripcion,
                    fecha_subida,
                    id_usuario
                FROM documento_obra
                WHERE id_obra = :id_obra
                ORDER BY fecha_subida DESC, id_documento DESC";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ':id_obra' => $id_obra
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER DOCUMENTO POR ID
    |--------------------------------------------------------------------------
    */

    public function obtenerPorId(int $id_documento): ?array
    {
        $sql = "SELECT *
                FROM documento_obra
                WHERE id_documento = :id_documento
                LIMIT 1";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ':id_documento' => $id_documento
        ]);

        $documento = $consulta->fetch(PDO::FETCH_ASSOC);

        return $documento ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function guardar(array $datos): int
    {
        $sql = "INSERT INTO documento_obra (
                    id_obra,
                    tipo_documento,
                    nombre_archivo,
                    ruta_archivo,
                    descripcion,
                    fecha_subida,
                    id_usuario
                ) VALUES (
                    :id_obra,
                    :tipo_documento,
                    :nombre_archivo,
                    :ruta_archivo,
                    :descripcion,
                    NOW(),
                    :id_usuario
                )";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            ':id_obra' => $datos['id_obra'],
            ':tipo_documento' => $datos['tipo_documento'],
            ':nombre_archivo' => $datos['nombre_archivo'],
            ':ruta_archivo' => $datos['ruta_archivo'],
            ':descripcion' => $datos['descripcion'],
            ':id_usuario' => $datos['id_usuario']
        ]);

        return (int)$this->conexion->lastInsertId();
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR DESCRIPCIÓN
    |--------------------------------------------------------------------------
    */

    public function actualizarDescripcion(
        int $id_documento,
        string $descripcion
    ): bool {

        $sql = "UPDATE documento_obra
                SET descripcion = :descripcion
                WHERE id_documento = :id_documento";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':descripcion' => $descripcion,
            ':id_documento' => $id_documento
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR DOCUMENTO
    |--------------------------------------------------------------------------
    */

    public function eliminar(int $id_documento): bool
    {
        $sql = "DELETE FROM documento_obra
                WHERE id_documento = :id_documento";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            ':id_documento' => $id_documento
        ]);
    }
}