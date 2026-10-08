<?php

require_once __DIR__ . "/../modelos/Incidencia.php";
require_once __DIR__ . "/../config/permisos.php";
require_once __DIR__ . "/../modelos/Auditoria.php";

verificarPermiso("obras");

$incidencia = new Incidencia();
$auditoria = new Auditoria();

$accion = $_GET["accion"] ?? "listar";

$id_usuario = $_SESSION["usuario"]["id_usuario"]
    ?? $_SESSION["usuario"]["id"]
    ?? null;


/* =====================================================
   LISTAR
   ===================================================== */

if ($accion === "listar") {

    $id_obra = $_GET["id_obra"] ?? 0;

    $incidencias = $incidencia->obtenerPorObra($id_obra);

    $resumen = $incidencia->obtenerResumen($id_obra);

    require_once __DIR__ . "/../vistas/obras/incidencias/index.php";

    exit;
}


/* =====================================================
   CREAR
   ===================================================== */

if ($accion === "crear") {

    $id_obra = $_GET["id_obra"] ?? 0;

    require_once __DIR__ . "/../vistas/obras/incidencias/crear.php";

    exit;
}


/* =====================================================
   GUARDAR
   ===================================================== */

if ($accion === "guardar") {

    $id_obra = $_POST["id_obra"] ?? 0;

    $datos = [
        "id_obra" => $id_obra,
        "fecha" => $_POST["fecha"] ?? date("Y-m-d"),
        "tipo_incidencia" => $_POST["tipo_incidencia"] ?? "",
        "descripcion" => $_POST["descripcion"] ?? "",
        "estado" => $_POST["estado"] ?? "Pendiente",
        "solucion" => $_POST["solucion"] ?? null
    ];

    if ($incidencia->crear($datos)) {

        $id_incidencia = $incidencia->obtenerUltimoId();


        /* =============================================
           GUARDAR VARIAS FOTOS
           ============================================= */

        if (
            isset($_FILES["fotos"]) &&
            isset($_FILES["fotos"]["name"]) &&
            is_array($_FILES["fotos"]["name"])
        ) {

            $carpeta = __DIR__ . "/../uploads/incidencias/";

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $cantidad = count($_FILES["fotos"]["name"]);

            for ($i = 0; $i < $cantidad; $i++) {

                if (
                    $_FILES["fotos"]["error"][$i]
                    !== UPLOAD_ERR_OK
                ) {
                    continue;
                }

                $extension = strtolower(
                    pathinfo(
                        $_FILES["fotos"]["name"][$i],
                        PATHINFO_EXTENSION
                    )
                );

                $extensionesPermitidas = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp"
                ];

                if (!in_array($extension, $extensionesPermitidas)) {
                    continue;
                }

                $nombreFoto =
                    uniqid("incidencia_") . "." . $extension;

                $ruta = $carpeta . $nombreFoto;

                if (
                    move_uploaded_file(
                        $_FILES["fotos"]["tmp_name"][$i],
                        $ruta
                    )
                ) {

                    $incidencia->agregarFoto(
                        $id_incidencia,
                        $nombreFoto
                    );
                }
            }
        }


        /* =============================================
           AUDITORÍA
           ============================================= */

        if ($id_usuario !== null) {

            $auditoria->registrar([
                "id_usuario" => $id_usuario,
                "accion" => "INSERTAR",
                "tabla_afectada" => "incidencia",
                "id_registro" => $id_incidencia,
                "descripcion" =>
                    "Se registró una nueva incidencia en la obra ID "
                    . $id_obra
            ]);
        }
    }

    header(
        "Location: /empresa_constructora/controladores/IncidenciaController.php?accion=listar&id_obra="
        . $id_obra
    );

    exit;
}


/* =====================================================
   VER
   ===================================================== */

if ($accion === "ver") {

    $id = $_GET["id"] ?? 0;

    $datos = $incidencia->buscarPorId($id);

    if (!$datos) {

        header(
            "Location: /empresa_constructora/vistas/obras/index.php"
        );

        exit;
    }

    $fotos = $incidencia->obtenerFotos($id);

    require_once __DIR__ . "/../vistas/obras/incidencias/ver.php";

    exit;
}


/* =====================================================
   EDITAR
   ===================================================== */

if ($accion === "editar") {

    $id = $_GET["id"] ?? 0;

    $datos = $incidencia->buscarPorId($id);

    if (!$datos) {

        header(
            "Location: /empresa_constructora/vistas/obras/index.php"
        );

        exit;
    }

    $fotos = $incidencia->obtenerFotos($id);

    require_once __DIR__ . "/../vistas/obras/incidencias/editar.php";

    exit;
}


/* =====================================================
   ACTUALIZAR
   ===================================================== */

if ($accion === "actualizar") {

    $id = $_POST["id_incidencia"] ?? 0;

    $actual = $incidencia->buscarPorId($id);

    if (!$actual) {

        header(
            "Location: /empresa_constructora/vistas/obras/index.php"
        );

        exit;
    }

    $datosActualizar = [
        "id_incidencia" => $id,
        "fecha" => $_POST["fecha"] ?? date("Y-m-d"),
        "tipo_incidencia" =>
            $_POST["tipo_incidencia"] ?? "",
        "descripcion" =>
            $_POST["descripcion"] ?? "",
        "estado" =>
            $_POST["estado"] ?? "Pendiente",
        "solucion" =>
            $_POST["solucion"] ?? null
    ];

    $incidencia->actualizar($datosActualizar);


    /* =============================================
       AGREGAR NUEVAS FOTOS
       ============================================= */

    if (
        isset($_FILES["fotos"]) &&
        isset($_FILES["fotos"]["name"]) &&
        is_array($_FILES["fotos"]["name"])
    ) {

        $carpeta = __DIR__ . "/../uploads/incidencias/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $cantidad = count($_FILES["fotos"]["name"]);

        for ($i = 0; $i < $cantidad; $i++) {

            if (
                $_FILES["fotos"]["error"][$i]
                !== UPLOAD_ERR_OK
            ) {
                continue;
            }

            $extension = strtolower(
                pathinfo(
                    $_FILES["fotos"]["name"][$i],
                    PATHINFO_EXTENSION
                )
            );

            $extensionesPermitidas = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            if (!in_array($extension, $extensionesPermitidas)) {
                continue;
            }

            $nombreFoto =
                uniqid("incidencia_") . "." . $extension;

            $ruta = $carpeta . $nombreFoto;

            if (
                move_uploaded_file(
                    $_FILES["fotos"]["tmp_name"][$i],
                    $ruta
                )
            ) {

                $incidencia->agregarFoto(
                    $id,
                    $nombreFoto
                );
            }
        }
    }


    /* =====================================================
   INCIDENCIAS DEL CAPATAZ
   ===================================================== */

if ($accion === "incidenciasCapataz") {

    require_once __DIR__ . "/../modelos/Obra.php";

    // Verificar que haya usuario iniciado
    if (!isset($_SESSION["usuario"])) {
        header("Location: /empresa_constructora/vistas/login.php");
        exit;
    }

    $usuario = $_SESSION["usuario"];

    // Verificar que sea Capataz
    if ((int)$usuario["id_rol"] !== 7) {
        header("Location: /empresa_constructora/vistas/dashboard.php");
        exit;
    }

    $obraModel = new Obra();

    // ID del capataz
    $idCapataz = $usuario["id_usuario"]
        ?? $usuario["id"]
        ?? null;

    if ($idCapataz === null) {
        header("Location: /empresa_constructora/vistas/dashboard/capataz.php?error=usuario");
        exit;
    }

    // Buscar la obra asignada al capataz
    $obra = $obraModel->obtenerObraPorCapataz($idCapataz);

    // Si no tiene obra asignada
    if (!$obra) {
        header("Location: /empresa_constructora/vistas/dashboard/capataz.php?error=sin_obra");
        exit;
    }

    // Entrar directamente al listado de incidencias
    header(
        "Location: /empresa_constructora/controladores/IncidenciaController.php?accion=listar&id_obra="
        . $obra["id_obra"]
    );

    exit;
}
    /* =============================================
       AUDITORÍA
       ============================================= */

    if ($id_usuario !== null) {

        $auditoria->registrar([
            "id_usuario" => $id_usuario,
            "accion" => "ACTUALIZAR",
            "tabla_afectada" => "incidencia",
            "id_registro" => $id,
            "descripcion" =>
                "Se actualizó la incidencia ID " . $id
        ]);
    }

    header(
        "Location: /empresa_constructora/controladores/IncidenciaController.php?accion=ver&id="
        . $id
    );

    exit;
}


/* =====================================================
   ELIMINAR
   ===================================================== */

if ($accion === "eliminar") {

    $id = $_GET["id"] ?? 0;

    $datos = $incidencia->buscarPorId($id);

    if ($datos) {

        $id_obra = $datos["id_obra"];

        /* Obtener fotos antes de eliminar */

        $fotos = $incidencia->obtenerFotos($id);

        if ($incidencia->eliminar($id)) {

            /* Eliminar archivos físicos */

            foreach ($fotos as $foto) {

                $rutaFoto =
                    __DIR__ .
                    "/../uploads/incidencias/" .
                    $foto["ruta_foto"];

                if (file_exists($rutaFoto)) {
                    unlink($rutaFoto);
                }
            }


            if ($id_usuario !== null) {

                $auditoria->registrar([
                    "id_usuario" => $id_usuario,
                    "accion" => "ELIMINAR",
                    "tabla_afectada" => "incidencia",
                    "id_registro" => $id,
                    "descripcion" =>
                        "Se eliminó la incidencia ID "
                        . $id
                ]);
            }
        }

        header(
            "Location: /empresa_constructora/controladores/IncidenciaController.php?accion=listar&id_obra="
            . $id_obra
        );

        exit;
    }

    header(
        "Location: /empresa_constructora/vistas/obras/index.php"
    );

    exit;
}


/* =====================================================
   ELIMINAR FOTO
   ===================================================== */

if ($accion === "eliminarFoto") {

    $id_foto = $_GET["id_foto"] ?? 0;

    $foto = $incidencia->obtenerFoto($id_foto);

    if ($foto) {

        $id_incidencia = $foto["id_incidencia"];

        $rutaFoto =
            __DIR__ .
            "/../uploads/incidencias/" .
            $foto["ruta_foto"];

        if ($incidencia->eliminarFoto($id_foto)) {

            if (file_exists($rutaFoto)) {
                unlink($rutaFoto);
            }

            if ($id_usuario !== null) {

                $auditoria->registrar([
                    "id_usuario" => $id_usuario,
                    "accion" => "ELIMINAR",
                    "tabla_afectada" => "foto_incidencia",
                    "id_registro" => $id_foto,
                    "descripcion" =>
                        "Se eliminó una foto de la incidencia ID "
                        . $id_incidencia
                ]);
            }
        }

        header(
            "Location: /empresa_constructora/controladores/IncidenciaController.php?accion=ver&id="
            . $id_incidencia
        );

        exit;
    }

    header(
        "Location: /empresa_constructora/vistas/obras/index.php"
    );

    exit;
}