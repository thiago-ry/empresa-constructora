<?php

require_once "../../modelos/Reporte.php";
require_once "../../modelos/Etapa.php";
require_once "../../config/permisos.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$obras = $reporte->obras();

$etapaModel = new Etapa();

$totalObras = count($obras);
$obrasActivas = 0;
$obrasFinalizadas = 0;
$obrasPausadas = 0;
$avanceTotal = 0;

$mayorAvance = -1;
$obraMayorAvance = "Sin datos";

$menorAvance = 101;
$obraMenorAvance = "Sin datos";

foreach ($obras as $obra) {
    $avance = (int)$etapaModel->calcularAvance($obra["id_obra"]);
    $avanceTotal += $avance;

    if ($avance > $mayorAvance) {
        $mayorAvance = $avance;
        $obraMayorAvance = $obra["nombre_obra"];
    }

    if ($avance < $menorAvance) {
        $menorAvance = $avance;
        $obraMenorAvance = $obra["nombre_obra"];
    }

    if ($obra["estado"] == "Activa") {
        $obrasActivas++;
    } elseif ($obra["estado"] == "Finalizada") {
        $obrasFinalizadas++;
    } elseif ($obra["estado"] == "Pausada") {
        $obrasPausadas++;
    }
}

$promedioAvance = $totalObras > 0 ? round($avanceTotal / $totalObras) : 0;

if ($totalObras === 0) {
    $mayorAvance = 0;
    $menorAvance = 0;
    $obraMayorAvance = "Sin datos";
    $obraMenorAvance = "Sin datos";
}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<main class="content">

    <div class="page-title no-print">
        <div>
            <h1>Reporte General de Obras</h1>
            <p>Administración, seguimiento detallado y análisis integral de las obras registradas.</p>
        </div>
    </div>

    <!-- Contadores Dinámicos -->
    <div class="alert-container no-print" id="kpiContainer">
        <div class="alert alert-primary">
            <p>Total de Obras</p>
            <h3 id="kpiTotal"><?= $totalObras ?></h3>
        </div>
        <div class="alert alert-warning">
            <p>Promedio de Avance</p>
            <h3 id="kpiPromedio"><?= $promedioAvance ?>%</h3>
        </div>
        <div class="alert alert-success">
            <p>Mayor Avance</p>
            <h3><?= $mayorAvance ?>%</h3>
        </div>
        <div class="alert alert-danger">
            <p>Obras Pausadas</p>
            <h3 id="kpiPausadas"><?= $obrasPausadas ?></h3>
        </div>
    </div>

    <div class="table-container">

        <div class="print-header">
            <h1>Empresa Constructora</h1>
            <h2>Reporte General de Obras</h2>
            <p>Fecha de generación: <?= date("d/m/Y H:i"); ?></p>
            <p>Generado por: <?= htmlspecialchars(($_SESSION["usuario"]["nombre"] ?? "") . " " . ($_SESSION["usuario"]["apellido"] ?? "")); ?></p>
        </div>

        <div class="toolbar no-print">
            <div class="toolbar-left">
                <input type="text" id="buscarObra" class="search-box" placeholder="Buscar por obra, cliente, responsable...">
                <select id="filtroEstado" class="filter">
                    <option value="">Todos los estados</option>
                    <option value="Activa">Activas</option>
                    <option value="Finalizada">Finalizadas</option>
                    <option value="Pausada">Pausadas</option>
                </select>
            </div>
            <div class="toolbar-right">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fa-solid fa-print"></i> Imprimir
                </button>
                <a href="../../reportes/pdf_obras.php" class="btn btn-danger">
                    <i class="fa-solid fa-file-pdf"></i> PDF
                </a>
                <a href="../../reportes/excel_obras.php" class="btn btn-success">
                    <i class="fa-solid fa-file-excel"></i> Excel
                </a>
            </div>
        </div>

        <table class="table" id="tablaObras">
            <thead>
                <tr>
                    <th>Obra</th>
                    <th>Cliente</th>
                    <th>Responsable</th>
                    <th>Dirección</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Avance</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($obras as $obra) { 
                    $avance = (int)$etapaModel->calcularAvance($obra["id_obra"]);
                ?>
                    <tr data-estado="<?= htmlspecialchars($obra["estado"]) ?>">
                        <td>
                            <strong><?= htmlspecialchars($obra["nombre_obra"]) ?></strong>
                        </td>
                        <td>
                            <?= htmlspecialchars($obra["nombre_cliente"] ?? "Sin asignar") ?>
                        </td>
                        <td>
                            <span class="text-muted"><i class="fa-solid fa-user-tie"></i> <?= htmlspecialchars($obra["responsable"] ?? "Sin asignar") ?></span>
                        </td>
                        <td>
                            <?= htmlspecialchars($obra["direccion"]) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($obra["fecha_inicio"]) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($obra["fecha_fin"]) ?>
                        </td>
                        <td style="min-width:220px;">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $avance ?>%;">
                                    <?= $avance ?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $clase = "badge badge-secondary";
                            if ($obra["estado"] == "Activa") {
                                $clase = "badge badge-success";
                            } elseif ($obra["estado"] == "Finalizada") {
                                $clase = "badge badge-primary";
                            } elseif ($obra["estado"] == "Pausada") {
                                $clase = "badge badge-danger";
                            }
                            ?>
                            <span class="<?= $clase ?>">
                                <?= htmlspecialchars($obra["estado"]) ?>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

    <!-- Alertas de Extremos de Avance -->
    <div class="alert-container no-print" style="margin-top:20px;">
        <div class="alert alert-success">
            <p>Obra con mayor avance</p>
            <h3><?= htmlspecialchars($obraMayorAvance) ?> (<?= $mayorAvance ?>%)</h3>
        </div>
        <div class="alert alert-warning">
            <p>Obra con menor avance</p>
            <h3><?= htmlspecialchars($obraMenorAvance) ?> (<?= $menorAvance ?>%)</h3>
        </div>
    </div>

    <!-- Gráfico Estadístico -->
    <div class="card no-print" style="margin-top:20px;">
        <div class="card-header">
            <div>
                <h2>Avance de las Obras</h2>
                <p>Comparación gráfica del porcentaje de avance de cada obra registrada en el sistema.</p>
            </div>
        </div>
        <div style="width:100%; height:450px; position:relative;">
            <canvas id="graficoAvance"></canvas>
        </div>
    </div>




    
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const buscarObra = document.getElementById("buscarObra");
    const filtroEstado = document.getElementById("filtroEstado");

    function filtrarObras() {
        const texto = buscarObra.value.toLowerCase().trim();
        const estado = filtroEstado.value;
        const filas = document.querySelectorAll("#tablaObras tbody tr");

        let visibles = 0;

        filas.forEach(fila => {
            const contenido = fila.textContent.toLowerCase();
            const estadoFila = fila.dataset.estado;

            const coincideTexto = contenido.includes(texto);
            const coincideEstado = (estado === "" || estadoFila === estado);

            if (coincideTexto && coincideEstado) {
                fila.style.display = "";
                visibles++;
            } else {
                fila.style.display = "none";
            }
        });

        // Actualizar contador total visible en vivo
        document.getElementById("kpiTotal").textContent = visibles;
    }

    buscarObra.addEventListener("keyup", filtrarObras);
    filtroEstado.addEventListener("change", filtrarObras);
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById("graficoAvance");
        if (!canvas || typeof Chart === "undefined") return;

        const etiquetasObras = <?= json_encode(array_map(fn($o) => $o["nombre_obra"], $obras), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const avancesObras = <?= json_encode(array_map(fn($o) => (int)$etapaModel->calcularAvance($o["id_obra"]), $obras)) ?>;

        new Chart(canvas, {
            type: "bar",
            data: {
                labels: etiquetasObras,
                datasets: [{
                    label: "Porcentaje de avance",
                    data: avancesObras,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { precision: 0, stepSize: 10 },
                        title: { display: true, text: "Porcentaje de avance (%)" }
                    },
                    x: {
                        title: { display: true, text: "Obras" }
                    }
                }
            }
        });
    });
</script>

<?php
$script = "reportes";
require_once "../../layouts/footer.php";
?>