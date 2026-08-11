<?php

if (!isset($herramientaObra)) {

    echo "No se encontró la asignación.";
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
                Editar asignación
            </h1>

            <p class="page-subtitle">
                Actualice la información de la herramienta asignada a la obra.
            </p>

        </div>

        <a
            href="/empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=<?= $herramientaObra["id_obra"] ?>"
            class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver

        </a>

    </div>


    <div class="card">

        <form
            action="/empresa_constructora/controladores/HerramientaObraController.php?accion=actualizar"
            method="POST">

            <input
                type="hidden"
                name="id_herramienta_obra"
                value="<?= $herramientaObra["id_herramienta_obra"] ?>">


            <div class="form-group">

                <label>
                    Herramienta
                </label>

                <input
                    type="text"
                    class="form-control input"
                    value="<?= htmlspecialchars($herramientaObra["herramienta"]) ?>"
                    disabled>

            </div>


            <div class="form-group">

                <label>
                    Cantidad asignada
                </label>

                <input
                    type="number"
                    name="cantidad"
                    class="form-control input"
                    min="<?= $herramientaObra["cantidad_devuelta"] ?>"
                    value="<?= $herramientaObra["cantidad_asignada"] ?>"
                    required>

            </div>


            <div class="form-group">

                <label>
                    Cantidad devuelta
                </label>

                <input
                    type="number"
                    class="form-control input"
                    value="<?= $herramientaObra["cantidad_devuelta"] ?>"
                    disabled>

            </div>


            <div class="form-group">

                <label>
                    Cantidad pendiente
                </label>

                <input
                    type="number"
                    class="form-control input"
                    value="<?= $herramientaObra["cantidad_pendiente"] ?>"
                    disabled>

            </div>


            <div class="form-group">

                <label>
                    Estado
                </label>

                <input
                    type="text"
                    class="form-control input"
                    value="<?= htmlspecialchars($herramientaObra["estado"]) ?>"
                    disabled>

            </div>


            <div class="form-group">

                <label>
                    Observaciones
                </label>

                <textarea
                    name="observaciones"
                    class="form-control input"
                    rows="5"
                    placeholder="Ingrese observaciones..."><?= htmlspecialchars($herramientaObra["observaciones"]) ?></textarea>

            </div>


            <div class="form-actions">

                <a
                    href="/empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=<?= $herramientaObra["id_obra"] ?>"
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

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>