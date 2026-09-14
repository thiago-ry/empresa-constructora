<?php

require_once "../../modelos/Obra.php";
require_once "../../config/permisos.php";

verificarPermiso("obras");

$obra = new Obra();

/*
|--------------------------------------------------------------------------
| USUARIO Y ROL ACTUAL
|--------------------------------------------------------------------------
*/

$id_usuario = $_SESSION["usuario"]["id"];
$id_rol = $_SESSION["usuario"]["id_rol"];


$obras = $obra->obtenerObrasSegunUsuario($id_usuario, $id_rol);

$estados = $obra->obtenerEstados();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<main class="content">
    <div class="page-title no-print">

        <h1>Obras</h1>

        <p>
            Administración de obras registradas.
        </p>

    </div>

    <div class="table-container">

        <!-- ENCABEZADO PARA IMPRESIÓN -->

        <div class="print-header">

            <h1>Empresa Constructora</h1>

            <h2>Reporte de Obras</h2>

            <p>
                Listado de obras registradas en el sistema.
            </p>

            <p>

                Fecha de generación:
                <?= date("d/m/Y H:i"); ?>

                <br>

                Generado por:
                <?= htmlspecialchars(
                    $_SESSION["usuario"]["nombre"] . " " .
                        $_SESSION["usuario"]["apellido"]
                ); ?>

            </p>

        </div>


        <!-- BARRA DE HERRAMIENTAS -->

        <div class="toolbar no-print">

            <div class="toolbar-left">

                <input
                    type="text"
                    id="buscarObra"
                    class="search-box"
                    placeholder="Buscar obra...">

                <select
                    id="filtroEstado"
                    class="filter">

                    <option value="">
                        Todos los estados
                    </option>

                    <?php foreach ($estados as $estado) { ?>

                        <option value="<?= strtolower($estado); ?>">
                            <?= htmlspecialchars($estado); ?>
                        </option>

                    <?php } ?>

                </select>

            </div>


            <!-- BOTONES -->

            <div
                style="
                display: flex;
                flex-direction: column;
                margin: 20px;
            ">

                <button
                    onclick="window.print()"
                    class="btn btn-primary"
                    style="margin-bottom: 10px;">

                    <i class="fa-solid fa-print"></i>

                    Imprimir

                </button>


                <?php if ($id_rol == 2) { ?>

                    <a
                        href="agregar.php"
                        class="btn btn-primary">

                        <i class="fa-solid fa-plus"></i>

                        Agregar

                    </a>

                <?php } ?>

            </div>

        </div>


        <!-- TABLA -->

        <table
            class="table"
            id="tablaObras">

            <thead>

                <tr>

                    <th>Obra</th>

                    <th>Cliente</th>

                    <th>Jefe de Obra</th>

                    <th>Capataz</th>

                    <th>Dirección</th>

                    <th>Inicio</th>

                    <th>Fin</th>

                    <th>Estado</th>

                    <th class="no-print">
                        Acciones
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php if (!empty($obras)) { ?>

                    <?php foreach ($obras as $o) { ?>

                        <tr>

                            <!-- OBRA -->

                            <td>

                                <?= htmlspecialchars(
                                    $o["nombre_obra"]
                                ); ?>

                            </td>


                            <!-- CLIENTE -->

                            <td>

                                <?= htmlspecialchars(
                                    $o["nombre_cliente"] . " " .
                                        $o["apellido_cliente"]
                                ); ?>

                            </td>


                            <!-- JEFE DE OBRA -->

                            <td>

                                <?php if (
                                    !empty($o["nombre_jefe_obra"])
                                ) { ?>

                                    <?= htmlspecialchars(
                                        $o["nombre_jefe_obra"] . " " .
                                            $o["apellido_jefe_obra"]
                                    ); ?>

                                <?php } else { ?>

                                    <span>
                                        Sin asignar
                                    </span>

                                <?php } ?>

                            </td>


                            <!-- CAPATAZ -->

                            <td>

                                <?php if (
                                    !empty($o["nombre_capataz"])
                                ) { ?>

                                    <?= htmlspecialchars(
                                        $o["nombre_capataz"] . " " .
                                            $o["apellido_capataz"]
                                    ); ?>

                                <?php } else { ?>

                                    <span>
                                        Sin asignar
                                    </span>

                                <?php } ?>

                            </td>


                            <!-- DIRECCIÓN -->

                            <td>

                                <?= htmlspecialchars(
                                    $o["direccion"]
                                ); ?>

                            </td>


                            <!-- FECHA INICIO -->

                            <td>

                                <?= htmlspecialchars(
                                    $o["fecha_inicio"]
                                ); ?>

                            </td>


                            <!-- FECHA FIN -->

                            <td>

                                <?= htmlspecialchars(
                                    $o["fecha_fin"]
                                ); ?>

                            </td>


                            <!-- ESTADO -->

                            <td>

                                <?= htmlspecialchars(
                                    $o["estado"]
                                ); ?>

                            </td>


                            <!-- ACCIONES -->

                            <td class="no-print">

                                <div class="table-actions">

                                    <a
                                        href="ver.php?id=<?= $o["id_obra"]; ?>"
                                        class="btn btn-secondary"
                                        title="Ver obra">

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    <?php if ($id_rol == 2) { ?>

                                        <a
                                            href="editar.php?id=<?= $o["id_obra"]; ?>"
                                            class="btn btn-warning"
                                            title="Editar obra">

                                            <i class="fa-solid fa-pen-to-square"></i>

                                        </a>

                                    <?php } ?>

                                </div>

                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>

                        <td
                            colspan="9"
                            style="text-align: center;">

                            No hay obras disponibles para mostrar.

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</main>

<?php

$script = "obras";

require_once "../../layouts/footer.php";

?>