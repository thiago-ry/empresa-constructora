
<?php

ob_start();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../modelos/FotoObra.php';
require_once __DIR__ . '/../modelos/Obra.php';
require_once __DIR__ . '/../modelos/Auditoria.php';

/* =========================================================
   CONFIGURACIÓN
========================================================= */

$urlBase = '/empresa_constructora';

$rutaCarpetaFotos = dirname(__DIR__) . '/uploads/obras/fotos/';

$fotoModel = new FotoObra();
$obraModel = new Obra();
$auditoria = new Auditoria();

$usuario = $_SESSION['usuario'] ?? [];

$id_usuario = (int)(
    $usuario['id_usuario'] ??
    $usuario['id'] ??
    0
);

$id_rol = (int)($usuario['id_rol'] ?? 0);

$accion = $_GET['accion'] ?? 'listar';

$id_obra = (int)(
    $_POST['id_obra'] ??
    $_GET['id_obra'] ??
    0
);

$id_obraRedireccion = $id_obra;

/* =========================================================
   FUNCIONES AUXILIARES
========================================================= */

function fotoMensaje(string $tipo, string $mensaje): void
{
    $_SESSION['flash_fotos'] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ];
}

/**
 * Redirigir a la galería de la obra.
 */
function fotoRedireccionar(int $id_obra): void
{
    $url = '/empresa_constructora/controladores/FotoObraController.php'
         . '?accion=listar&id_obra=' . $id_obra;

    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }

    error_log('No se pudo enviar la redirección HTTP de fotografías.');

    echo '<script>window.location.href = '
        . json_encode($url)
        . ';</script>';

    exit;
}

/**
 * Verificar permisos de acceso a la obra.
 */
function fotoVerificarAcceso(
    Obra $obraModel,
    int $id_obra,
    int $id_usuario,
    int $id_rol
): void {

    if ($id_obra <= 0 || $id_usuario <= 0 || $id_rol <= 0) {
        http_response_code(403);
        exit('Acceso no autorizado.');
    }

    if (!$obraModel->usuarioPuedeVerObra(
        $id_obra,
        $id_usuario,
        $id_rol
    )) {
        http_response_code(403);
        exit('No tenés permiso para acceder a esta obra.');
    }
}

/**
 * Verificar token CSRF.
 */
function fotoVerificarToken(): void
{
    $token = $_POST['csrf_fotos'] ?? '';
    $tokenSesion = $_SESSION['csrf_fotos'] ?? '';

    if (
        $token === '' ||
        $tokenSesion === '' ||
        !hash_equals($tokenSesion, $token)
    ) {
        http_response_code(403);
        exit('Formulario vencido. Recargá la página e intentá nuevamente.');
    }
}

/**
 * Registrar auditoría sin interrumpir la operación principal
 * si ocurre un problema con el registro de auditoría.
 */
function fotoRegistrarAuditoria(
    Auditoria $auditoria,
    int $id_usuario,
    string $accion,
    int $id_foto,
    string $descripcion
): void {

    try {
        $auditoria->registrar([
            'id_usuario' => $id_usuario,
            'accion' => $accion,
            'tabla_afectada' => 'foto_obra',
            'id_registro' => $id_foto,
            'descripcion' => $descripcion
        ]);

    } catch (Throwable $e) {

        error_log(
            'Error auditoría fotografías: ' .
            $e->getMessage()
        );
    }
}

/* =========================================================
   PROCESAR ACCIONES
========================================================= */

try {

    switch ($accion) {

        /* =================================================
           LISTAR FOTOGRAFÍAS
        ================================================= */

        case 'listar':

            fotoVerificarAcceso(
                $obraModel,
                $id_obra,
                $id_usuario,
                $id_rol
            );

            if (
                empty($_SESSION['csrf_fotos']) ||
                !is_string($_SESSION['csrf_fotos'])
            ) {
                $_SESSION['csrf_fotos'] = bin2hex(random_bytes(32));
            }

            $fotos = $fotoModel->listarPorObra($id_obra);

            $tokenCSRF = $_SESSION['csrf_fotos'];

            require __DIR__ . '/../vistas/obras/fotos/index.php';

            break;


        /* =================================================
           SUBIR FOTOGRAFÍA
        ================================================= */

        case 'subir':

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Método no permitido.');
            }

            fotoVerificarToken();

            fotoVerificarAcceso(
                $obraModel,
                $id_obra,
                $id_usuario,
                $id_rol
            );

            if (!isset($_FILES['foto'])) {
                throw new RuntimeException(
                    'No seleccionaste ninguna fotografía.'
                );
            }

            $archivo = $_FILES['foto'];

            if ($archivo['error'] !== UPLOAD_ERR_OK) {

                $errores = [
                    UPLOAD_ERR_INI_SIZE => 'La imagen supera el límite de PHP.',
                    UPLOAD_ERR_FORM_SIZE => 'La imagen supera el tamaño permitido.',
                    UPLOAD_ERR_PARTIAL => 'La imagen se subió parcialmente.',
                    UPLOAD_ERR_NO_FILE => 'Seleccioná una imagen.',
                    UPLOAD_ERR_NO_TMP_DIR => 'No existe la carpeta temporal.',
                    UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo.',
                    UPLOAD_ERR_EXTENSION => 'Una extensión de PHP bloqueó la carga.'
                ];

                throw new RuntimeException(
                    $errores[$archivo['error']] ??
                    'Error al subir la imagen.'
                );
            }

            if (!is_uploaded_file($archivo['tmp_name'])) {
                throw new RuntimeException(
                    'El archivo recibido no es válido.'
                );
            }

            if ($archivo['size'] > 5 * 1024 * 1024) {
                throw new RuntimeException(
                    'La imagen no puede superar los 5 MB.'
                );
            }

            $descripcion = trim(
                (string)($_POST['descripcion'] ?? '')
            );

            if (mb_strlen($descripcion, 'UTF-8') > 255) {
                throw new RuntimeException(
                    'La descripción no puede superar los 255 caracteres.'
                );
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($archivo['tmp_name']);

            $formatos = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($formatos[$mime])) {
                throw new RuntimeException(
                    'Formato no permitido. Utilizá JPG, PNG o WEBP.'
                );
            }

            if (getimagesize($archivo['tmp_name']) === false) {
                throw new RuntimeException(
                    'El archivo no es una imagen válida.'
                );
            }

            if (!is_dir($rutaCarpetaFotos)) {

                if (
                    !mkdir($rutaCarpetaFotos, 0755, true) &&
                    !is_dir($rutaCarpetaFotos)
                ) {
                    throw new RuntimeException(
                        'No se pudo crear la carpeta de fotografías.'
                    );
                }
            }

            if (!is_writable($rutaCarpetaFotos)) {
                throw new RuntimeException(
                    'La carpeta de fotografías no permite guardar archivos.'
                );
            }

            $nombreArchivo = bin2hex(random_bytes(16))
                . '.' . $formatos[$mime];

            $rutaDestino = $rutaCarpetaFotos . $nombreArchivo;

            if (!move_uploaded_file(
                $archivo['tmp_name'],
                $rutaDestino
            )) {
                throw new RuntimeException(
                    'No se pudo guardar la imagen en el servidor.'
                );
            }

            try {

                $id_foto = $fotoModel->guardar([
                    'id_obra' => $id_obra,
                    'ruta_imagen' => $nombreArchivo,
                    'descripcion' => $descripcion
                ]);

            } catch (Throwable $e) {

                if (is_file($rutaDestino)) {
                    unlink($rutaDestino);
                }

                throw $e;
            }

            fotoRegistrarAuditoria(
                $auditoria,
                $id_usuario,
                'INSERTAR',
                $id_foto,
                'Se agregó una fotografía a la obra ' . $id_obra
            );

            fotoMensaje(
                'success',
                'Fotografía subida correctamente.'
            );

            fotoRedireccionar($id_obra);

            break;


        /* =================================================
           EDITAR DESCRIPCIÓN
        ================================================= */

        case 'editar':

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Método no permitido.');
            }

            fotoVerificarToken();

            $id_foto = (int)($_POST['id_foto'] ?? 0);

            if ($id_foto <= 0) {
                throw new RuntimeException(
                    'Identificador de fotografía inválido.'
                );
            }

            $foto = $fotoModel->obtenerPorId($id_foto);

            if (!$foto) {
                throw new RuntimeException(
                    'La fotografía no existe.'
                );
            }

            $id_obraRedireccion = (int)$foto['id_obra'];

            fotoVerificarAcceso(
                $obraModel,
                $id_obraRedireccion,
                $id_usuario,
                $id_rol
            );

            $descripcion = trim(
                (string)($_POST['descripcion'] ?? '')
            );

            if (mb_strlen($descripcion, 'UTF-8') > 255) {
                throw new RuntimeException(
                    'La descripción no puede superar los 255 caracteres.'
                );
            }

            if (!$fotoModel->actualizarDescripcion(
                $id_foto,
                $descripcion
            )) {
                throw new RuntimeException(
                    'No se pudo actualizar la descripción.'
                );
            }

            fotoRegistrarAuditoria(
                $auditoria,
                $id_usuario,
                'ACTUALIZAR',
                $id_foto,
                'Se modificó la descripción de una fotografía de la obra '
                . $id_obraRedireccion
            );

            fotoMensaje(
                'success',
                'Descripción actualizada correctamente.'
            );

            fotoRedireccionar($id_obraRedireccion);

            break;


        /* =================================================
           MOSTRAR IMAGEN
        ================================================= */

        case 'archivo':

            $id_foto = (int)($_GET['id'] ?? 0);

            if ($id_foto <= 0) {
                http_response_code(400);
                exit('Identificador inválido.');
            }

            $foto = $fotoModel->obtenerPorId($id_foto);

            if (!$foto) {
                http_response_code(404);
                exit('Fotografía no encontrada.');
            }

            $id_obraRedireccion = (int)$foto['id_obra'];

            fotoVerificarAcceso(
                $obraModel,
                $id_obraRedireccion,
                $id_usuario,
                $id_rol
            );

            $nombre = basename($foto['nombre_archivo']);

            $ruta = $rutaCarpetaFotos . $nombre;

            if (!is_file($ruta)) {
                http_response_code(404);
                exit('No se encontró el archivo físico de la fotografía.');
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($ruta);

            $mimesPermitidos = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($mime, $mimesPermitidos, true)) {
                http_response_code(415);
                exit('El archivo no es una imagen válida.');
            }

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            header('Content-Type: ' . $mime);
            header('Content-Disposition: inline; filename="' . $nombre . '"');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: private, no-cache, no-store, must-revalidate');
            header('Content-Length: ' . filesize($ruta));

            readfile($ruta);
            exit;


        /* =================================================
           ELIMINAR FOTOGRAFÍA
        ================================================= */

        case 'eliminar':

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                exit('Método no permitido.');
            }

            fotoVerificarToken();

            $id_foto = (int)($_POST['id_foto'] ?? 0);

            if ($id_foto <= 0) {
                throw new RuntimeException(
                    'Identificador de fotografía inválido.'
                );
            }

            $foto = $fotoModel->obtenerPorId($id_foto);

            if (!$foto) {
                throw new RuntimeException(
                    'La fotografía no existe.'
                );
            }

            $id_obraRedireccion = (int)$foto['id_obra'];

            fotoVerificarAcceso(
                $obraModel,
                $id_obraRedireccion,
                $id_usuario,
                $id_rol
            );

            $nombre = basename($foto['nombre_archivo']);
            $ruta = $rutaCarpetaFotos . $nombre;

            if (!$fotoModel->eliminar($id_foto)) {
                throw new RuntimeException(
                    'No se pudo eliminar la fotografía de la base de datos.'
                );
            }

            if (is_file($ruta) && !unlink($ruta)) {
                error_log(
                    'No se pudo eliminar el archivo físico: ' . $ruta
                );
            }

            fotoRegistrarAuditoria(
                $auditoria,
                $id_usuario,
                'ELIMINAR',
                $id_foto,
                'Se eliminó una fotografía de la obra '
                . $id_obraRedireccion
            );

            fotoMensaje(
                'success',
                'Fotografía eliminada correctamente.'
            );

            fotoRedireccionar($id_obraRedireccion);

            break;


        /* =================================================
           ACCIÓN NO VÁLIDA
        ================================================= */

        default:

            throw new RuntimeException(
                'La acción solicitada no es válida.'
            );
    }

} catch (Throwable $e) {

    error_log(
        'Error FotoObraController: ' . $e->getMessage()
    );

    /*
     * IMPORTANTE:
     * Si falla el listado, no redirigir al mismo listado.
     * Así evitamos el bucle de redirecciones.
     */
    if ($accion === 'listar') {

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        http_response_code(500);

        echo '<h2>Error al cargar las fotografías</h2>';
        echo '<p>Se produjo un problema al cargar la galería.</p>';
        echo '<p>Revisá el registro de errores de PHP.</p>';

        exit;
    }

    fotoMensaje(
        'error',
        $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Ocurrió un error al procesar la fotografía.'
    );

    /*
     * Para editar o eliminar, usamos el ID de obra
     * obtenido desde la fotografía, no solamente el POST.
     */
    if ($id_obraRedireccion > 0) {
        fotoRedireccionar($id_obraRedireccion);
    }

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code(500);

    echo 'Ocurrió un error al procesar la solicitud.';

    exit;
}
