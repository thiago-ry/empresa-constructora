<?php

require_once "../../modelos/Reporte.php";
require_once "../../config/permisos.php";

verificarPermiso("reportes");

$reporte = new Reporte();

$empleados = $reporte->empleados();

$totalEmpleados = count($empleados);

$empleadosActivos = 0;
$empleadosInactivos = 0;
$empleadosAsignados = 0;
$empleadosSinObra = 0;

$cargos = [];

foreach ($empleados as $empleado) {

    if ((int)$empleado["estado"] === 1) {
        $empleadosActivos++;
    } else {
        $empleadosInactivos++;
    }

    if ((int)$empleado["cantidad_obras"] > 0) {
        $empleadosAsignados++;
    } else {
        $empleadosSinObra++;
    }

    $listaCargos = $empleado["nombre_cargo"] ?? "Sin cargo";

    foreach (explode(",", $listaCargos) as $cargo) {

        $cargo = trim($cargo);

        if ($cargo === "") {
            $cargo = "Sin cargo";
        }

        if (!isset($cargos[$cargo])) {
            $cargos[$cargo] = 0;
        }

        $cargos[$cargo]++;
    }
}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title no-print">

        <div>

            <h1>
                Reporte de Empleados
            </h1>

            <p>
                Información del personal, cargos y asignaciones a obras.
            </p>

        </div>

    </div>


    <div class="alert-container no-print">
        <div class="alert alert-primary">
            <p>
                Total Empleados
            </p>
            <h3>
                <?= $totalEmpleados ?>
            </h3>
        </div>


        <div class="alert alert-success">

            <p>
                Empleados Activos
            </p>

            <h3>
                <?= $empleadosActivos ?>
            </h3>

        </div>


        <div class="alert alert-warning">

            <p>
                Asignados a Obras
            </p>

            <h3>
                <?= $empleadosAsignados ?>
            </h3>

        </div>


        <div class="alert alert-info">

            <p>
                Sin Obra Asignada
            </p>

            <h3>
                <?= $empleadosSinObra ?>
            </h3>

        </div>

    </div>

    <div class="table-container">

        <div class="print-header">

            <h1>
                Empresa Constructora
            </h1>

            <h2>
                Reporte General de Empleados
            </h2>

            <p>
                Fecha de generación:
                <?= date("d/m/Y H:i"); ?>
            </p>

            <p>
                Generado por:
                <?= htmlspecialchars(
                    ($_SESSION["usuario"]["nombre"] ?? "") .
                        " " .
                        ($_SESSION["usuario"]["apellido"] ?? "")
                ); ?>
            </p>

        </div>


        <!-- ==================================================
         BARRA DE HERRAMIENTAS
    ================================================== -->

        <div class="toolbar no-print">

            <div class="toolbar-left">

                <input
                    type="text"
                    id="buscarEmpleado"
                    class="search-box"
                    placeholder="Buscar empleado...">


                <select
                    id="filtroEstado"
                    class="filter">

                    <option value="">
                        Todos los estados
                    </option>

                    <option value="1">
                        Activos
                    </option>

                    <option value="0">
                        Inactivos
                    </option>

                </select>


                <select
                    id="filtroAsignacion"
                    class="filter">

                    <option value="">
                        Todas las asignaciones
                    </option>

                    <option value="asignado">
                        Con obra
                    </option>

                    <option value="sin-obra">
                        Sin obra
                    </option>

                </select>


                <select
                    id="filtroCargo"
                    class="filter">

                    <option value="">
                        Todos los cargos
                    </option>

                    <?php foreach ($cargos as $cargo => $cantidad) { ?>

                        <option value="<?= htmlspecialchars($cargo) ?>">
                            <?= htmlspecialchars($cargo) ?>
                        </option>

                    <?php } ?>

                </select>

            </div>


       <div class="toolbar-right">

    <button onclick="window.print()" class="btn btn-primary">
        <i class="fa-solid fa-print"></i>
        Imprimir
    </button>

    <a href="../../reportes/pdf_empleados.php"
       class="btn btn-danger">

        <i class="fa-solid fa-file-pdf"></i>
        PDF

    </a>

    <a href="../../reportes/excel_empleados.php"
       class="btn btn-success">

        <i class="fa-solid fa-file-excel"></i>
        Excel

    </a>

</div>

        </div>


        <!-- ==================================================
         TABLA
    ================================================== -->

        <table
            class="table"
            id="tablaEmpleados">

            <thead>

                <tr>

                    <th>
                        Empleado
                    </th>

                    <th>
                        Documento
                    </th>

                    <th>
                        Cargo
                    </th>

                    <th>
                        Teléfono
                    </th>

                    <th>
                        Obras asignadas
                    </th>

                    <th>
                        Salario
                    </th>

                    <th>
                        Estado
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php foreach ($empleados as $empleado) { ?>

                    <?php

                    $asignado =
                        ((int)$empleado["cantidad_obras"] > 0);

                    ?>

                    <tr

                        data-estado="<?= (int)$empleado["estado"] ?>"

                        data-asignacion="<?= $asignado
                                                ? "asignado"
                                                : "sin-obra"
                                            ?>"

                        data-cargo="<?= htmlspecialchars(
                                        $empleado["nombre_cargo"] ?? "Sin cargo"
                                    ) ?>">

                        <!-- EMPLEADO -->

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $empleado["apellido"] .
                                        ", " .
                                        $empleado["nombre"]
                                ) ?>

                            </strong>

                            <br>

                            <small>

                                <?= htmlspecialchars(
                                    $empleado["correo"]
                                ) ?>

                            </small>

                        </td>


                        <!-- DOCUMENTO -->

                        <td>

                            <?= htmlspecialchars(
                                $empleado["documento"] ?? "-"
                            ) ?>

                        </td>


                        <!-- CARGO -->

                        <td>

                            <?= htmlspecialchars(
                                $empleado["nombre_cargo"] ??
                                    "Sin cargo"
                            ) ?>

                        </td>


                        <!-- TELEFONO -->

                        <td>

                            <?= htmlspecialchars(
                                $empleado["telefono"] ?? "-"
                            ) ?>

                        </td>


                        <!-- OBRAS -->

                        <td>

                            <?php if ($asignado) { ?>

                                <span class="badge badge-info">

                                    <?= htmlspecialchars(
                                        $empleado["obras"]
                                    ) ?>

                                </span>

                            <?php } else { ?>

                                <span class="badge badge-secondary">

                                    Sin obra

                                </span>

                            <?php } ?>

                        </td>


                        <!-- SALARIO -->

                        <td>

                            <?php if (
                                $empleado["salario"] !== null &&
                                $empleado["salario"] !== ""
                            ) { ?>

                                $<?= number_format(
                                        (float)$empleado["salario"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>

                            <?php } else { ?>

                                -

                            <?php } ?>

                        </td>


                        <!-- ESTADO -->

                        <td>

                            <?php if (
                                (int)$empleado["estado"] === 1
                            ) { ?>

                                <span class="badge badge-success">

                                    Activo

                                </span>

                            <?php } else { ?>

                                <span class="badge badge-danger">

                                    Inactivo

                                </span>

                            <?php } ?>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>


    <!-- ==================================================
     GRAFICO
================================================== -->

    <div
        class="card"
        style="margin-top:20px;">

        <div class="card-header">

            <div>

                <h2>
                    Empleados por Cargo
                </h2>

                <p>
                    Distribución del personal según su cargo.
                </p>

            </div>

        </div>


        <canvas id="graficoCargos"></canvas>

    </div>

</main>

<!-- ==================================================
     CHART.JS
================================================== -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- ==================================================
     BUSCADOR Y FILTROS
================================================== -->

<script>
    const buscarEmpleado =
        document.getElementById("buscarEmpleado");

    const filtroEstado =
        document.getElementById("filtroEstado");

    const filtroAsignacion =
        document.getElementById("filtroAsignacion");

    const filtroCargo =
        document.getElementById("filtroCargo");


    function filtrarEmpleados() {

        const texto =
            buscarEmpleado.value.toLowerCase();

        const estado =
            filtroEstado.value;

        const asignacion =
            filtroAsignacion.value;

        const cargo =
            filtroCargo.value;

        const filas =
            document.querySelectorAll(
                "#tablaEmpleados tbody tr"
            );


        filas.forEach(fila => {

            const contenido =
                fila.textContent.toLowerCase();

            const estadoFila =
                fila.dataset.estado;

            const asignacionFila =
                fila.dataset.asignacion;

            const cargoFila =
                fila.dataset.cargo;


            const coincideTexto =
                contenido.includes(texto);


            const coincideEstado =
                estado === "" ||
                estadoFila === estado;


            const coincideAsignacion =
                asignacion === "" ||
                asignacionFila === asignacion;


            const coincideCargo =
                cargo === "" ||
                cargoFila === cargo;


            if (
                coincideTexto &&
                coincideEstado &&
                coincideAsignacion &&
                coincideCargo
            ) {

                fila.style.display = "";

            } else {

                fila.style.display = "none";

            }

        });

    }


    buscarEmpleado.addEventListener(
        "keyup",
        filtrarEmpleados
    );

    filtroEstado.addEventListener(
        "change",
        filtrarEmpleados
    );

    filtroAsignacion.addEventListener(
        "change",
        filtrarEmpleados
    );

    filtroCargo.addEventListener(
        "change",
        filtrarEmpleados
    );
</script>

<!-- ==================================================
     GRAFICO
================================================== -->

<script>
    const etiquetasCargos = [

        <?php foreach ($cargos as $cargo => $cantidad) { ?>

            <?= json_encode($cargo) ?>,

        <?php } ?>

    ];


    const cantidadesCargos = [

        <?php foreach ($cargos as $cargo => $cantidad) { ?>

            <?= (int)$cantidad ?>,

        <?php } ?>

    ];


    new Chart(

        document.getElementById("graficoCargos"),

        {

            type: "bar",

            data: {

                labels: etiquetasCargos,

                datasets: [

                    {

                        label: "Cantidad de empleados",

                        data: cantidadesCargos

                    }

                ]

            },


            options: {

                responsive: true,

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            precision: 0

                        }

                    }

                }

            }

        }

    );
</script>

<?php

$script = "reportes";

require_once "../../layouts/footer.php";

?>