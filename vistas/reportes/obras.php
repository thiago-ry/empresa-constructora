<?php

require_once "../../modelos/Reporte.php";
require_once "../../modelos/Etapa.php";
require_once "../../config/permisos.php";

verificarPermiso("reportes");

$reporte = new Reporte();
$obras = $reporte->obras();

$etapaModel = new Etapa();

/* ==================================================
   ESTADÍSTICAS GENERALES
================================================== */

$totalObras = count($obras);

$obrasActivas = 0;
$obrasFinalizadas = 0;
$obrasPausadas = 0;

$avanceTotal = 0;

$mayorAvance = -1;
$obraMayorAvance = "Sin datos";

$menorAvance = 101;
$obraMenorAvance = "Sin datos";

/* ==================================================
   CALCULAR ESTADÍSTICAS
================================================== */

foreach ($obras as $obra) {

    $avance = $etapaModel->calcularAvance(
        $obra["id_obra"]
    );

    $avance = (int)$avance;

    $avanceTotal += $avance;

    /* Mayor avance */

    if ($avance > $mayorAvance) {

        $mayorAvance = $avance;

        $obraMayorAvance =
            $obra["nombre_obra"];
    }

    /* Menor avance */

    if ($avance < $menorAvance) {

        $menorAvance = $avance;

        $obraMenorAvance =
            $obra["nombre_obra"];
    }

    /* Estados */

    if ($obra["estado"] == "Activa") {

        $obrasActivas++;
    }

    if ($obra["estado"] == "Finalizada") {

        $obrasFinalizadas++;
    }

    if ($obra["estado"] == "Pausada") {

        $obrasPausadas++;
    }
}

/* ==================================================
   PROMEDIO DE AVANCE
================================================== */

$promedioAvance = $totalObras > 0
    ? round($avanceTotal / $totalObras)
    : 0;

/* Si no existen obras */

if ($totalObras === 0) {

    $mayorAvance = 0;
    $menorAvance = 0;

    $obraMayorAvance = "Sin datos";
    $obraMenorAvance = "Sin datos";
}


/* ==================================================
   LAYOUT
================================================== */

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">


    <!-- ==================================================
         TITULO
    ================================================== -->

    <div class="page-title no-print">

        <div>

            <h1>
                Reporte de Obras
            </h1>

            <p>
                Administración, seguimiento y análisis
                de las obras registradas.
            </p>

        </div>

    </div>


    <!-- ==================================================
         ESTADÍSTICAS PRINCIPALES
    ================================================== -->

    <div class="alert-container no-print">


        <!-- TOTAL -->

        <div class="alert alert-primary">

            <p>
                Total de Obras
            </p>

            <h3>
                <?= $totalObras ?>
            </h3>

        </div>


        <!-- PROMEDIO -->

        <div class="alert alert-warning">

            <p>
                Promedio de Avance
            </p>

            <h3>
                <?= $promedioAvance ?>%
            </h3>

        </div>


        <!-- MAYOR AVANCE -->

        <div class="alert alert-success">

            <p>
                Mayor Avance
            </p>

            <h3>
                <?= $mayorAvance ?>%
            </h3>

        </div>


        <!-- PAUSADAS -->

        <div class="alert alert-danger">

            <p>
                Obras Pausadas
            </p>

            <h3>
                <?= $obrasPausadas ?>
            </h3>

        </div>

    </div>


    <!-- ==================================================
         TABLA
    ================================================== -->

    <div class="table-container">


        <!-- ==================================================
             ENCABEZADO PARA IMPRESIÓN
        ================================================== -->

        <div class="print-header">

            <h1>
                Empresa Constructora
            </h1>

            <h2>
                Reporte General de Obras
            </h2>

            <p>
                Fecha de generación:
                <?= date("d/m/Y H:i"); ?>
            </p>

            <p>

                Generado por:
                <?= htmlspecialchars(
                    ($_SESSION["usuario"]["nombre"] ?? "")
                        . " " .
                        ($_SESSION["usuario"]["apellido"] ?? "")
                ); ?>

            </p>

        </div>


        <!-- ==================================================
             BARRA DE HERRAMIENTAS
        ================================================== -->

        <div class="toolbar no-print">


            <div class="toolbar-left">


                <!-- BUSCADOR -->

                <input
                    type="text"
                    id="buscarObra"
                    class="search-box"
                    placeholder="Buscar obra...">


                <!-- FILTRO ESTADO -->

                <select
                    id="filtroEstado"
                    class="filter">

                    <option value="">
                        Todos los estados
                    </option>

                    <option value="Activa">
                        Activas
                    </option>

                    <option value="Finalizada">
                        Finalizadas
                    </option>

                    <option value="Pausada">
                        Pausadas
                    </option>

                </select>


            </div>


            <!-- ==================================================
                 BOTONES
            ================================================== -->

            <div class="toolbar-right">


                <button
                    onclick="window.print()"
                    class="btn btn-primary">

                    <i class="fa-solid fa-print"></i>

                    Imprimir

                </button>


                <a
                    href="../../reportes/pdf_obras.php"
                    class="btn btn-danger">

                    <i class="fa-solid fa-file-pdf"></i>

                    PDF

                </a>


                <a
                    href="../../reportes/excel_obras.php"
                    class="btn btn-success">

                    <i class="fa-solid fa-file-excel"></i>

                    Excel

                </a>


            </div>

        </div>


        <!-- ==================================================
             TABLA DE OBRAS
        ================================================== -->

        <table
            class="table"
            id="tablaObras">


            <thead>

                <tr>

                    <th>
                        Obra
                    </th>

                    <th>
                        Cliente
                    </th>

                    <th>
                        Dirección
                    </th>

                    <th>
                        Inicio
                    </th>

                    <th>
                        Fin
                    </th>

                    <th>
                        Avance
                    </th>

                    <th>
                        Estado
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php foreach ($obras as $obra) { ?>


                    <?php

                    $avance =
                        (int)$etapaModel->calcularAvance(
                            $obra["id_obra"]
                        );

                    ?>


                    <tr
                        data-estado="<?= htmlspecialchars(
                                            $obra["estado"]
                                        ) ?>">


                        <!-- OBRA -->

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $obra["nombre_obra"]
                                ) ?>

                            </strong>

                        </td>


                        <!-- CLIENTE -->

                        <td>

                            <?= htmlspecialchars(
                                $obra["nombre_cliente"]
                                    ?? "Sin asignar"
                            ) ?>

                        </td>


                        <!-- DIRECCIÓN -->

                        <td>

                            <?= htmlspecialchars(
                                $obra["direccion"]
                            ) ?>

                        </td>


                        <!-- INICIO -->

                        <td>

                            <?= htmlspecialchars(
                                $obra["fecha_inicio"]
                            ) ?>

                        </td>


                        <!-- FIN -->

                        <td>

                            <?= htmlspecialchars(
                                $obra["fecha_fin"]
                            ) ?>

                        </td>


                        <!-- AVANCE -->

                        <td
                            style="min-width:220px;">

                            <div class="progress">

                                <div
                                    class="progress-bar"
                                    style="
                                        width: <?= $avance ?>%;
                                    ">

                                    <?= $avance ?>%

                                </div>

                            </div>

                        </td>


                        <!-- ESTADO -->

                        <td>


                            <?php

                            $clase =
                                "badge badge-secondary";


                            if (
                                $obra["estado"]
                                == "Activa"
                            ) {

                                $clase =
                                    "badge badge-success";
                            }


                            if (
                                $obra["estado"]
                                == "Finalizada"
                            ) {

                                $clase =
                                    "badge badge-primary";
                            }


                            if (
                                $obra["estado"]
                                == "Pausada"
                            ) {

                                $clase =
                                    "badge badge-danger";
                            }

                            ?>


                            <span
                                class="<?= $clase ?>">

                                <?= htmlspecialchars(
                                    $obra["estado"]
                                ) ?>

                            </span>


                        </td>


                    </tr>


                <?php } ?>


            </tbody>

        </table>

    </div>


    <!-- ==================================================
         RESUMEN ADICIONAL
    ================================================== -->

    <div
        class="alert-container no-print"
        style="margin-top:20px;">


        <div class="alert alert-success">

            <p>
                Obra con mayor avance
            </p>

            <h3>

                <?= htmlspecialchars(
                    $obraMayorAvance
                ) ?>

            </h3>

        </div>

        <div class="alert alert-warning">

            <p>
                Obra con menor avance
            </p>

            <h3>

                <?= htmlspecialchars(
                    $obraMenorAvance
                ) ?>

            </h3>

        </div>

    </div>


    <!-- ==================================================
         GRÁFICO DE AVANCE
    ================================================== -->

    <div
        class="card no-print"
        style="margin-top:20px;">


        <div class="card-header">

            <div>

                <h2>
                    Avance de las Obras
                </h2>

                <p>
                    Comparación del porcentaje de avance
                    de cada obra registrada.
                </p>

            </div>

        </div>


        <!-- CONTENEDOR DEL GRÁFICO -->

        <div
            style="
                width:100%;
                height:450px;
                position:relative;
            ">

            <canvas
                id="graficoAvance">
            </canvas>

        </div>


    </div>


</main>


<!-- ==================================================
     CHART.JS
================================================== -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<!-- ==================================================
     BUSCADOR Y FILTRO
================================================== -->

<script>
    const buscarObra =
        document.getElementById("buscarObra");

    const filtroEstado =
        document.getElementById("filtroEstado");


    function filtrarObras() {

        const texto =
            buscarObra.value
            .toLowerCase()
            .trim();

        const estado =
            filtroEstado.value;


        const filas =
            document.querySelectorAll(
                "#tablaObras tbody tr"
            );


        filas.forEach(fila => {


            const contenido =
                fila.textContent
                .toLowerCase();


            const estadoFila =
                fila.dataset.estado;


            const coincideTexto =
                contenido.includes(texto);


            const coincideEstado =
                estado === "" ||
                estadoFila === estado;


            if (
                coincideTexto &&
                coincideEstado
            ) {

                fila.style.display = "";

            } else {

                fila.style.display = "none";

            }

        });

    }


    buscarObra.addEventListener(
        "keyup",
        filtrarObras
    );


    filtroEstado.addEventListener(
        "change",
        filtrarObras
    );
</script>


<!-- ==================================================
     GRÁFICO DE AVANCE
================================================== -->

<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {


            const canvas =
                document.getElementById(
                    "graficoAvance"
                );


            if (!canvas) {

                console.error(
                    "No existe el canvas #graficoAvance"
                );

                return;
            }


            if (
                typeof Chart === "undefined"
            ) {

                console.error(
                    "Chart.js no está cargado"
                );

                return;
            }


            /* ==================================================
               DATOS DESDE PHP
            ================================================== */

            const etiquetasObras =
                <?= json_encode(
                    array_map(
                        function ($obra) {

                            return $obra["nombre_obra"];
                        },
                        $obras
                    ),
                    JSON_UNESCAPED_UNICODE |
                        JSON_UNESCAPED_SLASHES
                ) ?>;


            const avancesObras =
                <?= json_encode(
                    array_map(
                        function ($obra) use ($etapaModel) {

                            return (int)$etapaModel->calcularAvance(
                                $obra["id_obra"]
                            );
                        },
                        $obras
                    )
                ) ?>;


            console.log(
                "Obras:",
                etiquetasObras
            );


            console.log(
                "Avances:",
                avancesObras
            );


            /* ==================================================
               CREAR GRÁFICO
            ================================================== */

            new Chart(
                canvas, {

                    type: "bar",


                    data: {

                        labels: etiquetasObras,


                        datasets: [

                            {

                                label: "Porcentaje de avance",


                                data: avancesObras,


                                borderWidth: 1

                            }

                        ]

                    },


                    options: {

                        responsive: true,


                        maintainAspectRatio: false,


                        plugins: {

                            legend: {

                                display: true

                            }

                        },


                        scales: {

                            y: {

                                beginAtZero: true,


                                max: 100,


                                ticks: {

                                    precision: 0,


                                    stepSize: 10

                                },


                                title: {

                                    display: true,


                                    text: "Porcentaje de avance"

                                }

                            },


                            x: {

                                title: {

                                    display: true,


                                    text: "Obras"

                                }

                            }

                        }

                    }

                }
            );


        }
    );
</script>


<?php

$script = "reportes";

require_once "../../layouts/footer.php";

?>