<?php

require_once __DIR__ . "/../modelos/Reporte.php";
require_once __DIR__ . "/../modelos/Auditoria.php";
require_once __DIR__ . "/../config/permisos.php";

/**
 * Controlador de reportes.
 *
 * Las pantallas de cada reporte están en vistas/reportes/ y piden sus datos
 * al modelo Reporte. Este controlador:
 *   - redirige a esas pantallas (accion=obras, accion=empleados, ...)
 *   - exporta cualquier reporte a PDF (dompdf) o Excel (PhpSpreadsheet):
 *       ReporteController.php?accion=exportar&tipo=obras&formato=pdf
 *       ReporteController.php?accion=exportar&tipo=obras&formato=excel
 *     Acepta además &buscar=texto y &estado=valor para exportar
 *     lo mismo que se ve filtrado en pantalla.
 */
class ReporteController
{
    private $reporte;

    /* Pantallas disponibles (accion => archivo de la vista) */
    const VISTAS = [
        "index" => "index.php",
        "obras" => "obras.php",
        "empleados" => "empleados.php",
        "materiales" => "materiales.php",
        "herramientas" => "herramientas.php",
        "clientes" => "clientes.php",
        "usuarios" => "usuarios.php",
        "asignaciones" => "asignaciones.php",
        "incidencias" => "incidencias.php",
        "asistencia" => "asistencia.php",
        "auditoria" => "auditoria.php",
    ];

    public function __construct()
    {
        $this->reporte = new Reporte();
    }

    /** Lleva a la pantalla del reporte pedido. */
    public function mostrar($accion)
    {
        $vista = self::VISTAS[$accion] ?? "index.php";
        $query = $_GET;
        unset($query["accion"]);
        $extra = $query ? "?" . http_build_query($query) : "";
        header("Location: /empresa_constructora/vistas/reportes/" . $vista . $extra);
        exit();
    }

    /** Exporta un reporte a PDF o Excel. */
    public function exportar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Desde controladores/ la ruta relativa de verificarPermiso no llega al login
        if (!isset($_SESSION["usuario"])) {
            header("Location: /empresa_constructora/vistas/login.php");
            exit();
        }

        verificarPermiso("reportes");

        $tipo = $_GET["tipo"] ?? "";
        $formato = strtolower($_GET["formato"] ?? "pdf");

        if (!array_key_exists($tipo, Reporte::tiposExportables())) {
            http_response_code(400);
            echo "Tipo de reporte no válido.";
            exit();
        }

        $filtros = [
            "buscar" => $_GET["buscar"] ?? "",
            "estado" => $_GET["estado"] ?? "",
            // filtros propios de algunos reportes
            "desde" => $_GET["desde"] ?? "",
            "hasta" => $_GET["hasta"] ?? "",
            "tipo" => $_GET["tipo_registro"] ?? "",
            "accion" => $_GET["accion_filtro"] ?? "",
            "modulo" => $_GET["modulo"] ?? "",
            "fecha_desde" => $_GET["fecha_desde"] ?? "",
            "fecha_hasta" => $_GET["fecha_hasta"] ?? "",
        ];

        // En auditoría el texto ya lo filtra la consulta; no se filtra dos veces
        if ($tipo === "auditoria") {
            $filtros["estado"] = "";
        }

        $datos = $this->reporte->datosExportacion($tipo, $filtros);

        $usuario = trim(($_SESSION["usuario"]["nombre"] ?? "") . " " . ($_SESSION["usuario"]["apellido"] ?? ""));
        $datos["generado_por"] = $usuario !== "" ? $usuario : "-";
        $datos["fecha"] = date("d/m/Y H:i");
        $datos["archivo"] = "reporte_" . $tipo . "_" . date("Y-m-d_His");

        // Deja constancia en la auditoría
        if (!empty($_SESSION["usuario"]["id_usuario"])) {
            try {
                (new Auditoria())->registrar([
                    "id_usuario" => $_SESSION["usuario"]["id_usuario"],
                    "accion" => "EXPORTAR",
                    "tabla_afectada" => "reporte",
                    "id_registro" => null,
                    "descripcion" => "Se exportó " . $datos["titulo"] . " a " . ($formato === "excel" ? "Excel" : "PDF"),
                ]);
            } catch (Throwable $e) {
                // si la auditoría falla, la exportación sigue igual
            }
        }

        require_once __DIR__ . "/../vendor/autoload.php";

        if ($formato === "excel") {
            $this->exportarExcel($datos);
        } else {
            $this->exportarPdf($datos);
        }
    }

    /* ---------------------------------------------------------------- PDF */

    public function htmlPdf($datos)
    {
        $e = fn($t) => htmlspecialchars((string)$t, ENT_QUOTES, "UTF-8");
        $muchasColumnas = count($datos["columnas"]) > 9;

        $logo = "";
        $rutaLogo = __DIR__ . "/../assets/img/logo.png";
        if (is_file($rutaLogo)) {
            $logo = '<img src="data:image/png;base64,' . base64_encode(file_get_contents($rutaLogo)) . '" class="logo">';
        }

        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><style>
            @page { margin: 22mm 12mm 16mm 12mm; }
            body { font-family: DejaVu Sans, sans-serif; font-size: ' . ($muchasColumnas ? "7.5" : "9") . 'pt; color: #1f2937; }
            .encabezado { position: fixed; top: -16mm; left: 0; right: 0; height: 14mm; border-bottom: 2px solid #f5b400; }
            .logo { height: 11mm; float: left; margin-right: 8px; }
            .empresa { font-size: 12pt; font-weight: bold; color: #0f172a; }
            .sub { font-size: 8pt; color: #555; }
            .pie { position: fixed; bottom: -11mm; left: 0; right: 0; font-size: 7.5pt; color: #777; text-align: center; }
            .pie .pagina:after { content: counter(page); }
            h1 { font-size: 14pt; margin: 0 0 4px 0; color: #0f172a; }
            .meta { font-size: 8pt; color: #555; margin-bottom: 8px; }
            table { width: 100%; border-collapse: collapse; }
            th { background: #0f172a; color: #ffffff; padding: 5px 4px; text-align: left; font-weight: bold; }
            td { padding: 4px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
            tr:nth-child(even) td { background: #f8fafc; }
            .total { margin-top: 8px; font-size: 8pt; color: #333; }
            .vacio { padding: 12px; text-align: center; color: #777; }
        </style></head><body>';

        $html .= '<div class="encabezado">' . $logo .
            '<div class="empresa">BUILDPRO · Empresa Constructora</div>' .
            '<div class="sub">Sistema de gestión</div></div>';
        $html .= '<div class="pie">' . $e($datos["titulo"]) . ' · Página <span class="pagina"></span></div>';

        $html .= '<h1>' . $e($datos["titulo"]) . '</h1>';
        $html .= '<div class="meta">Fecha de generación: ' . $e($datos["fecha"]) .
            ' &nbsp;·&nbsp; Generado por: ' . $e($datos["generado_por"]) . '</div>';

        $html .= '<table><thead><tr>';
        foreach ($datos["columnas"] as $columna) {
            $html .= '<th>' . $e($columna) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        if (count($datos["filas"]) === 0) {
            $html .= '<tr><td class="vacio" colspan="' . count($datos["columnas"]) . '">No hay registros para mostrar.</td></tr>';
        }

        foreach ($datos["filas"] as $fila) {
            $html .= '<tr>';
            foreach ($fila as $valor) {
                $html .= '<td>' . nl2br($e($valor)) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '<div class="total">Total de registros: ' . count($datos["filas"]) . '</div>';
        $html .= '</body></html>';

        return $html;
    }

    public function generarPdf($datos)
    {
        $opciones = new \Dompdf\Options();
        $opciones->set("defaultFont", "DejaVu Sans");
        $opciones->set("isRemoteEnabled", false);

        $dompdf = new \Dompdf\Dompdf($opciones);
        $dompdf->loadHtml($this->htmlPdf($datos), "UTF-8");
        $dompdf->setPaper("A4", count($datos["columnas"]) > 6 ? "landscape" : "portrait");
        $dompdf->render();

        return $dompdf->output();
    }

    private function exportarPdf($datos)
    {
        $contenido = $this->generarPdf($datos);

        header("Content-Type: application/pdf");
        header('Content-Disposition: inline; filename="' . $datos["archivo"] . '.pdf"');
        header("Content-Length: " . strlen($contenido));
        echo $contenido;
        exit();
    }

    /* -------------------------------------------------------------- EXCEL */

    public function generarExcel($datos)
    {
        $libro = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $libro->getProperties()
            ->setCreator("BUILDPRO")
            ->setTitle($datos["titulo"]);

        $hoja = $libro->getActiveSheet();
        $hoja->setTitle(mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/', "", $datos["titulo"]), 0, 31));

        $totalColumnas = count($datos["columnas"]);
        $ultima = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(1, $totalColumnas));

        // Encabezado del reporte
        $hoja->setCellValue("A1", "BUILDPRO · Empresa Constructora");
        $hoja->setCellValue("A2", $datos["titulo"]);
        $hoja->setCellValue("A3", "Fecha de generación: " . $datos["fecha"] . "   ·   Generado por: " . $datos["generado_por"]);
        $hoja->mergeCells("A1:{$ultima}1");
        $hoja->mergeCells("A2:{$ultima}2");
        $hoja->mergeCells("A3:{$ultima}3");
        $hoja->getStyle("A1")->getFont()->setBold(true)->setSize(14);
        $hoja->getStyle("A2")->getFont()->setBold(true)->setSize(12);
        $hoja->getStyle("A3")->getFont()->setItalic(true)->getColor()->setRGB("555555");

        // Títulos de columnas (fila 5)
        $filaTitulos = 5;
        $hoja->fromArray($datos["columnas"], null, "A" . $filaTitulos);
        $estiloTitulos = $hoja->getStyle("A{$filaTitulos}:{$ultima}{$filaTitulos}");
        $estiloTitulos->getFont()->setBold(true)->getColor()->setRGB("FFFFFF");
        $estiloTitulos->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB("0F172A");
        $estiloTitulos->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Datos (se cargan como texto para que se vean igual que en pantalla)
        $fila = $filaTitulos + 1;
        foreach ($datos["filas"] as $registro) {
            $columna = 1;
            foreach ($registro as $valor) {
                $celda = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columna) . $fila;
                $hoja->setCellValueExplicit($celda, $valor, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $columna++;
            }
            $fila++;
        }

        $ultimaFila = max($filaTitulos, $fila - 1);

        if ($ultimaFila > $filaTitulos) {
            $hoja->getStyle("A{$filaTitulos}:{$ultima}{$ultimaFila}")->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->getColor()->setRGB("D1D5DB");
            $hoja->setAutoFilter("A{$filaTitulos}:{$ultima}{$ultimaFila}");
        }

        $hoja->setCellValue("A" . ($ultimaFila + 2), "Total de registros: " . count($datos["filas"]));
        $hoja->getStyle("A" . ($ultimaFila + 2))->getFont()->setBold(true);

        // Ancho de columnas
        for ($i = 1; $i <= $totalColumnas; $i++) {
            $letra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $hoja->getColumnDimension($letra)->setAutoSize(true);
        }

        $hoja->freezePane("A" . ($filaTitulos + 1));

        return $libro;
    }

    private function exportarExcel($datos)
    {
        $libro = $this->generarExcel($datos);

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment; filename="' . $datos["archivo"] . '.xlsx"');
        header("Cache-Control: max-age=0");

        $escritor = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($libro);
        $escritor->save("php://output");
        exit();
    }
}

/* ------------------------------------------------------------------ RUTEO */

if (basename($_SERVER["SCRIPT_FILENAME"] ?? "") === basename(__FILE__) || defined("REPORTE_EXPORTAR")) {

    $controlador = new ReporteController();
    $accion = $_GET["accion"] ?? "index";

    if ($accion === "exportar") {
        $controlador->exportar();
    } else {
        $controlador->mostrar($accion);
    }
}