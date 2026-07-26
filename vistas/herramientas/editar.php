<?php

require_once __DIR__ . "/../../config/permisos.php";

verificarPermiso("herramientas");


require_once __DIR__ . "/../../layouts/header.php";
require_once __DIR__ . "/../../layouts/sidebar.php";


if (!isset($herramienta) || !is_array($herramienta)) {

    echo "No se encontraron los datos de la herramienta.";
    exit;

}

?>


<main class="content">


    <div class="page-header">

        <div>

            <h1 class="page-title">
                Editar herramienta
            </h1>


            <p class="page-subtitle">
                Actualice los datos de la herramienta.
            </p>

        </div>



        <a href="/empresa_constructora/vistas/herramientas/index.php"
           class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver

        </a>


    </div>



    <div class="card">


        <form action="/empresa_constructora/controladores/HerramientaController.php?accion=actualizar"
              method="POST">



            <input type="hidden"
                   name="id_herramienta"
                   value="<?= $herramienta['id_herramienta'] ?>">





            <div class="form-group">

                <label>
                    Nombre
                </label>

                <input type="text"
                       name="nombre"
                       class="form-control input"
                       value="<?= htmlspecialchars($herramienta['nombre']) ?>"
                       required>

            </div>






            <div class="form-group">

                <label>
                    Tipo
                </label>


                <input type="text"
                       name="tipo"
                       class="form-control input"
                       value="<?= htmlspecialchars($herramienta['tipo']) ?>"
                       required>

            </div>







            <div class="form-group">

                <label>
                    Marca
                </label>


                <input type="text"
                       name="marca"
                       class="form-control input"
                       value="<?= htmlspecialchars($herramienta['marca']) ?>">

            </div>







            <div class="form-group">

                <label>
                    Modelo
                </label>


                <input type="text"
                       name="modelo"
                       class="form-control input"
                       value="<?= htmlspecialchars($herramienta['modelo']) ?>">

            </div>







            <div class="form-group">

                <label>
                    Cantidad total
                </label>


                <input type="number"
                       name="cantidad_total"
                       class="form-control input"
                       min="0"
                       value="<?= $herramienta['cantidad_total'] ?>"
                       required>

            </div>







            <div class="form-group">

                <label>
                    Fecha adquisición
                </label>


                <input type="date"
                       name="fecha_adquisicion"
                       class="form-control input"
                       value="<?= $herramienta['fecha_adquisicion'] ?>">


            </div>







            <div class="form-group">

                <label>
                    Costo
                </label>


                <input type="number"
                       step="0.01"
                       name="costo"
                       class="form-control input"
                       value="<?= $herramienta['costo'] ?>">


            </div>


            <div class="form-actions">


                <a href="/empresa_constructora/vistas/herramientas/index.php"
                   class="btn btn-secondary">

                    Cancelar

                </a>





                <button type="submit"
                        class="btn btn-primary">


                    <i class="fa-solid fa-save"></i>

                    Guardar cambios


                </button>



            </div>




        </form>


    </div>



</main>



<?php

require_once __DIR__ . "/../../layouts/footer.php";

?>