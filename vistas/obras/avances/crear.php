<?php


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

                Registrar avance

            </h1>


            <p class="page-subtitle">

                Agregue una nueva actividad realizada en la obra.

            </p>

        </div>



        <a
            href="/empresa_constructora/controladores/AvanceController.php?accion=listar&id_obra=<?= $id_obra ?>"
            class="btn btn-secondary">


            <i class="fa-solid fa-arrow-left"></i>

            Volver


        </a>



    </div>





    <div class="card">


        <form 
            action="/empresa_constructora/controladores/AvanceController.php?accion=guardar"
            method="POST">



            <input 
                type="hidden"
                name="id_obra"
                value="<?= $id_obra ?>">





            <div class="form-group">


                <label>

                    Fecha

                </label>
                <input
                    type="date"
                    name="fecha"
                    class="form-control input"
                    value="<?= date('Y-m-d') ?>"
                    required>
            </div>
            <div class="form-group">
                <label>
                    Actividad realizada
                </label>
                <textarea
                    name="descripcion"
                    class="form-control input"
                    rows="5"
                    placeholder="Describa las tareas realizadas..."
                    required></textarea>
            </div>
            <div class="form-actions">
                <a
                    href="/empresa_constructora/controladores/AvanceController.php?accion=listar&id_obra=<?= $id_obra ?>"
                    class="btn btn-secondary">
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="btn btn-primary">
                    <i class="fa-solid fa-save"></i>
                    Guardar avance
                </button>
            </div>
        </form>
    </div>
</main>




<?php

require_once __DIR__ . "/../../../layouts/footer.php";

?>