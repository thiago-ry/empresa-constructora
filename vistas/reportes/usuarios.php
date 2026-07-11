<?php require __DIR__ . "/../../layouts/header.php"; ?>
<?php require __DIR__ . "/../../layouts/sidebar.php"; ?>

<?php
if(!isset($usuarios)){
    $usuarios = [];
}
?>

<div class="main">


    <div class="report-container">


        <div class="report-header">


            <div class="report-title">

                <h1>
                    Reporte de Usuarios
                </h1>

                <p class="report-info">

                    Generado por:
                    <?= $_SESSION['usuario']['nombre']; ?>

                    <br>

                    Fecha:
                    <?= date("d/m/Y"); ?>
                    <?= date("H:i") ?>

                </p>

            </div>


            <div class="report-actions">

                <button class="btn btn-primary" onclick="window.print()">

                    Imprimir

                </button>


            </div>


        </div>



        <div class="table-container">


            <table class="table">


                <tr>

                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>

                </tr>


                <?php foreach ($usuarios as $u): ?>


                    <tr>

                        <td><?= $u["nombre"] ?></td>

                        <td><?= $u["apellido"] ?></td>

                        <td><?= $u["correo"] ?></td>

                        <td><?= $u["nombre_rol"] ?></td>


                        <td>

                            <?php if ($u["estado"] == 1): ?>

                                <span class="badge badge-success">
                                    Activo
                                </span>


                            <?php else: ?>


                                <span class="badge badge-danger">
                                    Inactivo
                                </span>


                            <?php endif; ?>


                        </td>


                    </tr>


                <?php endforeach; ?>


            </table>


        </div>


    </div>


</div>


<?php require __DIR__ . "/../../layouts/footer.php"; ?>