<?php

require_once "../../modelos/Herramienta.php";
require_once "../../config/permisos.php";

verificarPermiso("herramientas");

$herramienta = new Herramienta();
$herramientas = $herramienta->obtenerTodos();

$totalHerramientas = count($herramientas);

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title no-print">

        <div>

            <h1>
                Herramientas
            </h1>

            <p>
                Administración y control del inventario de herramientas.
            </p>

        </div>

    </div>

    <div class="table-container">

        <div class="print-header">

            <h1>
                Empresa Constructora
            </h1>

            <h2>
                Reporte de Herramientas
            </h2>

            <p>
                Listado de herramientas registradas en inventario.
            </p>

            <p>

                Fecha de generación:
                <?= date("d/m/Y H:i"); ?>

                <br>

                Generado por:
                <?= $_SESSION["usuario"]["nombre"] . " " . $_SESSION["usuario"]["apellido"]; ?>

            </p>

        </div>

        <div class="toolbar no-print">

            <div class="toolbar-left">

                <div class="search-container">

                    <input
                        type="text"
                        id="buscarHerramienta"
                        class="search-box"
                        placeholder="Buscar herramienta...">

                </div>

            </div>

            <div style="display: flex; flex-direction: column; margin: 20px;">

                <button
                    onclick="window.print()"
                    class="btn btn-primary"
                    style="margin-bottom: 10px;">

                    <i class="fa-solid fa-print"></i>
                    Imprimir

                </button>

                <a
                    href="agregar.php"
                    class="btn btn-primary">

                    <i class="fa-solid fa-plus"></i>
                    Agregar

                </a>

            </div>

        </div>

        <table
            class="table"
            id="tablaHerramientas">

            <thead>

                <tr>

                    <th>
                        Herramienta
                    </th>

                    <th>
                        Marca
                    </th>

                    <th>
                        Tipo
                    </th>

                    <th>
                        Cantidad
                    </th>

                    <th>
                        Fecha
                    </th>

                    <th>
                        Costo
                    </th>

                    <th class="no-print">
                        Acciones
                    </th>

                </tr>

            </thead>

            <tbody>
                <?php foreach ($herramientas as $h) { ?>

                    <tr>

                        <td>

                            <div class="tool-name">

                                <span>
                                    <?= htmlspecialchars($h["nombre"]); ?>
                                </span>

                            </div>

                        </td>

                        <td>
                            <?= $h["marca"]; ?>
                        </td>

                        <td>

                            <?= htmlspecialchars($h["tipo"]); ?>

                        </td>


                        <td>

                            <?php if ($h["cantidad_total"] <= 2) { ?>

                                <span class="text-danger">

                                    <strong>

                                        <?= $h["cantidad_total"]; ?>

                                    </strong>

                                </span>

                            <?php } else { ?>

                                <?= $h["cantidad_total"]; ?>

                            <?php } ?>

                        </td>

                        <td>

                            <?= date("d/m/Y", strtotime($h["fecha_adquisicion"])); ?>

                        </td>

                        <td>

                            $ <?= number_format($h["costo"], 2, ",", "."); ?>

                        </td>

                        <td class="no-print">

                            <div class="table-actions">

                                <a href="/empresa_constructora/controladores/HerramientaController.php?accion=editar&id=<?= $h['id_herramienta'] ?>"
                                    class="btn btn-warning">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <a
                                    href="/empresa_constructora/controladores/HerramientaController.php?accion=ver&id=<?= $h["id_herramienta"] ?>"
                                    class="btn btn-secondary">

                                    <i class="fa-solid fa-eye"></i>

                                </a>

                            </div>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</main>

<?php

$script = "herramientas";
require_once "../../layouts/footer.php";

?>