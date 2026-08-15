<?php

if (!isset($empleados)) {

    header(
        "Location: ../../../controladores/EmpleadoObraController.php?accion=listar&id_obra="
        . ($_GET["id_obra"] ?? 0)
    );

    exit();
}

require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

$id_obra = $_GET["id_obra"] ?? 0;

?>

<main class="content">


    <!-- =========================================================
         TÍTULO
    ========================================================== -->

    <div class="page-title no-print">

        <h1>
            Empleados de la Obra
        </h1>

        <p>
            Gestión del personal asignado a esta obra.
        </p>

    </div>



    <!-- =========================================================
         CONTENEDOR
    ========================================================== -->

    <div class="table-container">


        <!-- =====================================================
             MENSAJES
        ====================================================== -->

        <?php if (isset($_GET["success"])): ?>

            <?php if ($_GET["success"] === "reactivado"): ?>

                <div class="alert alert-success no-print">

                    <i class="fa-solid fa-circle-check"></i>

                    El empleado fue reactivado correctamente en esta obra.

                </div>

            <?php endif; ?>

        <?php endif; ?>


        <?php if (isset($_GET["error"])): ?>

            <div class="alert alert-danger no-print">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?php

                switch ($_GET["error"]) {

                    case "ya_activo":

                        echo "El empleado ya se encuentra activo en esta obra.";

                        break;

                    case "no_encontrado":

                        echo "No se encontró la asignación del empleado.";

                        break;

                    case "activar":

                        echo "No se pudo reactivar al empleado.";

                        break;

                    default:

                        echo "Ocurrió un error.";

                        break;
                }

                ?>

            </div>

        <?php endif; ?>



        <!-- =====================================================
             ENCABEZADO DE IMPRESIÓN
        ====================================================== -->

        <div class="print-header">

            <h1>
                Empresa Constructora
            </h1>

            <h2>
                Reporte de Empleados de Obra
            </h2>

            <p>
                Listado del personal asignado a la obra.
            </p>

            <p>

                Fecha de generación:
                <?= date("d/m/Y H:i"); ?>

                <br>

                Generado por:

                <?= htmlspecialchars(
                    ($_SESSION["usuario"]["nombre"] ?? "")
                    . " "
                    . ($_SESSION["usuario"]["apellido"] ?? "")
                ); ?>

            </p>

        </div>



        <!-- =====================================================
             TOOLBAR
        ====================================================== -->

        <div class="toolbar no-print">


            <!-- ================================================
                 IZQUIERDA
            ================================================= -->

            <div class="toolbar-left">


                <!-- BUSCADOR -->

                <input
                    type="text"
                    id="buscarEmpleadoObra"
                    class="search-box"
                    placeholder="Buscar empleado..."
                    autocomplete="off"
                >


                <!-- FILTRO ESTADO -->

                <select
                    class="filter"
                    id="filtroEstadoEmpleado"
                >

                    <option value="">
                        Todos los estados
                    </option>

                    <option value="activo">
                        Activos
                    </option>

                    <option value="retirado">
                        Retirados
                    </option>

                </select>


            </div>



            <!-- ================================================
                 DERECHA
            ================================================= -->

            <div
                style="
                    display:flex;
                    flex-direction:column;
                    gap:10px;
                    margin:20px;
                "
            >


                <!-- IMPRIMIR -->

                <button
                    type="button"
                    onclick="window.print()"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-print"></i>

                    Imprimir

                </button>


                <!-- AGREGAR -->

                <a
                    href="/empresa_constructora/controladores/EmpleadoObraController.php?accion=crear&id_obra=<?= htmlspecialchars($id_obra) ?>"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-plus"></i>

                    Agregar

                </a>


            </div>


        </div>



        <!-- =====================================================
             TABLA
        ====================================================== -->

        <div style="overflow-x:auto;">

            <table
                class="table"
                id="tablaEmpleadosObra"
            >

                <thead>

                    <tr>

                        <th>
                            Empleado
                        </th>

                        <th>
                            Documento
                        </th>

                        <th>
                            Teléfono
                        </th>

                        <th>
                            Cargo
                        </th>

                        <th>
                            Ingreso
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


                    <?php if (count($empleados) > 0): ?>


                        <?php foreach ($empleados as $empleado): ?>


                            <?php

                            /*
                            ==================================================
                                ESTADO
                            ==================================================
                            */

                            $activo =
                                ((int)($empleado["estado"] ?? 0) === 1);

                            ?>


                            <tr
                                data-estado="<?= $activo ? "activo" : "retirado" ?>"
                            >


                                <!-- ==========================================
                                     EMPLEADO
                                =========================================== -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $empleado["apellido"]
                                            . ", "
                                            . $empleado["nombre"]
                                        ) ?>

                                    </strong>

                                </td>



                                <!-- ==========================================
                                     DOCUMENTO
                                =========================================== -->

                                <td>

                                    <?= htmlspecialchars(
                                        $empleado["documento"] ?? "-"
                                    ) ?>

                                </td>



                                <!-- ==========================================
                                     TELÉFONO
                                =========================================== -->

                                <td>

                                    <?= htmlspecialchars(
                                        $empleado["telefono"] ?? "-"
                                    ) ?>

                                </td>



                                <!-- ==========================================
                                     CARGO
                                =========================================== -->

                                <td>

                                    <?php if (!empty($empleado["nombre_cargo"])): ?>

                                        <span
                                            style="
                                                display:inline-flex;
                                                align-items:center;
                                                gap:7px;
                                            "
                                        >

                                            <i class="fa-solid fa-briefcase"></i>

                                            <?= htmlspecialchars(
                                                $empleado["nombre_cargo"]
                                            ) ?>

                                        </span>

                                    <?php else: ?>

                                        <span style="opacity:.6;">
                                            Sin cargo
                                        </span>

                                    <?php endif; ?>

                                </td>



                                <!-- ==========================================
                                     FECHA INGRESO
                                =========================================== -->

                                <td>

                                    <?php if (!empty($empleado["fecha_ingreso"])): ?>

                                        <?= htmlspecialchars(
                                            date(
                                                "d/m/Y",
                                                strtotime(
                                                    $empleado["fecha_ingreso"]
                                                )
                                            )
                                        ) ?>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>



                                <!-- ==========================================
                                     ESTADO
                                =========================================== -->

                                <td>

                                    <?php if ($activo): ?>

                                        <span class="badge badge-success">

                                            <i
                                                class="fa-solid fa-circle"
                                                style="font-size:7px;"
                                            ></i>

                                            Activo

                                        </span>

                                    <?php else: ?>

                                        <span class="badge badge-danger">

                                            <i
                                                class="fa-solid fa-circle"
                                                style="font-size:7px;"
                                            ></i>

                                            Retirado

                                        </span>

                                    <?php endif; ?>

                                </td>



                                <!-- ==========================================
                                     ACCIONES
                                =========================================== -->

                                <td class="no-print">

                                    <div class="table-actions">


                                        <?php if ($activo): ?>


                                            <!-- =================================
                                                 EDITAR
                                            ================================== -->

                                            <a
                                                href="/empresa_constructora/vistas/obras/empleados/editar.php?id=<?= htmlspecialchars($empleado["id_empleado_obra"]) ?>"
                                                class="btn btn-warning"
                                                title="Editar asignación"
                                            >

                                                <i class="fa-solid fa-pen-to-square"></i>

                                            </a>



                                            <!-- =================================
                                                 RETIRAR
                                            ================================== -->

                                            <a
                                                href="/empresa_constructora/vistas/obras/empleados/retirar.php?id=<?= htmlspecialchars($empleado["id_empleado_obra"]) ?>"
                                                class="btn btn-danger"
                                                title="Retirar empleado"
                                            >

                                                <i class="fa-solid fa-user-minus"></i>

                                            </a>


                                        <?php else: ?>


                                            <!-- =================================
                                                 REACTIVAR
                                            ================================== -->

                                            <a
                                                href="/empresa_constructora/controladores/EmpleadoObraController.php?accion=activar&id=<?= htmlspecialchars($empleado["id_empleado_obra"]) ?>&id_obra=<?= htmlspecialchars($id_obra) ?>"
                                                class="btn btn-success"
                                                title="Reactivar empleado"
                                                onclick="return confirmarReactivacion(
                                                    '<?= htmlspecialchars(
                                                        $empleado["nombre"]
                                                        . " "
                                                        . $empleado["apellido"],
                                                        ENT_QUOTES
                                                    ) ?>'
                                                );"
                                            >

                                                <i class="fa-solid fa-user-check"></i>

                                            </a>


                                        <?php endif; ?>


                                    </div>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <!-- =================================================
                             SIN EMPLEADOS
                        ================================================== -->

                        <tr>

                            <td
                                colspan="7"
                                style="
                                    text-align:center;
                                    padding:50px 20px;
                                "
                            >

                                <i
                                    class="fa-solid fa-users"
                                    style="
                                        font-size:45px;
                                        opacity:.5;
                                    "
                                ></i>

                                <br><br>

                                <strong>
                                    Todavía no hay empleados asignados.
                                </strong>

                                <br>

                                <small>
                                    Esta obra todavía no tiene personal asignado.
                                </small>

                                <br><br>

                                <a
                                    href="/empresa_constructora/controladores/EmpleadoObraController.php?accion=crear&id_obra=<?= htmlspecialchars($id_obra) ?>"
                                    class="btn btn-primary no-print"
                                >

                                    <i class="fa-solid fa-user-plus"></i>

                                    Asignar empleado

                                </a>

                            </td>

                        </tr>


                    <?php endif; ?>


                </tbody>

            </table>

        </div>


    </div>

</main>



<!-- =========================================================
     BUSCADOR Y FILTROS
========================================================== -->

<script>

document.addEventListener("DOMContentLoaded", function() {


    const buscador =
        document.getElementById("buscarEmpleadoObra");


    const filtroEstado =
        document.getElementById("filtroEstadoEmpleado");


    const tabla =
        document.getElementById("tablaEmpleadosObra");


    const filas =
        tabla.querySelectorAll("tbody tr");


    function filtrarEmpleados() {


        const texto =
            buscador.value
                .toLowerCase()
                .trim();


        const estado =
            filtroEstado.value;


        filas.forEach(function(fila) {


            /*
            ==========================================
                FILAS ESPECIALES
            ==========================================
            */

            if (!fila.dataset.estado) {

                return;

            }


            const contenido =
                fila.textContent.toLowerCase();


            const estadoFila =
                fila.dataset.estado;


            const coincideTexto =
                contenido.includes(texto);


            const coincideEstado =
                estado === ""
                ||
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


    buscador.addEventListener(
        "input",
        filtrarEmpleados
    );


    filtroEstado.addEventListener(
        "change",
        filtrarEmpleados
    );


});



/*
=========================================================
    CONFIRMAR REACTIVACIÓN
=========================================================
*/

function confirmarReactivacion(nombre)
{

    return confirm(

        "¿Desea reactivar a "
        + nombre
        + " en esta obra?\n\n"
        + "El empleado volverá a figurar como ACTIVO."

    );

}

</script>



<?php

require_once __DIR__ . "/../../../layouts/footer.php";

?>