<?php

require_once "../../modelos/Material.php";
require_once "../../config/permisos.php";

verificarPermiso("materiales");

$material = new Material();
$materiales = $material->obtenerTodos();
$totalMateriales = count($materiales);
$stockCritico = 0;
$materialesActivos = 0;

foreach ($materiales as $m) {

    if ($m["stock"] <= $m["stock_minimo"]) {
        $stockCritico++;
    }

    if ($m["estado"] == 1) {
        $materialesActivos++;
    }
}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title no-print">

        <h1>
            Materiales
        </h1>

        <p>
            Administración y control del inventario de materiales.
        </p>

    </div>

    <div class="alert-container no-print">

        <div class="alert alert-danger">
            <p>
                Alertas de Stock Crítico
            </p>

            <h3>
                <?= $stockCritico ?> Materiales
            </h3>
        </div>

        <div class="alert alert-success">
            <p>
                Materiales Activos
            </p>

            <h3>
                <?= $materialesActivos ?> Materiales
            </h3>
        </div>

        <div class="alert alert-info">
            <p>
                Total Materiales
            </p>

            <h3>
                <?= $totalMateriales ?> Materiales
            </h3>
        </div>

    </div>

    <div class="table-container">

        <div class="print-header">

            <h1>
                Empresa Constructora
            </h1>

            <h2>
                Reporte de Materiales
            </h2>

            <p>
                Listado de materiales registrados en inventario.
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

                <input
                    type="text"
                    id="buscarMaterial"
                    class="search-box"
                    placeholder="Buscar material...">

                <select
                    id="filtroEstado"
                    class="filter">

                    <option value="">
                        Todos los estados
                    </option>

                    <option value="Activo">
                        Activos
                    </option>

                    <option value="Inactivo">
                        Inactivos
                    </option>

                </select>

                <select
                    id="filtroStock"
                    class="filter">

                    <option value="">
                        Todo el stock
                    </option>

                    <option value="critico">
                        Stock crítico
                    </option>

                    <option value="disponible">
                        Stock disponible
                    </option>

                </select>

            </div>
            <div>

                <button
                    onclick="window.print()"
                    class="btn btn-primary">
                    <i class="fa-solid fa-print"></i>
                </button>
                <a
                    href="agregar.php"
                    class="btn btn-primary">

                    <i class="fa-solid fa-plus"></i>

                </a>

            </div>

        </div>

        <table class="table" id="tablaMateriales">

            <thead>
                <tr>

                    <th>
                        Material
                    </th>

                    <th>
                        Descripción
                    </th>

                    <th>
                        Stock
                    </th>

                    <th>
                        Stock Mínimo
                    </th>

                    <th>
                        Unidad
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
                <?php foreach ($materiales as $m) { ?>

                    <tr
                        data-estado="<?= $m['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>"
                        data-stock="<?= $m['stock'] <= $m['stock_minimo'] ? 'critico' : 'disponible'; ?>">

                        <td>
                            <?= $m["nombre_material"]; ?>
                        </td>

                        <td title="<?= $m["descripcion"]; ?>">
                            <?= strlen($m["descripcion"]) > 40
                                ? substr($m["descripcion"], 0, 40) . "..."
                                : $m["descripcion"]; ?>
                        </td>

                        <td>
                            <?php if ($m["stock"] <= $m["stock_minimo"]) { ?>
                                <span class="badge badge-danger">
                                    <?= $m["stock"]; ?>
                                </span>
                            <?php } else { ?>
                                <?= $m["stock"]; ?>
                            <?php } ?>
                        </td>

                        <td>
                            <?= $m["stock_minimo"]; ?>
                        </td>

                        <td>
                            <?= $m["unidad_medida"]; ?>
                        </td>

                        <td>
                            <span class="<?= $m['estado'] == 1 ? 'badge badge-success' : 'badge badge-danger' ?>">
                                <?= $m['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>

                        <td class="no-print"
                            style="display:flex; flex-direction:row; justify-content:space-between;">
                            <a
                                href="editar.php?id=<?= $m["id_material"]; ?>"
                                class="btn btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <?php if ($m["estado"] == 1) { ?>
                                <a
                                    href="../../controladores/MaterialController.php?accion=eliminar&id=<?= $m["id_material"]; ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('¿Está seguro que desea dar de baja este material?');">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            <?php } else { ?>
                                <a
                                    href="../../controladores/MaterialController.php?accion=activar&id=<?= $m["id_material"]; ?>"
                                    class="btn btn-success">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                            <?php } ?>
                        </td>

                    </tr>

                <?php } ?>

            </tbody>
        </table>

    </div>

</main>

<?php

$script = "materiales";
require_once "../../layouts/footer.php";

?>