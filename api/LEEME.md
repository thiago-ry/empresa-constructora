# API Constructora – guía de instalación

Requisitos: PHP 8.0 o superior (con `mysqli`, `mysqlnd` y `fileinfo`, que XAMPP trae activadas) y Apache con `mod_rewrite`.
**Hay que ejecutar UNA migración SQL** (`migracion_v3.sql`, paso 2) para las solicitudes de herramientas y los consumos por obra. El resto usa las tablas que ya tiene tu base.
Escrita para PHP 7.4 o superior y MariaDB 10.4 (la de tu XAMPP).

## 1. Instalar

1. La carpeta tiene que quedar en `htdocs\empresa_constructora\api` (así coincide con la dirección que usa la app: `http://TU-IP/empresa_constructora/api`). Si usás otra ruta, cambiala en el `.env` de la app.
2. Si tenías fotos subidas antes, copiá tus carpetas `uploads\obras` y `uploads\incidencias` viejas dentro de `api\uploads\`.
3. Abrí `config.php` y revisá `db` (usuario y contraseña de MySQL). `auth_secret` ya viene con una clave única generada para vos: no la compartas ni la subas a git.
4. Verificá que **no** queden estos archivos viejos: `conexion.php`, `planning.php`, `materiales.php`, `obtener_obras.php`.

## 2. Ejecutar la migración SQL (una sola vez)

1. Entrá a phpMyAdmin → tu base `constructora` → pestaña **SQL**.
2. Pegá el contenido de `migracion_v3.sql` y tocá **Continuar**. Se puede correr más de una vez sin problema.

Qué hace: crea `solicitud_herramienta` y `detalle_solicitud_herramienta`; agrega `id_usuario` y `observacion` a `solicitud_material`; agrega `id_obra` a `movimiento_material`. El panel web sigue funcionando igual (son columnas nuevas, vacías por defecto).
Sin la migración la API no se rompe: las solicitudes de herramientas avisan que falta ejecutarla y los consumos por obra quedan desactivados.

## 3. Quién puede entrar y qué ve

| Rol | Obras que ve | Puede |
| --- | --- | --- |
| Gerente (2) | Todas las que están en *Planificación* o *En Proceso* | Todo |
| Jefe de Obra (4) | Las que lo tienen como `id_jefe_obra` | Todo en sus obras: cerrar incidencias, subir documentos |
| Capataz (7) | La que lo tiene como `id_capataz` | Cargar avances, asistencia, materiales, herramientas e incidencias |

Los usuarios dados de baja (`usuario.estado = 0`) no pueden entrar. Para probar, usá un Gerente (ve todo) o asignale una obra a un Jefe/Capataz en el panel web. Si un Jefe/Capataz no tiene ninguna obra asignada, la app le dirá que no tiene obras (es lo correcto). Solo para pruebas podés poner `'modo_compatibilidad' => true` en `config.php`.

## 4. Contraseñas

Hoy están guardadas en texto plano y el login las acepta así. Para migrarlas a hash seguro, poné `'migrar_hash_claves' => true` en `config.php`: cada usuario se convierte la próxima vez que inicie sesión. **Activalo solo si el panel web no lee esa columna en texto plano**, porque dejaría de funcionar para esos usuarios.

## 5. Probar que anda

```powershell
# debe devolver "status":"success" y un token
curl.exe -X POST http://TU-IP/empresa_constructora/api/login.php -H "Content-Type: application/json" -d "{\"correo\":\"tu@correo.com\",\"password\":\"tuclave\"}"

# sin token debe dar 401 (antes devolvía datos a cualquiera)
curl.exe -i http://TU-IP/empresa_constructora/api/herramientas.php
```

Y comprobá el bloqueo de ejecución: creá `uploads\prueba.php` con `<?php echo 1;`, abrilo en el navegador (debe dar **403**) y borralo.

## 6. Documentos del panel web (opcional)

La tabla `documento_obra` ya existe y se comparte con el panel web. Los documentos que suba la app quedan en `api\uploads\documentos`. Para que la app también pueda abrir los que se suben desde el panel web, poné en `config.php` la dirección donde están esos archivos:
`'url_archivos_web' => 'http://TU-IP/empresa_constructora'`.

## Endpoints nuevos (v3)

| Archivo | Para qué |
| --- | --- |
| `reporte_asistencia.php` | Días y horas trabajadas por persona entre dos fechas (base del PDF). |
| `asistencia.php` (`accion: salida`) | Marca la hora de salida; sin ella no se pueden calcular horas. |
| `solicitudes_materiales.php` | Pedir materiales al depósito y ver si los aprobaron / entregaron. |
| `solicitudes_herramientas.php` | Pedir herramientas; Jefe de Obra / Gerente aprueban, rechazan o marcan "entregada" (queda asignada a la obra). |
| `materiales_obra.php` | Materiales asignados a la obra: recibido, consumido y disponible. |
| `herramientas.php?asignadas=1` | Herramientas asignadas a la obra (en obra y devueltas). |
| `presupuesto.php` | Catálogo con precios de referencia, cálculo y guardado de presupuestos (guardar: Jefe de Obra / Gerente). |

### Opción `consumo_descuenta_deposito` (config.php)
Tu panel web ya descuenta el stock del depósito cuando entrega una solicitud. Si la app también lo descuenta al registrar un consumo en obra, el mismo material se resta **dos veces**. Con `true` (valor por defecto) todo sigue como antes. Con `false` el consumo solo resta de lo que la obra recibió. Recomendado: `false`, cuando confirmes que tu panel descuenta al entregar.

### Para tener en cuenta
- Las **horas** salen de `hora_salida − hora_entrada`. La entrada se guarda al guardar el parte y la salida al tocar "Salida" en la app: son horas de marcación, no de reloj biométrico.
- Los precios del presupuesto son los más bajos de `precio_material` entre proveedores; se pueden editar en la app antes de calcular.
- Las solicitudes de herramientas aún no existen en el panel web: hasta que las agregues allá, se responden desde la app (Jefe de Obra / Gerente).

## Qué cambió respecto a la versión anterior

- Todos los endpoints exigen sesión (token firmado) y el usuario sale del token.
- Consultas preparadas en todos lados (se acabó la inyección SQL).
- Fotos validadas por su contenido real, con nombre aleatorio; `uploads/` no puede ejecutar código.
- Stock de materiales y devoluciones de herramientas dentro de una transacción con bloqueo.
- **Herramientas**: ahora se devuelve lo que está *asignado a la obra* (con lo que falta devolver) y no se puede devolver más de lo asignado. Al completarse, la asignación pasa a "Devuelta".
- **Asistencia**: se guarda con la obra y con filas "Ausente" explícitas, igual que usa tu panel web.
- **Incidencias**: la foto va en `incidencia.foto`; al resolver se puede registrar la solución; "abiertas" incluye *En revisión*.
- El login frena después de 5 intentos fallidos (15 min).
