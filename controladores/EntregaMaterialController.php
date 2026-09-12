<?php

require_once __DIR__ . "/../modelos/SolicitudMaterial.php";
require_once __DIR__ . "/../modelos/EntregaMaterial.php";
require_once __DIR__ . "/../modelos/Obra.php";
require_once __DIR__ . "/../modelos/Auditoria.php";
require_once __DIR__ . "/../config/permisos.php";

verificarPermiso("materiales");

$solicitudModel = new SolicitudMaterial();
$entregaModel = new EntregaMaterial();
$obraModel = new Obra();
$auditoria = new Auditoria();

$accion = $_GET["accion"] ?? "listar";

switch ($accion) {

    /*
    |--------------------------------------------------------------------------
    | LISTAR SOLICITUDES
    |--------------------------------------------------------------------------
    */

    case "listar":

        $solicitudes = $solicitudModel->obtenerTodas();

        require __DIR__ . "/../vistas/deposito/solicitudes/index.php";

        break;


    /*
    |--------------------------------------------------------------------------
    | VER DETALLE
    |--------------------------------------------------------------------------
    */

    case "detalle":

        $id_solicitud = $_GET["id"] ?? 0;

        if (!$id_solicitud) {
            die("Solicitud no especificada.");
        }

        $solicitud = $solicitudModel->obtenerPorId($id_solicitud);

        if (!$solicitud) {
            die("La solicitud no existe.");
        }

        $obra = $obraModel->buscarPorId(
            $solicitud["id_obra"]
        );

        $detalle = $solicitudModel->obtenerDetalle(
            $id_solicitud
        );

        require __DIR__ . "/../vistas/deposito/solicitudes/detalle.php";

        break;


    /*
    |--------------------------------------------------------------------------
    | APROBAR
    |--------------------------------------------------------------------------
    */

    case "aprobar":

        $id_solicitud = $_GET["id"] ?? 0;

        if (!$id_solicitud) {
            die("Solicitud no especificada.");
        }

        $solicitud = $solicitudModel->obtenerPorId($id_solicitud);

        if (!$solicitud) {
            die("La solicitud no existe.");
        }

        if ($solicitud["estado"] !== "Pendiente") {

            echo "<script>
                alert('Solo se pueden aprobar solicitudes pendientes.');
                window.location.href='EntregaMaterialController.php?accion=listar';
              </script>";

            exit;
        }

        // Obtener los materiales solicitados
        $detalle = $solicitudModel->obtenerDetalle($id_solicitud);

        if (empty($detalle)) {

            echo "<script>
                alert('La solicitud no contiene materiales.');
                history.back();
              </script>";

            exit;
        }

        // Comprobar stock
        $stockInsuficiente = [];

        foreach ($detalle as $material) {

            $cantidadSolicitada = (float)$material["cantidad"];
            $stockActual = (float)$material["stock"];

            if ($stockActual < $cantidadSolicitada) {

                $stockInsuficiente[] =
                    $material["nombre_material"] .
                    " (solicitado: " . $cantidadSolicitada .
                    ", disponible: " . $stockActual . ")";
            }
        }

        // Si falta stock, no aprobar
        if (!empty($stockInsuficiente)) {

            $mensaje = "No se puede aprobar la solicitud. Stock insuficiente en:\\n\\n";

            foreach ($stockInsuficiente as $material) {
                $mensaje .= "- " . $material . "\\n";
            }

            echo "<script>
                alert(" . json_encode($mensaje) . ");
                history.back();
              </script>";

            exit;
        }

        // Aprobar solicitud
        $resultado = $solicitudModel->cambiarEstado(
            $id_solicitud,
            "Aprobada"
        );
        $auditoria->registrar([
            "id_usuario" => $_SESSION["usuario"]["id"],
            "accion" => "EDITAR",
            "tabla_afectada" => "solicitud_material",
            "id_registro" => $id_solicitud,
            "descripcion" => "Aprobó una solicitud de materiales"
        ]);
        if (!$resultado) {

            echo "<script>
                alert('No se pudo aprobar la solicitud.');
                history.back();
              </script>";

            exit;
        }

        header(
            "Location: EntregaMaterialController.php?accion=detalle&id="
                . $id_solicitud
        );

        exit;

        break;

    case "rechazar":

        $id_solicitud = $_GET["id"] ?? 0;

        if (!$id_solicitud) {
            die("Solicitud no especificada.");
        }

        $solicitud = $solicitudModel->obtenerPorId(
            $id_solicitud
        );

        if (!$solicitud) {
            die("La solicitud no existe.");
        }

        if ($solicitud["estado"] !== "Pendiente") {

            echo "<script>
                    alert('Solo se pueden rechazar solicitudes pendientes.');
                    window.location.href='EntregaMaterialController.php?accion=listar';
                  </script>";

            exit;
        }

        $resultado = $solicitudModel->cambiarEstado(
            $id_solicitud,
            "Rechazada"
        );

        if (!$resultado) {

            echo "<script>
                    alert('No se pudo rechazar la solicitud.');
                    history.back();
                  </script>";

            exit;
        }

        $auditoria->registrar([
            "id_usuario" => $_SESSION["usuario"]["id"],
            "accion" => "EDITAR",
            "tabla_afectada" => "solicitud_material",
            "id_registro" => $id_solicitud,
            "descripcion" => "Rechazó una solicitud de materiales"
        ]);

        header(
            "Location: EntregaMaterialController.php?accion=listar"
        );

        exit;

        break;


    /*
    |--------------------------------------------------------------------------
    | FORMULARIO DE ENTREGA
    |--------------------------------------------------------------------------
    */

    case "entregar":

        $id_solicitud = $_GET["id"] ?? 0;

        if (!$id_solicitud) {
            die("Solicitud no especificada.");
        }

        $solicitud = $solicitudModel->obtenerPorId(
            $id_solicitud
        );

        if (!$solicitud) {
            die("La solicitud no existe.");
        }

        if ($solicitud["estado"] !== "Aprobada") {

            echo "<script>
                    alert('Solo se pueden entregar solicitudes aprobadas.');
                    window.location.href='EntregaMaterialController.php?accion=listar';
                  </script>";

            exit;
        }

        $obra = $obraModel->buscarPorId(
            $solicitud["id_obra"]
        );

        $detalle = $solicitudModel->obtenerDetalle(
            $id_solicitud
        );

        require __DIR__ . "/../vistas/deposito/solicitudes/entregar.php";

        break;


    /*
    |--------------------------------------------------------------------------
    | GUARDAR ENTREGA
    |--------------------------------------------------------------------------
    */

    case "guardarEntrega":

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: EntregaMaterialController.php?accion=listar");
            exit;
        }

        $id_solicitud = $_POST["id_solicitud"] ?? 0;
        $materiales = $_POST["material"] ?? [];
        $cantidades = $_POST["cantidad_entregada"] ?? [];
        $observaciones = $_POST["observaciones"] ?? null;

        if (!$id_solicitud) {
            die("Solicitud no especificada.");
        }

        if (empty($materiales) || empty($cantidades)) {
            die("No hay materiales para entregar.");
        }

        $solicitud = $solicitudModel->obtenerPorId($id_solicitud);

        if (!$solicitud) {
            die("La solicitud no existe.");
        }

        if ($solicitud["estado"] !== "Aprobada") {
            die("La solicitud no está aprobada.");
        }

        if (!isset($_SESSION["usuario"]["id"])) {
            die("Sesión de usuario no válida.");
        }

        $id_usuario = $_SESSION["usuario"]["id"];

        /*
     * Obtener los materiales originales de la solicitud.
     */
        $detalleSolicitud = $solicitudModel->obtenerDetalle($id_solicitud);

        if (empty($detalleSolicitud)) {
            die("La solicitud no contiene materiales.");
        }

        /*
     * Crear un índice para validar rápidamente
     * qué materiales pertenecen a la solicitud.
     */
        $materialesSolicitados = [];

        foreach ($detalleSolicitud as $item) {

            $materialesSolicitados[$item["id_material"]] = [
                "cantidad" => (float)$item["cantidad"],
                "nombre" => $item["nombre_material"]
            ];
        }

        /*
     * Validar todos los materiales y cantidades
     * antes de modificar la base de datos.
     */
        $entregas = [];

        foreach ($materiales as $indice => $id_material) {

            $id_material = (int)$id_material;
            $cantidad = $cantidades[$indice] ?? 0;

            if ($id_material <= 0) {
                continue;
            }

            if (!is_numeric($cantidad) || (float)$cantidad <= 0) {
                die("Todas las cantidades entregadas deben ser mayores a 0.");
            }

            $cantidad = (float)$cantidad;

            /*
         * Comprobar que el material pertenece
         * a esta solicitud.
         */
            if (!isset($materialesSolicitados[$id_material])) {
                die("Se intentó entregar un material que no pertenece a la solicitud.");
            }

            $cantidadSolicitada =
                $materialesSolicitados[$id_material]["cantidad"];

            /*
         * No permitir entregar más de lo solicitado.
         */
            if ($cantidad > $cantidadSolicitada) {

                die("No se puede entregar más de lo solicitado para: " .
                    $materialesSolicitados[$id_material]["nombre"]);
            }

            $entregas[] = [
                "id_material" => $id_material,
                "cantidad" => $cantidad
            ];
        }

        if (empty($entregas)) {
            die("No se indicó ningún material válido para entregar.");
        }

        /*
     * Comenzar transacción.
     */
        $conexion = new Conexion();
        $db = $conexion->conectar();

        try {

            $db->beginTransaction();

            /*
         * Verificar stock actual y bloquear las filas
         * mientras se realiza la operación.
         */
            foreach ($entregas as $entrega) {

                $sqlStock = "SELECT
                            id_material,
                            nombre_material,
                            stock
                         FROM material
                         WHERE id_material = :id_material
                         FOR UPDATE";

                $stmtStock = $db->prepare($sqlStock);

                $stmtStock->bindParam(
                    ":id_material",
                    $entrega["id_material"],
                    PDO::PARAM_INT
                );

                $stmtStock->execute();

                $materialDB = $stmtStock->fetch(PDO::FETCH_ASSOC);

                if (!$materialDB) {
                    throw new Exception("El material no existe.");
                }

                $stockActual = (float)$materialDB["stock"];
                $cantidadEntregada = (float)$entrega["cantidad"];

                /*
             * Verificar stock.
             */
                if ($cantidadEntregada > $stockActual) {

                    throw new Exception(
                        "Stock insuficiente para " .
                            $materialDB["nombre_material"] .
                            ". Disponible: " .
                            $stockActual .
                            ". Necesario: " .
                            $cantidadEntregada . "."
                    );
                }
            }

            /*
         * Crear encabezado de entrega.
         */
            $sqlEntrega = "INSERT INTO entrega_material
                       (
                           id_solicitud,
                           id_usuario,
                           observaciones
                       )
                       VALUES
                       (
                           :id_solicitud,
                           :id_usuario,
                           :observaciones
                       )";

            $stmtEntrega = $db->prepare($sqlEntrega);

            $stmtEntrega->bindParam(
                ":id_solicitud",
                $id_solicitud,
                PDO::PARAM_INT
            );

            $stmtEntrega->bindParam(
                ":id_usuario",
                $id_usuario,
                PDO::PARAM_INT
            );

            $stmtEntrega->bindParam(
                ":observaciones",
                $observaciones
            );

            if (!$stmtEntrega->execute()) {
                throw new Exception("No se pudo registrar la entrega.");
            }

            $id_entrega = $db->lastInsertId();

            /*
         * Registrar cada detalle y descontar stock.
         */
            foreach ($entregas as $entrega) {

                /*
             * Registrar detalle de entrega.
             */
                $sqlDetalle = "INSERT INTO detalle_entrega_material
                           (
                               id_entrega,
                               id_material,
                               cantidad_entregada
                           )
                           VALUES
                           (
                               :id_entrega,
                               :id_material,
                               :cantidad_entregada
                           )";

                $stmtDetalle = $db->prepare($sqlDetalle);

                $stmtDetalle->bindParam(
                    ":id_entrega",
                    $id_entrega,
                    PDO::PARAM_INT
                );

                $stmtDetalle->bindParam(
                    ":id_material",
                    $entrega["id_material"],
                    PDO::PARAM_INT
                );

                $stmtDetalle->bindParam(
                    ":cantidad_entregada",
                    $entrega["cantidad"]
                );

                if (!$stmtDetalle->execute()) {
                    throw new Exception(
                        "No se pudo registrar uno de los materiales entregados."
                    );
                }

                /*
             * Descontar stock.
             */
                $sqlStockUpdate = "UPDATE material
                               SET stock = stock - :cantidad
                               WHERE id_material = :id_material";

                $stmtStockUpdate = $db->prepare($sqlStockUpdate);

                $stmtStockUpdate->bindParam(
                    ":cantidad",
                    $entrega["cantidad"]
                );

                $stmtStockUpdate->bindParam(
                    ":id_material",
                    $entrega["id_material"],
                    PDO::PARAM_INT
                );

                if (!$stmtStockUpdate->execute()) {
                    throw new Exception(
                        "No se pudo actualizar el stock."
                    );
                }
            }

            /*
         * Cambiar estado de la solicitud.
         */
            $sqlEstado = "UPDATE solicitud_material
                      SET estado = 'Entregada'
                      WHERE id_solicitud = :id_solicitud";

            $stmtEstado = $db->prepare($sqlEstado);

            $stmtEstado->bindParam(
                ":id_solicitud",
                $id_solicitud,
                PDO::PARAM_INT
            );

            if (!$stmtEstado->execute()) {
                throw new Exception(
                    "No se pudo actualizar el estado de la solicitud."
                );
            }

            /*
         * Confirmar todos los cambios.
         */
            $db->commit();

            $auditoria->registrar([
                "id_usuario" => $_SESSION["usuario"]["id"],
                "accion" => "INSERTAR",
                "tabla_afectada" => "entrega_material",
                "id_registro" => $id_entrega,
                "descripcion" => "Registró la entrega de materiales correspondiente a la solicitud #" . $id_solicitud
            ]);

            header(
                "Location: EntregaMaterialController.php?accion=detalle&id="
                    . $id_solicitud
            );

            exit;
        } catch (Exception $e) {

            /*
         * Si algo falla, deshacer todo.
         */
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            die("No se pudo completar la entrega: " .
                htmlspecialchars($e->getMessage()));
        }

        break;

    case "historial":

        $entregas = $entregaModel->obtenerHistorial();

        require __DIR__ . "/../vistas/deposito/solicitudes/historial.php";

        break;

    case "detalleEntrega":

        $id_entrega = $_GET["id"] ?? 0;

        if (!$id_entrega) {
            die("Entrega no especificada.");
        }

        $entrega = $entregaModel->obtenerPorId($id_entrega);

        if (!$entrega) {
            die("La entrega no existe.");
        }

        $detalleEntrega = $entregaModel->obtenerDetalle($id_entrega);

        $solicitud = $solicitudModel->obtenerPorId(
            $entrega["id_solicitud"]
        );

        $obra = null;

        if ($solicitud) {
            $obra = $obraModel->buscarPorId(
                $solicitud["id_obra"]
            );
        }

        require __DIR__ . "/../vistas/deposito/solicitudes/detalleEntrega.php";

        break;


    default:

        die("Acción no válida.");

        break;
}
