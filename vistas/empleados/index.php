```php
<?php

session_start();

require_once "../../modelos/Conexion.php";
require_once "../../config/permisos.php";

verificarPermiso("empleados");

$db = new Conexion();
$conexion = $db->conectar();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";


/*
==================================================
    BUSCADOR
==================================================
*/

$busqueda = trim($_GET["busqueda"] ?? "");


/*
==================================================
    CONSULTAR EMPLEADOS
==================================================
*/

$sql = "SELECT
            u.id_usuario,
            u.nombre,
            u.apellido,
            u.documento,
            u.telefono,
            u.direccion,
            u.salario,
            u.correo,
            u.estado,

            GROUP_CONCAT(
                DISTINCT c.nombre_cargo
                ORDER BY c.nombre_cargo
                SEPARATOR ', '
            ) AS cargos

        FROM usuario u

        INNER JOIN roles r
            ON u.id_rol = r.id_rol

        LEFT JOIN empleado_cargo ec
            ON u.id_usuario = ec.id_usuario

        LEFT JOIN cargo c
            ON ec.id_cargo = c.id_cargo

        WHERE r.nombre_rol = 'Empleado'";


/*
==================================================
    BUSCADOR
==================================================
*/

if ($busqueda !== "") {

    $sql .= " AND (
                u.nombre LIKE :busqueda
                OR u.apellido LIKE :busqueda
                OR u.documento LIKE :busqueda
                OR u.telefono LIKE :busqueda
                OR u.correo LIKE :busqueda
                OR CONCAT(u.nombre, ' ', u.apellido)
                    LIKE :busqueda_completo
                OR CONCAT(u.apellido, ' ', u.nombre)
                    LIKE :busqueda_completo2
            )";
}


$sql .= "

        GROUP BY
            u.id_usuario,
            u.nombre,
            u.apellido,
            u.documento,
            u.telefono,
            u.direccion,
            u.salario,
            u.correo,
            u.estado

        ORDER BY
            u.apellido ASC,
            u.nombre ASC
";


$consulta = $conexion->prepare($sql);


/*
==================================================
    PARÁMETROS
==================================================
*/

if ($busqueda !== "") {

    $texto = "%" . $busqueda . "%";

    $consulta->bindValue(
        ":busqueda",
        $texto,
        PDO::PARAM_STR
    );

    $consulta->bindValue(
        ":busqueda_completo",
        $texto,
        PDO::PARAM_STR
    );

    $consulta->bindValue(
        ":busqueda_completo2",
        $texto,
        PDO::PARAM_STR
    );
}


$consulta->execute();

$empleados = $consulta->fetchAll(PDO::FETCH_ASSOC);


/*
==================================================
    ESTADÍSTICAS
==================================================
*/

$sqlEstadisticas = "SELECT

                        COUNT(*) AS total,

                        SUM(
                            CASE
                                WHEN u.estado = 1
                                THEN 1
                                ELSE 0
                            END
                        ) AS activos,

                        SUM(
                            CASE
                                WHEN u.estado = 0
                                THEN 1
                                ELSE 0
                            END
                        ) AS inactivos

                    FROM usuario u

                    INNER JOIN roles r
                        ON u.id_rol = r.id_rol

                    WHERE r.nombre_rol = 'Empleado'";


$consultaEstadisticas =
    $conexion->prepare($sqlEstadisticas);

$consultaEstadisticas->execute();

$estadisticas =
    $consultaEstadisticas->fetch(PDO::FETCH_ASSOC);


$totalEmpleados =
    $estadisticas["total"] ?? 0;

$empleadosActivos =
    $estadisticas["activos"] ?? 0;

$empleadosInactivos =
    $estadisticas["inactivos"] ?? 0;

?>

<main class="content">


    <!-- ==================================================
         TÍTULO
    ================================================== -->

    <div class="page-title no-print">

        <h1>
            Empleados
        </h1>

        <p>
            Gestión y administración del personal.
        </p>

    </div>


    <!-- ==================================================
         CONTENEDOR PRINCIPAL
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


        <!-- ==================================================
             BARRA DE HERRAMIENTAS
        ================================================== -->

        <div class="toolbar no-print">


            <div class="toolbar-left">


                <!-- BUSCADOR -->

                <form
                    method="GET"
                    style="display: flex; gap: 10px; align-items: center;"
                >

                    <input
                        type="text"
                        name="busqueda"
                        class="search-box"
                        placeholder="Buscar empleado..."
                        value="<?= htmlspecialchars($busqueda); ?>"
                    >


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Buscar
                    </button>


                    <?php if ($busqueda !== ""): ?>

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            <i class="fa-solid fa-rotate-left"></i>
                            Limpiar
                        </a>

                    <?php endif; ?>

                </form>


            </div>


            <!-- BOTONES -->

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


        <!-- ==================================================
             ESTADÍSTICAS
        ================================================== -->

        <div
            class="toolbar no-print"
            style="
                justify-content: flex-start;
                gap: 15px;
                padding: 10px 20px;
            "
        >

            <div class="badge badge-primary">
                Total:
                <?= $totalEmpleados; ?>
            </div>

            <div class="badge badge-success">
                Activos:
                <?= $empleadosActivos; ?>
            </div>

            <div class="badge badge-danger">
                Inactivos:
                <?= $empleadosInactivos; ?>
            </div>

        </div>


        <!-- ==================================================
             TABLA
        ================================================== -->

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
                            colspan="8"
                            style="text-align: center;"
                        >

                            <?php if ($busqueda !== ""): ?>

                                No se encontraron empleados
                                con esa búsqueda.

                            <?php else: ?>

                                No hay empleados registrados.

                            <?php endif; ?>

                        </td>

                    </tr>


                <?php else: ?>


                    <?php foreach ($empleados as $empleado): ?>

                        <tr>


                            <!-- NOMBRE -->

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $empleado["nombre"] .
                                        " " .
                                        $empleado["apellido"]
                                    ); ?>
                                </strong>

                                <br>

                                <small>
                                    ID:
                                    <?= htmlspecialchars(
                                        $empleado["id_usuario"]
                                    ); ?>
                                </small>

                            </td>


                            <!-- DOCUMENTO -->

                            <td>

                                <?= htmlspecialchars(
                                    $empleado["documento"]
                                ); ?>

                            </td>


                            <!-- CORREO -->

                            <td
                                title="<?= htmlspecialchars(
                                    $empleado["correo"]
                                ); ?>"
                            >

                                <?php

                                $correo =
                                    $empleado["correo"];

                                echo strlen($correo) > 20
                                    ? htmlspecialchars(
                                        substr($correo, 0, 20)
                                    ) . "..."
                                    : htmlspecialchars($correo);

                                ?>

                            </td>


                            <!-- CARGO -->

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $empleado["cargos"]
                                    )
                                ) {

                                    $listaCargos =
                                        explode(
                                            ", ",
                                            $empleado["cargos"]
                                        );

                                    foreach (
                                        $listaCargos
                                        as $cargo
                                    ) {

                                        echo '<span class="badge badge-primary" style="margin: 2px;">'
                                            . htmlspecialchars($cargo)
                                            . '</span>';
                                    }

                                } else {

                                    echo '<span class="badge badge-secondary">
                                            Sin cargo
                                          </span>';
                                }

                                ?>

                            </td>


                            <!-- SALARIO -->

                            <td>

                                <?php

                                if (
                                    $empleado["salario"] !== null
                                    &&
                                    $empleado["salario"] !== ""
                                ) {

                                    echo "$ " .
                                        number_format(
                                            (float)
                                            $empleado["salario"],
                                            2,
                                            ",",
                                            "."
                                        );

                                } else {

                                    echo "No especificado";

                                }

                                ?>

                            </td>


                            <!-- ESTADO -->

                            <td>

                                <?php if (
                                    $empleado["estado"] == 1
                                ): ?>

                                    <span
                                        class="badge badge-success"
                                    >
                                        Activo
                                    </span>

                                <?php else: ?>

                                    <span
                                        class="badge badge-danger"
                                    >
                                        Inactivo
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ACCIONES -->

                            <td class="no-print">

                                <div class="table-actions">


                                    <!-- VER -->

                                    <a
                                        href="ver.php?id=<?= $empleado["id_usuario"]; ?>"
                                        class="btn btn-primary"
                                        title="Ver empleado"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </a>


                                    <!-- EDITAR -->

                                    <a
                                        href="editar.php?id=<?= $empleado["id_usuario"]; ?>"
                                        class="btn btn-warning"
                                        title="Editar empleado"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
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
```
