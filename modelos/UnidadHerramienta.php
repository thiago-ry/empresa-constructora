
<?php

require_once "Conexion.php";

class UnidadHerramienta
{
    private $conexion;

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    // =========================================================
    // OBTENER TODAS LAS UNIDADES
    // =========================================================

    public function obtenerTodas()
    {
        $sql = "
            SELECT
                u.id_unidad,
                u.numero_unidad,
                u.id_herramienta,
                h.nombre AS herramienta,
                h.marca,
                h.modelo,
                u.id_estado_herramienta,
                e.nombre AS estado
            FROM unidad_herramienta u
            INNER JOIN herramienta h
                ON u.id_herramienta = h.id_herramienta
            INNER JOIN estado_herramienta e
                ON u.id_estado_herramienta = e.id_estado_herramienta
            ORDER BY h.nombre ASC, u.numero_unidad ASC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // OBTENER UNIDADES DE UNA HERRAMIENTA ESPECÍFICA
    // =========================================================

    public function obtenerPorHerramienta($id_herramienta)
    {
        $sql = "
            SELECT
                u.id_unidad,
                u.numero_unidad,
                u.id_herramienta,
                h.nombre AS herramienta,
                h.marca,
                h.modelo,
                u.id_estado_herramienta,
                e.nombre AS estado
            FROM unidad_herramienta u
            INNER JOIN herramienta h
                ON u.id_herramienta = h.id_herramienta
            INNER JOIN estado_herramienta e
                ON u.id_estado_herramienta = e.id_estado_herramienta
            WHERE u.id_herramienta = ?
            ORDER BY u.numero_unidad ASC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $id_herramienta
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // CREAR UNIDADES AL REGISTRAR UNA HERRAMIENTA
    // =========================================================

    public function crearUnidades($id_herramienta, $cantidad)
    {
        try {

            $this->conexion->beginTransaction();

            // Buscamos el ID del estado "Disponible"
            // De esta manera no dependemos de que sea necesariamente
            // el ID 1.

            $sqlEstado = "
                SELECT id_estado_herramienta
                FROM estado_herramienta
                WHERE LOWER(nombre) = 'disponible'
                LIMIT 1
            ";

            $stmtEstado = $this->conexion->prepare($sqlEstado);
            $stmtEstado->execute();

            $estadoDisponible = $stmtEstado->fetch(PDO::FETCH_ASSOC);

            if (!$estadoDisponible) {
                throw new Exception(
                    "No existe el estado 'Disponible'."
                );
            }

            $id_estado_disponible =
                $estadoDisponible["id_estado_herramienta"];

            // Insertamos las unidades

            $sql = "
                INSERT INTO unidad_herramienta
                (
                    id_herramienta,
                    numero_unidad,
                    id_estado_herramienta
                )
                VALUES
                (
                    :id_herramienta,
                    :numero_unidad,
                    :id_estado_herramienta
                )
            ";

            $stmt = $this->conexion->prepare($sql);

            for ($i = 1; $i <= (int)$cantidad; $i++) {

                $stmt->execute([
                    ":id_herramienta" =>
                        $id_herramienta,

                    ":numero_unidad" =>
                        $i,

                    ":id_estado_herramienta" =>
                        $id_estado_disponible
                ]);
            }

            $this->conexion->commit();

            return true;

        } catch (Exception $e) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }

    // =========================================================
    // OBTENER UNIDADES DISPONIBLES
    // =========================================================

    public function obtenerDisponibles($id_herramienta)
    {
        $sql = "
            SELECT
                u.id_unidad,
                u.numero_unidad,
                u.id_herramienta,
                u.id_estado_herramienta,
                e.nombre AS estado
            FROM unidad_herramienta u
            INNER JOIN estado_herramienta e
                ON u.id_estado_herramienta =
                   e.id_estado_herramienta
            WHERE u.id_herramienta = ?
            AND LOWER(e.nombre) = 'disponible'
            ORDER BY u.numero_unidad ASC
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $id_herramienta
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // CAMBIAR ESTADO
    // =========================================================

    public function cambiarEstado($id_unidad, $estado)
    {
        $sql = "
            UPDATE unidad_herramienta
            SET id_estado_herramienta = ?
            WHERE id_unidad = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $estado,
            $id_unidad
        ]);
    }

    // =========================================================
    // BUSCAR POR ID
    // =========================================================

    public function buscarPorId($id)
    {
        $sql = "
            SELECT
                u.id_unidad,
                u.numero_unidad,
                u.id_herramienta,
                u.id_estado_herramienta,
                h.nombre AS herramienta,
                h.marca,
                h.modelo,
                e.nombre AS estado
            FROM unidad_herramienta u
            INNER JOIN herramienta h
                ON u.id_herramienta = h.id_herramienta
            INNER JOIN estado_herramienta e
                ON u.id_estado_herramienta =
                   e.id_estado_herramienta
            WHERE u.id_unidad = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // CONTAR UNIDADES DE UNA HERRAMIENTA
    // =========================================================

    public function contarPorHerramienta($id_herramienta)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM unidad_herramienta
            WHERE id_herramienta = ?
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            $id_herramienta
        ]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)$resultado["total"];
    }

    // =========================================================
    // LIBERAR UNIDADES AL REALIZAR UNA DEVOLUCIÓN
    // =========================================================
    //
    // Busca unidades ASIGNADAS de una herramienta y cambia
    // la cantidad indicada al estado DISPONIBLE.
    //
    // No depende de que Disponible sea el ID 1.
    //
    // =========================================================

    public function liberarUnidades($id_herramienta, $cantidad)
    {
        try {

            $cantidad = (int)$cantidad;

            if ($cantidad <= 0) {
                return false;
            }

            $this->conexion->beginTransaction();

            // -------------------------------------------------
            // BUSCAR ESTADO DISPONIBLE
            // -------------------------------------------------

            $sqlDisponible = "
                SELECT id_estado_herramienta
                FROM estado_herramienta
                WHERE LOWER(nombre) = 'disponible'
                LIMIT 1
            ";

            $stmtDisponible =
                $this->conexion->prepare($sqlDisponible);

            $stmtDisponible->execute();

            $estadoDisponible =
                $stmtDisponible->fetch(PDO::FETCH_ASSOC);

            if (!$estadoDisponible) {
                throw new Exception(
                    "No existe el estado 'Disponible'."
                );
            }

            $id_estado_disponible =
                $estadoDisponible["id_estado_herramienta"];


            // -------------------------------------------------
            // BUSCAR UNIDADES ASIGNADAS
            // -------------------------------------------------

            $sqlAsignadas = "
                SELECT
                    u.id_unidad
                FROM unidad_herramienta u
                INNER JOIN estado_herramienta e
                    ON u.id_estado_herramienta =
                       e.id_estado_herramienta
                WHERE u.id_herramienta = ?
                AND LOWER(e.nombre) = 'asignada'
                ORDER BY u.numero_unidad ASC
                LIMIT ?
            ";

            $stmtAsignadas =
                $this->conexion->prepare($sqlAsignadas);

            $stmtAsignadas->bindValue(
                1,
                $id_herramienta,
                PDO::PARAM_INT
            );

            $stmtAsignadas->bindValue(
                2,
                $cantidad,
                PDO::PARAM_INT
            );

            $stmtAsignadas->execute();

            $unidadesAsignadas =
                $stmtAsignadas->fetchAll(PDO::FETCH_ASSOC);


            // -------------------------------------------------
            // VERIFICAR QUE HAYA SUFICIENTES UNIDADES
            // -------------------------------------------------

            if (count($unidadesAsignadas) < $cantidad) {

                throw new Exception(
                    "No hay suficientes unidades asignadas para realizar la devolución."
                );
            }


            // -------------------------------------------------
            // CAMBIAR LAS UNIDADES A DISPONIBLE
            // -------------------------------------------------

            $sqlActualizar = "
                UPDATE unidad_herramienta
                SET id_estado_herramienta = ?
                WHERE id_unidad = ?
            ";

            $stmtActualizar =
                $this->conexion->prepare($sqlActualizar);

            foreach ($unidadesAsignadas as $unidad) {

                $stmtActualizar->execute([
                    $id_estado_disponible,
                    $unidad["id_unidad"]
                ]);
            }


            // -------------------------------------------------
            // CONFIRMAR
            // -------------------------------------------------

            $this->conexion->commit();

            return true;

        } catch (Exception $e) {

            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }
}
