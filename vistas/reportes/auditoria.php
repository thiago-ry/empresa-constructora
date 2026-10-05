<?php

require_once "../../modelos/Auditoria.php";
require_once "../../config/permisos.php";

verificarPermiso("reportes");

$auditoria = new Auditoria();

$filtros = [
    "buscar" => $_GET["buscar"] ?? "",
    "tipo" => $_GET["tipo"] ?? "",
    "accion" => $_GET["accion_filtro"] ?? "",
    "modulo" => $_GET["modulo"] ?? "",
    "fecha_desde" => $_GET["fecha_desde"] ?? "",
    "fecha_hasta" => $_GET["fecha_hasta"] ?? ""
];

$registros = $auditoria->obtenerReporte($filtros);

$totalRegistros = count($registros);
$totalAccesos = 0;
$totalAuditorias = 0;
$ingresos = 0;
$sesionesAbiertas = 0;

foreach ($registros as $registro) {

    if (($registro["tipo"] ?? "") == "ACCESO") {
        $totalAccesos++;
        $ingresos++;

        if (empty($registro["fecha_egreso"])) {
            $sesionesAbiertas++;
        }
    }

    if (($registro["tipo"] ?? "") == "AUDITORIA") {
        $totalAuditorias++;
    }
}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte de Auditoría</h1>
            <p>Control y seguimiento de las acciones y accesos realizados en el sistema.</p>
        </div>
    </div>

    <div class="alert-container no-print">

        <div class="alert alert-primary">
            <p>Total de Registros</p>
            <h3><?= $totalRegistros ?></h3>
        </div>

        <div class="alert alert-success">
            <p>Ingresos</p>
            <h3><?= $ingresos ?></h3>
        </div>

        <div class="alert alert-warning">
            <p>Acciones</p>
            <h3><?= $totalAuditorias ?></h3>
        </div>

        <div class="alert alert-danger">
            <p>Sesiones abiertas</p>
            <h3><?= $sesionesAbiertas ?></h3>
        </div>

    </div>

    <div class="table-container">

        <div class="print-header">
            <h1>Empresa Constructora</h1>
            <h2>Reporte General de Auditoría</h2>

            <p>
                Fecha de generación:
                <?= date("d/m/Y H:i"); ?>
            </p>

            <p>
                Generado por:
                <?= htmlspecialchars(
                    ($_SESSION["usuario"]["nombre"] ?? "") . " " .
                        ($_SESSION["usuario"]["apellido"] ?? "")
                ); ?>
            </p>
        </div>

        <div class="toolbar no-print">

            <div class="toolbar-left">

                <input
                    type="text"
                    id="buscarAuditoria"
                    class="search-box"
                    placeholder="Buscar usuario o descripción..."
                    value="<?= htmlspecialchars($filtros["buscar"]) ?>">

                <select id="filtroTipo" class="filter">

                    <option value="">Todos los tipos</option>

                    <option value="ACCESO"
                        <?= $filtros["tipo"] == "ACCESO" ? "selected" : "" ?>>
                        Accesos
                    </option>

                    <option value="AUDITORIA"
                        <?= $filtros["tipo"] == "AUDITORIA" ? "selected" : "" ?>>
                        Auditorías
                    </option>

                </select>

                <select id="filtroAccion" class="filter">

                    <option value="">Todas las acciones</option>

                    <option value="INGRESO"
                        <?= $filtros["accion"] == "INGRESO" ? "selected" : "" ?>>
                        Ingreso
                    </option>

                    <option value="INSERTAR"
                        <?= $filtros["accion"] == "INSERTAR" ? "selected" : "" ?>>
                        Insertar
                    </option>

                    <option value="EDITAR"
                        <?= $filtros["accion"] == "EDITAR" ? "selected" : "" ?>>
                        Editar
                    </option>

                    <option value="ELIMINAR"
                        <?= $filtros["accion"] == "ELIMINAR" ? "selected" : "" ?>>
                        Eliminar
                    </option>

                    <option value="BAJA"
                        <?= $filtros["accion"] == "BAJA" ? "selected" : "" ?>>
                        Baja
                    </option>

                    <option value="ACTIVAR"
                        <?= $filtros["accion"] == "ACTIVAR" ? "selected" : "" ?>>
                        Activar
                    </option>

                </select>

                <select id="filtroModulo" class="filter">

                    <option value="">Todos los módulos</option>

                    <option value="usuario"
                        <?= $filtros["modulo"] == "usuario" ? "selected" : "" ?>>
                        Usuarios
                    </option>

                    <option value="obra"
                        <?= $filtros["modulo"] == "obra" ? "selected" : "" ?>>
                        Obras
                    </option>

                    <option value="empleado_obra"
                        <?= $filtros["modulo"] == "empleado_obra" ? "selected" : "" ?>>
                        Empleados en obras
                    </option>

                    <option value="herramienta"
                        <?= $filtros["modulo"] == "herramienta" ? "selected" : "" ?>>
                        Herramientas
                    </option>

                    <option value="herramienta_obra"
                        <?= $filtros["modulo"] == "herramienta_obra" ? "selected" : "" ?>>
                        Herramientas en obras
                    </option>

                    <option value="devolucion_herramienta"
                        <?= $filtros["modulo"] == "devolucion_herramienta" ? "selected" : "" ?>>
                        Devoluciones de herramientas
                    </option>

                    <option value="solicitud_material"
                        <?= $filtros["modulo"] == "solicitud_material" ? "selected" : "" ?>>
                        Solicitudes de materiales
                    </option>

                    <option value="entrega_material"
                        <?= $filtros["modulo"] == "entrega_material" ? "selected" : "" ?>>
                        Entregas de materiales
                    </option>

                </select>

                <input
                    type="date"
                    id="fechaDesde"
                    class="filter"
                    value="<?= htmlspecialchars($filtros["fecha_desde"]) ?>">

                <input
                    type="date"
                    id="fechaHasta"
                    class="filter"
                    value="<?= htmlspecialchars($filtros["fecha_hasta"]) ?>">

            </div>

            <div class="toolbar-right">

                <button
                    type="button"
                    onclick="window.print()"
                    class="btn btn-primary">
                    <i class="fa-solid fa-print"></i>
                    Imprimir
                </button>

                <a
                    href="../../reportes/pdf_auditoria.php"
                    class="btn btn-danger">
                    <i class="fa-solid fa-file-pdf"></i>
                    PDF
                </a>

                <a
                    href="../../reportes/excel_auditoria.php"
                    class="btn btn-success">
                    <i class="fa-solid fa-file-excel"></i>
                    Excel
                </a>

            </div>

        </div>

        <table class="table" id="tablaAuditoria">

            <thead>

                <tr>
                    <th>Usuario</th>
                    <th>Ingreso</th>
                    <th>Egreso</th>
                    <th>Tipo</th>
                    <th>Acción</th>
                    <th>Módulo</th>
                    <th>Registro</th>
                    <th>Descripción</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($registros as $registro) { ?>

                    <?php

                    $tipo = $registro["tipo"] ?? "";
                    $accion = $registro["accion"] ?? "";
                    $modulo = $registro["modulo"] ?? "";

                    $usuario = $registro["usuario"] ?? "Usuario desconocido";

                    $fechaIngreso = $registro["fecha_hora"] ?? "";
                    $fechaEgreso = $registro["fecha_egreso"] ?? "";

                    $ingresoFormateado = !empty($fechaIngreso)
                        ? date("d/m/Y H:i", strtotime($fechaIngreso))
                        : "-";

                    $egresoFormateado = !empty($fechaEgreso)
                        ? date("d/m/Y H:i", strtotime($fechaEgreso))
                        : "-";

                    $claseTipo = "badge badge-secondary";

                    if ($tipo == "ACCESO") {
                        $claseTipo = "badge badge-success";
                    }

                    if ($tipo == "AUDITORIA") {
                        $claseTipo = "badge badge-primary";
                    }

                    $claseAccion = "badge badge-secondary";

                    if (
                        $accion == "INGRESO" ||
                        $accion == "INSERTAR" ||
                        $accion == "ACTIVAR"
                    ) {
                        $claseAccion = "badge badge-success";
                    }

                    if ($accion == "EDITAR") {
                        $claseAccion = "badge badge-warning";
                    }

                    if (
                        $accion == "ELIMINAR" ||
                        $accion == "BAJA"
                    ) {
                        $claseAccion = "badge badge-danger";
                    }
                    ?>

                    <tr
                        data-tipo="<?= htmlspecialchars($tipo) ?>"
                        data-accion="<?= htmlspecialchars($accion) ?>"
                        data-modulo="<?= htmlspecialchars($modulo) ?>">

                        <td>
                            <strong>
                                <?= htmlspecialchars($usuario) ?>
                            </strong>
                        </td>

                        <td>
                            <?= htmlspecialchars($ingresoFormateado) ?>
                        </td>

                        <td>

                            <?php if ($tipo == "ACCESO" && empty($fechaEgreso)) { ?>

                                <span class="badge badge-warning">
                                    En sesión
                                </span>

                            <?php } else { ?>

                                <?= htmlspecialchars($egresoFormateado) ?>

                            <?php } ?>

                        </td>

                        <td>
                            <span class="<?= $claseTipo ?>">
                                <?= htmlspecialchars($tipo) ?>
                            </span>
                        </td>

                        <td>
                            <span class="<?= $claseAccion ?>">
                                <?= htmlspecialchars($accion) ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars($modulo ?: "-") ?>
                        </td>

                        <td>

                            <?php if (!empty($registro["id_registro"])) { ?>

                                #<?= htmlspecialchars($registro["id_registro"]) ?>

                            <?php } else { ?>

                                -

                            <?php } ?>

                        </td>

                        <td>
                            <?= htmlspecialchars($registro["descripcion"] ?? "-") ?>
                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

        <?php if (empty($registros)) { ?>

            <div class="alert alert-warning" style="margin-top:20px;">
                <p>
                    No se encontraron registros de auditoría con los filtros seleccionados.
                </p>
            </div>

        <?php } ?>

    </div>

</main>

<script>
    const buscarAuditoria = document.getElementById("buscarAuditoria");
    const filtroTipo = document.getElementById("filtroTipo");
    const filtroAccion = document.getElementById("filtroAccion");
    const filtroModulo = document.getElementById("filtroModulo");
    const fechaDesde = document.getElementById("fechaDesde");
    const fechaHasta = document.getElementById("fechaHasta");

    function filtrarAuditoria() {

        const texto = buscarAuditoria.value.toLowerCase().trim();
        const tipo = filtroTipo.value;
        const accion = filtroAccion.value;
        const modulo = filtroModulo.value;
        const desde = fechaDesde.value;
        const hasta = fechaHasta.value;

        const filas = document.querySelectorAll("#tablaAuditoria tbody tr");

        filas.forEach(function(fila) {

            const contenido = fila.textContent.toLowerCase();

            const tipoFila = fila.dataset.tipo;
            const accionFila = fila.dataset.accion;
            const moduloFila = fila.dataset.modulo;

            let coincideTexto = contenido.includes(texto);
            let coincideTipo = tipo === "" || tipoFila === tipo;
            let coincideAccion = accion === "" || accionFila === accion;
            let coincideModulo = modulo === "" || moduloFila === modulo;

            let coincideFechaDesde = true;
            let coincideFechaHasta = true;

            const celdas = fila.children;

            if (celdas.length > 1 && (desde || hasta)) {

                const fechaTexto = celdas[1].textContent.trim();

                if (fechaTexto.includes("/")) {

                    const partes = fechaTexto.split(" ");

                    const fechaPartes = partes[0].split("/");

                    if (fechaPartes.length === 3) {

                        const fechaFila =
                            fechaPartes[2] +
                            "-" +
                            fechaPartes[1] +
                            "-" +
                            fechaPartes[0];

                        if (desde) {
                            coincideFechaDesde = fechaFila >= desde;
                        }

                        if (hasta) {
                            coincideFechaHasta = fechaFila <= hasta;
                        }

                    }

                }

            }

            if (
                coincideTexto &&
                coincideTipo &&
                coincideAccion &&
                coincideModulo &&
                coincideFechaDesde &&
                coincideFechaHasta
            ) {

                fila.style.display = "";

            } else {

                fila.style.display = "none";

            }

        });

    }

    buscarAuditoria.addEventListener("keyup", filtrarAuditoria);
    filtroTipo.addEventListener("change", filtrarAuditoria);
    filtroAccion.addEventListener("change", filtrarAuditoria);
    filtroModulo.addEventListener("change", filtrarAuditoria);
    fechaDesde.addEventListener("change", filtrarAuditoria);
    fechaHasta.addEventListener("change", filtrarAuditoria);
</script>

<?php

$script = "reportes";

require_once "../../layouts/footer.php";

?>