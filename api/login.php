<?php
require __DIR__ . '/bootstrap.php';
iniciar_api(['POST']);

const MAX_INTENTOS = 5;
const VENTANA_SEGUNDOS = 900; // 15 minutos

$datos = leer_json();
$correo = trim((string) ($datos['correo'] ?? ''));
$password = trim((string) ($datos['password'] ?? ''));

if ($correo === '' || $password === '') {
    responder_error('Ingresá tu correo y contraseña.', 422);
}

/* --- Freno contra adivinar contraseñas: 5 fallos por correo + IP cada 15 min --- */
$archivoIntentos = sys_get_temp_dir() . '/constructora_login_' . sha1(strtolower($correo) . '|' . ($_SERVER['REMOTE_ADDR'] ?? '')) . '.json';
$intentos = ['n' => 0, 't' => time()];
if (is_file($archivoIntentos)) {
    $guardado = json_decode((string) file_get_contents($archivoIntentos), true);
    if (is_array($guardado) && time() - (int) ($guardado['t'] ?? 0) < VENTANA_SEGUNDOS) {
        $intentos = ['n' => (int) $guardado['n'], 't' => (int) $guardado['t']];
    }
}
if ($intentos['n'] >= MAX_INTENTOS) {
    $minutos = max(1, (int) ceil((VENTANA_SEGUNDOS - (time() - $intentos['t'])) / 60));
    responder_error("Demasiados intentos fallidos. Probá de nuevo en $minutos min.", 429);
}

/* --- Verificación --- */
$usuario = consultar_fila(
    'SELECT id_usuario, nombre, apellido, correo, id_rol, estado, `contraseña` AS clave FROM usuario WHERE correo = ? LIMIT 1',
    's',
    [$correo]
);

$ok = false;
$guardada = (string) ($usuario['clave'] ?? '');
$esHash = $guardada !== '' && (strpos($guardada, '$2y$') === 0 || strpos($guardada, '$argon2') === 0);
if ($usuario !== null) {
    // Acepta hashes seguros y, mientras dure la migración, contraseñas viejas en texto plano.
    $ok = $esHash ? password_verify($password, $guardada) : hash_equals($guardada, $password);
}

if (!$ok) {
    $intentos['n']++;
    @file_put_contents($archivoIntentos, json_encode($intentos));
    responder_error('Correo o contraseña incorrectos.', 401);
}

if ((int) $usuario['estado'] !== 1) {
    responder_error('Tu usuario está dado de baja. Consultá con la oficina.', 403);
}
if (!in_array((int) $usuario['id_rol'], ROLES_PERMITIDOS, true)) {
    responder_error('Esta aplicación es solo para Gerentes, Jefes de Obra y Capataces.', 403);
}

@unlink($archivoIntentos);

/* --- Migración opcional de contraseñas en texto plano a hash --- */
if (!$esHash && config('migrar_hash_claves', false)) {
    ejecutar(
        'UPDATE usuario SET `contraseña` = ? WHERE id_usuario = ?',
        'si',
        [password_hash($password, PASSWORD_DEFAULT), (int) $usuario['id_usuario']]
    );
}

/* --- Registro de acceso (si falla no impide el ingreso) --- */
try {
    ejecutar('INSERT INTO acceso_usuario (id_usuario, fecha_hora_ingreso) VALUES (?, NOW())', 'i', [(int) $usuario['id_usuario']]);
} catch (Throwable $e) {
    error_log('[constructora-api] No se pudo registrar el acceso: ' . $e->getMessage());
}

$sesion = ['id_usuario' => (int) $usuario['id_usuario'], 'id_rol' => (int) $usuario['id_rol']];

/* --- El capataz trabaja siempre en su obra --- */
$obra = null;
if ($sesion['id_rol'] === ROL_CAPATAZ) {
    $obras = obras_del_usuario($sesion);
    if ($obras) {
        $obra = [
            'id_obra' => (int) $obras[0]['id_obra'],
            'nombre_obra' => $obras[0]['nombre_obra'],
            'direccion' => $obras[0]['direccion'],
        ];
    }
}

[$token, $vence] = emitir_token($sesion['id_usuario'], $sesion['id_rol']);

responder_ok([
    'token' => $token,
    'expira' => $vence,
    'user' => [
        'id_usuario' => $sesion['id_usuario'],
        'nombre' => $usuario['nombre'],
        'apellido' => $usuario['apellido'],
        'correo' => $usuario['correo'],
        'id_rol' => $sesion['id_rol'],
    ],
    'obra' => $obra,
], '¡Inicio de sesión exitoso!');
