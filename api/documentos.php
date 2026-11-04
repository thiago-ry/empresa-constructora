<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['GET', 'POST']);

// Tipos que ya usa el sistema web (documento_obra.tipo_documento).
const TIPOS_DOCUMENTO = ['Plano', 'Contrato', 'Permiso', 'Otro'];
const DOCUMENTO_MAX_BYTES = 20 * 1024 * 1024;

$usuario = usuario_actual();

/** Los archivos subidos desde la app están en esta API; los del panel web pueden estar en otra carpeta. */
function url_del_archivo(string $ruta): ?string
{
    if (preg_match('#^https?://#i', $ruta)) {
        return $ruta;
    }
    if (is_file(__DIR__ . '/' . ltrim($ruta, '/'))) {
        return null; // la app la arma con la dirección de la API
    }
    $base = trim((string) config('url_archivos_web', ''));
    return $base !== '' ? rtrim($base, '/') . '/' . ltrim($ruta, '/') : null;
}

function mime_por_extension(string $ruta): string
{
    $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
    return ['pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp'][$ext] ?? 'application/octet-stream';
}

/* ------------------------------ GET: listado ------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idObra = (int) ($_GET['id_obra'] ?? 0);
    exigir_acceso_obra($usuario, $idObra);

    $filas = consultar(
        'SELECT id_documento, tipo_documento, nombre_archivo, ruta_archivo, descripcion, fecha_subida
         FROM documento_obra WHERE id_obra = ? ORDER BY fecha_subida DESC LIMIT 100',
        'i',
        [$idObra]
    );

    $documentos = array_map(static fn(array $f): array => [
        'id_documento' => (int) $f['id_documento'],
        'nombre' => $f['nombre_archivo'],
        'tipo' => $f['tipo_documento'],
        'descripcion' => $f['descripcion'],
        'archivo' => $f['ruta_archivo'],
        'url' => url_del_archivo((string) $f['ruta_archivo']),
        'mime' => mime_por_extension((string) $f['ruta_archivo']),
        'fecha' => $f['fecha_subida'],
    ], $filas);

    responder_ok(['documentos' => $documentos]);
}

/* ------------------------------ POST: subir ------------------------------- */
exigir_gestion($usuario);
$idObra = (int) ($_POST['id_obra'] ?? 0);
exigir_acceso_obra($usuario, $idObra);

$nombre = texto_requerido($_POST['nombre'] ?? '', 'El nombre', 255);
$descripcion = mb_substr(trim((string) ($_POST['descripcion'] ?? '')), 0, 1000);
$tipo = trim((string) ($_POST['tipo'] ?? 'Otro'));
if (!in_array($tipo, TIPOS_DOCUMENTO, true)) {
    $tipo = 'Otro';
}

$ruta = guardar_archivo(
    'archivo',
    'documentos',
    'doc_' . $idObra,
    ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'],
    DOCUMENTO_MAX_BYTES
);
if ($ruta === null) {
    throw new ErrorValidacion('Adjuntá el archivo del documento.');
}

try {
    ejecutar(
        'INSERT INTO documento_obra (id_obra, tipo_documento, nombre_archivo, ruta_archivo, descripcion, id_usuario) VALUES (?, ?, ?, ?, ?, ?)',
        'issssi',
        [$idObra, $tipo, $nombre, $ruta, $descripcion !== '' ? $descripcion : null, $usuario['id_usuario']]
    );
} catch (Throwable $e) {
    borrar_foto($ruta);
    throw $e;
}

responder_ok(['id_documento' => db()->insert_id], 'Documento subido.');
