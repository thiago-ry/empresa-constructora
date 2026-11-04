<?php
// Copiá este archivo como config.php y completá tus datos. config.php NO se sube a git.
return [
    'db' => [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'constructora',
    ],

    // Clave para firmar las sesiones. Tiene que ser larga y secreta (mínimo 32 caracteres).
    // Generá una nueva con:  php -r "echo bin2hex(random_bytes(32));"
    'auth_secret' => '547054bee4576b2af2972694a633244749336c868f4667be8dc2ae01a6d787c7',

    // Duración de la sesión en segundos (14 días).
    'token_ttl' => 1209600,

    'zona_horaria' => 'America/Argentina/Buenos_Aires',

    // Orígenes web que pueden llamar a la API (solo importa para la versión web de Expo).
    'origenes_permitidos' => [
        'http://localhost:8081',
        'http://localhost:19006',
    ],

    // Quién ve qué obras: el Gerente ve todas; el Jefe de Obra, las que tiene en obra.id_jefe_obra;
    // el Capataz, la que tiene en obra.id_capataz.
    // true  = si un Jefe/Capataz no tiene ninguna obra asignada, ve todas (útil solo para probar).
    // false = ve únicamente las suyas (recomendado).
    'modo_compatibilidad' => false,

    // Opcional: dirección web donde están los archivos subidos desde el panel web de la empresa,
    // para poder abrir esos documentos desde la app. Ej: 'http://192.168.0.10/empresa_constructora'
    'url_archivos_web' => '',

    // true = al iniciar sesión, las contraseñas en texto plano se convierten a hash seguro.
    // ATENCIÓN: activalo solo si NINGÚN otro sistema (panel web, etc.) lee esa columna en texto plano.
    'migrar_hash_claves' => false,

    // Qué hace un "consumo en obra" con el stock del depósito:
    //   true  = también lo resta del depósito (comportamiento de siempre de la app).
    //   false = solo lo resta de lo que la obra recibió. Recomendado si el panel web ya descuenta el
    //           stock al entregar una solicitud, porque si no el mismo material se resta dos veces.
    'consumo_descuenta_deposito' => true,

    // true = las respuestas de error muestran el detalle técnico. Solo en desarrollo.
    'debug' => false,
];
