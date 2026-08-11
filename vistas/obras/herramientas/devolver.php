<?php

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

?>
<?php
/** @var array $herramientaObra */
?>
<main class="content">

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Registrar devolución
            </h1>

            <p class="page-subtitle">
                Registrar herramientas devueltas por la obra.
            </p>

        </div>

    </div>

    <div class="card">

        <form
            action="/empresa_constructora/controladores/HerramientaObraController.php?accion=guardarDevolucion"
            method="POST">

            <input
                type="hidden"
                name="id_herramienta_obra"
                value="<?= $herramientaObra["id_herramienta_obra"] ?>">

            <div class="form-group">

                <label>Herramienta</label>

                <input
                    type="text"
                    class="form-control input"
                    value="<?= $herramientaObra["herramienta"] ?>"
                    disabled>

            </div>

            <div class="form-group">

                <label>Pendientes</label>

                <input
                    type="text"
                    class="form-control input"
                    value="<?= $herramientaObra["cantidad_pendiente"] ?>"
                    disabled>

            </div>

            <div class="form-group">

                <label>Cantidad a devolver</label>

                <input
                    type="number"
                    name="cantidad"
                    min="1"
                    max="<?= $herramientaObra["cantidad_pendiente"] ?>"
                    class="form-control input"
                    required>

            </div>

            <div class="form-group">

                <label>Observaciones</label>

                <textarea
                    name="observaciones"
                    class="form-control input"
                    rows="4"></textarea>

            </div>

            <div class="form-actions">

                <a
                    href="/empresa_constructora/controladores/HerramientaObraController.php?accion=listar&id_obra=<?= $herramientaObra["id_obra"] ?>"
                    class="btn btn-secondary">

                    Cancelar

                </a>

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="fa-solid fa-check"></i>

                    Registrar devolución

                </button>

            </div>

        </form>

    </div>

</main>

<?php require_once __DIR__ . "/../../../layouts/footer.php"; ?>