<?php


if (!isset($registro)) {

    header("Location: ../../../controladores/AvanceController.php?accion=listar");

    exit();

}


require_once __DIR__ . "/../../../config/permisos.php";

verificarPermiso("obras");


require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>


<main class="content">


    <div class="page-header">


        <div>

            <h1 class="page-title">
                Editar avance
            </h1>

            <p class="page-subtitle">
                Modifique la información del avance diario registrado.
            </p>

        </div>



        <a
            href="/empresa_constructora/controladores/AvanceController.php?accion=listar&id_obra=<?= $registro["id_obra"] ?>"
            class="btn btn-secondary">


            <i class="fa-solid fa-arrow-left"></i>

            Volver


        </a>


    </div>





    <div class="card">



        <form
            action="/empresa_constructora/controladores/AvanceController.php?accion=actualizar"
            method="POST">



            <input
                type="hidden"
                name="id_avance_diario"
                value="<?= $registro["id_avance_diario"] ?>">



            <input
                type="hidden"
                name="id_obra"
                value="<?= $registro["id_obra"] ?>">






            <div class="form-group">


                <label>
                    Fecha
                </label>


                <input

                    type="date"

                    name="fecha"

                    class="form-control input"

                    value="<?= $registro["fecha"] ?>"

                    required

                >


            </div>








            <div class="form-group">


                <label>
                    Actividad realizada
                </label>



                <textarea

                    name="descripcion"

                    class="form-control input"

                    rows="6"

                    required

                ><?= htmlspecialchars($registro["descripcion"]) ?></textarea>


            </div>







            <div class="form-actions">


                <a

                    href="/empresa_constructora/controladores/AvanceController.php?accion=listar&id_obra=<?= $registro["id_obra"] ?>"

                    class="btn btn-secondary">


                    Cancelar


                </a>





                <button

                    type="submit"

                    class="btn btn-primary">


                    <i class="fa-solid fa-save"></i>

                    Guardar cambios


                </button>



            </div>




        </form>



    </div>



</main>



<?php

require_once __DIR__ . "/../../../layouts/footer.php";

?>