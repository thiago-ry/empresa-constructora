<?php

session_start();

$raiz = dirname(__DIR__, 2);
$urlBase = "/empresa_constructora";

require_once $raiz . "/modelos/Obra.php";
require_once $raiz . "/modelos/Incidencia.php";
require_once $raiz . "/config/permisos.php";

verificarPermiso("obras");

$obraModel = new Obra();
$incidenciaModel = new Incidencia();

$usuario = $_SESSION["usuario"] ?? null;

if (!$usuario) {
    header("Location: " . $urlBase . "/vistas/login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Obtener la obra del capataz
|--------------------------------------------------------------------------
*/

$idCapataz = $usuario["id_usuario"]
    ?? $usuario["id"]
    ?? null;

$obraSeleccionada = null;
$incidencias = [];
$resumen = [
    "total" => 0,
    "pendientes" => 0,
    "revision" => 0,
    "resueltas" => 0
];

if ($idCapataz !== null) {

    $obraSeleccionada = $obraModel->obtenerObraPorCapataz($idCapataz);

    if ($obraSeleccionada) {

        $id_obra = (int)$obraSeleccionada["id_obra"];

        $incidencias = $incidenciaModel->obtenerPorObra($id_obra);

        $resumen = $incidenciaModel->obtenerResumen($id_obra);
    }
}

$id_obra = $obraSeleccionada["id_obra"] ?? 0;

require_once $raiz . "/layouts/header.php";
require_once $raiz . "/layouts/sidebar.php";

?>

<main class="content fade-up">

    <!-- ENCABEZADO -->

    <div class="page-header">

        <div>

            <h1>
                Incidencias
            </h1>

            <p>
                Registro y seguimiento de problemas detectados durante la obra.
            </p>

        </div>

        <?php if ($obraSeleccionada): ?>

            <a
                href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=crear&id_obra=<?= $id_obra ?>"
                class="btn btn-primary"
            >

                <i class="fas fa-plus"></i>

                Nueva incidencia

            </a>

        <?php endif; ?>

    </div>


    <!-- SI NO TIENE OBRA -->

    <?php if (!$obraSeleccionada): ?>

        <div class="table-container">

            <div
                style="
                    text-align: center;
                    padding: 50px 30px;
                "
            >

                <i
                    class="fa-solid fa-building-circle-exclamation"
                    style="
                        font-size: 40px;
                        color: var(--primary);
                        margin-bottom: 15px;
                    "
                ></i>

                <h2
                    style="
                        color: white;
                        margin-bottom: 8px;
                    "
                >
                    No tenés una obra asignada
                </h2>

                <p
                    style="
                        color: var(--text-secondary);
                    "
                >
                    No se encontró una obra asignada a tu usuario.
                </p>

            </div>

        </div>

    <?php else: ?>


        <!-- OBRA ACTUAL -->

        <div
            class="table-container"
            style="
                margin-top: 0;
                margin-bottom: 25px;
            "
        >

            <div class="table-header">

                <div>

                    <h2>

                        <?= htmlspecialchars(
                            $obraSeleccionada["nombre_obra"]
                        ); ?>

                    </h2>

                    <p
                        style="
                            color: var(--text-secondary);
                            font-size: 13px;
                            margin-top: 5px;
                        "
                    >

                        Incidencias registradas en esta obra

                    </p>

                </div>

                <span class="badge badge-primary">

                    <i class="fa-solid fa-building"></i>

                    Mi obra

                </span>

            </div>

        </div>


        <!-- MENSAJES -->

        <?php if (isset($_GET["mensaje"])): ?>

            <div
                class="alert-container"
                style="
                    margin-top: 0;
                    margin-bottom: 20px;
                "
            >

                <div class="alert alert-success">

                    <h3>

                        <i class="fa-solid fa-circle-check"></i>

                        Incidencias

                    </h3>

                    <p>

                        <?php if ($_GET["mensaje"] === "creada"): ?>

                            La incidencia se registró correctamente.

                        <?php elseif ($_GET["mensaje"] === "actualizada"): ?>

                            La incidencia se actualizó correctamente.

                        <?php elseif ($_GET["mensaje"] === "eliminada"): ?>

                            La incidencia se eliminó correctamente.

                        <?php else: ?>

                            Operación realizada correctamente.

                        <?php endif; ?>

                    </p>

                </div>

            </div>

        <?php endif; ?>


        <?php if (isset($_GET["error"])): ?>

            <div
                class="alert-container"
                style="
                    margin-top: 0;
                    margin-bottom: 20px;
                "
            >

                <div class="alert alert-danger">

                    <h3>

                        <i class="fa-solid fa-circle-exclamation"></i>

                        Atención

                    </h3>

                    <p>

                        <?= htmlspecialchars($_GET["error"]); ?>

                    </p>

                </div>

            </div>

        <?php endif; ?>


        <!-- RESUMEN -->

        <div class="card-grid">

            <div class="kpi-card">

                <i class="fas fa-list-check"></i>

                <h2>
                    <?= htmlspecialchars($resumen["total"]); ?>
                </h2>

                <h4>
                    Total de incidencias
                </h4>

            </div>


            <div class="kpi-card">

                <i class="fas fa-clock"></i>

                <h2 style="color: var(--warning);">

                    <?= htmlspecialchars(
                        $resumen["pendientes"]
                    ); ?>

                </h2>

                <h4>
                    Pendientes
                </h4>

            </div>


            <div class="kpi-card">

                <i class="fas fa-magnifying-glass"></i>

                <h2 style="color: var(--info);">

                    <?= htmlspecialchars(
                        $resumen["revision"]
                    ); ?>

                </h2>

                <h4>
                    En revisión
                </h4>

            </div>


            <div class="kpi-card">

                <i class="fas fa-circle-check"></i>

                <h2 style="color: var(--success);">

                    <?= htmlspecialchars(
                        $resumen["resueltas"]
                    ); ?>

                </h2>

                <h4>
                    Resueltas
                </h4>

            </div>

        </div>


        <!-- TABLA -->

        <div class="table-container">

            <div class="table-header">

                <div>

                    <h2>
                        Incidencias registradas
                    </h2>

                    <p
                        style="
                            color: var(--text-secondary);
                            font-size: 13px;
                            margin-top: 5px;
                        "
                    >
                        Problemas detectados durante el desarrollo de la obra.
                    </p>

                </div>

            </div>


            <?php if (empty($incidencias)): ?>

                <div
                    class="empty-state"
                    style="
                        margin: 20px;
                        border: none;
                    "
                >

                    <i class="fas fa-exclamation-triangle"></i>

                    <h3>
                        No hay incidencias
                    </h3>

                    <p>
                        Todavía no se registraron incidencias para esta obra.
                    </p>

                    <a
                        href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=crear&id_obra=<?= $id_obra ?>"
                        class="btn btn-primary"
                        style="margin-top: 15px;"
                    >

                        Registrar primera incidencia

                    </a>

                </div>

            <?php else: ?>

                <table class="table">

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Fecha
                            </th>

                            <th>
                                Tipo
                            </th>

                            <th>
                                Descripción
                            </th>

                            <th>
                                Estado
                            </th>

                            <th style="text-align: center;">
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($incidencias as $item): ?>

                            <?php

                            $estado = $item["estado"] ?? "Pendiente";

                            $badgeClass = "badge-warning";

                            if ($estado === "En revisión") {

                                $badgeClass = "badge-info";

                            } elseif ($estado === "Resuelta") {

                                $badgeClass = "badge-success";

                            }

                            ?>

                            <tr>

                                <td>

                                    <strong>

                                        #<?= htmlspecialchars(
                                            $item["id_incidencia"]
                                        ); ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= !empty($item["fecha"])
                                        ? date(
                                            "d/m/Y",
                                            strtotime($item["fecha"])
                                        )
                                        : "-"; ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $item["tipo_incidencia"] ?? "N/A"
                                    ); ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        mb_strimwidth(
                                            $item["descripcion"] ?? "",
                                            0,
                                            70,
                                            "..."
                                        )
                                    ); ?>

                                </td>


                                <td>

                                    <span
                                        class="badge <?= $badgeClass ?>"
                                    >

                                        <?= htmlspecialchars($estado); ?>

                                    </span>

                                </td>


                                <td>

                                    <div class="table-actions">

                                        <!-- VER -->

                                        <a
                                            href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=ver&id=<?= $item["id_incidencia"] ?>"
                                            class="btn btn-secondary"
                                            title="Ver detalle"
                                        >

                                            <i class="fas fa-eye"></i>

                                        </a>


                                        <!-- EDITAR -->

                                        <a
                                            href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=editar&id=<?= $item["id_incidencia"] ?>"
                                            class="btn btn-warning"
                                            title="Editar"
                                        >

                                            <i class="fas fa-edit"></i>

                                        </a>


                                        <!-- ELIMINAR -->

                                        <a
                                            href="<?= $urlBase ?>/controladores/IncidenciaController.php?accion=eliminar&id=<?= $item["id_incidencia"] ?>"
                                            class="btn btn-danger"
                                            title="Eliminar"
                                            onclick="return confirm('¿Está seguro de eliminar esta incidencia?');"
                                        >

                                            <i class="fas fa-trash"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>


        <!-- VOLVER -->

        <div
            class="form-actions"
            style="
                justify-content: flex-start;
                margin-top: 25px;
            "
        >

        </div>

    <?php endif; ?>

</main>


<?php

$script = "incidencias";

require_once $raiz . "/layouts/footer.php";

?>