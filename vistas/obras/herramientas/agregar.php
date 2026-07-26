<?php


$herramientas = $herramientas ?? [];


$id_obra = $id_obra ?? $_GET["id_obra"];


require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");


require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>

<main class="content">


    <div class="page-header">


        <div>

            <h1 class="page-title">

                Asignar herramienta

            </h1>


            <p class="page-subtitle">

                Registre una herramienta utilizada en la obra.

            </p>

        </div>



        <a
            href="/empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=<?= $id_obra ?>"
            class="btn btn-secondary">


            <i class="fa-solid fa-arrow-left"></i>

            Volver


        </a>



    </div>






    <div class="card">


        <form

            action="/empresa_constructora/controladores/HerramientaObraController.php?accion=guardar"

            method="POST">



            <input

                type="hidden"

                name="id_obra"

                value="<?= $id_obra ?>">







            <div class="form-group">


                <label>

                    Herramienta

                </label>


                <select

                    name="id_herramienta"

                    class="form-control input"

                    required>


                    <option value="">

                        Seleccione una herramienta

                    </option>



                    <?php foreach($herramientas as $herramienta) { ?>


<option
    value="<?= $herramienta["id_herramienta"] ?>">

    <?= htmlspecialchars($herramienta["nombre"]) ?>
    (<?= $herramienta["disponibles"] ?> disponibles)

</option>



                    <?php } ?>


                </select>


            </div>







            <div class="form-group">


                <label>

                    Cantidad

                </label>


                <input

                    type="number"

                    name="cantidad"

                    class="form-control input"

                    min="1"

                    value="1"

                    required>


            </div>








            <div class="form-group">


                <label>

                    Fecha de asignación

                </label>


                <input

                    type="date"

                    name="fecha_asignacion"

                    class="form-control input"

                    value="<?= date('Y-m-d') ?>"

                    required>


            </div>








            <div class="form-group">


                <label>

                    Observaciones

                </label>


                <textarea

                    name="observaciones"

                    class="form-control input"

                    rows="5"

                    placeholder="Ingrese observaciones sobre la herramienta..."></textarea>


            </div>







            <div class="form-actions">


                <a

                    href="/empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=<?= $id_obra ?>"

                    class="btn btn-secondary">


                    Cancelar


                </a>





                <button

                    type="submit"

                    class="btn btn-primary">


                    <i class="fa-solid fa-save"></i>


                    Asignar herramienta


                </button>



            </div>



        </form>


    </div>


</main>





<?php

require_once __DIR__ . "/../../../layouts/footer.php";

?>