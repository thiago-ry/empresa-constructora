<?php

require_once __DIR__ . "/Conexion.php";

/**
 * Modelo de reportes.
 *
 * Reúne en un solo lugar las consultas de todos los reportes del sistema
 * (obras, empleados, materiales, herramientas, clientes, usuarios,
 * asignaciones, incidencias y asistencia) y arma los datos que se
 * exportan a PDF y Excel.
 *
 * Los nombres se devuelven separados (nombre y apellido) y se unen en PHP
 * con nombreCompleto(), así una persona sin asignar no rompe el resultado.
 */
class Reporte
{
    private $conexion;

    /* Estados de obra que cuentan como "en curso" */
    const ESTADOS_OBRA_ACTIVOS = ["Planificacion", "En Proceso"];

    public function __construct()
    {
        $db = new Conexion();
        $this->conexion = $db->conectar();
    }

    /* =====================================================================
     *  AYUDANTES
     * ===================================================================== */

    private function consultar($sql, $parametros = [])
    {
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function valor($sql, $parametros = [])
    {
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($parametros);
        $valor = $stmt->fetchColumn();
        return $valor === false || $valor === null ? 0 : $valor;
    }

    /** Une nombre y apellido; devuelve $vacio si no hay persona. */
    public static function nombreCompleto($nombre, $apellido, $vacio = "Sin asignar")
    {
        $texto = trim(trim($nombre ?? "") . " " . trim($apellido ?? ""));
        return $texto === "" ? $vacio : $texto;
    }

    /** Fecha en formato dd/mm/aaaa; "-" si está vacía o es 0000-00-00. */
    public static function fecha($valor, $conHora = false)
    {
        if (empty($valor) || strpos($valor, "0000-00-00") === 0) {
            return "-";
        }
        $tiempo = strtotime($valor);
        if ($tiempo === false) {
            return "-";
        }
        return date($conHora ? "d/m/Y H:i" : "d/m/Y", $tiempo);
    }

    /** Importe con formato argentino: $ 1.234,50 */
    public static function moneda($valor)
    {
        return "$ " . number_format((float)$valor, 2, ",", ".");
    }

    /** Número con formato argentino, sin decimales si es entero. */
    public static function numero($valor)
    {
        $valor = (float)$valor;
        $decimales = floor($valor) == $valor ? 0 : 2;
        return number_format($valor, $decimales, ",", ".");
    }

    /** Separa un GROUP_CONCAT con coma y lo vuelve a unir con ", ". */
    private static function lista($texto)
    {
        if ($texto === null || $texto === "") {
            return "";
        }
        $partes = array_filter(array_map("trim", explode(",", $texto)), "strlen");
        $partes = array_unique($partes);
        sort($partes);
        return implode(", ", $partes);
    }

    private static function porcentaje($parte, $total)
    {
        return $total > 0 ? (int)round($parte / $total * 100) : 0;
    }

    /* =====================================================================
     *  RESUMEN GENERAL (pantalla principal de reportes)
     * ===================================================================== */

    public function resumenGeneral()
    {
        $activos = "'" . implode("','", self::ESTADOS_OBRA_ACTIVOS) . "'";

        return [
            "obras_total" => (int)$this->valor("SELECT COUNT(*) FROM obra WHERE activo = 1"),
            "obras_en_curso" => (int)$this->valor("SELECT COUNT(*) FROM obra WHERE activo = 1 AND estado IN ($activos)"),
            "obras_finalizadas" => (int)$this->valor("SELECT COUNT(*) FROM obra WHERE activo = 1 AND estado = 'Finalizada'"),
            "clientes" => (int)$this->valor("SELECT COUNT(*) FROM usuario WHERE id_rol = 6 AND estado = 1"),
            "empleados" => (int)$this->valor("SELECT COUNT(*) FROM usuario WHERE id_rol = 1 AND estado = 1"),
            "materiales_criticos" => (int)$this->valor(
                "SELECT COUNT(*) FROM material WHERE estado = 1 AND stock <= stock_minimo"
            ),
            "herramientas_en_obra" => (int)$this->valor(
                "SELECT COALESCE(SUM(CASE WHEN cantidad_asignada > cantidad_devuelta THEN cantidad_asignada - cantidad_devuelta ELSE 0 END), 0) FROM herramienta_obra"
            ),
            "incidencias_abiertas" => (int)$this->valor(
                "SELECT COUNT(*) FROM incidencia WHERE estado <> 'Resuelta' OR estado IS NULL"
            ),
            "presupuesto_total" => (float)$this->valor(
                "SELECT COALESCE(SUM(p.costo_total), 0)
                 FROM presupuesto p
                 WHERE p.id_presupuesto = (
                     SELECT p2.id_presupuesto FROM presupuesto p2
                     WHERE p2.id_obra = p.id_obra
                     ORDER BY p2.version DESC, p2.id_presupuesto DESC
                     LIMIT 1
                 )"
            ),
            "valor_herramientas" => (float)$this->valor(
                "SELECT COALESCE(SUM(cantidad_total * costo), 0) FROM herramienta"
            ),
        ];
    }

    /* =====================================================================
     *  OBRAS
     * ===================================================================== */

    public function obras()
    {
        $sql = "
            SELECT
                o.id_obra, o.nombre_obra, o.direccion, o.descripcion,
                o.fecha_inicio, o.fecha_fin, o.estado, o.activo,
                cli.nombre AS cliente_nombre, cli.apellido AS cliente_apellido,
                cli.telefono AS cliente_telefono,
                jef.nombre AS jefe_nombre, jef.apellido AS jefe_apellido,
                cap.nombre AS capataz_nombre, cap.apellido AS capataz_apellido,

                (SELECT COUNT(*) FROM etapa_obra e WHERE e.id_obra = o.id_obra) AS total_etapas,
                (SELECT COUNT(*) FROM etapa_obra e WHERE e.id_obra = o.id_obra AND e.estado = 'Finalizada') AS etapas_finalizadas,
                (SELECT COUNT(*) FROM etapa_obra e WHERE e.id_obra = o.id_obra AND e.estado = 'En Proceso') AS etapas_en_proceso,

                (SELECT COUNT(*) FROM empleado_obra eo WHERE eo.id_obra = o.id_obra AND eo.estado = 1) AS empleados_activos,

                (SELECT COUNT(*) FROM avance_diario a WHERE a.id_obra = o.id_obra) AS total_avances,
                (SELECT MAX(a.fecha) FROM avance_diario a WHERE a.id_obra = o.id_obra) AS ultimo_avance,

                (SELECT COUNT(*) FROM incidencia i WHERE i.id_obra = o.id_obra) AS total_incidencias,
                (SELECT COUNT(*) FROM incidencia i WHERE i.id_obra = o.id_obra
                    AND (i.estado <> 'Resuelta' OR i.estado IS NULL)) AS incidencias_abiertas,

                (SELECT COALESCE(SUM(CASE WHEN ho.cantidad_asignada > ho.cantidad_devuelta THEN ho.cantidad_asignada - ho.cantidad_devuelta ELSE 0 END), 0)
                    FROM herramienta_obra ho WHERE ho.id_obra = o.id_obra) AS herramientas_en_obra,
                (SELECT COALESCE(SUM(CASE WHEN ho.cantidad_asignada > ho.cantidad_devuelta THEN (ho.cantidad_asignada - ho.cantidad_devuelta) * h.costo ELSE 0 END), 0)
                    FROM herramienta_obra ho
                    INNER JOIN herramienta h ON h.id_herramienta = ho.id_herramienta
                    WHERE ho.id_obra = o.id_obra) AS valor_herramientas,

                (SELECT COUNT(*) FROM solicitud_material s WHERE s.id_obra = o.id_obra) AS solicitudes_material,
                (SELECT COUNT(*) FROM solicitud_material s WHERE s.id_obra = o.id_obra AND s.estado = 'Pendiente') AS solicitudes_pendientes,
                (SELECT COUNT(*) FROM entrega_material en
                    INNER JOIN solicitud_material s ON s.id_solicitud = en.id_solicitud
                    WHERE s.id_obra = o.id_obra) AS entregas_material,

                (SELECT p.costo_total FROM presupuesto p WHERE p.id_obra = o.id_obra
                    ORDER BY p.version DESC, p.id_presupuesto DESC LIMIT 1) AS presupuesto,
                (SELECT p.estado FROM presupuesto p WHERE p.id_obra = o.id_obra
                    ORDER BY p.version DESC, p.id_presupuesto DESC LIMIT 1) AS presupuesto_estado,
                (SELECT COUNT(*) FROM presupuesto p WHERE p.id_obra = o.id_obra) AS versiones_presupuesto
            FROM obra o
            LEFT JOIN usuario cli ON cli.id_usuario = o.id_usuario
            LEFT JOIN usuario jef ON jef.id_usuario = o.id_jefe_obra
            LEFT JOIN usuario cap ON cap.id_usuario = o.id_capataz
            WHERE o.activo = 1
            ORDER BY o.fecha_inicio DESC, o.nombre_obra ASC
        ";

        $obras = $this->consultar($sql);

        foreach ($obras as &$o) {
            $o["cliente"] = self::nombreCompleto($o["cliente_nombre"], $o["cliente_apellido"]);
            $o["jefe_obra"] = self::nombreCompleto($o["jefe_nombre"], $o["jefe_apellido"]);
            $o["capataz"] = self::nombreCompleto($o["capataz_nombre"], $o["capataz_apellido"]);
            // Mismo cálculo que Etapa::calcularAvance(): etapas finalizadas / total
            $o["avance"] = self::porcentaje((int)$o["etapas_finalizadas"], (int)$o["total_etapas"]);
            $o["presupuesto"] = $o["presupuesto"] === null ? null : (float)$o["presupuesto"];
        }
        unset($o);

        return $obras;
    }

    /** Se mantiene por compatibilidad con código anterior. */
    public function obrasPorEstado($estado)
    {
        return array_values(array_filter($this->obras(), fn($o) => $o["estado"] === $estado));
    }

    public function promedioAvance()
    {
        $obras = $this->obras();
        if (count($obras) === 0) {
            return 0;
        }
        return (int)round(array_sum(array_column($obras, "avance")) / count($obras));
    }

    /* =====================================================================
     *  EMPLEADOS
     * ===================================================================== */

    public function empleados()
    {
        $sql = "
            SELECT
                u.id_usuario, u.nombre, u.apellido, u.documento, u.correo,
                u.telefono, u.direccion, u.salario, u.fecha_registro, u.estado,

                (SELECT GROUP_CONCAT(DISTINCT c.nombre_cargo)
                    FROM empleado_cargo ec
                    INNER JOIN cargo c ON c.id_cargo = ec.id_cargo
                    WHERE ec.id_usuario = u.id_usuario) AS cargos,

                (SELECT GROUP_CONCAT(DISTINCT o.nombre_obra)
                    FROM empleado_obra eo
                    INNER JOIN obra o ON o.id_obra = eo.id_obra
                    WHERE eo.id_usuario = u.id_usuario AND eo.estado = 1) AS obras,

                (SELECT COUNT(DISTINCT eo.id_obra) FROM empleado_obra eo
                    WHERE eo.id_usuario = u.id_usuario AND eo.estado = 1) AS cantidad_obras,
                (SELECT COUNT(DISTINCT eo.id_obra) FROM empleado_obra eo
                    WHERE eo.id_usuario = u.id_usuario) AS obras_historicas,

                (SELECT COUNT(*) FROM asistencia a WHERE a.id_usuario = u.id_usuario AND a.estado = 'Presente') AS presentes,
                (SELECT COUNT(*) FROM asistencia a WHERE a.id_usuario = u.id_usuario AND a.estado = 'Tarde') AS tardes,
                (SELECT COUNT(*) FROM asistencia a WHERE a.id_usuario = u.id_usuario AND a.estado = 'Ausente') AS ausentes,
                (SELECT MAX(a.fecha) FROM asistencia a WHERE a.id_usuario = u.id_usuario) AS ultima_asistencia,

                (SELECT COUNT(*) FROM tarea_empleado te WHERE te.id_empleado = u.id_usuario) AS tareas_asignadas,
                (SELECT COUNT(*) FROM tarea_empleado te WHERE te.id_empleado = u.id_usuario
                    AND te.estado = 'Completada') AS tareas_completadas
            FROM usuario u
            WHERE u.id_rol = 1
            ORDER BY u.apellido ASC, u.nombre ASC
        ";

        $empleados = $this->consultar($sql);

        foreach ($empleados as &$e) {
            $e["empleado"] = self::nombreCompleto($e["nombre"], $e["apellido"], "-");
            $e["nombre_cargo"] = self::lista($e["cargos"]) ?: "Sin cargo";
            $e["obras"] = self::lista($e["obras"]);
            $diasRegistrados = (int)$e["presentes"] + (int)$e["tardes"] + (int)$e["ausentes"];
            $e["dias_registrados"] = $diasRegistrados;
            $e["asistencia_pct"] = $diasRegistrados > 0
                ? self::porcentaje((int)$e["presentes"] + (int)$e["tardes"], $diasRegistrados)
                : null;
        }
        unset($e);

        return $empleados;
    }

    /* =====================================================================
     *  MATERIALES
     * ===================================================================== */

    public function materiales()
    {
        $sql = "
            SELECT
                m.id_material, m.nombre_material, m.descripcion, m.stock,
                m.stock_minimo, m.unidad_medida, m.estado,

                (SELECT COALESCE(SUM(mm.cantidad), 0) FROM movimiento_material mm
                    WHERE mm.id_material = m.id_material AND mm.tipo = 'INGRESO') AS total_ingresos,
                (SELECT COALESCE(SUM(mm.cantidad), 0) FROM movimiento_material mm
                    WHERE mm.id_material = m.id_material AND mm.tipo = 'EGRESO') AS total_egresos,
                (SELECT COUNT(*) FROM movimiento_material mm
                    WHERE mm.id_material = m.id_material) AS cantidad_movimientos,
                (SELECT MAX(mm.fecha) FROM movimiento_material mm
                    WHERE mm.id_material = m.id_material) AS ultimo_movimiento,

                (SELECT COALESCE(SUM(d.cantidad_entregada), 0) FROM detalle_entrega_material d
                    WHERE d.id_material = m.id_material) AS total_entregado,

                (SELECT COALESCE(SUM(ds.cantidad), 0) FROM detalle_solicitud_material ds
                    INNER JOIN solicitud_material s ON s.id_solicitud = ds.id_solicitud
                    WHERE ds.id_material = m.id_material
                    AND s.estado IN ('Pendiente', 'Aprobada')) AS cantidad_pedida_pendiente
            FROM material m
            ORDER BY m.nombre_material ASC
        ";

        $materiales = $this->consultar($sql);

        foreach ($materiales as &$m) {
            $stock = (float)$m["stock"];
            $minimo = (float)$m["stock_minimo"];

            if ($stock <= 0) {
                $m["estado_stock"] = "Sin stock";
            } elseif ($stock <= $minimo) {
                $m["estado_stock"] = "Crítico";
            } else {
                $m["estado_stock"] = "Normal";
            }

            // Si se entrega todo lo pedido pendiente, ¿alcanza el stock?
            $m["alcanza_pedidos"] = $stock >= (float)$m["cantidad_pedida_pendiente"];
        }
        unset($m);

        return $materiales;
    }

    /** Últimos movimientos de stock (ingresos y egresos). */
    public function movimientosMateriales($limite = 300)
    {
        $limite = max(1, (int)$limite);

        $sql = "
            SELECT
                mm.id_movimiento, mm.fecha, mm.tipo, mm.cantidad, mm.observacion,
                m.nombre_material, m.unidad_medida,
                u.nombre AS usuario_nombre, u.apellido AS usuario_apellido,
                o.nombre_obra
            FROM movimiento_material mm
            INNER JOIN material m ON m.id_material = mm.id_material
            LEFT JOIN usuario u ON u.id_usuario = mm.id_usuario
            LEFT JOIN obra o ON o.id_obra = mm.id_obra
            ORDER BY mm.fecha DESC, mm.id_movimiento DESC
            LIMIT $limite
        ";

        $filas = $this->consultar($sql);

        foreach ($filas as &$f) {
            $f["usuario"] = self::nombreCompleto($f["usuario_nombre"], $f["usuario_apellido"], "-");
        }
        unset($f);

        return $filas;
    }

    /* =====================================================================
     *  HERRAMIENTAS
     * ===================================================================== */

    public function herramientas()
    {
        $sql = "
            SELECT
                h.id_herramienta, h.nombre, h.tipo, h.marca, h.modelo,
                h.cantidad_total, h.fecha_adquisicion, h.costo,

                (SELECT COUNT(*) FROM unidad_herramienta uh
                    WHERE uh.id_herramienta = h.id_herramienta) AS unidades,
                (SELECT COUNT(*) FROM unidad_herramienta uh
                    INNER JOIN estado_herramienta eh ON eh.id_estado_herramienta = uh.id_estado_herramienta
                    WHERE uh.id_herramienta = h.id_herramienta AND eh.nombre = 'Disponible') AS disponibles,
                (SELECT COUNT(*) FROM unidad_herramienta uh
                    INNER JOIN estado_herramienta eh ON eh.id_estado_herramienta = uh.id_estado_herramienta
                    WHERE uh.id_herramienta = h.id_herramienta AND eh.nombre = 'Asignada') AS asignadas,
                (SELECT COUNT(*) FROM unidad_herramienta uh
                    INNER JOIN estado_herramienta eh ON eh.id_estado_herramienta = uh.id_estado_herramienta
                    WHERE uh.id_herramienta = h.id_herramienta AND eh.nombre = 'En reparación') AS en_reparacion,
                (SELECT COUNT(*) FROM unidad_herramienta uh
                    INNER JOIN estado_herramienta eh ON eh.id_estado_herramienta = uh.id_estado_herramienta
                    WHERE uh.id_herramienta = h.id_herramienta AND eh.nombre = 'Fuera de servicio') AS fuera_servicio,

                (SELECT COUNT(DISTINCT ho.id_obra) FROM herramienta_obra ho
                    WHERE ho.id_herramienta = h.id_herramienta
                    AND ho.cantidad_asignada > ho.cantidad_devuelta) AS obras_actuales,
                (SELECT GROUP_CONCAT(DISTINCT o.nombre_obra) FROM herramienta_obra ho
                    INNER JOIN obra o ON o.id_obra = ho.id_obra
                    WHERE ho.id_herramienta = h.id_herramienta
                    AND ho.cantidad_asignada > ho.cantidad_devuelta) AS obras,
                (SELECT COUNT(*) FROM herramienta_obra ho
                    WHERE ho.id_herramienta = h.id_herramienta) AS veces_asignada,

                (SELECT COALESCE(SUM(dsh.cantidad), 0) FROM detalle_solicitud_herramienta dsh
                    INNER JOIN solicitud_herramienta sh ON sh.id_solicitud = dsh.id_solicitud
                    WHERE dsh.id_herramienta = h.id_herramienta
                    AND sh.estado = 'Pendiente') AS solicitadas_pendientes
            FROM herramienta h
            ORDER BY h.nombre ASC
        ";

        $herramientas = $this->consultar($sql);

        foreach ($herramientas as &$h) {
            $h["obras"] = self::lista($h["obras"]);
            $h["valor_total"] = (float)$h["cantidad_total"] * (float)$h["costo"];
            $h["uso_pct"] = self::porcentaje((int)$h["asignadas"], (int)$h["unidades"]);
        }
        unset($h);

        return $herramientas;
    }

    /* =====================================================================
     *  CLIENTES
     * ===================================================================== */

    public function clientes()
    {
        $activos = "'" . implode("','", self::ESTADOS_OBRA_ACTIVOS) . "'";

        $sql = "
            SELECT
                u.id_usuario, u.nombre, u.apellido, u.documento, u.correo,
                u.telefono, u.direccion, u.fecha_registro, u.estado,

                (SELECT COUNT(*) FROM obra o WHERE o.id_usuario = u.id_usuario AND o.activo = 1) AS total_obras,
                (SELECT COUNT(*) FROM obra o WHERE o.id_usuario = u.id_usuario AND o.activo = 1
                    AND o.estado IN ($activos)) AS obras_en_curso,
                (SELECT COUNT(*) FROM obra o WHERE o.id_usuario = u.id_usuario AND o.activo = 1
                    AND o.estado = 'Finalizada') AS obras_finalizadas,
                (SELECT GROUP_CONCAT(o.nombre_obra) FROM obra o
                    WHERE o.id_usuario = u.id_usuario AND o.activo = 1) AS obras,
                (SELECT MIN(o.fecha_inicio) FROM obra o WHERE o.id_usuario = u.id_usuario
                    AND o.activo = 1 AND o.fecha_inicio > '1000-01-01') AS primera_obra,

                (SELECT COALESCE(SUM(p.costo_total), 0) FROM presupuesto p
                    INNER JOIN obra o ON o.id_obra = p.id_obra
                    WHERE o.id_usuario = u.id_usuario AND o.activo = 1
                    AND p.id_presupuesto = (
                        SELECT p2.id_presupuesto FROM presupuesto p2
                        WHERE p2.id_obra = p.id_obra
                        ORDER BY p2.version DESC, p2.id_presupuesto DESC
                        LIMIT 1
                    )) AS total_presupuestado,

                (SELECT COUNT(*) FROM incidencia i
                    INNER JOIN obra o ON o.id_obra = i.id_obra
                    WHERE o.id_usuario = u.id_usuario AND o.activo = 1
                    AND (i.estado <> 'Resuelta' OR i.estado IS NULL)) AS incidencias_abiertas
            FROM usuario u
            WHERE u.id_rol = 6
            ORDER BY u.apellido ASC, u.nombre ASC
        ";

        $clientes = $this->consultar($sql);

        foreach ($clientes as &$c) {
            $c["cliente"] = self::nombreCompleto($c["nombre"], $c["apellido"], "-");
            $c["obras"] = self::lista($c["obras"]);
        }
        unset($c);

        return $clientes;
    }

    /* =====================================================================
     *  USUARIOS
     * ===================================================================== */

    public function usuarios()
    {
        $sql = "
            SELECT
                u.id_usuario, u.nombre, u.apellido, u.documento, u.correo,
                u.telefono, u.fecha_registro, u.estado, r.nombre_rol,

                (SELECT COUNT(*) FROM acceso_usuario ac WHERE ac.id_usuario = u.id_usuario) AS cantidad_accesos,
                (SELECT MAX(ac.fecha_hora_ingreso) FROM acceso_usuario ac
                    WHERE ac.id_usuario = u.id_usuario) AS ultimo_acceso,
                (SELECT COUNT(*) FROM auditoria au WHERE au.id_usuario = u.id_usuario) AS cantidad_acciones,
                (SELECT MAX(au.fecha) FROM auditoria au WHERE au.id_usuario = u.id_usuario) AS ultima_accion
            FROM usuario u
            INNER JOIN roles r ON r.id_rol = u.id_rol
            ORDER BY r.nombre_rol ASC, u.apellido ASC, u.nombre ASC
        ";

        $usuarios = $this->consultar($sql);

        foreach ($usuarios as &$u) {
            $u["usuario"] = self::nombreCompleto($u["nombre"], $u["apellido"], "-");
        }
        unset($u);

        return $usuarios;
    }

    /* =====================================================================
     *  ASIGNACIONES (personal, herramientas y materiales en cada obra)
     * ===================================================================== */

    public function asignacionesPersonal()
    {
        $sql = "
            SELECT
                eo.id_empleado_obra, eo.fecha_ingreso, eo.fecha_egreso,
                eo.motivo_egreso, eo.estado,
                o.nombre_obra, o.estado AS estado_obra,
                u.nombre, u.apellido, u.documento,
                c.nombre_cargo
            FROM empleado_obra eo
            INNER JOIN obra o ON o.id_obra = eo.id_obra
            INNER JOIN usuario u ON u.id_usuario = eo.id_usuario
            LEFT JOIN cargo c ON c.id_cargo = eo.id_cargo
            WHERE o.activo = 1
            ORDER BY o.nombre_obra ASC, eo.estado DESC, u.apellido ASC
        ";

        $filas = $this->consultar($sql);

        foreach ($filas as &$f) {
            $f["empleado"] = self::nombreCompleto($f["nombre"], $f["apellido"], "-");
            $f["nombre_cargo"] = $f["nombre_cargo"] ?: "Sin cargo";
        }
        unset($f);

        return $filas;
    }

    public function asignacionesHerramientas()
    {
        $sql = "
            SELECT
                ho.id_herramienta_obra, ho.cantidad_asignada, ho.cantidad_devuelta,
                ho.fecha_asignacion, ho.fecha_devolucion, ho.observaciones,
                h.nombre AS herramienta, h.marca, h.costo,
                o.nombre_obra,
                eh.nombre AS estado
            FROM herramienta_obra ho
            INNER JOIN herramienta h ON h.id_herramienta = ho.id_herramienta
            INNER JOIN obra o ON o.id_obra = ho.id_obra
            LEFT JOIN estado_herramienta eh ON eh.id_estado_herramienta = ho.id_estado_herramienta
            WHERE o.activo = 1
            ORDER BY ho.fecha_asignacion DESC, o.nombre_obra ASC
        ";

        $filas = $this->consultar($sql);

        foreach ($filas as &$f) {
            $f["pendiente"] = max(0, (int)$f["cantidad_asignada"] - (int)$f["cantidad_devuelta"]);
            $f["devuelta_total"] = $f["pendiente"] === 0;
        }
        unset($f);

        return $filas;
    }

    public function asignacionesMateriales()
    {
        $sql = "
            SELECT
                en.id_entrega, en.fecha, s.id_solicitud,
                o.nombre_obra,
                m.nombre_material, m.unidad_medida,
                d.cantidad_entregada,
                u.nombre AS entrego_nombre, u.apellido AS entrego_apellido
            FROM detalle_entrega_material d
            INNER JOIN entrega_material en ON en.id_entrega = d.id_entrega
            INNER JOIN solicitud_material s ON s.id_solicitud = en.id_solicitud
            INNER JOIN obra o ON o.id_obra = s.id_obra
            INNER JOIN material m ON m.id_material = d.id_material
            LEFT JOIN usuario u ON u.id_usuario = en.id_usuario
            ORDER BY en.fecha DESC, o.nombre_obra ASC
        ";

        $filas = $this->consultar($sql);

        foreach ($filas as &$f) {
            $f["entrego"] = self::nombreCompleto($f["entrego_nombre"], $f["entrego_apellido"], "-");
        }
        unset($f);

        return $filas;
    }

    /* =====================================================================
     *  INCIDENCIAS
     * ===================================================================== */

    public function incidencias()
    {
        $sql = "
            SELECT
                i.id_incidencia, i.fecha, i.tipo_incidencia, i.descripcion,
                i.estado, i.solucion,
                o.nombre_obra,
                (SELECT COUNT(*) FROM foto_incidencia f WHERE f.id_incidencia = i.id_incidencia) AS fotos
            FROM incidencia i
            INNER JOIN obra o ON o.id_obra = i.id_obra
            ORDER BY i.fecha DESC, i.id_incidencia DESC
        ";

        $filas = $this->consultar($sql);

        foreach ($filas as &$f) {
            $f["estado"] = $f["estado"] ?: "Pendiente";
            $f["tipo_incidencia"] = $f["tipo_incidencia"] ?: "Sin tipo";
        }
        unset($f);

        return $filas;
    }

    /* =====================================================================
     *  ASISTENCIA
     * ===================================================================== */

    public function asistencia($desde = "", $hasta = "")
    {
        $sql = "
            SELECT
                a.id_asistencia, a.fecha, a.estado, a.hora_entrada, a.hora_salida,
                a.observacion,
                u.nombre, u.apellido, u.documento,
                o.nombre_obra
            FROM asistencia a
            INNER JOIN usuario u ON u.id_usuario = a.id_usuario
            LEFT JOIN obra o ON o.id_obra = a.id_obra
            WHERE 1 = 1
        ";

        $parametros = [];

        if ($desde !== "") {
            $sql .= " AND a.fecha >= :desde";
            $parametros[":desde"] = $desde;
        }
        if ($hasta !== "") {
            $sql .= " AND a.fecha <= :hasta";
            $parametros[":hasta"] = $hasta;
        }

        $sql .= " ORDER BY a.fecha DESC, u.apellido ASC";

        $filas = $this->consultar($sql, $parametros);

        foreach ($filas as &$f) {
            $f["empleado"] = self::nombreCompleto($f["nombre"], $f["apellido"], "-");
            $f["horas"] = self::horasTrabajadas($f["hora_entrada"], $f["hora_salida"]);
        }
        unset($f);

        return $filas;
    }

    private static function horasTrabajadas($entrada, $salida)
    {
        if (empty($entrada) || empty($salida)) {
            return null;
        }
        $inicio = strtotime("2000-01-01 " . $entrada);
        $fin = strtotime("2000-01-01 " . $salida);
        if ($inicio === false || $fin === false || $fin <= $inicio) {
            return null;
        }
        return round(($fin - $inicio) / 3600, 2);
    }

    /* =====================================================================
     *  EXPORTACIÓN (PDF / Excel)
     *
     *  Cada reporte devuelve:
     *   - titulo:   nombre del reporte
     *   - columnas: encabezados
     *   - filas:    lista de filas (cada fila, lista de textos)
     *   - estados:  valor de "estado" de cada fila, para poder filtrar
     * ===================================================================== */

    public static function tiposExportables()
    {
        return [
            "obras" => "Reporte de Obras",
            "empleados" => "Reporte de Empleados",
            "materiales" => "Reporte de Materiales",
            "movimientos" => "Movimientos de Materiales",
            "herramientas" => "Reporte de Herramientas",
            "clientes" => "Reporte de Clientes",
            "usuarios" => "Reporte de Usuarios",
            "asignaciones_personal" => "Asignaciones de Personal",
            "asignaciones_herramientas" => "Asignaciones de Herramientas",
            "asignaciones_materiales" => "Materiales Entregados a Obras",
            "incidencias" => "Reporte de Incidencias",
            "asistencia" => "Reporte de Asistencia",
            "auditoria" => "Reporte de Auditoría",
        ];
    }

    public function datosExportacion($tipo, $filtros = [])
    {
        $titulos = self::tiposExportables();

        if (!isset($titulos[$tipo])) {
            throw new InvalidArgumentException("Tipo de reporte no válido.");
        }

        $columnas = [];
        $filas = [];
        $estados = [];

        switch ($tipo) {

            case "obras":
                $columnas = ["Obra", "Cliente", "Jefe de obra", "Capataz", "Dirección", "Inicio", "Fin",
                    "Estado", "Avance", "Etapas", "Empleados", "Incidencias abiertas",
                    "Herramientas en obra", "Presupuesto"];
                foreach ($this->obras() as $o) {
                    $filas[] = [$o["nombre_obra"], $o["cliente"], $o["jefe_obra"], $o["capataz"],
                        $o["direccion"], self::fecha($o["fecha_inicio"]), self::fecha($o["fecha_fin"]),
                        $o["estado"], $o["avance"] . "%",
                        $o["etapas_finalizadas"] . " de " . $o["total_etapas"],
                        $o["empleados_activos"], $o["incidencias_abiertas"], $o["herramientas_en_obra"],
                        $o["presupuesto"] === null ? "Sin presupuesto" : self::moneda($o["presupuesto"])];
                    $estados[] = $o["estado"];
                }
                break;

            case "empleados":
                $columnas = ["Empleado", "Documento", "Correo", "Teléfono", "Cargo", "Obras actuales",
                    "Salario", "Presentes", "Tardes", "Ausentes", "Tareas", "Estado"];
                foreach ($this->empleados() as $e) {
                    $filas[] = [$e["empleado"], $e["documento"] ?: "-", $e["correo"], $e["telefono"] ?: "-",
                        $e["nombre_cargo"], $e["obras"] ?: "Sin obra",
                        $e["salario"] !== null && $e["salario"] !== "" ? self::moneda($e["salario"]) : "-",
                        $e["presentes"], $e["tardes"], $e["ausentes"],
                        $e["tareas_completadas"] . " de " . $e["tareas_asignadas"],
                        (int)$e["estado"] === 1 ? "Activo" : "Inactivo"];
                    $estados[] = (int)$e["estado"] === 1 ? "Activo" : "Inactivo";
                }
                break;

            case "materiales":
                $columnas = ["Material", "Unidad", "Stock", "Stock mínimo", "Estado de stock",
                    "Ingresos", "Egresos", "Entregado a obras", "Pedido pendiente",
                    "Último movimiento", "Estado"];
                foreach ($this->materiales() as $m) {
                    $filas[] = [$m["nombre_material"], $m["unidad_medida"], self::numero($m["stock"]),
                        self::numero($m["stock_minimo"]), $m["estado_stock"],
                        self::numero($m["total_ingresos"]), self::numero($m["total_egresos"]),
                        self::numero($m["total_entregado"]), self::numero($m["cantidad_pedida_pendiente"]),
                        self::fecha($m["ultimo_movimiento"], true),
                        (int)$m["estado"] === 1 ? "Activo" : "Inactivo"];
                    $estados[] = $m["estado_stock"];
                }
                break;

            case "movimientos":
                $columnas = ["Fecha", "Material", "Tipo", "Cantidad", "Unidad", "Obra", "Usuario", "Observación"];
                foreach ($this->movimientosMateriales(1000) as $mv) {
                    $filas[] = [self::fecha($mv["fecha"], true), $mv["nombre_material"], $mv["tipo"],
                        self::numero($mv["cantidad"]), $mv["unidad_medida"], $mv["nombre_obra"] ?: "-",
                        $mv["usuario"], $mv["observacion"] ?: "-"];
                    $estados[] = $mv["tipo"];
                }
                break;

            case "herramientas":
                $columnas = ["Herramienta", "Tipo", "Marca", "Modelo", "Unidades", "Disponibles",
                    "Asignadas", "En reparación", "Fuera de servicio", "Obras actuales",
                    "Costo unitario", "Valor total"];
                foreach ($this->herramientas() as $h) {
                    $filas[] = [$h["nombre"], $h["tipo"] ?: "-", $h["marca"] ?: "-", $h["modelo"] ?: "-",
                        $h["unidades"], $h["disponibles"], $h["asignadas"], $h["en_reparacion"],
                        $h["fuera_servicio"], $h["obras"] ?: "-", self::moneda($h["costo"]),
                        self::moneda($h["valor_total"])];
                    $estados[] = (int)$h["asignadas"] > 0 ? "Con asignaciones" : "Sin asignaciones";
                }
                break;

            case "clientes":
                $columnas = ["Cliente", "Documento", "Correo", "Teléfono", "Obras", "En curso",
                    "Finalizadas", "Nombres de las obras", "Total presupuestado", "Incidencias abiertas", "Estado"];
                foreach ($this->clientes() as $c) {
                    $filas[] = [$c["cliente"], $c["documento"] ?: "-", $c["correo"], $c["telefono"] ?: "-",
                        $c["total_obras"], $c["obras_en_curso"], $c["obras_finalizadas"],
                        $c["obras"] ?: "-", self::moneda($c["total_presupuestado"]),
                        $c["incidencias_abiertas"], (int)$c["estado"] === 1 ? "Activo" : "Inactivo"];
                    $estados[] = (int)$c["estado"] === 1 ? "Activo" : "Inactivo";
                }
                break;

            case "usuarios":
                $columnas = ["Usuario", "Documento", "Correo", "Rol", "Fecha de alta", "Accesos",
                    "Último acceso", "Acciones registradas", "Estado"];
                foreach ($this->usuarios() as $u) {
                    $filas[] = [$u["usuario"], $u["documento"] ?: "-", $u["correo"], $u["nombre_rol"],
                        self::fecha($u["fecha_registro"]), $u["cantidad_accesos"],
                        self::fecha($u["ultimo_acceso"], true), $u["cantidad_acciones"],
                        (int)$u["estado"] === 1 ? "Activo" : "Inactivo"];
                    $estados[] = (int)$u["estado"] === 1 ? "Activo" : "Inactivo";
                }
                break;

            case "asignaciones_personal":
                $columnas = ["Obra", "Empleado", "Documento", "Cargo", "Ingreso", "Egreso", "Motivo de egreso", "Estado"];
                foreach ($this->asignacionesPersonal() as $a) {
                    $filas[] = [$a["nombre_obra"], $a["empleado"], $a["documento"] ?: "-", $a["nombre_cargo"],
                        self::fecha($a["fecha_ingreso"]), self::fecha($a["fecha_egreso"]),
                        $a["motivo_egreso"] ?: "-", (int)$a["estado"] === 1 ? "Activo" : "Retirado"];
                    $estados[] = (int)$a["estado"] === 1 ? "Activo" : "Retirado";
                }
                break;

            case "asignaciones_herramientas":
                $columnas = ["Obra", "Herramienta", "Marca", "Asignadas", "Devueltas", "Pendientes",
                    "Fecha de asignación", "Fecha de devolución", "Estado"];
                foreach ($this->asignacionesHerramientas() as $a) {
                    $estado = $a["devuelta_total"] ? "Devuelta" : "En obra";
                    $filas[] = [$a["nombre_obra"], $a["herramienta"], $a["marca"] ?: "-",
                        $a["cantidad_asignada"], $a["cantidad_devuelta"], $a["pendiente"],
                        self::fecha($a["fecha_asignacion"]), self::fecha($a["fecha_devolucion"]), $estado];
                    $estados[] = $estado;
                }
                break;

            case "asignaciones_materiales":
                $columnas = ["Fecha", "Obra", "Solicitud", "Material", "Cantidad", "Unidad", "Entregó"];
                foreach ($this->asignacionesMateriales() as $a) {
                    $filas[] = [self::fecha($a["fecha"], true), $a["nombre_obra"], "#" . $a["id_solicitud"],
                        $a["nombre_material"], self::numero($a["cantidad_entregada"]), $a["unidad_medida"],
                        $a["entrego"]];
                    $estados[] = "";
                }
                break;

            case "incidencias":
                $columnas = ["Fecha", "Obra", "Tipo", "Descripción", "Solución", "Fotos", "Estado"];
                foreach ($this->incidencias() as $i) {
                    $filas[] = [self::fecha($i["fecha"]), $i["nombre_obra"], $i["tipo_incidencia"],
                        $i["descripcion"] ?: "-", $i["solucion"] ?: "-", $i["fotos"], $i["estado"]];
                    $estados[] = $i["estado"];
                }
                break;

            case "asistencia":
                $columnas = ["Fecha", "Empleado", "Documento", "Obra", "Entrada", "Salida", "Horas", "Observación", "Estado"];
                $desde = $filtros["desde"] ?? "";
                $hasta = $filtros["hasta"] ?? "";
                foreach ($this->asistencia($desde, $hasta) as $a) {
                    $filas[] = [self::fecha($a["fecha"]), $a["empleado"], $a["documento"] ?: "-",
                        $a["nombre_obra"] ?: "-", $a["hora_entrada"] ? substr($a["hora_entrada"], 0, 5) : "-",
                        $a["hora_salida"] ? substr($a["hora_salida"], 0, 5) : "-",
                        $a["horas"] === null ? "-" : self::numero($a["horas"]),
                        $a["observacion"] ?: "-", $a["estado"]];
                    $estados[] = $a["estado"];
                }
                break;

            case "auditoria":
                require_once __DIR__ . "/Auditoria.php";
                $auditoria = new Auditoria();
                $columnas = ["Fecha", "Usuario", "Tipo", "Acción", "Módulo", "Registro", "Descripción", "Salida"];
                foreach ($auditoria->obtenerReporte($filtros) as $r) {
                    $filas[] = [self::fecha($r["fecha_hora"], true), $r["usuario"], $r["tipo"], $r["accion"],
                        $r["modulo"] ?: "-", $r["id_registro"] ?: "-", $r["descripcion"] ?: "-",
                        self::fecha($r["fecha_egreso"], true)];
                    $estados[] = $r["tipo"];
                }
                break;
        }

        // Filtros que llegan desde la pantalla (buscador y selector de estado)
        $buscar = mb_strtolower(trim($filtros["buscar"] ?? ""), "UTF-8");
        $estado = trim($filtros["estado"] ?? "");

        if ($buscar !== "" || $estado !== "") {
            $filtradas = [];
            foreach ($filas as $i => $fila) {
                if ($estado !== "" && $estados[$i] !== $estado) {
                    continue;
                }
                if ($buscar !== "" && mb_strpos(mb_strtolower(implode(" ", $fila), "UTF-8"), $buscar) === false) {
                    continue;
                }
                $filtradas[] = $fila;
            }
            $filas = $filtradas;
        }

        // Todo como texto, para que PDF y Excel muestren lo mismo que la pantalla
        $filas = array_map(fn($fila) => array_map(fn($v) => $v === null ? "-" : (string)$v, $fila), $filas);

        return [
            "titulo" => $titulos[$tipo],
            "columnas" => $columnas,
            "filas" => $filas,
        ];
    }
}