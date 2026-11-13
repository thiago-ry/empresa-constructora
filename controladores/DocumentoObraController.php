
<?php

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../modelos/DocumentoObra.php';
require_once __DIR__ . '/../modelos/Obra.php';

$urlBase = '/empresa_constructora';
$urlControlador = $urlBase . '/controladores/DocumentoObraController.php';

$directorio = dirname(__DIR__) . '/uploads/documentos/obras/';

if (!is_dir($directorio)) {
    mkdir($directorio, 0775, true);
}

/*
|--------------------------------------------------------------------------
| USUARIO Y SESIÓN
|--------------------------------------------------------------------------
*/

$id_usuario = (int)(
    $_SESSION['usuario']['id_usuario']
    ?? $_SESSION['usuario']['id']
    ?? $_SESSION['id_usuario']
    ?? 0
);

$id_rol = (int)(
    $_SESSION['usuario']['id_rol']
    ?? $_SESSION['id_rol']
    ?? 0
);

if ($id_usuario <= 0) {
    header('Location: ' . $urlBase . '/index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| TOKEN DE SEGURIDAD
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_documentos'])) {
    $_SESSION['csrf_documentos'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| FUNCIONES AUXILIARES
|--------------------------------------------------------------------------
*/

function documentoRedireccionar(int $id_obra): void
{
    global $urlControlador;

    header(
        'Location: ' . $urlControlador .
        '?accion=listar&id_obra=' . $id_obra
    );

    exit;
}

function documentoMensaje(string $tipo, string $mensaje): void
{
    $_SESSION['flash_documentos'] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje
    ];
}

function documentoVerificarToken(): bool
{
    $tokenSesion = $_SESSION['csrf_documentos'] ?? '';
    $tokenFormulario = $_POST['csrf_documentos'] ?? '';

    return (
        is_string($tokenSesion) &&
        is_string($tokenFormulario) &&
        $tokenSesion !== '' &&
        $tokenFormulario !== '' &&
        hash_equals($tokenSesion, $tokenFormulario)
    );
}

function documentoVerificarAcceso(
    int $id_obra,
    int $id_usuario,
    int $id_rol
): bool {
    if ($id_obra <= 0 || $id_usuario <= 0) {
        return false;
    }

    try {
        $obra = new Obra();

        return (bool)$obra->usuarioPuedeVerObra(
            $id_obra,
            $id_usuario,
            $id_rol
        );

    } catch (Throwable $e) {
        error_log('Error verificando acceso a documentos: ' . $e->getMessage());
        return false;
    }
}

function documentoConvertirBytes(string $valor): int
{
    $valor = trim($valor);

    if ($valor === '') {
        return 0;
    }

    $unidad = strtolower(substr($valor, -1));
    $numero = (float)$valor;

    if ($unidad === 'g') {
        $numero *= 1024 * 1024 * 1024;
    } elseif ($unidad === 'm') {
        $numero *= 1024 * 1024;
    } elseif ($unidad === 'k') {
        $numero *= 1024;
    }

    return (int)$numero;
}

/*
|--------------------------------------------------------------------------
| CONEXIÓN CON EL MODELO
|--------------------------------------------------------------------------
*/

try {
    $documentoModelo = new DocumentoObra();
} catch (Throwable $e) {
    error_log('Error al conectar con documentos: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con el módulo de documentos.');
}

/*
|--------------------------------------------------------------------------
| ACCIÓN E ID DE OBRA
|--------------------------------------------------------------------------
*/

$accion = $_POST['accion'] ?? $_GET['accion'] ?? 'listar';

$id_obra = (int)(
    $_POST['id_obra']
    ?? $_GET['id_obra']
    ?? 0
);

/*
|--------------------------------------------------------------------------
| DETECTAR SI PHP RECHAZÓ EL TAMAÑO DEL ENVÍO
|--------------------------------------------------------------------------
*/

$contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
$postMaxBytes = documentoConvertirBytes((string)ini_get('post_max_size'));

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $contentLength > 0 &&
    $postMaxBytes > 0 &&
    $contentLength > $postMaxBytes
) {
    documentoMensaje(
        'error',
        'El envío supera el límite configurado en PHP. El máximo permitido es 100 MB. Verificá post_max_size en php.ini.'
    );

    documentoRedireccionar($id_obra);
}

/*
|--------------------------------------------------------------------------
| VERIFICAR ACCESO A LA OBRA
|--------------------------------------------------------------------------
*/

if (!documentoVerificarAcceso($id_obra, $id_usuario, $id_rol)) {
    documentoMensaje(
        'error',
        'No tenés permiso para consultar los documentos de esta obra.'
    );

    header('Location: ' . $urlBase . '/vistas/obras/index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| LISTAR DOCUMENTOS
|--------------------------------------------------------------------------
*/

if ($accion === 'listar') {

    try {
        $documentos = $documentoModelo->listarPorObra($id_obra);
    } catch (Throwable $e) {
        error_log('Error al listar documentos: ' . $e->getMessage());

        $documentos = [];

        documentoMensaje(
            'error',
            'No se pudieron cargar los documentos.'
        );
    }

    $token = $_SESSION['csrf_documentos'];

    $puedeGestionar = in_array($id_rol, [2, 3, 4], true);

    $raiz = dirname(__DIR__);

    require $raiz . '/vistas/obras/documentos/index.php';

    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDAR MÉTODO
|--------------------------------------------------------------------------
*/

if (
    $accion !== 'descargar' &&
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {
    documentoRedireccionar($id_obra);
}

/*
|--------------------------------------------------------------------------
| VALIDAR TOKEN
|--------------------------------------------------------------------------
*/

if ($accion !== 'descargar' && !documentoVerificarToken()) {

    documentoMensaje(
        'error',
        'La sesión del formulario venció. Actualizá la página e intentá nuevamente.'
    );

    documentoRedireccionar($id_obra);
}

/*
|--------------------------------------------------------------------------
| PERMISOS PARA MODIFICAR
|--------------------------------------------------------------------------
*/

$puedeGestionar = in_array($id_rol, [2, 3, 4], true);

if (
    in_array($accion, ['subir', 'describir', 'eliminar'], true) &&
    !$puedeGestionar
) {
    documentoMensaje(
        'error',
        'No tenés permiso para modificar los documentos.'
    );

    documentoRedireccionar($id_obra);
}

/*
|--------------------------------------------------------------------------
| SUBIR DOCUMENTO
|--------------------------------------------------------------------------
*/

if ($accion === 'subir') {

    if (!isset($_FILES['archivo'])) {
        documentoMensaje(
            'error',
            'No se recibió ningún archivo. Verificá el límite de carga de PHP.'
        );

        documentoRedireccionar($id_obra);
    }

    $archivo = $_FILES['archivo'];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {

        $erroresCarga = [
            UPLOAD_ERR_INI_SIZE => 'El archivo supera upload_max_filesize de PHP.',
            UPLOAD_ERR_FORM_SIZE => 'El archivo supera el tamaño máximo del formulario.',
            UPLOAD_ERR_PARTIAL => 'El archivo se recibió parcialmente.',
            UPLOAD_ERR_NO_FILE => 'No seleccionaste ningún archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'No existe la carpeta temporal de PHP.',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el servidor.',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP impidió la carga.'
        ];

        $mensajeError = $erroresCarga[$archivo['error']]
            ?? 'Ocurrió un error al cargar el archivo.';

        documentoMensaje('error', $mensajeError);

        documentoRedireccionar($id_obra);
    }

    /*
    | LÍMITE DEL MÓDULO: 100 MB
    */

    $limiteArchivo = 100 * 1024 * 1024;

    if ((int)$archivo['size'] > $limiteArchivo) {
        documentoMensaje(
            'error',
            'El archivo supera el límite de 100 MB.'
        );

        documentoRedireccionar($id_obra);
    }

    /*
    | EXTENSIONES PERMITIDAS
    */

    $extensionesPermitidas = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'jpg',
        'jpeg',
        'png'
    ];

    $nombreOriginal = basename($archivo['name']);

    $extension = strtolower(
        pathinfo($nombreOriginal, PATHINFO_EXTENSION)
    );

    if (!in_array($extension, $extensionesPermitidas, true)) {
        documentoMensaje(
            'error',
            'El formato del archivo no está permitido.'
        );

        documentoRedireccionar($id_obra);
    }

    /*
    | VALIDACIÓN MIME
    */

    $tiposPermitidos = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/jpeg',
        'image/png',
        'application/zip',
        'application/x-zip',
        'application/octet-stream'
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($archivo['tmp_name']);

    if (!in_array($mime, $tiposPermitidos, true)) {
        documentoMensaje(
            'error',
            'El contenido del archivo no corresponde a un formato permitido.'
        );

        documentoRedireccionar($id_obra);
    }

    /*
    | TIPO Y DESCRIPCIÓN
    */

    $tipo = trim($_POST['tipo_documento'] ?? '');

    $tiposDocumentos = [
        'Plano',
        'Contrato',
        'Permiso',
        'Factura',
        'Presupuesto',
        'Informe',
        'Otro'
    ];

    if (!in_array($tipo, $tiposDocumentos, true)) {
        documentoMensaje(
            'error',
            'Seleccioná un tipo de documento válido.'
        );

        documentoRedireccionar($id_obra);
    }

    $descripcion = trim($_POST['descripcion'] ?? '');

    /*
    | GUARDAR ARCHIVO
    */

    $nombreGuardado = bin2hex(random_bytes(16)) . '.' . $extension;

    $destino = $directorio . $nombreGuardado;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        documentoMensaje(
            'error',
            'No se pudo guardar el archivo en el servidor.'
        );

        documentoRedireccionar($id_obra);
    }

    /*
    | REGISTRAR EN BASE DE DATOS
    */

    try {

        $documentoModelo->guardar([
            'id_obra' => $id_obra,
            'tipo_documento' => $tipo,
            'nombre_archivo' => $nombreOriginal,
            'ruta_archivo' => 'uploads/documentos/obras/' . $nombreGuardado,
            'descripcion' => $descripcion,
            'id_usuario' => $id_usuario
        ]);

        documentoMensaje(
            'success',
            'El documento se subió correctamente.'
        );

    } catch (Throwable $e) {

        error_log('Error guardando documento: ' . $e->getMessage());

        if (is_file($destino)) {
            unlink($destino);
        }

        documentoMensaje(
            'error',
            'No se pudo registrar el documento en la base de datos.'
        );
    }

    documentoRedireccionar($id_obra);
}

/*
|--------------------------------------------------------------------------
| OBTENER DOCUMENTO
|--------------------------------------------------------------------------
*/

$id_documento = (int)(
    $_POST['id_documento']
    ?? $_GET['id_documento']
    ?? 0
);

$documento = $documentoModelo->obtenerPorId($id_documento);

if (
    !$documento ||
    (int)$documento['id_obra'] !== $id_obra
) {
    documentoMensaje(
        'error',
        'El documento no existe o no pertenece a esta obra.'
    );

    documentoRedireccionar($id_obra);
}

/*
|--------------------------------------------------------------------------
| DESCARGAR DOCUMENTO
|--------------------------------------------------------------------------
*/

if ($accion === 'descargar') {

    $ruta = dirname(__DIR__) . '/' .
        ltrim($documento['ruta_archivo'], '/\\');

    $rutaReal = realpath($ruta);
    $directorioReal = realpath($directorio);

    if (
        !$rutaReal ||
        !$directorioReal ||
        strpos(
            $rutaReal,
            $directorioReal . DIRECTORY_SEPARATOR
        ) !== 0 ||
        !is_file($rutaReal)
    ) {
        documentoMensaje(
            'error',
            'No se encontró el archivo solicitado.'
        );

        documentoRedireccionar($id_obra);
    }

    $nombreDescarga = basename($documento['nombre_archivo']);

    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header(
        'Content-Disposition: attachment; filename="' .
        str_replace('"', '', $nombreDescarga) . '"'
    );
    header('Content-Length: ' . filesize($rutaReal));
    header('Cache-Control: private');

    readfile($rutaReal);
    exit;
}

/*
|--------------------------------------------------------------------------
| DESCRIBIR DOCUMENTO
|--------------------------------------------------------------------------
*/

if ($accion === 'describir') {

    $descripcion = trim($_POST['descripcion'] ?? '');

    try {

        $documentoModelo->actualizarDescripcion(
            $id_documento,
            $descripcion
        );

        documentoMensaje(
            'success',
            'La descripción se actualizó correctamente.'
        );

    } catch (Throwable $e) {

        error_log('Error actualizando descripción: ' . $e->getMessage());

        documentoMensaje(
            'error',
            'No se pudo actualizar la descripción.'
        );
    }

    documentoRedireccionar($id_obra);
}

/*
|--------------------------------------------------------------------------
| ELIMINAR DOCUMENTO
|--------------------------------------------------------------------------
*/

if ($accion === 'eliminar') {

    try {

        $documentoModelo->eliminar($id_documento);

        $ruta = dirname(__DIR__) . '/' .
            ltrim($documento['ruta_archivo'], '/\\');

        $rutaReal = realpath($ruta);
        $directorioReal = realpath($directorio);

        if (
            $rutaReal &&
            $directorioReal &&
            strpos(
                $rutaReal,
                $directorioReal . DIRECTORY_SEPARATOR
            ) === 0 &&
            is_file($rutaReal)
        ) {
            unlink($rutaReal);
        }

        documentoMensaje(
            'success',
            'El documento se eliminó correctamente.'
        );

    } catch (Throwable $e) {

        error_log('Error eliminando documento: ' . $e->getMessage());

        documentoMensaje(
            'error',
            'No se pudo eliminar el documento.'
        );
    }

    documentoRedireccionar($id_obra);
}

/*
|--------------------------------------------------------------------------
| ACCIÓN NO VÁLIDA
|--------------------------------------------------------------------------
*/

documentoMensaje('error', 'La acción solicitada no es válida.');

documentoRedireccionar($id_obra);
