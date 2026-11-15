<?php

/**@var array $resumenEtapas 
 * @var array $etapas
 * @var array $fotografias
 * @var array $documentos
 * @var array $empleados
 * @var array $jefeObra
 * @var array $capataz
 * @var array $presupuestos
 * @var array $presupuestoSeleccionado
 * @var array $detallePresupuesto
 * @var array $manoObraPresupuesto
 * @var string $accion
 * @var array $acciones
 * @var array $avances 
*/

require_once __DIR__ . "/../../layouts/header.php";

/*
|--------------------------------------------------------------------------
| FUNCIONES AUXILIARES
|--------------------------------------------------------------------------
*/

function escapar($valor): string
{
    return htmlspecialchars((string)($valor ?? ""), ENT_QUOTES, "UTF-8");
}

function fechaPortal($fecha): string
{
    if (empty($fecha)) {
        return "No especificada";
    }

    $timestamp = strtotime($fecha);

    return $timestamp !== false
        ? date("d/m/Y", $timestamp)
        : "No especificada";
}

function dineroPortal($valor): string
{
    return "$ " . number_format((float)($valor ?? 0), 2, ",", ".");
}

function urlPortal(string $base, string $accion, int $idObra): string
{
    return $base . "?accion=" . urlencode($accion) .
        "&id_obra=" . $idObra;
}

/*
|--------------------------------------------------------------------------
| NORMALIZACIÓN DE VARIABLES
|--------------------------------------------------------------------------
*/

$accionActual = $accion ?? ($_GET["accion"] ?? "dashboard");

$obra = is_array($obraActual ?? null)
    ? $obraActual
    : null;

$idObraActual = (int)($obra["id_obra"] ?? 0);

$nombreObra = $obra["nombre_obra"] ?? "Mi obra";

$estadoObra = $obra["estado"] ?? "Sin estado";

$estadoMostrar = $estadoMostrar ?? $estadoObra;

$baseUrl = $rutaCliente ?? "../controladores/PortalClienteController.php";

/*
 * El controlador puede entregar el porcentaje dentro de $resumenEtapas.
 * Se admiten ambas claves porque el resumen del portal utiliza "avance"
 * y algunas versiones del modelo utilizan "porcentaje_avance".
 */
$resumenEtapas = is_array($resumenEtapas ?? null)
    ? $resumenEtapas
    : [];

$porcentajeFuente = $resumenEtapas["porcentaje_avance"]
    ?? $resumenEtapas["avance"]
    ?? $porcentajeAvance
    ?? null;

/*
 * Respaldo: si el controlador no envió el porcentaje, se calcula
 * contando las etapas finalizadas, igual que el criterio de avance
 * utilizado por el módulo de Etapas.
 */
if (!is_numeric($porcentajeFuente)) {
    $totalEtapas = count($etapas);
    $etapasFinalizadas = 0;

    foreach ($etapas as $etapaProgreso) {
        $estadoProgreso = strtolower(trim((string)($etapaProgreso["estado"] ?? "")));

        if ($estadoProgreso === "finalizada") {
            $etapasFinalizadas++;
        }
    }

    $porcentajeFuente = $totalEtapas > 0
        ? round(($etapasFinalizadas / $totalEtapas) * 100)
        : 0;
}

$porcentajeAvance = round(max(
    0,
    min(100, (float)$porcentajeFuente)
));

$acciones = is_array($acciones ?? null) ? $acciones : [
    "dashboard" => ["Resumen", "fa-chart-pie"],
    "obra" => ["Mi obra", "fa-building"],
    "avances" => ["Avances", "fa-list-check"],
    "etapas" => ["Etapas", "fa-bars-progress"],
    "fotos" => ["Fotografías", "fa-camera"],
    "documentos" => ["Documentos", "fa-folder-open"],
    "equipo" => ["Equipo", "fa-users"],
    "presupuesto" => ["Presupuesto", "fa-file-invoice-dollar"]
];

$avances = is_array($avances ?? null) ? $avances : [];
$etapas = is_array($etapas ?? null) ? $etapas : [];
$fotografias = is_array($fotografias ?? null) ? $fotografias : [];
$documentos = is_array($documentos ?? null) ? $documentos : [];
$empleados = is_array($empleados ?? null) ? $empleados : [];

$jefeObra = is_array($jefeObra ?? null) ? $jefeObra : [];
$capataz = is_array($capataz ?? null) ? $capataz : [];

$presupuestos = is_array($presupuestos ?? null)
    ? $presupuestos
    : [];

$presupuestoSeleccionado = is_array($presupuestoSeleccionado ?? null)
    ? $presupuestoSeleccionado
    : [];

$detallePresupuesto = is_array($detallePresupuesto ?? null)
    ? $detallePresupuesto
    : [];

$manoObraPresupuesto = is_array($manoObraPresupuesto ?? null)
    ? $manoObraPresupuesto
    : [];

?>

<main class="portal-cliente">

    <div class="portal-encabezado">
        <div>
            <span class="portal-etiqueta">PORTAL DEL CLIENTE</span>
            <h1>
                <?php echo escapar($acciones[$accionActual][0] ?? "Mi portal"); ?>
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
                <div
                    class="progreso-barra"
                    role="progressbar"
                    aria-label="Progreso de etapas de la obra"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-valuenow="<?php echo $porcentajeAvance; ?>">
                    <div style="width: <?php echo $porcentajeAvance; ?>%"></div>
                </div>
            </div>
        </div>

        <nav class="portal-navegacion">
            <?php foreach ($acciones as $clave => $datos): ?>
                <a
                    href="<?php echo escapar(urlPortal($baseUrl, $clave, $idObraActual)); ?>"
                    class="<?php echo $accionActual === $clave ? "activo" : ""; ?>">
                    <i class="fa-solid <?php echo escapar($datos[1] ?? "fa-circle"); ?>"></i>
                    <span><?php echo escapar($datos[0] ?? $clave); ?></span>
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
                        <strong><?php echo count($etapas); ?></strong>
                    </div>

                    <div class="portal-card">
                        <div class="card-icono"><i class="fa-solid fa-camera"></i></div>
                        <span>Fotografías</span>
                        <strong><?php echo count($fotografias); ?></strong>
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
                                        <strong><?php echo escapar($avance["descripcion"] ?? "Sin descripción"); ?></strong>
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
                                    <strong><?php echo escapar($avance["descripcion"] ?? "Sin descripción"); ?></strong>
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
                                    <strong><?php echo escapar($etapa["nombre_etapa"] ?? "Etapa sin nombre"); ?></strong>
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
                                <a href="<?php echo escapar($foto["ruta_imagen"] ?? ""); ?>" target="_blank" rel="noopener">
                                    <img
                                        src="<?php echo escapar($foto["ruta_imagen"] ?? ""); ?>"
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
                                    <strong><?php echo escapar($documento["nombre_archivo"] ?? "Documento"); ?></strong>
                                    <small>
                                        <?php echo escapar($documento["tipo_documento"] ?? "Documento"); ?>
                                        · <?php echo fechaPortal($documento["fecha_subida"] ?? null); ?>
                                    </small>
                                </div>
                                <?php
                                $rutaDocumento = $documento["ruta_archivo"] ?? "";
                                $urlDocumento = "";

                                if (!empty($rutaDocumento)) {
                                    $rutaDocumento = str_replace("\\", "/", $rutaDocumento);

                                    $posicionUploads = strpos($rutaDocumento, "uploads/");

                                    if ($posicionUploads !== false) {
                                        $rutaDocumento = substr($rutaDocumento, $posicionUploads);
                                    }

                                    $urlDocumento = "/empresa_constructora/" . ltrim($rutaDocumento, "/");
                                }
                                ?>

                                <?php if (!empty($urlDocumento)): ?>
                                    <a href="<?php echo escapar($urlDocumento); ?>"
                                        target="_blank"
                                        rel="noopener">
                                        Consultar
                                    </a>
                                <?php endif; ?>
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

                <?php
                /*
                |--------------------------------------------------------------------------
                | ESTADO Y VIGENCIA DEL PRESUPUESTO
                |--------------------------------------------------------------------------
                */

                $esUltimoPresupuesto = false;
                $puedeResponderPresupuesto = false;

                if (!empty($presupuestos) && !empty($presupuestoSeleccionado)) {

                    $ultimoPresupuesto = $presupuestos[0] ?? [];

                    $esUltimoPresupuesto =
                        (int)($ultimoPresupuesto["id_presupuesto"] ?? 0) ===
                        (int)($presupuestoSeleccionado["id_presupuesto"] ?? 0);

                    $puedeResponderPresupuesto =
                        $esUltimoPresupuesto &&
                        ($presupuestoSeleccionado["estado"] ?? "") === "Activo";
                }

                $estadoPresupuesto = $presupuestoSeleccionado["estado"] ?? "Sin estado";

                $coloresEstado = [
                    "Activo" => "#f4b400",
                    "Aprobado" => "#22c55e",
                    "Rechazado" => "#ef4444",
                    "Inactivo" => "#94a3b8"
                ];

                $colorEstado = $coloresEstado[$estadoPresupuesto] ?? "#94a3b8";

                $mensajePresupuesto = $_SESSION["mensaje_presupuesto"] ?? null;
                $tipoMensajePresupuesto = $_SESSION["tipo_mensaje_presupuesto"] ?? "success";

                unset(
                    $_SESSION["mensaje_presupuesto"],
                    $_SESSION["tipo_mensaje_presupuesto"]
                );
                ?>

                <section class="presupuesto-cliente">

                    <div class="presupuesto-encabezado">
                        <div>
                            <span class="presupuesto-etiqueta">DOCUMENTACIÓN ECONÓMICA</span>
                            <h2>Presupuestos de obra</h2>
                            <p>
                                Consultá las versiones, los costos y el estado de cada presupuesto.
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($mensajePresupuesto)): ?>
                        <div class="mensaje-presupuesto <?php echo escapar($tipoMensajePresupuesto); ?>">
                            <?php echo escapar($mensajePresupuesto); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($presupuestos)): ?>

                        <div class="presupuesto-vacio">
                            <h3>Todavía no hay presupuestos</h3>
                            <p>
                                Cuando se genere un presupuesto para esta obra,
                                vas a poder consultarlo desde este apartado.
                            </p>
                        </div>

                    <?php elseif (empty($presupuestoSeleccionado)): ?>

                        <div class="presupuesto-vacio">
                            <h3>No se pudo seleccionar el presupuesto</h3>
                            <p>
                                No se encontró una versión válida para mostrar.
                                Volvé a ingresar al apartado para consultar los presupuestos disponibles.
                            </p>
                        </div>

                    <?php else: ?>

                        <div class="presupuesto-selector">
                            <label for="selectorPresupuesto">Seleccionar versión</label>

                            <select
                                id="selectorPresupuesto"
                                onchange="cambiarVersionPresupuesto(this.value)">

                                <?php foreach ($presupuestos as $p): ?>
                                    <option
                                        value="<?php echo (int)($p["id_presupuesto"] ?? 0); ?>"
                                        <?php
                                        echo (
                                            (int)($p["id_presupuesto"] ?? 0) ===
                                            (int)($presupuestoSeleccionado["id_presupuesto"] ?? 0)
                                        ) ? "selected" : "";
                                        ?>>
                                        <?php echo escapar($p["nombre_presupuesto"] ?? "Presupuesto"); ?>
                                        - Versión <?php echo (int)($p["version"] ?? 0); ?>
                                        (<?php echo escapar($p["estado"] ?? "Sin estado"); ?>)
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="presupuesto-tarjeta">

                            <div class="presupuesto-tarjeta-superior">

                                <div>
                                    <span class="presupuesto-etiqueta">PRESUPUESTO</span>

                                    <h3>
                                        <?php echo escapar($presupuestoSeleccionado["nombre_presupuesto"] ?? "Presupuesto"); ?>
                                    </h3>

                                    <p>
                                        Versión <?php echo (int)($presupuestoSeleccionado["version"] ?? 0); ?>
                                        ·
                                        <?php echo fechaPortal($presupuestoSeleccionado["fecha"] ?? null); ?>
                                    </p>
                                </div>

                                <span
                                    class="estado-presupuesto"
                                    style="--estado-color: <?php echo escapar($colorEstado); ?>">
                                    <?php echo escapar($estadoPresupuesto); ?>
                                </span>

                            </div>

                            <div class="presupuesto-total">

                                <span>Importe total del presupuesto</span>

                                <strong>
                                    <?php echo dineroPortal($presupuestoSeleccionado["monto_cliente"] ?? 0); ?>
                                </strong>

                            </div>

                            <?php if (!empty($presupuestoSeleccionado["detalle_general"])): ?>
                                <div class="presupuesto-descripcion">
                                    <h4>Descripción general</h4>

                                    <p>
                                        <?php echo nl2br(escapar($presupuestoSeleccionado["detalle_general"])); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <div class="presupuesto-informacion">

                                <div>
                                    <span>Validez</span>
                                    <strong>
                                        <?php echo (int)($presupuestoSeleccionado["validez_dias"] ?? 0); ?>
                                        días
                                    </strong>
                                </div>

                                <div>
                                    <span>Forma de pago</span>
                                    <strong>
                                        <?php echo escapar($presupuestoSeleccionado["forma_pago"] ?? "No especificada"); ?>
                                    </strong>
                                </div>

                            </div>

                            <div class="presupuesto-detalles">

                                <h4>Detalle de materiales y conceptos</h4>

                                <?php if (!empty($detallePresupuesto)): ?>

                                    <div class="tabla-presupuesto-contenedor">
                                        <table class="tabla-presupuesto">

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
                                                            <?php echo escapar($detalle["concepto"] ?? $detalle["descripcion"] ?? ""); ?>
                                                        </td>

                                                        <td>
                                                            <?php echo escapar($detalle["unidad_medida"] ?? "-"); ?>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            echo number_format(
                                                                (float)($detalle["cantidad"] ?? 0),
                                                                2,
                                                                ",",
                                                                "."
                                                            );
                                                            ?>
                                                        </td>

                                                        <td>
                                                            <?php echo dineroPortal($detalle["subtotal"] ?? 0); ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>

                                        </table>
                                    </div>

                                <?php else: ?>

                                    <p class="presupuesto-sin-detalles">
                                        No hay materiales registrados en esta versión.
                                    </p>

                                <?php endif; ?>

                            </div>

                            <div class="presupuesto-detalles">

                                <h4>Mano de obra</h4>

                                <?php if (!empty($manoObraPresupuesto)): ?>

                                    <div class="tabla-presupuesto-contenedor">
                                        <table class="tabla-presupuesto">

                                            <thead>
                                                <tr>
                                                    <th>Trabajo</th>
                                                    <th>Horas</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach ($manoObraPresupuesto as $trabajo): ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo escapar($trabajo["descripcion"] ?? "Sin descripción"); ?>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            echo number_format(
                                                                (float)($trabajo["cantidad_horas"] ?? 0),
                                                                2,
                                                                ",",
                                                                "."
                                                            );
                                                            ?>
                                                        </td>

                                                        <td>
                                                            <?php echo dineroPortal($trabajo["subtotal"] ?? 0); ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>

                                        </table>
                                    </div>

                                <?php else: ?>

                                    <p class="presupuesto-sin-detalles">
                                        No hay trabajos de mano de obra registrados.
                                    </p>

                                <?php endif; ?>

                            </div>

                            <?php if ($puedeResponderPresupuesto): ?>

                                <div class="presupuesto-decision">

                                    <div>
                                        <h4>Decisión del presupuesto</h4>

                                        <p>
                                            Revisá los importes y las condiciones.
                                            Podés aprobar o rechazar esta versión.
                                        </p>
                                    </div>

                                    <div class="presupuesto-acciones">

                                        <form
                                            method="POST"
                                            action="<?php echo escapar($baseUrl); ?>"
                                            onsubmit="return confirm('¿Estás seguro de que querés aprobar este presupuesto? Esta decisión quedará registrada.');">

                                            <input
                                                type="hidden"
                                                name="accion"
                                                value="aprobar_presupuesto">

                                            <input
                                                type="hidden"
                                                name="id_obra"
                                                value="<?php echo (int)$idObraActual; ?>">

                                            <input
                                                type="hidden"
                                                name="id_presupuesto"
                                                value="<?php echo (int)($presupuestoSeleccionado["id_presupuesto"] ?? 0); ?>">

                                            <button type="submit" class="btn-aprobar-presupuesto">
                                                Aprobar presupuesto
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="<?php echo escapar($baseUrl); ?>"
                                            onsubmit="return confirm('¿Estás seguro de que querés rechazar este presupuesto?');">

                                            <input
                                                type="hidden"
                                                name="accion"
                                                value="rechazar_presupuesto">

                                            <input
                                                type="hidden"
                                                name="id_obra"
                                                value="<?php echo (int)$idObraActual; ?>">

                                            <input
                                                type="hidden"
                                                name="id_presupuesto"
                                                value="<?php echo (int)($presupuestoSeleccionado["id_presupuesto"] ?? 0); ?>">

                                            <button type="submit" class="btn-rechazar-presupuesto">
                                                Rechazar presupuesto
                                            </button>
                                        </form>

                                    </div>

                                </div>

                            <?php elseif ($estadoPresupuesto === "Inactivo"): ?>

                                <div class="presupuesto-aviso">
                                    Esta versión quedó inactiva porque se generó una versión más reciente.
                                    Ya no es posible aprobarla ni rechazarla.
                                </div>

                            <?php elseif ($estadoPresupuesto === "Aprobado"): ?>

                                <div class="presupuesto-aviso aprobado">
                                    Este presupuesto ya fue aprobado.

                                    <?php if (!empty($presupuestoSeleccionado["fecha_aprobacion"])): ?>
                                        <br>
                                        Fecha de decisión:
                                        <?php
                                        $fechaAprobacion = strtotime($presupuestoSeleccionado["fecha_aprobacion"]);

                                        echo $fechaAprobacion !== false
                                            ? date("d/m/Y H:i", $fechaAprobacion)
                                            : "No especificada";
                                        ?>
                                    <?php endif; ?>
                                </div>

                            <?php elseif ($estadoPresupuesto === "Rechazado"): ?>

                                <div class="presupuesto-aviso rechazado">
                                    Este presupuesto fue rechazado y ya no admite nuevas decisiones.
                                </div>

                            <?php else: ?>

                                <div class="presupuesto-aviso">
                                    Este presupuesto no admite nuevas decisiones.
                                </div>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>

                </section>

                <script>
                    function cambiarVersionPresupuesto(idPresupuesto) {
                        const url = new URL(window.location.href);

                        url.searchParams.set("accion", "presupuesto");
                        url.searchParams.set("id_presupuesto", idPresupuesto);

                        window.location.href = url.toString();
                    }
                </script>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</main>

<style>
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

    /* ESTILOS DEL PRESUPUESTO */

    .presupuesto-cliente {
        width: 100%;
    }

    .presupuesto-encabezado {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .presupuesto-encabezado h2 {
        color: #f8fafc;
        font-size: 25px;
        margin: 7px 0;
    }

    .presupuesto-encabezado p {
        color: #94a3b8;
        margin: 0;
    }

    .presupuesto-etiqueta {
        color: #f4b400;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.4px;
    }

    .presupuesto-selector {
        margin-bottom: 20px;
    }

    .presupuesto-selector label {
        display: block;
        color: #cbd5e1;
        font-size: 13px;
        margin-bottom: 9px;
    }

    .presupuesto-selector select {
        width: 100%;
        max-width: 420px;
        padding: 13px 15px;
        color: #f8fafc;
        background: #111c2a;
        border: 1px solid #29384b;
        border-radius: 12px;
        outline: none;
    }

    .presupuesto-tarjeta {
        background: #111c2a;
        border: 1px solid #26364a;
        border-radius: 18px;
        padding: 25px;
    }

    .presupuesto-tarjeta-superior {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        flex-wrap: wrap;
    }

    .presupuesto-tarjeta-superior h3 {
        color: #f8fafc;
        font-size: 21px;
        margin: 8px 0;
    }

    .presupuesto-tarjeta-superior p {
        color: #94a3b8;
        font-size: 13px;
    }

    .estado-presupuesto {
        display: inline-flex;
        padding: 8px 13px;
        border-radius: 20px;
        color: var(--estado-color);
        background: color-mix(in srgb, var(--estado-color) 13%, transparent);
        border: 1px solid var(--estado-color);
        font-size: 12px;
        font-weight: 800;
    }

    .presupuesto-total {
        margin: 25px 0;
        padding: 25px;
        background: #0b1320;
        border: 1px solid #29384b;
        border-radius: 15px;
    }

    .presupuesto-total span {
        display: block;
        color: #94a3b8;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .presupuesto-total strong {
        color: #f4b400;
        font-size: clamp(25px, 4vw, 36px);
        font-weight: 800;
    }

    .presupuesto-descripcion {
        margin: 25px 0;
    }

    .presupuesto-descripcion h4,
    .presupuesto-detalles h4,
    .presupuesto-decision h4 {
        color: #f8fafc;
        margin-bottom: 12px;
    }

    .presupuesto-descripcion p {
        color: #cbd5e1;
        line-height: 1.7;
    }

    .presupuesto-informacion {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .presupuesto-informacion div {
        background: #0b1320;
        padding: 17px;
        border-radius: 12px;
    }

    .presupuesto-informacion span,
    .presupuesto-informacion strong {
        display: block;
    }

    .presupuesto-informacion span {
        color: #94a3b8;
        font-size: 12px;
        margin-bottom: 7px;
    }

    .presupuesto-informacion strong {
        color: #f8fafc;
        font-size: 14px;
    }

    .presupuesto-detalles {
        margin-top: 25px;
    }

    .tabla-presupuesto-contenedor {
        width: 100%;
        overflow-x: auto;
    }

    .tabla-presupuesto {
        width: 100%;
        border-collapse: collapse;
        min-width: 550px;
    }

    .tabla-presupuesto th {
        color: #94a3b8;
        font-size: 12px;
        text-align: left;
        padding: 13px;
        background: #0b1320;
    }

    .tabla-presupuesto td {
        color: #e2e8f0;
        font-size: 13px;
        padding: 14px 13px;
        border-bottom: 1px solid #29384b;
    }

    .presupuesto-sin-detalles {
        color: #94a3b8;
        font-size: 13px;
    }

    .presupuesto-decision {
        margin-top: 30px;
        padding: 22px;
        border: 1px solid #34465c;
        border-radius: 15px;
        background: #0b1320;
    }

    .presupuesto-decision p {
        color: #94a3b8;
        font-size: 13px;
        line-height: 1.6;
    }

    .presupuesto-acciones {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }

    .presupuesto-acciones form {
        flex: 1;
        min-width: 180px;
    }

    .presupuesto-acciones button {
        width: 100%;
        border: 0;
        border-radius: 11px;
        padding: 14px 18px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-aprobar-presupuesto {
        background: #16a34a;
        color: white;
    }

    .btn-aprobar-presupuesto:hover {
        background: #15803d;
    }

    .btn-rechazar-presupuesto {
        background: #dc2626;
        color: white;
    }

    .btn-rechazar-presupuesto:hover {
        background: #b91c1c;
    }

    .presupuesto-aviso {
        margin-top: 25px;
        padding: 17px;
        border-radius: 12px;
        color: #cbd5e1;
        background: #26364a;
        border: 1px solid #475569;
        line-height: 1.6;
    }

    .presupuesto-aviso.aprobado {
        color: #86efac;
        background: #14532d22;
        border-color: #16a34a;
    }

    .presupuesto-aviso.rechazado {
        color: #fca5a5;
        background: #7f1d1d22;
        border-color: #dc2626;
    }

    .presupuesto-vacio {
        padding: 35px;
        text-align: center;
        background: #111c2a;
        border: 1px solid #29384b;
        border-radius: 16px;
    }

    .presupuesto-vacio h3 {
        color: #f8fafc;
    }

    .presupuesto-vacio p {
        color: #94a3b8;
    }

    .mensaje-presupuesto {
        padding: 15px 18px;
        margin-bottom: 20px;
        border-radius: 12px;
        font-size: 14px;
    }

    .mensaje-presupuesto.success {
        color: #86efac;
        background: #14532d33;
        border: 1px solid #16a34a;
    }

    .mensaje-presupuesto.error {
        color: #fca5a5;
        background: #7f1d1d33;
        border: 1px solid #dc2626;
    }

    @media (max-width: 600px) {
        .presupuesto-tarjeta {
            padding: 17px;
        }

        .presupuesto-informacion {
            grid-template-columns: 1fr;
        }

        .presupuesto-acciones {
            flex-direction: column;
        }

        .presupuesto-acciones form {
            width: 100%;
        }
    }
</style>

<?php require_once __DIR__ . "/../../layouts/footer.php"; ?>