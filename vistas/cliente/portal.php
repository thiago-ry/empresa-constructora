<?php
require_once __DIR__ . "/../../layouts/header.php";

function escapar($valor)
{
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

function fechaPortal($fecha)
{
    if (empty($fecha)) {
        return "No especificada";
    }

    return date("d/m/Y", strtotime($fecha));
}

function dineroPortal($valor)
{
    return "$ " . number_format((float)$valor, 2, ",", ".");
}

$accionActual = $accion ?? "dashboard";
$obra = $obraActual ?? null;
$idObraActual = (int)($obra["id_obra"] ?? 0);

$nombreObra = $obra["nombre_obra"] ?? "Mi obra";
$estadoObra = $obra["estado"] ?? "Sin estado";

$estados = [
    "Planificacion" => "Planificación",
    "En Proceso" => "En proceso",
    "Finalizada" => "Finalizada",
    "Suspendida" => "Suspendida",
    "Cancelada" => "Cancelada"
];

$estadoMostrar = $estados[$estadoObra] ?? $estadoObra;

$porcentajeAvance = (int)($resumenEtapas["porcentaje_avance"] ?? 0);
$porcentajeAvance = max(0, min(100, $porcentajeAvance));

$acciones = [
    "dashboard" => ["Inicio", "fa-house"],
    "obra" => ["Mi obra", "fa-building"],
    "avances" => ["Avances diarios", "fa-chart-line"],
    "etapas" => ["Etapas", "fa-list-check"],
    "fotos" => ["Fotografías", "fa-camera"],
    "documentos" => ["Documentación", "fa-folder-open"],
    "equipo" => ["Equipo de trabajo", "fa-users"],
    "presupuesto" => ["Presupuesto", "fa-file-invoice-dollar"]
];

$baseUrl = $rutaCliente ?? "../controladores/PortalClienteController.php";

function urlPortal($base, $accion, $idObra)
{
    return $base . "?accion=" . urlencode($accion) .
        "&id_obra=" . (int)$idObra;
}
?>

<main class="portal-cliente">

    <div class="portal-encabezado">
        <div>
            <span class="portal-etiqueta">PORTAL DEL CLIENTE</span>
            <h1>
                <?php
                echo escapar($acciones[$accionActual][0] ?? "Mi portal");
                ?>
            </h1>
            <p>Consultá el estado y el progreso de tu obra.</p>
        </div>

        <?php if ($obra): ?>
            <div class="portal-estado">
                <span class="estado-punto"></span>
                <?php echo escapar($estadoMostrar); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$obra): ?>

        <div class="portal-vacio">
            <i class="fa-solid fa-building-circle-exclamation"></i>
            <h2>No tenés obras registradas</h2>
            <p>Cuando se te asigne una obra, vas a poder consultar toda su información desde este portal.</p>
        </div>

    <?php else: ?>

        <div class="portal-obra-resumen">
            <div>
                <span class="portal-etiqueta">OBRA ASIGNADA</span>
                <h2><?php echo escapar($nombreObra); ?></h2>
                <p>
                    <i class="fa-solid fa-location-dot"></i>
                    <?php echo escapar($obra["direccion"] ?? "Dirección no especificada"); ?>
                </p>
            </div>

            <div class="portal-progreso">
                <div class="progreso-texto">
                    <span>Progreso de etapas</span>
                    <strong><?php echo $porcentajeAvance; ?>%</strong>
                </div>
                <div class="progreso-barra">
                    <div style="width: <?php echo $porcentajeAvance; ?>%"></div>
                </div>
            </div>
        </div>

        <nav class="portal-navegacion">
            <?php foreach ($acciones as $clave => $datos): ?>
                <a
                    href="<?php echo escapar(urlPortal($baseUrl, $clave, $idObraActual)); ?>"
                    class="<?php echo $accionActual === $clave ? "activo" : ""; ?>">
                    <i class="fa-solid <?php echo escapar($datos[1]); ?>"></i>
                    <span><?php echo escapar($datos[0]); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <section class="portal-contenido">

            <?php if ($accionActual === "dashboard"): ?>

                <div class="portal-seccion-titulo">
                    <h2>Resumen de tu obra</h2>
                    <p>Una vista general del estado actual de la construcción.</p>
                </div>

                <div class="portal-estadisticas">

                    <div class="portal-card">
                        <div class="card-icono"><i class="fa-solid fa-building"></i></div>
                        <span>Estado de la obra</span>
                        <strong><?php echo escapar($estadoMostrar); ?></strong>
                    </div>

                    <div class="portal-card">
                        <div class="card-icono"><i class="fa-solid fa-chart-pie"></i></div>
                        <span>Progreso</span>
                        <strong><?php echo $porcentajeAvance; ?>%</strong>
                    </div>

                    <div class="portal-card">
                        <div class="card-icono"><i class="fa-solid fa-list-check"></i></div>
                        <span>Etapas registradas</span>
                        <strong><?php echo count($etapas ?? []); ?></strong>
                    </div>

                    <div class="portal-card">
                        <div class="card-icono"><i class="fa-solid fa-camera"></i></div>
                        <span>Fotografías</span>
                        <strong><?php echo count($fotografias ?? []); ?></strong>
                    </div>

                </div>

                <div class="portal-dos-columnas">

                    <div class="portal-panel">
                        <div class="portal-panel-titulo">
                            <h3>Información general</h3>
                            <a href="<?php echo escapar(urlPortal($baseUrl, "obra", $idObraActual)); ?>">Ver detalles</a>
                        </div>

                        <div class="portal-dato">
                            <span>Nombre</span>
                            <strong><?php echo escapar($nombreObra); ?></strong>
                        </div>

                        <div class="portal-dato">
                            <span>Dirección</span>
                            <strong><?php echo escapar($obra["direccion"] ?? "No especificada"); ?></strong>
                        </div>

                        <div class="portal-dato">
                            <span>Fecha de inicio</span>
                            <strong><?php echo fechaPortal($obra["fecha_inicio"] ?? null); ?></strong>
                        </div>

                        <div class="portal-dato">
                            <span>Fecha estimada de finalización</span>
                            <strong><?php echo fechaPortal($obra["fecha_fin"] ?? null); ?></strong>
                        </div>
                    </div>

                    <div class="portal-panel">
                        <div class="portal-panel-titulo">
                            <h3>Últimos avances</h3>
                            <a href="<?php echo escapar(urlPortal($baseUrl, "avances", $idObraActual)); ?>">Ver todos</a>
                        </div>

                        <?php if (!empty($avances)): ?>
                            <?php foreach (array_slice($avances, 0, 3) as $avance): ?>
                                <div class="portal-lista-item">
                                    <div class="lista-punto"></div>
                                    <div>
                                        <strong><?php echo escapar($avance["descripcion"]); ?></strong>
                                        <small><?php echo fechaPortal($avance["fecha"] ?? null); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="portal-sin-datos">Todavía no hay avances registrados.</p>
                        <?php endif; ?>
                    </div>

                </div>

            <?php elseif ($accionActual === "obra"): ?>

                <div class="portal-seccion-titulo">
                    <h2>Información de la obra</h2>
                    <p>Datos generales de tu proyecto de construcción.</p>
                </div>

                <div class="portal-panel">
                    <div class="portal-dato">
                        <span>Nombre de la obra</span>
                        <strong><?php echo escapar($nombreObra); ?></strong>
                    </div>

                    <div class="portal-dato">
                        <span>Dirección</span>
                        <strong><?php echo escapar($obra["direccion"] ?? "No especificada"); ?></strong>
                    </div>

                    <div class="portal-dato">
                        <span>Descripción</span>
                        <strong><?php echo escapar($obra["descripcion"] ?? "Sin descripción"); ?></strong>
                    </div>

                    <div class="portal-dato">
                        <span>Estado</span>
                        <strong><?php echo escapar($estadoMostrar); ?></strong>
                    </div>

                    <div class="portal-dato">
                        <span>Fecha de inicio</span>
                        <strong><?php echo fechaPortal($obra["fecha_inicio"] ?? null); ?></strong>
                    </div>

                    <div class="portal-dato">
                        <span>Fecha de finalización</span>
                        <strong><?php echo fechaPortal($obra["fecha_fin"] ?? null); ?></strong>
                    </div>
                </div>

            <?php elseif ($accionActual === "avances"): ?>

                <div class="portal-seccion-titulo">
                    <h2>Avances diarios</h2>
                    <p>Registro de las actividades realizadas en tu obra.</p>
                </div>

                <div class="portal-panel">
                    <?php if (!empty($avances)): ?>
                        <?php foreach ($avances as $avance): ?>
                            <div class="portal-lista-item">
                                <div class="lista-punto"></div>
                                <div>
                                    <strong><?php echo escapar($avance["descripcion"]); ?></strong>
                                    <small><?php echo fechaPortal($avance["fecha"] ?? null); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="portal-sin-datos">No hay avances diarios registrados.</p>
                    <?php endif; ?>
                </div>

            <?php elseif ($accionActual === "etapas"): ?>

                <div class="portal-seccion-titulo">
                    <h2>Etapas de construcción</h2>
                    <p>Consultá el estado de cada etapa de tu obra.</p>
                </div>

                <div class="portal-panel">
                    <?php if (!empty($etapas)): ?>
                        <?php foreach ($etapas as $etapa): ?>
                            <?php
                            $estadoEtapa = $etapa["estado"] ?? "Pendiente";
                            $claseEstado = strtolower(str_replace(" ", "-", $estadoEtapa));
                            ?>
                            <div class="portal-etapa">
                                <div>
                                    <strong><?php echo escapar($etapa["nombre_etapa"]); ?></strong>
                                    <p><?php echo escapar($etapa["descripcion"] ?? "Sin descripción"); ?></p>
                                    <small>
                                        Inicio: <?php echo fechaPortal($etapa["fecha_inicio"] ?? null); ?>
                                        · Fin: <?php echo fechaPortal($etapa["fecha_fin"] ?? null); ?>
                                    </small>
                                </div>
                                <span class="etapa-estado <?php echo escapar($claseEstado); ?>">
                                    <?php echo escapar($estadoEtapa); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="portal-sin-datos">No hay etapas registradas.</p>
                    <?php endif; ?>
                </div>

            <?php elseif ($accionActual === "fotos"): ?>

                <div class="portal-seccion-titulo">
                    <h2>Fotografías de la obra</h2>
                    <p>Imágenes del desarrollo y progreso de la construcción.</p>
                </div>

                <?php if (!empty($fotografias)): ?>
                    <div class="portal-galeria">
                        <?php foreach ($fotografias as $foto): ?>
                            <div class="portal-foto">
                                <a href="<?php echo escapar($foto["ruta_imagen"]); ?>" target="_blank" rel="noopener">
                                    <img
                                        src="<?php echo escapar($foto["ruta_imagen"]); ?>"
                                        alt="Fotografía de la obra">
                                </a>
                                <div>
                                    <strong><?php echo escapar($foto["descripcion"] ?? "Fotografía de la obra"); ?></strong>
                                    <small><?php echo fechaPortal($foto["fecha"] ?? null); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="portal-panel">
                        <p class="portal-sin-datos">Todavía no se han publicado fotografías de esta obra.</p>
                    </div>
                <?php endif; ?>

            <?php elseif ($accionActual === "documentos"): ?>

                <div class="portal-seccion-titulo">
                    <h2>Documentación</h2>
                    <p>Documentos disponibles relacionados con tu obra.</p>
                </div>

                <div class="portal-panel">
                    <?php if (!empty($documentos)): ?>
                        <?php foreach ($documentos as $documento): ?>
                            <div class="portal-documento">
                                <div class="documento-icono">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div class="documento-info">
                                    <strong><?php echo escapar($documento["nombre_archivo"]); ?></strong>
                                    <small>
                                        <?php echo escapar($documento["tipo_documento"] ?? "Documento"); ?>
                                        · <?php echo fechaPortal($documento["fecha_subida"] ?? null); ?>
                                    </small>
                                </div>
                                <a href="<?php echo escapar($documento["ruta_archivo"]); ?>" target="_blank" rel="noopener">
                                    Consultar
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="portal-sin-datos">No hay documentos disponibles.</p>
                    <?php endif; ?>
                </div>

            <?php elseif ($accionActual === "equipo"): ?>

                <div class="portal-seccion-titulo">
                    <h2>Equipo de trabajo</h2>
                    <p>Personal responsable y trabajadores asignados a tu obra.</p>
                </div>

                <div class="portal-equipo">

                    <?php if (!empty($jefeObra)): ?>
                        <div class="portal-persona">
                            <div class="persona-icono"><i class="fa-solid fa-user-tie"></i></div>
                            <div>
                                <span>Jefe de obra</span>
                                <strong>
                                    <?php echo escapar(($jefeObra["nombre"] ?? "") . " " . ($jefeObra["apellido"] ?? "")); ?>
                                </strong>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($capataz)): ?>
                        <div class="portal-persona">
                            <div class="persona-icono"><i class="fa-solid fa-helmet-safety"></i></div>
                            <div>
                                <span>Capataz</span>
                                <strong>
                                    <?php echo escapar(($capataz["nombre"] ?? "") . " " . ($capataz["apellido"] ?? "")); ?>
                                </strong>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($empleados)): ?>
                        <?php foreach ($empleados as $empleado): ?>
                            <div class="portal-persona">
                                <div class="persona-icono"><i class="fa-solid fa-user"></i></div>
                                <div>
                                    <span><?php echo escapar($empleado["nombre_cargo"] ?? "Empleado"); ?></span>
                                    <strong>
                                        <?php echo escapar(($empleado["nombre"] ?? "") . " " . ($empleado["apellido"] ?? "")); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (empty($jefeObra) && empty($capataz) && empty($empleados)): ?>
                        <div class="portal-panel">
                            <p class="portal-sin-datos">No hay integrantes del equipo registrados.</p>
                        </div>
                    <?php endif; ?>

                </div>

            <?php elseif ($accionActual === "presupuesto"): ?>

                <div class="portal-seccion-titulo">
                    <h2>Presupuesto de la obra</h2>
                    <p>Consultá los presupuestos disponibles y sus conceptos.</p>
                </div>

                <?php if (!empty($presupuestos)): ?>

                    <?php if (count($presupuestos) > 1): ?>
                        <div class="portal-panel">
                            <label for="seleccionarPresupuesto">Seleccionar presupuesto</label>
                            <select
                                id="seleccionarPresupuesto"
                                onchange="window.location.href=this.value">
                                <?php foreach ($presupuestos as $p): ?>
                                    <option
                                        value="<?php echo escapar(urlPortal($baseUrl, "presupuesto", $idObraActual) . "&id_presupuesto=" . (int)$p["id_presupuesto"]); ?>"
                                        <?php echo ((int)$p["id_presupuesto"] === (int)($presupuestoSeleccionado["id_presupuesto"] ?? 0)) ? "selected" : ""; ?>>
                                        <?php echo escapar($p["nombre_presupuesto"]); ?>
                                        - Versión <?php echo escapar($p["version"]); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($presupuestoSeleccionado)): ?>

                        <div class="portal-presupuesto-total">
                            <span>
                                <?php echo escapar($presupuestoSeleccionado["nombre_presupuesto"]); ?>
                            </span>
                            <strong>
                                <?php echo dineroPortal($presupuestoSeleccionado["monto_cliente"] ?? 0); ?>
                            </strong>
                            <small>
                                Versión <?php echo escapar($presupuestoSeleccionado["version"]); ?>
                                · <?php echo fechaPortal($presupuestoSeleccionado["fecha"] ?? null); ?>
                            </small>
                        </div>

                        <div class="portal-panel">
                            <h3>Materiales y conceptos</h3>

                            <?php if (!empty($detallePresupuesto)): ?>
                                <div class="portal-tabla-contenedor">
                                    <table class="portal-tabla">
                                        <thead>
                                            <tr>
                                                <th>Concepto</th>
                                                <th>Unidad</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($detallePresupuesto as $detalle): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo escapar($detalle["nombre_rubro"] ?? $detalle["descripcion"] ?? "Concepto"); ?></strong>
                                                        <?php if (!empty($detalle["descripcion"])): ?>
                                                            <small><?php echo escapar($detalle["descripcion"]); ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo escapar($detalle["unidad_medida"] ?? "-"); ?></td>
                                                    <td><?php echo escapar($detalle["cantidad"] ?? "-"); ?></td>
                                                    <td><?php echo dineroPortal($detalle["subtotal"] ?? 0); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="portal-sin-datos">No hay conceptos de materiales registrados.</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($manoObraPresupuesto)): ?>
                            <div class="portal-panel">
                                <h3>Mano de obra presupuestada</h3>

                                <?php foreach ($manoObraPresupuesto as $trabajo): ?>
                                    <div class="portal-dato">
                                        <span>
                                            <?php echo escapar($trabajo["descripcion"]); ?>
                                            (<?php echo escapar($trabajo["cantidad_horas"]); ?> horas)
                                        </span>
                                        <strong><?php echo dineroPortal($trabajo["subtotal"] ?? 0); ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                <?php else: ?>
                    <div class="portal-panel">
                        <p class="portal-sin-datos">Todavía no hay presupuestos disponibles para consultar.</p>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</main>

<style>

/* =====================================================
   BUILDPRO | PORTAL DEL CLIENTE
   Diseño moderno, responsive y profesional
===================================================== */

.portal-cliente {
    --pc-bg: #0b1220;
    --pc-surface: #111c2c;
    --pc-surface-2: #172438;
    --pc-border: rgba(148, 163, 184, .13);
    --pc-text: #f1f5f9;
    --pc-muted: #91a0b5;
    --pc-accent: #f4b400;
    --pc-accent-soft: rgba(244, 180, 0, .12);
    --pc-green: #35c58a;
    --pc-blue: #55a8ff;
    --pc-red: #ef7272;

    width: 100%;
    min-height: 100vh;
    padding: 30px;
    color: var(--pc-text);
    background: var(--pc-bg);
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
    margin-top: 5%;
}

.portal-cliente *,
.portal-cliente *::before,
.portal-cliente *::after {
    box-sizing: border-box;
}

/* ENCABEZADO */

.portal-encabezado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 28px;
}

.portal-encabezado h1 {
    margin: 8px 0;
    font-size: clamp(23px, 3vw, 31px);
    font-weight: 700;
    letter-spacing: -.8px;
}

.portal-encabezado p,
.portal-seccion-titulo p {
    margin: 0;
    color: var(--pc-muted);
    font-size: 13px;
    line-height: 1.7;
}

.portal-etiqueta {
    color: var(--pc-accent);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.portal-estado {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 10px 15px;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-radius: 30px;
    color: #dce5f0;
    font-size: 12px;
    font-weight: 500;
}

.estado-punto {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--pc-green);
    box-shadow: 0 0 10px rgba(53, 197, 138, .5);
}

/* TARJETA PRINCIPAL DE LA OBRA */

.portal-obra-resumen {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 35px;
    min-height: 185px;
    padding: 30px;
    margin-bottom: 22px;
    overflow: hidden;
    background:
        radial-gradient(circle at 85% 20%, rgba(244, 180, 0, .13), transparent 35%),
        linear-gradient(120deg, #17263a, #101b2b);
    border: 1px solid rgba(244, 180, 0, .16);
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, .12);
}

.portal-obra-resumen::after {
    content: "";
    position: absolute;
    right: -65px;
    bottom: -110px;
    width: 250px;
    height: 250px;
    border: 1px solid rgba(244, 180, 0, .12);
    border-radius: 50%;
    pointer-events: none;
}

.portal-obra-resumen h2 {
    margin: 10px 0;
    font-size: clamp(20px, 3vw, 27px);
    font-weight: 700;
}

.portal-obra-resumen p {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0;
    color: #aab8ca;
    font-size: 13px;
}

.portal-obra-resumen p i {
    color: var(--pc-accent);
}

.portal-progreso {
    position: relative;
    z-index: 1;
    width: 300px;
    flex-shrink: 0;
}

.progreso-texto {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    color: #b7c3d3;
    font-size: 12px;
}

.progreso-texto strong {
    color: var(--pc-accent);
    font-size: 23px;
}

.progreso-barra {
    height: 9px;
    overflow: hidden;
    background: #344258;
    border-radius: 20px;
}

.progreso-barra div {
    height: 100%;
    background: linear-gradient(90deg, #d99b00, #ffd04a);
    border-radius: 20px;
    transition: width .5s ease;
}

/* NAVEGACIÓN */

.portal-navegacion {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px;
    margin-bottom: 28px;
    overflow-x: auto;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-radius: 13px;
    scrollbar-width: thin;
    scrollbar-color: #344258 transparent;
}

.portal-navegacion a {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    flex-shrink: 0;
    padding: 12px 15px;
    border-radius: 9px;
    color: #9cabc0;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all .2s ease;
}

.portal-navegacion a i {
    font-size: 13px;
}

.portal-navegacion a:hover {
    color: #fff;
    background: rgba(255, 255, 255, .045);
}

.portal-navegacion a.activo {
    color: var(--pc-accent);
    background: var(--pc-accent-soft);
    box-shadow: inset 0 0 0 1px rgba(244, 180, 0, .13);
}

/* CONTENIDO */

.portal-contenido {
    animation: portalEntrada .35s ease;
}

@keyframes portalEntrada {
    from {
        opacity: 0;
        transform: translateY(7px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.portal-seccion-titulo {
    margin-bottom: 22px;
}

.portal-seccion-titulo h2 {
    margin: 0 0 7px;
    font-size: 20px;
    font-weight: 600;
    letter-spacing: -.4px;
}

/* ESTADÍSTICAS */

.portal-estadisticas {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.portal-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 11px;
    min-width: 0;
    padding: 21px;
    overflow: hidden;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-radius: 15px;
    transition: transform .2s, border-color .2s;
}

.portal-card:hover {
    transform: translateY(-3px);
    border-color: rgba(244, 180, 0, .3);
}

.card-icono {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 43px;
    height: 43px;
    color: var(--pc-accent);
    background: var(--pc-accent-soft);
    border-radius: 12px;
    font-size: 17px;
}

.portal-card span {
    color: var(--pc-muted);
    font-size: 12px;
}

.portal-card strong {
    font-size: 21px;
    font-weight: 650;
    overflow-wrap: anywhere;
}

/* PANELES */

.portal-panel {
    min-width: 0;
    padding: 23px;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, .07);
}

.portal-panel h3 {
    margin: 0 0 17px;
    font-size: 15px;
    font-weight: 600;
}

.portal-dos-columnas {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 17px;
}

.portal-panel-titulo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.portal-panel-titulo h3 {
    margin: 0;
}

.portal-panel-titulo a {
    color: var(--pc-accent);
    text-decoration: none;
    font-size: 11px;
    font-weight: 600;
}

.portal-panel-titulo a:hover {
    color: #ffd04a;
}

/* DATOS */

.portal-dato {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    padding: 14px 0;
    border-bottom: 1px solid var(--pc-border);
}

.portal-dato:last-child {
    border-bottom: none;
}

.portal-dato span {
    color: var(--pc-muted);
    font-size: 12px;
    line-height: 1.6;
}

.portal-dato strong {
    max-width: 60%;
    color: #e7edf5;
    text-align: right;
    font-size: 12px;
    font-weight: 500;
    overflow-wrap: anywhere;
}

/* AVANCES */

.portal-lista-item {
    display: flex;
    gap: 13px;
    padding: 14px 0;
    border-bottom: 1px solid var(--pc-border);
}

.portal-lista-item:last-child {
    border-bottom: none;
}

.lista-punto {
    width: 9px;
    height: 9px;
    flex-shrink: 0;
    margin-top: 5px;
    background: var(--pc-accent);
    border-radius: 50%;
    box-shadow: 0 0 9px rgba(244, 180, 0, .25);
}

.portal-lista-item strong,
.portal-lista-item small {
    display: block;
}

.portal-lista-item strong {
    color: #e6edf6;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.6;
}

.portal-lista-item small {
    margin-top: 6px;
    color: var(--pc-muted);
    font-size: 10px;
}

/* ETAPAS */

.portal-etapa {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    padding: 19px 0;
    border-bottom: 1px solid var(--pc-border);
}

.portal-etapa:last-child {
    border-bottom: none;
}

.portal-etapa strong {
    color: #edf2f8;
    font-size: 13px;
    font-weight: 600;
}

.portal-etapa p {
    margin: 6px 0;
    color: var(--pc-muted);
    font-size: 11px;
    line-height: 1.6;
}

.portal-etapa small {
    color: #77869b;
    font-size: 10px;
}

.etapa-estado {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    padding: 7px 11px;
    border: 1px solid var(--pc-border);
    border-radius: 30px;
    background: #1b2a3c;
    color: #aebdd0;
    font-size: 10px;
    font-weight: 600;
}

.etapa-estado.finalizada {
    color: var(--pc-green);
    background: rgba(53, 197, 138, .09);
    border-color: rgba(53, 197, 138, .2);
}

.etapa-estado.en-proceso {
    color: var(--pc-accent);
    background: var(--pc-accent-soft);
    border-color: rgba(244, 180, 0, .2);
}

.etapa-estado.pendiente {
    color: #aab8c9;
}

/* FOTOGRAFÍAS */

.portal-galeria {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 17px;
}

.portal-foto {
    overflow: hidden;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-radius: 14px;
    transition: transform .25s, border-color .25s;
}

.portal-foto:hover {
    transform: translateY(-4px);
    border-color: rgba(244, 180, 0, .3);
}

.portal-foto a {
    display: block;
    overflow: hidden;
    background: #0a111d;
}

.portal-foto img {
    display: block;
    width: 100%;
    height: 215px;
    object-fit: cover;
    transition: transform .35s ease;
}

.portal-foto:hover img {
    transform: scale(1.045);
}

.portal-foto div {
    padding: 15px;
}

.portal-foto strong,
.portal-foto small {
    display: block;
}

.portal-foto strong {
    color: #e8edf5;
    font-size: 12px;
    font-weight: 500;
    line-height: 1.6;
}

.portal-foto small {
    margin-top: 7px;
    color: var(--pc-muted);
    font-size: 10px;
}

/* DOCUMENTOS */

.portal-documento {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid var(--pc-border);
}

.portal-documento:last-child {
    border-bottom: none;
}

.documento-icono {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 43px;
    height: 43px;
    flex-shrink: 0;
    border-radius: 11px;
    background: rgba(85, 168, 255, .1);
    color: var(--pc-blue);
    font-size: 17px;
}

.documento-info {
    flex: 1;
    min-width: 0;
}

.documento-info strong,
.documento-info small {
    display: block;
    overflow-wrap: anywhere;
}

.documento-info strong {
    color: #e8edf5;
    font-size: 12px;
    font-weight: 500;
}

.documento-info small {
    margin-top: 6px;
    color: var(--pc-muted);
    font-size: 10px;
}

.portal-documento a {
    padding: 9px 13px;
    color: var(--pc-accent);
    background: var(--pc-accent-soft);
    border: 1px solid rgba(244, 180, 0, .15);
    border-radius: 8px;
    text-decoration: none;
    font-size: 11px;
    font-weight: 600;
    transition: .2s;
}

.portal-documento a:hover {
    background: rgba(244, 180, 0, .2);
}

/* EQUIPO */

.portal-equipo {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 15px;
}

.portal-persona {
    display: flex;
    align-items: center;
    gap: 15px;
    min-width: 0;
    padding: 19px;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-radius: 13px;
    transition: border-color .2s, transform .2s;
}

.portal-persona:hover {
    transform: translateY(-2px);
    border-color: rgba(244, 180, 0, .25);
}

.persona-icono {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    flex-shrink: 0;
    color: var(--pc-accent);
    background: var(--pc-accent-soft);
    border-radius: 12px;
    font-size: 17px;
}

.portal-persona span,
.portal-persona strong {
    display: block;
    overflow-wrap: anywhere;
}

.portal-persona span {
    margin-bottom: 5px;
    color: var(--pc-muted);
    font-size: 11px;
}

.portal-persona strong {
    color: #edf2f8;
    font-size: 13px;
    font-weight: 600;
}

/* PRESUPUESTO */

.portal-presupuesto-total {
    display: flex;
    flex-direction: column;
    gap: 9px;
    padding: 25px;
    margin-bottom: 18px;
    background:
        radial-gradient(circle at 90% 10%, rgba(244, 180, 0, .12), transparent 35%),
        var(--pc-surface-2);
    border: 1px solid rgba(244, 180, 0, .18);
    border-radius: 15px;
}

.portal-presupuesto-total span {
    color: #aebbd0;
    font-size: 12px;
}

.portal-presupuesto-total strong {
    color: var(--pc-accent);
    font-size: clamp(23px, 4vw, 31px);
    font-weight: 700;
}

.portal-presupuesto-total small {
    color: var(--pc-muted);
    font-size: 10px;
}

.portal-panel select {
    display: block;
    width: 100%;
    margin-top: 12px;
    padding: 13px 15px;
    color: var(--pc-text);
    background: #0b1422;
    border: 1px solid #34445a;
    border-radius: 9px;
    outline: none;
    font-family: inherit;
    font-size: 12px;
    cursor: pointer;
}

.portal-panel select:focus {
    border-color: var(--pc-accent);
    box-shadow: 0 0 0 3px rgba(244, 180, 0, .08);
}

.portal-tabla-contenedor {
    width: 100%;
    overflow-x: auto;
}

.portal-tabla {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.portal-tabla th {
    padding: 14px 12px;
    color: #9eacc0;
    background: #172438;
    text-align: left;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
}

.portal-tabla td {
    padding: 15px 12px;
    color: #dce5f0;
    border-bottom: 1px solid var(--pc-border);
    vertical-align: middle;
}

.portal-tabla tbody tr:hover {
    background: rgba(255, 255, 255, .025);
}

.portal-tabla td small {
    display: block;
    margin-top: 5px;
    color: var(--pc-muted);
    font-size: 10px;
}

/* ESTADOS VACÍOS */

.portal-sin-datos {
    padding: 20px 0;
    color: var(--pc-muted);
    font-size: 12px;
    line-height: 1.7;
}

.portal-vacio {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 330px;
    padding: 40px 25px;
    text-align: center;
    background: var(--pc-surface);
    border: 1px solid var(--pc-border);
    border-radius: 17px;
}

.portal-vacio i {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 75px;
    height: 75px;
    margin-bottom: 20px;
    color: var(--pc-accent);
    background: var(--pc-accent-soft);
    border-radius: 20px;
    font-size: 29px;
}

.portal-vacio h2 {
    margin: 0 0 10px;
    font-size: 19px;
}

.portal-vacio p {
    max-width: 400px;
    margin: 0;
    color: var(--pc-muted);
    font-size: 12px;
    line-height: 1.8;
}

/* TABLETS */

@media (max-width: 1100px) {
    .portal-cliente {
        padding: 24px;
        margin-top: 20%;
    }

    .portal-estadisticas {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .portal-galeria {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .portal-navegacion a {
        padding: 11px 12px;
    }
}

/* MÓVILES */

@media (max-width: 700px) {
    .portal-cliente {
        padding: 18px 14px;
    }

    .portal-encabezado {
        align-items: flex-start;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 20px;
    }

    .portal-encabezado h1 {
        font-size: 24px;
    }

    .portal-obra-resumen {
        align-items: flex-start;
        flex-direction: column;
        gap: 25px;
        padding: 22px;
    }

    .portal-progreso {
        width: 100%;
    }

    .portal-navegacion {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 5px;
        overflow: visible;
    }

    .portal-navegacion a {
        justify-content: flex-start;
        min-width: 0;
        padding: 12px 9px;
        font-size: 11px;
    }

    .portal-navegacion a span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .portal-estadisticas {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .portal-card {
        padding: 15px;
    }

    .portal-card strong {
        font-size: 18px;
    }

    .portal-dos-columnas,
    .portal-equipo {
        grid-template-columns: 1fr;
    }

    .portal-panel {
        padding: 17px;
    }

    .portal-galeria {
        grid-template-columns: 1fr;
    }

    .portal-foto img {
        height: 230px;
    }

    .portal-etapa {
        align-items: flex-start;
        flex-direction: column;
        gap: 12px;
    }

    .portal-dato {
        flex-direction: column;
        gap: 6px;
    }

    .portal-dato strong {
        max-width: 100%;
        text-align: left;
    }

    .portal-documento {
        gap: 10px;
    }

    .portal-documento a {
        padding: 8px 10px;
    }

    .portal-tabla {
        min-width: 570px;
    }
}

/* MÓVILES PEQUEÑOS */

@media (max-width: 380px) {
    .portal-cliente {
        padding: 14px 10px;
    }

    .portal-estadisticas {
        grid-template-columns: 1fr;
    }

    .portal-navegacion a {
        font-size: 10px;
    }

    .portal-obra-resumen {
        padding: 17px;
    }
}

/* ACCESIBILIDAD */

@media (prefers-reduced-motion: reduce) {
    .portal-cliente *,
    .portal-cliente *::before,
    .portal-cliente *::after {
        animation-duration: .01ms !important;
        transition-duration: .01ms !important;
    }
}

</style>

<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>