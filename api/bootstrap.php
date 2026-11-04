<?php
/**
 * Núcleo común de la API: configuración, CORS, respuestas JSON, base de datos,
 * sesión (token firmado) y subida segura de fotos.
 *
 * Requiere PHP 8.0 o superior.
 */
declare(strict_types=1);

const ROL_GERENTE = 2;
const ROL_JEFE_OBRA = 4;
const ROL_CAPATAZ = 7;
/** Roles que pueden usar la app. El Gerente ve todas las obras; Jefe y Capataz, solo las suyas. */
const ROLES_PERMITIDOS = [ROL_GERENTE, ROL_JEFE_OBRA, ROL_CAPATAZ];
/** Estados de obra en los que se trabaja (obra.estado es un ENUM de texto). */
const OBRA_ESTADOS_OPERATIVOS = "'Planificacion','En Proceso'";
const FOTO_MAX_BYTES = 10 * 1024 * 1024;

/** Error de validación: el mensaje es seguro para mostrarle al usuario (HTTP 422). */
class ErrorValidacion extends RuntimeException
{
}

/* -------------------------------------------------------------------------- */
/* Configuración                                                              */
/* -------------------------------------------------------------------------- */

function config(string $clave, $defecto = null)
{
    static $cfg = null;
    if ($cfg === null) {
        $archivo = __DIR__ . '/config.php';
        if (!is_file($archivo)) {
            http_response_code(500);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['status' => 'error', 'message' => 'Falta api/config.php (copiá config.example.php).']);
            exit;
        }
        $cfg = require $archivo;
    }
    return $cfg[$clave] ?? $defecto;
}

/* -------------------------------------------------------------------------- */
/* Respuestas                                                                 */
/* -------------------------------------------------------------------------- */

function responder(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function responder_error(string $mensaje, int $codigo = 400): void
{
    responder(['status' => 'error', 'message' => $mensaje], $codigo);
}

function responder_ok(array $extra = [], string $mensaje = ''): void
{
    $respuesta = ['status' => 'success'];
    if ($mensaje !== '') {
        $respuesta['message'] = $mensaje;
    }
    responder($respuesta + $extra);
}

/**
 * Debe llamarse al inicio de cada endpoint. Configura cabeceras, CORS, manejo de
 * errores y valida el método HTTP.
 */
function iniciar_api(array $metodos): void
{
    date_default_timezone_set((string) config('zona_horaria', 'America/Argentina/Buenos_Aires'));
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');

    $origen = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origen !== '' && in_array($origen, (array) config('origenes_permitidos', []), true)) {
        header('Access-Control-Allow-Origin: ' . $origen);
        header('Vary: Origin');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        header('Access-Control-Allow-Methods: ' . implode(', ', $metodos) . ', OPTIONS');
        header('Access-Control-Max-Age: 600');
    }

    set_exception_handler(function (Throwable $e): void {
        if ($e instanceof ErrorValidacion) {
            responder_error($e->getMessage(), 422);
        }
        error_log('[constructora-api] ' . get_class($e) . ': ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
        $mensaje = config('debug', false) ? $e->getMessage() : 'Error interno del servidor.';
        responder_error($mensaje, 500);
    });

    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($metodo === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
    if (!in_array($metodo, $metodos, true)) {
        header('Allow: ' . implode(', ', $metodos));
        responder_error('Método no permitido.', 405);
    }

    // Si la subida supera post_max_size, PHP descarta todo el cuerpo en silencio.
    if (
        $metodo === 'POST'
        && empty($_POST)
        && empty($_FILES)
        && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0
        && stripos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') === 0
    ) {
        responder_error('El archivo es demasiado pesado. Probá con uno más liviano.', 413);
    }
}

function leer_json(): array
{
    $crudo = file_get_contents('php://input');
    $datos = json_decode($crudo === false ? '' : $crudo, true);
    return is_array($datos) ? $datos : [];
}

/* -------------------------------------------------------------------------- */
/* Base de datos                                                              */
/* -------------------------------------------------------------------------- */

function db(): mysqli
{
    static $conexion = null;
    if ($conexion === null) {
        $c = (array) config('db', []);
        $conexion = new mysqli(
            (string) ($c['host'] ?? 'localhost'),
            (string) ($c['user'] ?? 'root'),
            (string) ($c['pass'] ?? ''),
            (string) ($c['name'] ?? 'constructora')
        );
        $conexion->set_charset('utf8mb4');
    }
    return $conexion;
}

/** Ejecuta un SELECT preparado y devuelve todas las filas. */
function consultar(string $sql, string $tipos = '', array $params = []): array
{
    $stmt = db()->prepare($sql);
    if ($tipos !== '') {
        $stmt->bind_param($tipos, ...$params);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();
    $filas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $filas;
}

function consultar_fila(string $sql, string $tipos = '', array $params = []): ?array
{
    $filas = consultar($sql, $tipos, $params);
    return $filas[0] ?? null;
}

function consultar_valor(string $sql, string $tipos = '', array $params = [])
{
    $fila = consultar_fila($sql, $tipos, $params);
    return $fila === null ? null : array_values($fila)[0];
}

/** Ejecuta INSERT/UPDATE/DELETE preparado. Devuelve las filas afectadas. */
function ejecutar(string $sql, string $tipos = '', array $params = []): int
{
    $stmt = db()->prepare($sql);
    if ($tipos !== '') {
        $stmt->bind_param($tipos, ...$params);
    }
    $stmt->execute();
    $afectadas = $stmt->affected_rows;
    $stmt->close();
    return $afectadas;
}

/* -------------------------------------------------------------------------- */
/* Sesión (token firmado, sin tabla extra)                                    */
/* -------------------------------------------------------------------------- */

function base64url_encode(string $datos): string
{
    return rtrim(strtr(base64_encode($datos), '+/', '-_'), '=');
}

function base64url_decode(string $datos)
{
    return base64_decode(strtr($datos, '-_', '+/'), true);
}

function secreto_firma(): string
{
    $secreto = (string) config('auth_secret', '');
    if (strlen($secreto) < 32 || strpos($secreto, 'CAMBIAR_') === 0) {
        error_log('[constructora-api] auth_secret sin configurar en config.php');
        responder_error('El servidor no está configurado correctamente.', 500);
    }
    return $secreto;
}

/** @return array{0:string,1:int} [token, timestamp de vencimiento] */
function emitir_token(int $idUsuario, int $idRol): array
{
    $vence = time() + (int) config('token_ttl', 1209600);
    $payload = base64url_encode((string) json_encode(['u' => $idUsuario, 'r' => $idRol, 'e' => $vence]));
    $firma = base64url_encode(hash_hmac('sha256', $payload, secreto_firma(), true));
    return [$payload . '.' . $firma, $vence];
}

function cabecera_authorization(): string
{
    foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION'] as $clave) {
        if (!empty($_SERVER[$clave])) {
            return (string) $_SERVER[$clave];
        }
    }
    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $nombre => $valor) {
            if (strcasecmp((string) $nombre, 'Authorization') === 0) {
                return (string) $valor;
            }
        }
    }
    return '';
}

/**
 * Exige una sesión válida. Devuelve ['id_usuario' => int, 'id_rol' => int]
 * o corta la petición con 401/403.
 */
function usuario_actual(): array
{
    if (!preg_match('/^Bearer\s+(\S+)$/i', cabecera_authorization(), $m)) {
        responder_error('Necesitás iniciar sesión.', 401);
    }
    $partes = explode('.', $m[1]);
    if (count($partes) !== 2) {
        responder_error('Sesión inválida. Volvé a iniciar sesión.', 401);
    }
    [$payload, $firma] = $partes;

    $esperada = base64url_encode(hash_hmac('sha256', $payload, secreto_firma(), true));
    if (!hash_equals($esperada, $firma)) {
        responder_error('Sesión inválida. Volvé a iniciar sesión.', 401);
    }

    $datos = json_decode((string) base64url_decode($payload), true);
    if (!is_array($datos) || (int) ($datos['e'] ?? 0) < time()) {
        responder_error('Tu sesión venció. Volvé a iniciar sesión.', 401);
    }

    // Se verifica contra la base en cada pedido: si cambian el rol o borran al usuario, se nota al instante.
    $fila = consultar_fila('SELECT id_usuario, id_rol FROM usuario WHERE id_usuario = ? AND estado = 1', 'i', [(int) ($datos['u'] ?? 0)]);
    if ($fila === null || !in_array((int) $fila['id_rol'], ROLES_PERMITIDOS, true)) {
        responder_error('Tu usuario ya no tiene acceso a la app.', 403);
    }

    return ['id_usuario' => (int) $fila['id_usuario'], 'id_rol' => (int) $fila['id_rol']];
}

/**
 * Obras en las que el usuario puede trabajar:
 *  - Gerente: todas las operativas.
 *  - Jefe de obra: donde figura en obra.id_jefe_obra.
 *  - Capataz: donde figura en obra.id_capataz.
 *
 * @return array<int,array{id_obra:int|string,nombre_obra:string,direccion:string}>
 */
function obras_del_usuario(array $usuario): array
{
    $base = 'SELECT o.id_obra, o.nombre_obra, o.direccion FROM obra o
             WHERE o.activo = 1 AND o.estado IN (' . OBRA_ESTADOS_OPERATIVOS . ')';
    $orden = ' ORDER BY o.nombre_obra ASC';

    if ($usuario['id_rol'] === ROL_GERENTE) {
        return consultar($base . $orden);
    }

    $columna = $usuario['id_rol'] === ROL_JEFE_OBRA ? 'o.id_jefe_obra' : 'o.id_capataz';
    $filas = consultar($base . " AND $columna = ?" . $orden, 'i', [$usuario['id_usuario']]);

    // Modo compatibilidad: quien no tiene ninguna obra asignada ve todas las operativas.
    if (!$filas && config('modo_compatibilidad', false)) {
        return consultar($base . $orden);
    }
    return $filas;
}

/** Corta con 403 si el usuario no puede operar sobre la obra indicada. */
function exigir_acceso_obra(array $usuario, int $idObra): void
{
    if ($idObra <= 0) {
        responder_error('Falta indicar la obra.', 422);
    }
    foreach (obras_del_usuario($usuario) as $obra) {
        if ((int) $obra['id_obra'] === $idObra) {
            return;
        }
    }
    responder_error('No tenés acceso a esta obra.', 403);
}

/** Corta con 403 si el usuario es Capataz (acciones reservadas a Jefe de Obra y Gerente). */
function exigir_gestion(array $usuario): void
{
    if ($usuario['id_rol'] === ROL_CAPATAZ) {
        responder_error('Esta acción es solo para el Jefe de Obra.', 403);
    }
}

/** ¿La excepción es "la tabla no existe" (1146)? */
function tabla_inexistente(Throwable $e): bool
{
    return $e instanceof mysqli_sql_exception && (int) $e->getCode() === 1146;
}

/** ¿Existe la tabla? (así la API avisa claro si falta correr la migración SQL). */
function tabla_existe(string $tabla): bool
{
    static $cache = [];
    if (!isset($cache[$tabla])) {
        $cache[$tabla] = (int) consultar_valor(
            'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
            's',
            [$tabla]
        ) > 0;
    }
    return $cache[$tabla];
}

/** ¿Existe la columna? Permite que la API funcione con o sin la migración SQL aplicada. */
function columna_existe(string $tabla, string $columna): bool
{
    static $cache = [];
    $clave = $tabla . '.' . $columna;
    if (!isset($cache[$clave])) {
        $cache[$clave] = (int) consultar_valor(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            'ss',
            [$tabla, $columna]
        ) > 0;
    }
    return $cache[$clave];
}

/** Corta con un mensaje claro si falta una tabla de la migración. */
function exigir_tabla(string $tabla): void
{
    if (!tabla_existe($tabla)) {
        responder_error('Falta ejecutar api/migracion_v3.sql en la base de datos (no existe la tabla ' . $tabla . ').', 503);
    }
}

/** Placeholders para un IN (...): devuelve "?, ?, ?" y el string de tipos "iii". */
function placeholders_in(int $cantidad, string $tipo = 'i'): array
{
    return [implode(', ', array_fill(0, $cantidad, '?')), str_repeat($tipo, $cantidad)];
}

/**
 * Material que la obra recibió del depósito menos lo que registró como consumido.
 * Lo recibido sale de las entregas (entrega_material) hechas a solicitudes de esa obra.
 */
function material_disponible_en_obra(int $idObra, int $idMaterial): float
{
    $entregado = (float) consultar_valor(
        'SELECT IFNULL(SUM(de.cantidad_entregada), 0)
         FROM entrega_material e
         INNER JOIN solicitud_material s ON s.id_solicitud = e.id_solicitud
         INNER JOIN detalle_entrega_material de ON de.id_entrega = e.id_entrega
         WHERE s.id_obra = ? AND de.id_material = ?',
        'ii',
        [$idObra, $idMaterial]
    );
    $consumido = 0.0;
    if (columna_existe('movimiento_material', 'id_obra')) {
        $consumido = (float) consultar_valor(
            "SELECT IFNULL(SUM(cantidad), 0) FROM movimiento_material WHERE id_obra = ? AND id_material = ? AND tipo = 'EGRESO'",
            'ii',
            [$idObra, $idMaterial]
        );
    }
    return $entregado - $consumido;
}

/** Número decimal positivo y razonable, o error de validación. */
function cantidad_valida($valor, string $nombreCampo, float $maximo = 1000000.0): float
{
    $n = is_numeric($valor) ? (float) $valor : 0.0;
    if (!is_finite($n) || $n <= 0) {
        throw new ErrorValidacion("$nombreCampo tiene que ser mayor a cero.");
    }
    if ($n > $maximo) {
        throw new ErrorValidacion("$nombreCampo es demasiado grande.");
    }
    return $n;
}

/**
 * Personal activo de la obra en una fecha (tabla empleado_obra).
 *
 * @return array<int,array{id_usuario:int|string,nombre:string,apellido:string,cargo:string}>
 */
function personal_de_obra(int $idObra, string $fecha): array
{
    return consultar(
        "SELECT u.id_usuario, u.nombre, u.apellido, IFNULL(MAX(c.nombre_cargo), 'Sin cargo') AS cargo
         FROM empleado_obra eo
         INNER JOIN usuario u ON u.id_usuario = eo.id_usuario AND u.estado = 1
         LEFT JOIN cargo c ON c.id_cargo = eo.id_cargo
         WHERE eo.id_obra = ?
           AND eo.fecha_ingreso <= ?
           AND (eo.fecha_egreso >= ? OR (eo.fecha_egreso IS NULL AND eo.estado = 1))
         GROUP BY u.id_usuario, u.nombre, u.apellido
         ORDER BY u.apellido, u.nombre",
        'iss',
        [$idObra, $fecha, $fecha]
    );
}

/* -------------------------------------------------------------------------- */
/* Validación                                                                 */
/* -------------------------------------------------------------------------- */

function texto_requerido(?string $valor, string $nombreCampo, int $maximo = 2000): string
{
    $valor = trim((string) $valor);
    if ($valor === '') {
        throw new ErrorValidacion("$nombreCampo es obligatorio.");
    }
    if (mb_strlen($valor) > $maximo) {
        throw new ErrorValidacion("$nombreCampo no puede superar los $maximo caracteres.");
    }
    return $valor;
}

function fecha_valida(?string $valor): string
{
    $hoy = date('Y-m-d');
    if ($valor === null || $valor === '') {
        return $hoy;
    }
    $fecha = DateTime::createFromFormat('Y-m-d', $valor);
    if ($fecha === false || $fecha->format('Y-m-d') !== $valor) {
        throw new ErrorValidacion('La fecha no es válida.');
    }
    if ($valor > $hoy) {
        throw new ErrorValidacion('No se puede registrar una fecha futura.');
    }
    return $valor;
}

/* -------------------------------------------------------------------------- */
/* Fotos                                                                      */
/* -------------------------------------------------------------------------- */

/**
 * Guarda un archivo recibido por multipart. Valida el contenido real (no confía en el
 * nombre ni en la extensión que manda el cliente).
 *
 * @param array<string,string> $permitidos  mime => extensión (ej. ['image/jpeg' => 'jpg'])
 * @return string|null Ruta relativa (ej. "uploads/obras/obra_20261013_ab12.jpg") o null si no se envió archivo.
 */
function guardar_archivo(string $campo, string $subcarpeta, string $prefijo, array $permitidos, int $maxBytes): ?string
{
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $archivo = $_FILES[$campo];

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $pesado = in_array($archivo['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);
        throw new ErrorValidacion($pesado ? 'El archivo es demasiado pesado.' : 'No se pudo recibir el archivo. Intentá de nuevo.');
    }
    if (!is_uploaded_file($archivo['tmp_name'])) {
        throw new ErrorValidacion('Archivo inválido.');
    }
    if ($archivo['size'] > $maxBytes) {
        throw new ErrorValidacion('El archivo es demasiado pesado (máximo ' . (int) ($maxBytes / 1048576) . ' MB).');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
    if (!isset($permitidos[$mime])) {
        throw new ErrorValidacion('Formato no permitido. Se aceptan: ' . strtoupper(implode(', ', array_unique($permitidos))) . '.');
    }
    if (strpos($mime, 'image/') === 0 && @getimagesize($archivo['tmp_name']) === false) {
        throw new ErrorValidacion('La imagen está dañada o no es válida.');
    }

    $directorio = __DIR__ . '/uploads/' . $subcarpeta;
    if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
        throw new RuntimeException('No se pudo crear la carpeta de archivos.');
    }

    $nombre = $prefijo . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $permitidos[$mime];
    if (!move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombre)) {
        throw new RuntimeException('No se pudo guardar el archivo en el servidor.');
    }

    return 'uploads/' . $subcarpeta . '/' . $nombre;
}

/** Foto de obra o incidencia: JPG, PNG o WebP de hasta 10 MB. */
function guardar_foto(string $campo, string $subcarpeta, string $prefijo): ?string
{
    return guardar_archivo(
        $campo,
        $subcarpeta,
        $prefijo,
        ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'],
        FOTO_MAX_BYTES
    );
}

function borrar_foto(?string $ruta): void
{
    if ($ruta !== null && $ruta !== '') {
        $absoluta = __DIR__ . '/' . $ruta;
        if (is_file($absoluta)) {
            @unlink($absoluta);
        }
    }
}
