<?php

require_once "../../modelos/Usuario.php";
require_once "../../modelos/EmpleadoObra.php";
require_once "../../config/permisos.php";

verificarPermiso("empleados");

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET["id"]);

$usuarioModel = new Usuario();
$empleadoObraModel = new EmpleadoObra();

$empleado = $usuarioModel->buscarPorId($id);

if (!$empleado || $empleado["nombre_rol"] != "Empleado") {
    header("Location: index.php");
    exit;
}


/*
==========================================================
    CARGOS DEL EMPLEADO
==========================================================
*/

$cargos = $usuarioModel->obtenerCargosEmpleado($id);


/*
==========================================================
    OBRAS DEL EMPLEADO
==========================================================
*/

$conexion = $empleadoObraModel->getConexion();

$sql = "SELECT
            eo.id_empleado_obra,
            eo.id_obra,
            eo.fecha_ingreso,
            eo.fecha_egreso,
            eo.estado,
            eo.observaciones,
            o.nombre_obra

        FROM empleado_obra eo

        INNER JOIN obra o
            ON eo.id_obra = o.id_obra

        WHERE eo.id_usuario = ?

        ORDER BY
            eo.estado DESC,
            eo.fecha_ingreso DESC";

$stmt = $conexion->prepare($sql);

$stmt->execute([$id]);

$obras = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
==========================================================
    RESUMEN DE OBRAS
==========================================================
*/

$totalObras = count($obras);

$obrasActivas = 0;
$obrasRetiradas = 0;

foreach ($obras as $obra) {

    if ($obra["estado"] == 1) {

        $obrasActivas++;

    } else {

        $obrasRetiradas++;

    }
}


require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">


    <!-- ==================================================
        TÍTULO
    ================================================== -->

    <div class="page-title no-print">

        <h1>

            <?= htmlspecialchars(
                $empleado["nombre"] . " " . $empleado["apellido"]
            ); ?>

        </h1>

        <p>
            Información completa del empleado.
        </p>

    </div>


    <!-- ==================================================
        RESUMEN
    ================================================== -->

    <div class="alert-container">

        <div class="alert alert-info">

            <p>
                Total Obras
            </p>

            <h3>
                <?= $totalObras; ?>
            </h3>

        </div>


        <div class="alert alert-success">

            <p>
                Obras Activas
            </p>

            <h3>
                <?= $obrasActivas; ?>
            </h3>

        </div>


        <div class="alert alert-warning">

            <p>
                Obras Retiradas
            </p>

            <h3>
                <?= $obrasRetiradas; ?>
            </h3>

        </div>

    </div>


    <!-- ==================================================
        DATOS DEL EMPLEADO
    ================================================== -->

    <div class="table-container">

        <h2 style="margin-bottom:20px;">
            Datos del Empleado
        </h2>

        <table class="table">

            <tbody>


                <tr>

                    <th>
                        Nombre Completo
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            $empleado["nombre"] . " " .
                            $empleado["apellido"]
                        ); ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Documento
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            $empleado["documento"] ?? "No registrado"
                        ); ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Correo
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            $empleado["correo"] ?? "No registrado"
                        ); ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Teléfono
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            $empleado["telefono"] ?? "No registrado"
                        ); ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Dirección
                    </th>

                    <td>

                        <?= !empty($empleado["direccion"])
                            ? htmlspecialchars($empleado["direccion"])
                            : "No registrada"; ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Salario
                    </th>

                    <td>

                        <?php if (
                            isset($empleado["salario"]) &&
                            $empleado["salario"] !== "" &&
                            $empleado["salario"] !== null
                        ) { ?>

                            $<?= number_format(
                                $empleado["salario"],
                                2,
                                ",",
                                "."
                            ); ?>

                        <?php } else { ?>

                            No registrado

                        <?php } ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Rol
                    </th>

                    <td>
                        Empleado
                    </td>

                </tr>


                <tr>

                    <th>
                        Estado
                    </th>

                    <td>

                        <span class="<?= $empleado["estado"] == 1
                            ? "badge badge-success"
                            : "badge badge-danger"; ?>">

                            <?= $empleado["estado"] == 1
                                ? "Activo"
                                : "Inactivo"; ?>

                        </span>

                    </td>

                </tr>


            </tbody>

        </table>

    </div>


    <!-- ==================================================
        CARGOS
    ================================================== -->

    <div class="table-container">

        <div class="toolbar">

            <div class="toolbar-left">

                <h2>
                    Cargos del Empleado
                </h2>

            </div>

        </div>


        <table class="table">

            <thead>

                <tr>

                    <th>
                        Cargo
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($cargos)) { ?>

                    <tr>

                        <td>
                            No posee cargos registrados.
                        </td>

                    </tr>

                <?php } else { ?>

                    <?php foreach ($cargos as $cargo) { ?>

                        <tr>

                            <td>

                                <?= htmlspecialchars(
                                    $cargo["nombre_cargo"]
                                ); ?>

                            </td>

                        </tr>

                    <?php } ?>

                <?php } ?>

            </tbody>

        </table>

    </div>


    <!-- ==================================================
        OBRAS ASIGNADAS
    ================================================== -->

    <div class="table-container">

        <div class="toolbar">

            <div class="toolbar-left">

                <h2>
                    Obras Asignadas
                </h2>

            </div>


            <div>

                <a
                    href="index.php"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Volver

                </a>

            </div>

        </div>


        <table class="table">

            <thead>

                <tr>

                    <th>
                        Obra
                    </th>

                    <th>
                        Fecha de ingreso
                    </th>

                    <th>
                        Fecha de egreso
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Acciones
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($obras)) { ?>

                    <tr>

                        <td colspan="5">

                            No posee obras asignadas.

                        </td>

                    </tr>

                <?php } else { ?>


                    <?php foreach ($obras as $obra) { ?>

                        <tr>


                            <!-- OBRA -->

                            <td>

                                <?= htmlspecialchars(
                                    $obra["nombre_obra"]
                                ); ?>

                            </td>


                            <!-- FECHA INGRESO -->

                            <td>

                                <?=
                                    !empty($obra["fecha_ingreso"])
                                        ? date(
                                            "d/m/Y",
                                            strtotime(
                                                $obra["fecha_ingreso"]
                                            )
                                        )
                                        : "-";
                                ?>

                            </td>


                            <!-- FECHA EGRESO -->

                            <td>

                                <?=
                                    !empty($obra["fecha_egreso"])
                                        ? date(
                                            "d/m/Y",
                                            strtotime(
                                                $obra["fecha_egreso"]
                                            )
                                        )
                                        : "-";
                                ?>

                            </td>


                            <!-- ESTADO -->

                            <td>

                                <?php if ($obra["estado"] == 1) { ?>

                                    <span class="badge badge-success">

                                        Activo

                                    </span>

                                <?php } else { ?>

                                    <span class="badge badge-danger">

                                        Retirado

                                    </span>

                                <?php } ?>

                            </td>


                            <!-- ACCIONES -->

                            <td>

                                <a
                                    href="../obras/ver.php?id=<?= $obra["id_obra"]; ?>"
                                    class="btn btn-info">

                                    <i class="fa-solid fa-eye"></i>

                                </a>

                            </td>


                        </tr>

                    <?php } ?>


                <?php } ?>

            </tbody>

        </table>

    </div>

</main>


<?php

require_once "../../layouts/footer.php";

?>