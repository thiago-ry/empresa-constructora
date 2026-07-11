<?php

require_once "../../modelos/Cliente.php";
require_once "../../config/permisos.php";

verificarPermiso("obras");

$cliente = new Cliente();

$clientes = $cliente->obtenerTodos();


require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title">

        <h1>Agregar obra</h1>

        <p>
            Complete los datos para registrar una nueva obra.
        </p>

    </div>


    <div class="form-card">

        <form
            class="form"
            action="../../controladores/ObraController.php"
            method="POST"
            autocomplete="off">

            <input type="hidden" name="accion" value="agregar">


            <div class="form-row">


                <div class="form-group">

                    <label for="nombre_obra">
                        Nombre de la obra
                    </label>

                    <input
                        type="text"
                        id="nombre_obra"
                        name="nombre_obra"
                        class="input"
                        placeholder="Ingrese el nombre de la obra"
                        maxlength="150"
                        required>

                </div>



                <div class="form-group">

                    <label for="cliente">
                        Cliente
                    </label>


                    <select
                        id="cliente"
                        name="id_cliente"
                        class="filter"
                        required>


                        <option value="">
                            Seleccione un cliente
                        </option>


                        <?php foreach ($clientes as $c) { ?>

                            <option value="<?= $c["id_cliente"]; ?>">

                                <?= $c["nombre"] . " " . $c["apellido"]; ?>

                            </option>

                        <?php } ?>


                    </select>


                </div>


            </div>



            <div class="form-row">


                <div class="form-group">

                    <label for="direccion">
                        Dirección
                    </label>


                    <input
                        type="text"
                        id="direccion"
                        name="direccion"
                        class="input"
                        placeholder="Ingrese la dirección"
                        maxlength="255"
                        required>


                </div>


                <div class="form-group">

                    <label for="estado">
                        Estado
                    </label>


                    <select
                        id="estado"
                        name="estado"
                        class="filter">


                        <option value="Planificacion">
                            Planificación
                        </option>

                        <option value="En Proceso">
                            En Proceso
                        </option>

                        <option value="Suspendida">
                            Suspendida
                        </option>

                        <option value="Finalizada">
                            Finalizada
                        </option>

                        <option value="Cancelada">
                            Cancelada
                        </option>


                    </select>


                </div>


            </div>



            <div class="form-group">

                <label for="descripcion">
                    Descripción
                </label>


                <textarea
                    id="descripcion"
                    name="descripcion"
                    class="input"
                    rows="4"
                    placeholder="Ingrese una descripción de la obra"></textarea>


            </div>




            <div class="form-row">


                <div class="form-group">

                    <label for="fecha_inicio">
                        Fecha de inicio
                    </label>


                    <input
                        type="date"
                        id="fecha_inicio"
                        name="fecha_inicio"
                        class="input">


                </div>



                <div class="form-group">

                    <label for="fecha_fin">
                        Fecha estimada de finalización
                    </label>


                    <input
                        type="date"
                        id="fecha_fin"
                        name="fecha_fin"
                        class="input">


                </div>


            </div>




            <div class="form-actions">


                <a
                    href="index.php"
                    class="btn btn-secondary">

                    <i class="fa-solid fa-arrow-left"></i>

                    Cancelar

                </a>



                <button
                    type="reset"
                    class="btn btn-warning">

                    <i class="fa-solid fa-rotate-left"></i>

                    Limpiar

                </button>




                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa-solid fa-floppy-disk"></i>

                    Guardar obra

                </button>



            </div>


        </form>


    </div>


</main>


<?php require_once "../../layouts/footer.php"; ?>