<?php

class Presupuesto
{
    private PDO $conexion;

    public function __construct(PDO $db)
    {
        $this->conexion = $db;
    }

    // LISTAR PRESUPUESTOS
    public function listar(): array
    {
        $sql = "
            SELECT p.*, o.nombre_obra
            FROM presupuesto p
            INNER JOIN obra o ON o.id_obra = p.id_obra
            ORDER BY p.id_presupuesto DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // LISTAR OBRAS ACTIVAS
    public function listarObras(): array
    {
        $sql = "
            SELECT id_obra, nombre_obra
            FROM obra
            WHERE estado != 'Cancelada'
            ORDER BY nombre_obra ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // LISTAR TODOS LOS MATERIALES ACTIVOS DEL CATÁLOGO
    public function listarMaterialesActivos(): array
    {
        $sql = "
            SELECT
                id_material,
                nombre_material,
                unidad_medida
            FROM material
            WHERE estado = 1
            ORDER BY nombre_material ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // MANTENER EL MÉTODO DE BÚSQUEDA POR COMPATIBILIDAD
    public function buscarMateriales(string $busqueda = ''): array
    {
        $sql = "
            SELECT
                id_material,
                nombre_material,
                descripcion,
                unidad_medida
            FROM material
            WHERE estado = 1
        ";

        $parametros = [];

        if ($busqueda !== '') {
            $sql .= " AND nombre_material LIKE :busqueda";
            $parametros[':busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= " ORDER BY nombre_material ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // MATERIALES ASOCIADOS A UNA OBRA (NO SE USA PARA EL PRESUPUESTO)
    public function materialesDeObra(int $idObra): array
    {
        $sql = "
            SELECT
                mo.id_material,
                m.nombre_material,
                m.unidad_medida,
                mo.cantidad
            FROM material_obra mo
            INNER JOIN material m ON m.id_material = mo.id_material
            WHERE mo.id_obra = :id_obra
              AND m.estado = 1
            ORDER BY m.nombre_material ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([':id_obra' => $idObra]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // LISTAR RUBROS
    public function listarRubros(): array
    {
        $sql = "
            SELECT id_rubro, nombre_rubro, orden
            FROM rubros_obra
            ORDER BY orden ASC, nombre_rubro ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // OBTENER UN PRESUPUESTO CON SUS DETALLES
    public function obtener(int $id): ?array
    {
        $sql = "
            SELECT p.*, o.nombre_obra
            FROM presupuesto p
            INNER JOIN obra o ON o.id_obra = p.id_obra
            WHERE p.id_presupuesto = :id
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([':id' => $id]);

        $presupuesto = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$presupuesto) {
            return null;
        }

        $sqlDetalles = "
            SELECT *
            FROM detalle_presupuesto
            WHERE id_presupuesto = :id
            ORDER BY id_detalle ASC
        ";

        $consultaDetalles = $this->conexion->prepare($sqlDetalles);
        $consultaDetalles->execute([':id' => $id]);

        $presupuesto['materiales'] =
            $consultaDetalles->fetchAll(PDO::FETCH_ASSOC);

        $sqlManoObra = "
            SELECT *
            FROM mano_obra_presupuesto
            WHERE id_presupuesto = :id
            ORDER BY id_mano_obra_pres ASC
        ";

        $consultaManoObra = $this->conexion->prepare($sqlManoObra);
        $consultaManoObra->execute([':id' => $id]);

        $presupuesto['mano_obra'] =
            $consultaManoObra->fetchAll(PDO::FETCH_ASSOC);

        return $presupuesto;
    }

    // CREAR PRESUPUESTO
    public function crear(array $datos): int
    {
        $idObra = filter_var(
            $datos['id_obra'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$idObra || $idObra <= 0) {
            throw new InvalidArgumentException(
                'Debe seleccionar una obra válida.'
            );
        }

        $materiales = $datos['materiales'] ?? [];
        $trabajos = $datos['trabajos'] ?? [];

        if (!is_array($materiales) || !is_array($trabajos)) {
            throw new InvalidArgumentException(
                'Los datos de materiales o mano de obra no son válidos.'
            );
        }

        $porcGastosObra = (float)($datos['porc_gastos_obra'] ?? 8);
        $porcGastosOficina = (float)($datos['porc_gastos_oficina'] ?? 12);
        $porcImprevistos = (float)($datos['porc_imprevistos'] ?? 5);
        $porcGanancia = (float)($datos['porc_ganancia'] ?? 15);

        foreach ([
            $porcGastosObra,
            $porcGastosOficina,
            $porcImprevistos,
            $porcGanancia
        ] as $porcentaje) {
            if (!is_finite($porcentaje) || $porcentaje < 0 || $porcentaje > 100) {
                throw new InvalidArgumentException(
                    'Los porcentajes deben estar entre 0 y 100.'
                );
            }
        }

        $this->conexion->beginTransaction();

        try {
            // Verificar obra activa
            $consultaObra = $this->conexion->prepare("
                SELECT id_obra
                FROM obra
                WHERE id_obra = :id
                  AND activo = 1
                LIMIT 1
            ");

            $consultaObra->execute([':id' => $idObra]);

            if (!$consultaObra->fetch(PDO::FETCH_ASSOC)) {
                throw new RuntimeException(
                    'La obra seleccionada no existe o está inactiva.'
                );
            }

            $totalMateriales = 0;
            $totalManoObra = 0;
            $materialesValidados = [];
            $trabajosValidados = [];
            $materialesRepetidos = [];

            // VALIDAR MATERIALES
            foreach ($materiales as $material) {
                $idMaterial = (int)($material['id_material'] ?? 0);
                $cantidad = (float)($material['cantidad'] ?? 0);
                $precio = (float)($material['costo_unitario'] ?? 0);

                if (
                    $idMaterial <= 0 ||
                    !is_finite($cantidad) ||
                    !is_finite($precio) ||
                    $cantidad <= 0 ||
                    $precio < 0
                ) {
                    throw new InvalidArgumentException(
                        'Hay un material con cantidad o precio inválido.'
                    );
                }

                if (isset($materialesRepetidos[$idMaterial])) {
                    throw new InvalidArgumentException(
                        'No se puede agregar dos veces el mismo material.'
                    );
                }

                $materialesRepetidos[$idMaterial] = true;

                $consultaMaterial = $this->conexion->prepare("
                    SELECT id_material, nombre_material, unidad_medida
                    FROM material
                    WHERE id_material = :id
                      AND estado = 1
                    LIMIT 1
                ");

                $consultaMaterial->execute([':id' => $idMaterial]);

                $materialDB = $consultaMaterial->fetch(PDO::FETCH_ASSOC);

                if (!$materialDB) {
                    throw new RuntimeException(
                        'Uno de los materiales seleccionados no está activo.'
                    );
                }

                $desperdicio = (float)(
                    $material['porcentaje_desperdicio'] ?? 0
                );

                if (
                    !is_finite($desperdicio) ||
                    $desperdicio < 0 ||
                    $desperdicio > 100
                ) {
                    throw new InvalidArgumentException(
                        'El porcentaje de desperdicio no es válido.'
                    );
                }

                $subtotal = $cantidad * $precio;
                $subtotal += $subtotal * ($desperdicio / 100);

                $totalMateriales += $subtotal;

                $materialesValidados[] = [
                    'id_material' => $idMaterial,
                    'descripcion' => $materialDB['nombre_material'],
                    'unidad_medida' => $materialDB['unidad_medida'] ?? 'unidad',
                    'cantidad' => $cantidad,
                    'costo_unitario' => $precio,
                    'porcentaje_desperdicio' => $desperdicio,
                    'subtotal' => $subtotal
                ];
            }

            // VALIDAR MANO DE OBRA
            foreach ($trabajos as $trabajo) {
                $descripcion = trim($trabajo['descripcion'] ?? '');
                $horas = (float)($trabajo['cantidad_horas'] ?? 0);
                $costoHora = (float)($trabajo['costo_hora'] ?? 0);

                if (
                    $descripcion === '' ||
                    !is_finite($horas) ||
                    !is_finite($costoHora) ||
                    $horas <= 0 ||
                    $costoHora < 0
                ) {
                    throw new InvalidArgumentException(
                        'Hay un trabajo con datos inválidos.'
                    );
                }

                $subtotal = $horas * $costoHora;
                $totalManoObra += $subtotal;

                $trabajosValidados[] = [
                    'descripcion' => $descripcion,
                    'cantidad_horas' => $horas,
                    'costo_hora' => $costoHora,
                    'subtotal' => $subtotal
                ];
            }

            if (count($materialesValidados) === 0 &&
                count($trabajosValidados) === 0) {
                throw new InvalidArgumentException(
                    'Agregá al menos un material o un trabajo.'
                );
            }

            // CÁLCULO DE COSTOS
            $costoDirecto = $totalMateriales + $totalManoObra;

            $gastosObra = $costoDirecto * ($porcGastosObra / 100);
            $gastosOficina = $costoDirecto * ($porcGastosOficina / 100);
            $imprevistos = $costoDirecto * ($porcImprevistos / 100);

            $baseGanancia = $costoDirecto
                + $gastosObra
                + $gastosOficina
                + $imprevistos;

            $ganancia = $baseGanancia * ($porcGanancia / 100);
            $costoTotal = $baseGanancia + $ganancia;

            // INSERTAR PRESUPUESTO
            $sqlPresupuesto = "
                INSERT INTO presupuesto (
                    id_obra,
                    nombre_presupuesto,
                    costo_total,
                    detalle_general,
                    validez_dias,
                    forma_pago,
                    clausula_redeterminacion,
                    porc_gastos_obra,
                    porc_gastos_oficina,
                    porc_imprevistos,
                    porc_ganancia,
                    costo_directo
                ) VALUES (
                    :id_obra,
                    :nombre,
                    :costo_total,
                    :detalle,
                    :validez,
                    :forma_pago,
                    :clausula,
                    :gastos_obra,
                    :gastos_oficina,
                    :imprevistos,
                    :ganancia,
                    :costo_directo
                )
            ";

            $consulta = $this->conexion->prepare($sqlPresupuesto);

            $consulta->execute([
                ':id_obra' => $idObra,
                ':nombre' => trim($datos['nombre_presupuesto'] ?? '')
                    ?: 'Presupuesto General',
                ':costo_total' => $costoTotal,
                ':detalle' => $datos['detalle_general'] ?? null,
                ':validez' => (int)($datos['validez_dias'] ?? 15),
                ':forma_pago' => $datos['forma_pago'] ?? null,
                ':clausula' => $datos['clausula_redeterminacion'] ?? null,
                ':gastos_obra' => $porcGastosObra,
                ':gastos_oficina' => $porcGastosOficina,
                ':imprevistos' => $porcImprevistos,
                ':ganancia' => $porcGanancia,
                ':costo_directo' => $costoDirecto
            ]);

            $idPresupuesto = (int)$this->conexion->lastInsertId();

            // GUARDAR MATERIALES EN detalle_presupuesto
            $sqlMaterial = "
                INSERT INTO detalle_presupuesto (
                    id_presupuesto,
                    id_material,
                    descripcion,
                    unidad_medida,
                    cantidad,
                    costo_unitario,
                    porcentaje_desperdicio,
                    subtotal
                ) VALUES (
                    :id_presupuesto,
                    :id_material,
                    :descripcion,
                    :unidad,
                    :cantidad,
                    :precio,
                    :desperdicio,
                    :subtotal
                )
            ";

            $insertarMaterial = $this->conexion->prepare($sqlMaterial);

            foreach ($materialesValidados as $material) {
                $insertarMaterial->execute([
                    ':id_presupuesto' => $idPresupuesto,
                    ':id_material' => $material['id_material'],
                    ':descripcion' => $material['descripcion'],
                    ':unidad' => $material['unidad_medida'],
                    ':cantidad' => $material['cantidad'],
                    ':precio' => $material['costo_unitario'],
                    ':desperdicio' => $material['porcentaje_desperdicio'],
                    ':subtotal' => $material['subtotal']
                ]);
            }

            // GUARDAR MANO DE OBRA
            $sqlTrabajo = "
                INSERT INTO mano_obra_presupuesto (
                    id_presupuesto,
                    descripcion,
                    cantidad_horas,
                    costo_hora,
                    subtotal
                ) VALUES (
                    :id_presupuesto,
                    :descripcion,
                    :horas,
                    :costo_hora,
                    :subtotal
                )
            ";

            $insertarTrabajo = $this->conexion->prepare($sqlTrabajo);

            foreach ($trabajosValidados as $trabajo) {
                $insertarTrabajo->execute([
                    ':id_presupuesto' => $idPresupuesto,
                    ':descripcion' => $trabajo['descripcion'],
                    ':horas' => $trabajo['cantidad_horas'],
                    ':costo_hora' => $trabajo['costo_hora'],
                    ':subtotal' => $trabajo['subtotal']
                ]);
            }

            $this->conexion->commit();

            return $idPresupuesto;

        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $e;
        }
    }
}