<?php
/**
 * Funciones compartidas por las pantallas de reportes.
 *
 * - e()                 escapa texto para HTML
 * - encabezadoImpresion()   cabecera que solo aparece al imprimir
 * - botonesReporte()    botones Imprimir / PDF / Excel
 * - badge()             etiqueta de color según el estado
 *
 * Los botones PDF y Excel llaman a ReporteController.php?accion=exportar.
 * El archivo assets/js/reportes.js les agrega a esos enlaces lo que el
 * usuario escribió en el buscador y el estado elegido, así se exporta
 * lo mismo que se ve en pantalla.
 */

require_once __DIR__ . "/../../../modelos/Reporte.php";

if (!function_exists("e")) {
    function e($texto)
    {
        return htmlspecialchars((string)($texto ?? ""), ENT_QUOTES, "UTF-8");
    }
}

function urlExportar($tipo, $formato, $extra = [])
{
    $parametros = array_merge([
        "accion" => "exportar",
        "tipo" => $tipo,
        "formato" => $formato,
    ], $extra);

    return "/empresa_constructora/controladores/ReporteController.php?" . http_build_query($parametros);
}

function encabezadoImpresion($titulo)
{
    $usuario = trim(($_SESSION["usuario"]["nombre"] ?? "") . " " . ($_SESSION["usuario"]["apellido"] ?? ""));
    ?>
    <div class="print-header">
        <h1>Empresa Constructora BUILDPRO</h1>
        <h2><?= e($titulo) ?></h2>
        <p>Fecha de generación: <?= date("d/m/Y H:i") ?></p>
        <p>Generado por: <?= e($usuario) ?></p>
    </div>
    <?php
}

/**
 * @param string $tipo    tipo de reporte para exportar (ver Reporte::tiposExportables)
 * @param string $tabla   id de la tabla que filtran el buscador y los selectores
 * @param array  $extra   parámetros fijos que se agregan al exportar
 */
function botonesReporte($tipo, $tabla = "", $extra = [])
{
    ?>
    <div class="toolbar-right">
        <button type="button" onclick="window.print()" class="btn btn-primary">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
        <a href="<?= e(urlExportar($tipo, "pdf", $extra)) ?>" target="_blank"
            class="btn btn-danger" data-exportar data-tabla="<?= e($tabla) ?>">
            <i class="fa-solid fa-file-pdf"></i> PDF
        </a>
        <a href="<?= e(urlExportar($tipo, "excel", $extra)) ?>"
            class="btn btn-success" data-exportar data-tabla="<?= e($tabla) ?>">
            <i class="fa-solid fa-file-excel"></i> Excel
        </a>
    </div>
    <?php
}

function badge($texto)
{
    $clases = [
        // positivos
        "Activo" => "badge-success", "Activa" => "badge-success", "Finalizada" => "badge-success",
        "Resuelta" => "badge-success", "Presente" => "badge-success", "Normal" => "badge-success",
        "Devuelta" => "badge-success", "Entregada" => "badge-success", "INGRESO" => "badge-success",
        "Completada" => "badge-success",
        // en curso
        "En Proceso" => "badge-info", "En obra" => "badge-info", "Asignada" => "badge-info",
        "Aprobada" => "badge-info", "AUDITORIA" => "badge-info",
        // atención
        "Planificacion" => "badge-warning", "Pendiente" => "badge-warning", "En revisión" => "badge-warning",
        "Tarde" => "badge-warning", "Crítico" => "badge-warning", "Suspendida" => "badge-warning",
        "En reparación" => "badge-warning", "ACCESO" => "badge-warning",
        // negativos
        "Inactivo" => "badge-danger", "Cancelada" => "badge-danger", "Ausente" => "badge-danger",
        "Sin stock" => "badge-danger", "Retirado" => "badge-danger", "Rechazada" => "badge-danger",
        "EGRESO" => "badge-danger", "Fuera de servicio" => "badge-danger",
    ];

    $etiqueta = $texto === "Planificacion" ? "Planificación" : $texto;
    $clase = $clases[$texto] ?? "badge-secondary";

    return '<span class="badge ' . $clase . '">' . e($etiqueta) . '</span>';
}

function barraAvance($porcentaje)
{
    $porcentaje = max(0, min(100, (int)$porcentaje));
    return '<div class="progress"><div class="progress-bar" style="width:' . $porcentaje . '%;">'
        . $porcentaje . '%</div></div>';
}