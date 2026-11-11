
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN
|--------------------------------------------------------------------------
*/

$raiz = dirname(__DIR__, 3);
$urlBase = '/empresa_constructora';

$urlControlador = $urlBase . '/controladores/FotoObraController.php';

/*
|--------------------------------------------------------------------------
| DATOS RECIBIDOS DEL CONTROLADOR
|--------------------------------------------------------------------------
*/

$id_obra = (int)($id_obra ?? $_GET['id_obra'] ?? 0);

$fotos = $fotos ?? [];

$token = $_SESSION['csrf_fotos'] ?? '';

/*
|--------------------------------------------------------------------------
| FUNCIÓN PARA PROTEGER TEXTO HTML
|--------------------------------------------------------------------------
*/

if (!function_exists('escaparFoto')) {
    function escaparFoto($valor): string
    {
        return htmlspecialchars(
            (string)$valor,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

/*
|--------------------------------------------------------------------------
| MENSAJES
|--------------------------------------------------------------------------
*/


$flash = $_SESSION['flash_fotos'] ?? '';

unset($_SESSION['flash_fotos']);

$tipoMensaje = 'info';

if (is_array($flash)) {

    $mensaje = $flash['mensaje']
        ?? $flash['message']
        ?? $flash['texto']
        ?? '';

    $tipoMensaje = $flash['tipo']
        ?? $flash['type']
        ?? 'info';

} else {

    $mensaje = $flash;
}


unset($_SESSION['flash_fotos']);

?>

<?php require_once $raiz . '/layouts/header.php'; ?>

<?php require_once $raiz . '/layouts/sidebar.php'; ?>


<style>
.contenedor-fotos {
    padding: 25px;
}

.encabezado-fotos {
    margin-bottom: 25px;
}

.encabezado-fotos h1 {
    margin-bottom: 8px;
}

.formulario-subir-foto {
    background: #111c2a;
    border: 1px solid #24384d;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
}

.formulario-subir-foto input,
.formulario-subir-foto textarea {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    margin-bottom: 15px;
    border: 1px solid #24384d;
    border-radius: 8px;
    background: #182536;
    color: #fff;
}

.formulario-subir-foto label,
.acciones-foto label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.galeria-fotos {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 22px;
}

.tarjeta-foto {
    background: #111c2a;
    border: 1px solid #24384d;
    border-radius: 15px;
    overflow: hidden;
}

.tarjeta-foto img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
}

.info-foto {
    padding: 18px;
}

.info-foto p {
    margin: 8px 0;
    overflow-wrap: anywhere;
}

.fecha-foto {
    font-size: 13px;
    opacity: 0.7;
}

.acciones-foto {
    padding: 0 18px 18px;
}

.acciones-foto textarea {
    width: 100%;
    min-height: 75px;
    resize: vertical;
    padding: 10px;
    margin: 8px 0 12px;
    border: 1px solid #24384d;
    border-radius: 8px;
    background: #182536;
    color: #fff;
}

.acciones-foto form {
    margin-bottom: 12px;
}

.acciones-foto button {
    width: 100%;
    padding: 11px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}

.btn-describir {
    background: #f4b400;
    color: #111;
}

.btn-eliminar {
    background: #c0392b;
    color: #fff;
}

.btn-subir {
    background: #f4b400;
    color: #111;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}

.mensaje-fotos {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 8px;
    background: #182536;
    border: 1px solid #24384d;
}

.sin-fotos {
    padding: 25px;
    text-align: center;
    background: #111c2a;
    border-radius: 12px;
}
</style>


<main class="content">

    <!-- ENCABEZADO -->

    <div class="encabezado-fotos">

        <h1>Fotografías de la obra</h1>

        <p>
            Cargá fotografías para registrar el avance y el estado de la obra.
        </p>

    </div>


    <!-- MENSAJES -->

    <?php if (!empty($mensaje)): ?>

        <div class="mensaje-fotos">
            <?= escaparFoto($mensaje) ?>
        </div>

    <?php endif; ?>


    <!-- FORMULARIO PARA SUBIR FOTOGRAFÍA -->

    <div class="formulario-subir-foto">

        <h2>Agregar fotografía</h2>

        <form
            action="<?= escaparFoto($urlControlador) ?>?accion=subir"
            method="POST"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="id_obra"
                value="<?= $id_obra ?>"
            >

            <input
                type="hidden"
                name="csrf_fotos"
                value="<?= escaparFoto($token) ?>"
            >

            <label for="foto">
                Seleccionar fotografía
            </label>

            <input
                type="file"
                name="foto"
                id="foto"
                accept="image/jpeg,image/png,image/webp"
                required
            >

            <label for="descripcion_nueva">
                Descripción (opcional)
            </label>

            <textarea
                name="descripcion"
                id="descripcion_nueva"
                maxlength="255"
                placeholder="Describí la fotografía..."
            ></textarea>

            <button type="submit" class="btn-subir">
                Subir fotografía
            </button>

        </form>

    </div>


    <!-- GALERÍA DE FOTOGRAFÍAS -->

    <h2>Galería de fotografías</h2>

    <?php if (empty($fotos)): ?>

        <div class="sin-fotos">
            <p>Esta obra todavía no tiene fotografías cargadas.</p>
        </div>

    <?php else: ?>

        <div class="galeria-fotos">

            <?php foreach ($fotos as $foto): ?>

                <?php

                $idFoto = (int)($foto['id_foto_obra'] ?? $foto['id_foto'] ?? 0);

                $descripcion = $foto['descripcion'] ?? '';

                $fecha = $foto['fecha_subida'] ?? $foto['fecha'] ?? '';

                $urlImagen = $urlControlador
                    . '?accion=archivo&id='
                    . $idFoto;

                ?>

                <div class="tarjeta-foto">

                    <!-- IMAGEN -->

                    <img
                        src="<?= escaparFoto($urlImagen) ?>"
                        alt="<?= escaparFoto($descripcion ?: 'Fotografía de la obra') ?>"
                    >


                    <!-- INFORMACIÓN -->

                    <div class="info-foto">

                        <p>
                            <strong>Descripción:</strong>

                            <?= $descripcion !== ''
                                ? escaparFoto($descripcion)
                                : 'Sin descripción'
                            ?>
                        </p>

                        <p class="fecha-foto">
                            <strong>Fecha:</strong>

                            <?= escaparFoto($fecha) ?>
                        </p>

                    </div>


                    <!-- BOTONES DESCRIBIR Y ELIMINAR -->

                    <div class="acciones-foto">


                        <!-- DESCRIBIR -->

                        <form
                            action="<?= escaparFoto($urlControlador) ?>?accion=editar"
                            method="POST"
                        >

                            <input
                                type="hidden"
                                name="id_foto"
                                value="<?= $idFoto ?>"
                            >

                            <input
                                type="hidden"
                                name="id_obra"
                                value="<?= $id_obra ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_fotos"
                                value="<?= escaparFoto($token) ?>"
                            >

                            <label for="descripcion_<?= $idFoto ?>">
                                Descripción de la fotografía
                            </label>

                            <textarea
                                name="descripcion"
                                id="descripcion_<?= $idFoto ?>"
                                maxlength="255"
                                placeholder="Escribí una descripción..."
                            ><?= escaparFoto($descripcion) ?></textarea>

                            <button
                                type="submit"
                                class="btn-describir"
                            >
                                DESCRIBIR
                            </button>

                        </form>


                        <!-- ELIMINAR -->

                        <form
                            action="<?= escaparFoto($urlControlador) ?>?accion=eliminar"
                            method="POST"
                            onsubmit="return confirm('¿Estás segura de que querés eliminar esta fotografía?');"
                        >

                            <input
                                type="hidden"
                                name="id_foto"
                                value="<?= $idFoto ?>"
                            >

                            <input
                                type="hidden"
                                name="id_obra"
                                value="<?= $id_obra ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_fotos"
                                value="<?= escaparFoto($token) ?>"
                            >

                            <button
                                type="submit"
                                class="btn-eliminar"
                            >
                                ELIMINAR
                            </button>

                        </form>


                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>


<?php require_once $raiz . '/layouts/footer.php'; ?>
