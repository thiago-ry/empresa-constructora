<?php

require_once "../../modelos/MovimientoMaterial.php";
require_once "../../modelos/Material.php";
require_once "../../config/permisos.php";

verificarPermiso("materiales");

$id_material = $_GET["id"] ?? 0;

$materialModel = new Material();
$movimientoModel = new MovimientoMaterial();

$material = $materialModel->buscar($id_material);

if (!$material) {

    header("Location: ../materiales/");
    exit;

}

$movimientos = $movimientoModel->obtenerPorMaterial($id_material);

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>
            Historial de Movimientos
        </h1>

        <p>
            Control de ingresos y egresos del material.
        </p>

    </div>

    <div class="alert-container">

        <div class="alert">

            <h3>
                Material
            </h3>

            <p>
                <?= $material["nombre_material"]; ?>
            </p>

        </div>

        <div class="alert">

            <h3>
                Stock Actual
            </h3>

            <p>
                <?= $material["stock"]; ?>
            </p>

        </div>

        <div class="alert">

            <h3>
                Total Movimientos
            </h3>

            <p>
                <?= count($movimientos); ?>
            </p>

        </div>

    </div>

    <div class="table-container">

        <div class="toolbar">

            <div class="toolbar-left">

                <input
                    type="text"
                    id="buscarMovimiento"
                    class="search-box"
                    placeholder="Buscar...">

            </div>

            <div>

                <a
                    href="agregar.php?id=<?= $material["id_material"]; ?>"
                    class="btn btn-success">

                    <i class="fa-solid fa-plus"></i>

                    Movimiento

                </a>

                <a
                    href="../materiales/"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Volver

                </a>

            </div>

        </div>

        <table
            class="table"
            id="tablaMovimientos">

            <thead>

                <tr>

                    <th>
                        Fecha
                    </th>

                    <th>
                        Tipo
                    </th>

                    <th>
                        Cantidad
                    </th>

                    <th>
                        Usuario
                    </th>

                    <th>
                        Observación
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($movimientos as $m) { ?>

                    <tr>

                        <td>

                            <?= date("d/m/Y H:i", strtotime($m["fecha"])); ?>

                        </td>

                        <td>

                            <?php if ($m["tipo"] == "INGRESO") { ?>

                                <span class="badge badge-success">

                                    INGRESO

                                </span>

                            <?php } else { ?>

                                <span class="badge badge-danger">

                                    EGRESO

                                </span>

                            <?php } ?>

                        </td>

                        <td>

                            <?= $m["cantidad"]; ?>

                        </td>

                        <td>

                            <?= $m["nombre"] . " " . $m["apellido"]; ?>

                        </td>

                        <td>

                            <?= $m["observacion"]; ?>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</main>

<script>

document.getElementById("buscarMovimiento")
.addEventListener("keyup", function(){

    let filtro = this.value.toLowerCase();

    document.querySelectorAll("#tablaMovimientos tbody tr")
    .forEach(function(fila){

        fila.style.display =
            fila.textContent.toLowerCase().includes(filtro)
            ? ""
            : "none";

    });

});

</script>

<?php

require_once "../../layouts/footer.php";

?>