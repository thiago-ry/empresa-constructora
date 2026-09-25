
<?php

session_start();

require_once "../../modelos/Empleado.php";
require_once "../../config/permisos.php";

verificarPermiso("empleados");

$empleado = new Empleado();

$busqueda = trim($_GET["busqueda"] ?? "");
$estado = $_GET["estado"] ?? "";
$id_cargo = $_GET["id_cargo"] ?? "";


$cargos = $empleado->obtenerTodosLosCargos();

$empleados = $empleado->obtenerTodos(
    $busqueda,
    $estado,
    $id_cargo
);

$estadisticas = $empleado->obtenerEstadisticas();

$totalEmpleados = $estadisticas["total"] ?? 0;
$empleadosActivos = $estadisticas["activos"] ?? 0;
$empleadosInactivos = $estadisticas["inactivos"] ?? 0;


require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title no-print">

        <h1>
            Empleados
        </h1>

        <p>
            Gestión y administración del personal.
        </p>

    </div>


    <div class="table-container">


        <div class="print-header">

            <h1>
                Empresa Constructora
            </h1>

            <h2>
                Reporte de Empleados
            </h2>

            <p>
                Listado de empleados registrados en el sistema.
            </p>

            <p>

                Fecha de generación:

                <?= date("d/m/Y H:i"); ?>

                <br>

                Generado por:

                <?= htmlspecialchars(
                    ($_SESSION["usuario"]["nombre"] ?? "") .
                    " " .
                    ($_SESSION["usuario"]["apellido"] ?? "")
                ); ?>

            </p>

        </div>


        <div class="toolbar no-print">

            <form
                method="GET"
                style="display: contents;"
            >

                <div class="toolbar-left">


                    <input
                        type="text"
                        name="busqueda"
                        class="search-box"
                        placeholder="Buscar empleado..."
                        value="<?= htmlspecialchars($busqueda); ?>"
                    >


                    <select
                        name="estado"
                        class="filter"
                    >

                        <option value="">
                            Todos los estados
                        </option>

                        <option
                            value="1"
                            <?= $estado === "1" ? "selected" : ""; ?>
                        >
                            Activos
                        </option>

                        <option
                            value="0"
                            <?= $estado === "0" ? "selected" : ""; ?>
                        >
                            Inactivos
                        </option>

                    </select>


                    <select
                        name="id_cargo"
                        class="filter"
                    >

                        <option value="">
                            Todos los cargos
                        </option>

                        <?php foreach ($cargos as $cargo): ?>

                            <option
                                value="<?= $cargo["id_cargo"]; ?>"
                                <?= (string) $id_cargo ===
                                    (string) $cargo["id_cargo"]
                                    ? "selected"
                                    : ""; ?>
                            >

                                <?= htmlspecialchars(
                                    $cargo["nombre_cargo"]
                                ); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>


                    <?php
                    if (
                        $busqueda !== "" ||
                        $estado !== "" ||
                        $id_cargo !== ""
                    ):
                    ?>

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >

                            <i class="fa-solid fa-rotate-left"></i>

                            Limpiar

                        </a>

                    <?php endif; ?>


                </div>

            </form>


            <div
                style="
                    display: flex;
                    flex-direction: column;
                    margin: 20px;
                "
            >

                <button
                    onclick="window.print()"
                    class="btn btn-primary"
                    style="margin-bottom: 10px;"
                >

                    <i class="fa-solid fa-print"></i>

                    Imprimir

                </button>


                <a
                    href="agregar.php"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-plus"></i>

                    Agregar empleado

                </a>

            </div>

        </div>


        <?php
        if (
            $busqueda !== "" ||
            $estado !== "" ||
            $id_cargo !== ""
        ):
        ?>

            <div
                class="toolbar no-print"
                style="
                    justify-content: flex-start;
                    gap: 10px;
                    padding: 5px 20px 15px;
                    flex-wrap: wrap;
                "
            >

                <strong>
                    Filtros aplicados:
                </strong>


                <?php if ($busqueda !== ""): ?>

                    <span class="badge badge-primary">

                        Búsqueda:

                        <?= htmlspecialchars($busqueda); ?>

                    </span>

                <?php endif; ?>


                <?php if ($estado === "1"): ?>

                    <span class="badge badge-success">

                        Estado: Activo

                    </span>

                <?php elseif ($estado === "0"): ?>

                    <span class="badge badge-danger">

                        Estado: Inactivo

                    </span>

                <?php endif; ?>


                <?php if ($id_cargo !== ""): ?>

                    <?php

                    $nombreCargoSeleccionado = "";

                    foreach ($cargos as $cargo) {

                        if (
                            (string) $cargo["id_cargo"] ===
                            (string) $id_cargo
                        ) {

                            $nombreCargoSeleccionado =
                                $cargo["nombre_cargo"];

                            break;
                        }
                    }

                    ?>

                    <?php if ($nombreCargoSeleccionado !== ""): ?>

                        <span class="badge badge-primary">

                            Cargo:

                            <?= htmlspecialchars(
                                $nombreCargoSeleccionado
                            ); ?>

                        </span>

                    <?php endif; ?>

                <?php endif; ?>


            </div>

        <?php endif; ?>


        <table
            class="table"
            id="tablaEmpleados"
        >

            <thead>

                <tr>

                    <th>
                        Nombre
                    </th>

                    <th>
                        Documento
                    </th>

                    <th>
                        Correo
                    </th>

                    <th>
                        Cargo
                    </th>

                    <th>
                        Salario
                    </th>

                    <th>
                        Estado
                    </th>

                    <th class="no-print">
                        Acciones
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php if (empty($empleados)): ?>

                    <tr>

                        <td
                            colspan="7"
                            style="
                                text-align: center;
                                padding: 30px;
                            "
                        >

                            <?php
                            if (
                                $busqueda !== "" ||
                                $estado !== "" ||
                                $id_cargo !== ""
                            ):
                            ?>

                                <i
                                    class="fa-solid fa-filter"
                                    style="
                                        font-size: 25px;
                                        margin-bottom: 10px;
                                    "
                                ></i>

                                <br>

                                No se encontraron empleados
                                con los filtros seleccionados.

                            <?php else: ?>

                                No hay empleados registrados.

                            <?php endif; ?>

                        </td>

                    </tr>


                <?php else: ?>


                    <?php foreach ($empleados as $empleadoActual): ?>

                        <tr>


                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $empleadoActual["nombre"] .
                                        " " .
                                        $empleadoActual["apellido"]
                                    ); ?>

                                </strong>

                                <br>

                                <small>

                                    ID:

                                    <?= htmlspecialchars(
                                        $empleadoActual["id_usuario"]
                                    ); ?>

                                </small>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $empleadoActual["documento"]
                                ); ?>

                            </td>


                            <td
                                title="<?= htmlspecialchars(
                                    $empleadoActual["correo"]
                                ); ?>"
                            >

                                <?php

                                $correo =
                                    $empleadoActual["correo"];

                                echo strlen($correo) > 20
                                    ? htmlspecialchars(
                                        substr(
                                            $correo,
                                            0,
                                            20
                                        )
                                    ) . "..."
                                    : htmlspecialchars($correo);

                                ?>

                            </td>


                            <td>

                                <?php

                                if (
                                    !empty(
                                        $empleadoActual["cargos"]
                                    )
                                ) {

                                    $listaCargos =
                                        explode(
                                            ", ",
                                            $empleadoActual["cargos"]
                                        );

                                    foreach (
                                        $listaCargos
                                        as $cargo
                                    ) {

                                        echo '<span
                                                class="badge badge-primary"
                                                style="margin: 2px;"
                                            >'
                                            .
                                            htmlspecialchars($cargo)
                                            .
                                            '</span>';
                                    }

                                } else {

                                    echo '<span
                                            class="badge badge-secondary"
                                        >
                                            Sin cargo
                                          </span>';
                                }

                                ?>

                            </td>


                            <td>

                                <?php

                                if (
                                    $empleadoActual["salario"] !== null
                                    &&
                                    $empleadoActual["salario"] !== ""
                                ) {

                                    echo "$ " .
                                        number_format(
                                            (float)
                                            $empleadoActual["salario"],
                                            2,
                                            ",",
                                            "."
                                        );

                                } else {

                                    echo "No especificado";
                                }

                                ?>

                            </td>


                            <td>

                                <?php
                                if (
                                    $empleadoActual["estado"] == 1
                                ):
                                ?>

                                    <span class="badge badge-success">

                                        Activo

                                    </span>

                                <?php else: ?>

                                    <span class="badge badge-danger">

                                        Inactivo

                                    </span>

                                <?php endif; ?>

                            </td>


                            <td class="no-print">

                                <div class="table-actions">


                                    <a
                                        href="ver.php?id=<?= $empleadoActual["id_usuario"]; ?>"
                                        class="btn btn-secondary"
                                        title="Ver empleado"
                                    >

                                        <i
                                            class="fa-solid fa-eye"
                                        ></i>

                                    </a>


                                    <a
                                        href="editar.php?id=<?= $empleadoActual["id_usuario"]; ?>"
                                        class="btn btn-warning"
                                        title="Editar empleado"
                                    >

                                        <i
                                            class="fa-solid fa-pen-to-square"
                                        ></i>

                                    </a>


                                </div>

                            </td>


                        </tr>

                    <?php endforeach; ?>


                <?php endif; ?>


            </tbody>

        </table>


    </div>

</main>


<?php

$script = "empleados";

require_once "../../layouts/footer.php";

?>