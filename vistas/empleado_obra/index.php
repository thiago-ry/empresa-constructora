<?php

session_start();

require_once "../../modelos/Empleado.php";
require_once "../../modelos/Obra.php";
require_once "../../config/permisos.php";

verificarPermiso("empleados");

$empleado = new Empleado();
$obra = new Obra();

$usuario = $_SESSION["usuario"];

$obras = $obra->obtenerObrasVisibles($usuario);

$id_obra = isset($_GET["id_obra"])
    ? (int)$_GET["id_obra"]
    : 0;

/*
|--------------------------------------------------------------------------
| Si el capataz tiene una sola obra, se selecciona automáticamente
|--------------------------------------------------------------------------
*/

if ($id_obra <= 0 && count($obras) === 1) {
    $id_obra = (int)$obras[0]["id_obra"];
}

/*
|--------------------------------------------------------------------------
| Buscar la obra seleccionada dentro de las obras permitidas
|--------------------------------------------------------------------------
*/

$obraSeleccionada = null;

foreach ($obras as $obraActual) {

    if ((int)$obraActual["id_obra"] === $id_obra) {
        $obraSeleccionada = $obraActual;
        break;
    }
}

/*
|--------------------------------------------------------------------------
| Obtener empleados de la obra
|--------------------------------------------------------------------------
*/

$empleados = [];

if ($obraSeleccionada) {
    $empleados = $empleado->obtenerEmpleadosPorObra($id_obra);
}

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>

<main class="content">

    <div class="page-title no-print">
        <h1>
            Empleados
        </h1>

        <p>
            Personal y control de asistencia de la obra.
        </p>
    </div>

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
                    No hay una obra seleccionada
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

        <!-- INFORMACIÓN DE LA OBRA -->

        <div
            class="table-container"
            style="margin-top: 0; margin-bottom: 25px;"
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
                        Empleados asignados a esta obra
                    </p>

                </div>

                <span class="badge badge-primary">

                    <i class="fa-solid fa-building"></i>

                    Obra

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

                        Asistencia

                    </h3>

                    <p>

                        <?php if ($_GET["mensaje"] === "entrada"): ?>

                            Entrada registrada correctamente.

                        <?php elseif ($_GET["mensaje"] === "salida"): ?>

                            Salida registrada correctamente.

                        <?php elseif ($_GET["mensaje"] === "ausencia"): ?>

                            Ausencia registrada correctamente.

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
                        <?= htmlspecialchars(
                            $_GET["error"]
                        ); ?>
                    </p>

                </div>

            </div>

        <?php endif; ?>


        <!-- TABLA DE EMPLEADOS -->

        <div class="table-container">

            <div class="table-header">

                <div>

                    <h2>
                        Personal de la obra
                    </h2>

                    <p
                        style="
                            color: var(--text-secondary);
                            font-size: 13px;
                            margin-top: 5px;
                        "
                    >
                        Control diario de entradas, salidas y ausencias.
                    </p>

                </div>

            </div>


            <table
                class="table"
                id="tablaEmpleadosObra"
            >

                <thead>

                    <tr>

                        <th>
                            Nombre
                        </th>

                        <th>
                            Cargo
                        </th>

                        <th>
                            Entrada
                        </th>

                        <th>
                            Salida
                        </th>

                        <th>
                            Estado
                        </th>

                        <th class="no-print">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($empleados)): ?>

                        <tr>

                            <td
                                colspan="6"
                                style="
                                    text-align: center;
                                    padding: 40px;
                                "
                            >

                                <i
                                    class="fa-solid fa-users-slash"
                                    style="
                                        font-size: 28px;
                                        margin-bottom: 10px;
                                    "
                                ></i>

                                <br>

                                No hay empleados asignados a esta obra.

                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach ($empleados as $empleadoActual): ?>

                            <?php

                            $estado = $empleadoActual["estado"] ?? "";

                            $entrada =
                                $empleadoActual["hora_entrada"]
                                ?? null;

                            $salida =
                                $empleadoActual["hora_salida"]
                                ?? null;

                            ?>

                            <tr>

                                <!-- NOMBRE -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $empleadoActual["nombre"]
                                            . " "
                                            . $empleadoActual["apellido"]
                                        ); ?>

                                    </strong>

                                    <br>

                                    <small>

                                        ID:

                                        <?= htmlspecialchars(
                                            $empleadoActual["id_usuario"]
                                        ); ?>

                                    </small>

                                </td>


                                <!-- CARGO -->

                                <td>

                                    <?php if (
                                        !empty(
                                            $empleadoActual["nombre_cargo"]
                                        )
                                    ): ?>

                                        <span class="badge badge-primary">

                                            <?= htmlspecialchars(
                                                $empleadoActual["nombre_cargo"]
                                            ); ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="badge badge-secondary">
                                            Sin cargo
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ENTRADA -->

                                <td>

                                    <?php if ($entrada): ?>

                                        <strong>

                                            <i
                                                class="fa-regular fa-clock"
                                            ></i>

                                            <?= date(
                                                "H:i",
                                                strtotime($entrada)
                                            ); ?>

                                        </strong>

                                    <?php else: ?>

                                        <span
                                            style="
                                                color: var(--text-secondary);
                                            "
                                        >
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- SALIDA -->

                                <td>

                                    <?php if ($salida): ?>

                                        <strong>

                                            <i
                                                class="fa-regular fa-clock"
                                            ></i>

                                            <?= date(
                                                "H:i",
                                                strtotime($salida)
                                            ); ?>

                                        </strong>

                                    <?php else: ?>

                                        <span
                                            style="
                                                color: var(--text-secondary);
                                            "
                                        >
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ESTADO -->

                                <td>

                                    <?php if ($estado === "Ausente"): ?>

                                        <span class="badge badge-danger">

                                            <i
                                                class="fa-solid fa-user-xmark"
                                            ></i>

                                            Ausente

                                        </span>


                                    <?php elseif ($estado === "Tarde"): ?>

                                        <span class="badge badge-warning">

                                            <i
                                                class="fa-solid fa-clock"
                                            ></i>

                                            Tarde

                                        </span>


                                    <?php elseif (
                                        $estado === "Presente"
                                        && $entrada
                                        && $salida
                                    ): ?>

                                        <span class="badge badge-info">

                                            <i
                                                class="fa-solid fa-circle-check"
                                            ></i>

                                            Completa

                                        </span>


                                    <?php elseif (
                                        $estado === "Presente"
                                        && $entrada
                                    ): ?>

                                        <span class="badge badge-success">

                                            <i
                                                class="fa-solid fa-person-running"
                                            ></i>

                                            Trabajando

                                        </span>


                                    <?php else: ?>

                                        <span class="badge badge-secondary">

                                            <i
                                                class="fa-regular fa-clock"
                                            ></i>

                                            Sin registrar

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ACCIONES -->

                                <td class="no-print">

                                    <div class="table-actions">


                                        <?php if (!$estado): ?>

                                            <!-- ENTRADA -->

                                            <a
                                                href="../../controladores/AsistenciaController.php?accion=entrada&id_usuario=<?= $empleadoActual["id_usuario"]; ?>&id_obra=<?= $id_obra; ?>"
                                                class="btn btn-primary"
                                                title="Registrar entrada"
                                            >

                                                <i
                                                    class="fa-solid fa-right-to-bracket"
                                                ></i>

                                            </a>


                                            <!-- AUSENCIA -->

                                            <a
                                                href="../../controladores/AsistenciaController.php?accion=ausencia&id_usuario=<?= $empleadoActual["id_usuario"]; ?>&id_obra=<?= $id_obra; ?>"
                                                class="btn btn-danger"
                                                title="Marcar ausencia"
                                                onclick="return confirm('¿Querés marcar a este empleado como ausente?');"
                                            >

                                                <i
                                                    class="fa-solid fa-user-xmark"
                                                ></i>

                                            </a>


                                        <?php elseif (
                                            $estado === "Ausente"
                                        ): ?>


                                            <!-- CORREGIR AUSENCIA / ENTRADA -->

                                            <a
                                                href="../../controladores/AsistenciaController.php?accion=entrada&id_usuario=<?= $empleadoActual["id_usuario"]; ?>&id_obra=<?= $id_obra; ?>"
                                                class="btn btn-primary"
                                                title="Registrar entrada"
                                            >

                                                <i
                                                    class="fa-solid fa-right-to-bracket"
                                                ></i>

                                            </a>


                                        <?php elseif (
                                            $estado === "Presente"
                                            && $entrada
                                            && !$salida
                                        ): ?>


                                            <!-- SALIDA -->

                                            <a
                                                href="../../controladores/AsistenciaController.php?accion=salida&id_usuario=<?= $empleadoActual["id_usuario"]; ?>&id_obra=<?= $id_obra; ?>"
                                                class="btn btn-secondary"
                                                title="Registrar salida"
                                            >

                                                <i
                                                    class="fa-solid fa-right-from-bracket"
                                                ></i>

                                            </a>


                                        <?php else: ?>


                                            <!-- JORNADA COMPLETA -->

                                            <span
                                                class="badge badge-info"
                                                title="Jornada completa"
                                            >

                                                <i
                                                    class="fa-solid fa-check"
                                                ></i>

                                            </span>


                                        <?php endif; ?>


                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</main>


<?php

$script = "empleado_obra";

require_once "../../layouts/footer.php";

?>