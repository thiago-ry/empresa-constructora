
<?php

require_once __DIR__ . "/../../../layouts/header.php";
require_once __DIR__ . "/../../../layouts/sidebar.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$raiz = dirname(__DIR__, 3);

$urlBase = '/empresa_constructora';

$urlControlador = $urlBase . '/controladores/DocumentoObraController.php';

$id_obra = (int)($id_obra ?? 0);
$documentos = $documentos ?? [];
$token = $_SESSION['csrf_documentos'] ?? '';
$puedeGestionar = $puedeGestionar ?? false;

function escaparDocumento($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$mensaje = '';
$tipoMensaje = '';

if (isset($_SESSION['flash_documentos'])) {
    $flash = $_SESSION['flash_documentos'];

    if (is_array($flash)) {
        $mensaje = $flash['mensaje'] ?? '';
        $tipoMensaje = $flash['tipo'] ?? 'success';
    }

    unset($_SESSION['flash_documentos']);
}

?>


    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>

        .documentos-contenedor {
            padding: 25px;
        }

        .documentos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .documentos-header h1 {
            margin: 0;
        }

        .documentos-header p {
            opacity: .7;
        }

        .documentos-card {
            background: #111c2a;
            border: 1px solid #24384d;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 25px;
        }

        .documentos-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .documentos-form .campo {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .campo-completo {
            grid-column: 1 / -1;
        }

        .documentos-form input,
        .documentos-form select,
        .documentos-form textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border-radius: 9px;
            border: 1px solid #24384d;
            background: #182536;
            color: white;
        }

        .documentos-form textarea {
            min-height: 90px;
            resize: vertical;
        }

        .documentos-ayuda {
            font-size: 12px;
            opacity: .7;
        }

        .documentos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 18px;
        }

        .documento-item {
            background: #111c2a;
            border: 1px solid #24384d;
            border-radius: 15px;
            padding: 20px;
        }

        .documento-icono {
            font-size: 35px;
            color: #f4b400;
            margin-bottom: 15px;
        }

        .documento-item h3 {
            overflow-wrap: anywhere;
        }

        .documento-tipo {
            display: inline-block;
            background: #24384d;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .documento-descripcion {
            min-height: 35px;
            opacity: .8;
            overflow-wrap: anywhere;
        }

        .documento-fecha {
            font-size: 12px;
            opacity: .6;
            margin: 12px 0;
        }

        .documento-acciones {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }

        .documento-acciones form {
            margin: 0;
        }

        .documento-acciones button,
        .documento-acciones a {
            font-size: 12px;
            padding: 9px 12px;
        }

        .mensaje-documentos {
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .mensaje-success {
            background: #153d2c;
            color: #9be5bd;
        }

        .mensaje-error {
            background: #4a2020;
            color: #ffc0c0;
        }

        .documentos-vacio {
            text-align: center;
            padding: 45px 20px;
            opacity: .7;
        }

        .documentos-vacio i {
            font-size: 45px;
            color: #f4b400;
        }

        @media(max-width: 650px) {
            .documentos-form {
                grid-template-columns: 1fr;
            }

            .campo-completo {
                grid-column: auto;
            }
        }

    </style>

<?php

if (file_exists($raiz . '/layouts/header.php')) {
    require $raiz . '/layouts/header.php';
}

if (file_exists($raiz . '/layouts/sidebar.php')) {
    require $raiz . '/layouts/sidebar.php';
}

?>

<main class="content">

    <div class="documentos-header">

        <div>

            <h1>
                <i class="fa-solid fa-folder-open"></i>
                Documentos de obra
            </h1>

            <p>
                Consultá y administrá los archivos correspondientes a esta obra.
            </p>

        </div>

        <a href="<?= $urlBase ?>/vistas/obras/ver.php?id=<?= $id_obra ?>"
           class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>
            Volver a la obra

        </a>

    </div>

    <?php if ($mensaje !== ''): ?>

        <div class="mensaje-documentos <?= $tipoMensaje === 'error' ? 'mensaje-error' : 'mensaje-success' ?>">

            <?= escaparDocumento($mensaje) ?>

        </div>

    <?php endif; ?>

    <?php if ($puedeGestionar): ?>

        <section class="documentos-card">

            <h2>
                <i class="fa-solid fa-cloud-arrow-up"></i>
                Subir nuevo documento
            </h2>

            <form
                action="<?= escaparDocumento($urlControlador) ?>?accion=subir&id_obra=<?= $id_obra ?>"
                method="POST"
                enctype="multipart/form-data"
                class="documentos-form"
            >

                <input type="hidden" name="accion" value="subir">

                <input type="hidden"
                       name="id_obra"
                       value="<?= $id_obra ?>">

                <input type="hidden"
                       name="csrf_documentos"
                       value="<?= escaparDocumento($token) ?>">

                <div class="campo">

                    <label for="tipo_documento">
                        Tipo de documento
                    </label>

                    <select name="tipo_documento"
                            id="tipo_documento"
                            required>

                        <option value="">Seleccionar tipo</option>
                        <option value="Plano">Plano</option>
                        <option value="Contrato">Contrato</option>
                        <option value="Permiso">Permiso</option>
                        <option value="Factura">Factura</option>
                        <option value="Presupuesto">Presupuesto</option>
                        <option value="Informe">Informe</option>
                        <option value="Otro">Otro</option>

                    </select>

                </div>

                <div class="campo">

                    <label for="archivo">
                        Seleccionar archivo
                    </label>

                    <input
                        type="file"
                        name="archivo"
                        id="archivo"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                        required
                    >

                    <span class="documentos-ayuda">
                        Formatos permitidos. Tamaño máximo: 100 MB.
                    </span>

                </div>

                <div class="campo campo-completo">

                    <label for="descripcion">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        id="descripcion"
                        placeholder="Escribí una descripción del documento..."
                    ></textarea>

                </div>

                <div class="campo campo-completo">

                    <button type="submit" class="btn btn-primary">

                        <i class="fa-solid fa-upload"></i>
                        Subir documento

                    </button>

                </div>

            </form>

        </section>

    <?php endif; ?>

    <section class="documentos-card">

        <h2>
            <i class="fa-solid fa-file-lines"></i>
            Documentos registrados
            (<?= count($documentos) ?>)
        </h2>

        <?php if (empty($documentos)): ?>

            <div class="documentos-vacio">

                <i class="fa-solid fa-folder-open"></i>

                <h3>No hay documentos registrados</h3>

                <p>
                    Los documentos que se carguen para esta obra aparecerán acá.
                </p>

            </div>

        <?php else: ?>

            <div class="documentos-grid">

                <?php foreach ($documentos as $documento): ?>

                    <?php

                    $idDocumento = (int)$documento['id_documento'];

                    $nombre = $documento['nombre_archivo'] ?? 'Documento';

                    $tipo = $documento['tipo_documento'] ?? 'Otro';

                    $descripcion = $documento['descripcion'] ?? '';

                    $fecha = $documento['fecha_subida'] ?? '';

                    $extension = strtolower(
                        pathinfo($nombre, PATHINFO_EXTENSION)
                    );

                    $icono = 'fa-file-lines';

                    if ($extension === 'pdf') {
                        $icono = 'fa-file-pdf';
                    } elseif (in_array($extension, ['doc', 'docx'])) {
                        $icono = 'fa-file-word';
                    } elseif (in_array($extension, ['xls', 'xlsx'])) {
                        $icono = 'fa-file-excel';
                    } elseif (in_array($extension, ['ppt', 'pptx'])) {
                        $icono = 'fa-file-powerpoint';
                    } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                        $icono = 'fa-file-image';
                    }

                    ?>

                    <article class="documento-item">

                        <div class="documento-icono">
                            <i class="fa-solid <?= escaparDocumento($icono) ?>"></i>
                        </div>

                        <span class="documento-tipo">
                            <?= escaparDocumento($tipo) ?>
                        </span>

                        <h3>
                            <?= escaparDocumento($nombre) ?>
                        </h3>

                        <p class="documento-descripcion">
                            <?= escaparDocumento($descripcion ?: 'Sin descripción') ?>
                        </p>

                        <div class="documento-fecha">

                            <i class="fa-regular fa-calendar"></i>

                            <?= escaparDocumento($fecha) ?>

                        </div>

                        <div class="documento-acciones">

                            <a
                                href="<?= escaparDocumento($urlControlador) ?>?accion=descargar&id_obra=<?= $id_obra ?>&id_documento=<?= $idDocumento ?>"
                                class="btn btn-primary"
                            >

                                <i class="fa-solid fa-download"></i>
                                Descargar

                            </a>

                            <?php if ($puedeGestionar): ?>

                                <form
                                    action="<?= escaparDocumento($urlControlador) ?>?accion=describir&id_obra=<?= $id_obra ?>"
                                    method="POST"
                                >

                                    <input type="hidden"
                                           name="accion"
                                           value="describir">

                                    <input type="hidden"
                                           name="id_obra"
                                           value="<?= $id_obra ?>">

                                    <input type="hidden"
                                           name="id_documento"
                                           value="<?= $idDocumento ?>">

                                    <input type="hidden"
                                           name="csrf_documentos"
                                           value="<?= escaparDocumento($token) ?>">

                                    <input type="hidden"
                                           name="descripcion"
                                           value="<?= escaparDocumento($descripcion) ?>">

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        onclick="editarDescripcion(this)"
                                    >

                                        <i class="fa-solid fa-pen"></i>
                                        Describir

                                    </button>

                                </form>

                                <form
                                    action="<?= escaparDocumento($urlControlador) ?>?accion=eliminar&id_obra=<?= $id_obra ?>"
                                    method="POST"
                                    onsubmit="return confirm('¿Querés eliminar este documento?')"
                                >

                                    <input type="hidden"
                                           name="accion"
                                           value="eliminar">

                                    <input type="hidden"
                                           name="id_obra"
                                           value="<?= $id_obra ?>">

                                    <input type="hidden"
                                           name="id_documento"
                                           value="<?= $idDocumento ?>">

                                    <input type="hidden"
                                           name="csrf_documentos"
                                           value="<?= escaparDocumento($token) ?>">

                                    <button type="submit"
                                            class="btn btn-danger">

                                        <i class="fa-solid fa-trash"></i>
                                        Eliminar

                                    </button>

                                </form>

                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

<script>

function editarDescripcion(boton) {

    const formulario = boton.closest('form');

    const campo = formulario.querySelector('[name="descripcion"]');

    const actual = campo.value;

    const nueva = prompt(
        'Escribí la descripción del documento:',
        actual
    );

    if (nueva === null) {
        return;
    }

    campo.value = nueva.trim();

    formulario.submit();
}

</script>

<?php

if (file_exists($raiz . '/layouts/footer.php')) {
    require $raiz . '/layouts/footer.php';
}

?>