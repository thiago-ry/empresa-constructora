<?php

require_once __DIR__ . '/iniciar.php';

// RESPONDER LA CONSULTA DE MATERIALES DEL JAVASCRIPT
if (isset($_GET['ajax']) && $_GET['ajax'] === 'materiales') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        echo json_encode(
            $presupuestoController->materiales(),
            JSON_UNESCAPED_UNICODE
        );
    } catch (Throwable $e) {
        http_response_code(500);

        echo json_encode([
            'error' => 'No se pudieron cargar los materiales.'
        ]);
    }

    exit;
}

$error = '';
$obras = $presupuestoController->obras();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $idPresupuesto = $presupuestoController->guardar($_POST);

        header(
            'Location: index.php?guardado=1&id=' . $idPresupuesto
        );
        exit;

    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

function e($valor): string
{
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<?php
$archivoHeader = __DIR__ . '/../../layouts/header.php';
$archivoSidebar = __DIR__ . '/../../layouts/sidebar.php';

if (file_exists($archivoHeader)) {
    require_once $archivoHeader;
}

if (file_exists($archivoSidebar)) {
    require_once $archivoSidebar;
}
?>

<main class="content">

    <div class="presupuesto-contenedor">

        <header class="presupuesto-header">
            <div>
                <h1>Crear presupuesto</h1>
                <p>Calculá materiales, mano de obra y costos adicionales.</p>
            </div>

            <a href="index.php" class="btn btn-outline">
                Volver
            </a>
        </header>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="formPresupuesto">

            <!-- DATOS GENERALES -->
            <section class="panel-presupuesto">
                <h2>1. Datos generales</h2>

                <div class="form-group">
                    <label for="nombre_presupuesto">
                        Nombre del presupuesto
                    </label>

                    <input
                        type="text"
                        id="nombre_presupuesto"
                        name="nombre_presupuesto"
                        class="form-control"
                        value="Presupuesto General"
                        maxlength="200"
                    >
                </div>

                <div class="form-group">
                    <label for="id_obra">Seleccionar obra</label>

                    <select
                        name="id_obra"
                        id="id_obra"
                        class="form-control"
                        required
                    >
                        <option value="">Seleccioná una obra</option>

                        <?php foreach ($obras as $obra): ?>
                            <option value="<?= (int)$obra['id_obra'] ?>">
                                <?= e($obra['nombre_obra']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <a
                        href="../obras/agregar.php?origen=presupuesto"
                        class="btn btn-outline"
                        style="display:inline-block; margin-top:10px;"
                    >
                        + Agregar obra
                    </a>
                </div>
            </section>

            <!-- MATERIALES -->
            <section class="panel-presupuesto">
                <h2>2. Materiales</h2>

                <p>
                    Elegí los materiales del catálogo. Después ingresá
                    la cantidad y el precio que quieras presupuestar.
                </p>

                <div class="form-group">
                    <label for="selectorMaterial">
                        Seleccionar material del catálogo
                    </label>

                    <div class="material-selector">
                        <select
                            id="selectorMaterial"
                            class="form-control"
                        >
                            <option value="">Cargando materiales...</option>
                        </select>

                        <button
                            type="button"
                            id="agregarMaterial"
                            class="btn-guardar"
                        >
                            + Agregar material
                        </button>
                    </div>

                    <small id="estadoMateriales"></small>
                </div>

                <div class="tabla-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Unidad</th>
                                <th>Cantidad</th>
                                <th>Precio unitario</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody id="materiales"></tbody>
                    </table>
                </div>

                <div class="total-section">
                    <strong>Total materiales:</strong>
                    <span id="totalMateriales">$0,00</span>
                </div>
            </section>

            <!-- MANO DE OBRA -->
            <section class="panel-presupuesto">
                <h2>3. Mano de obra y trabajos adicionales</h2>

                <p>
                    Agregá los trabajos necesarios, por ejemplo:
                    albañilería, plomería, electricidad o instalación.
                </p>

                <button
                    type="button"
                    id="agregarTrabajo"
                    class="btn-guardar"
                >
                    + Agregar trabajo
                </button>

                <div class="tabla-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Trabajo</th>
                                <th>Horas</th>
                                <th>Costo por hora</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>

                        <tbody id="trabajos"></tbody>
                    </table>
                </div>

                <div class="total-section">
                    <strong>Total mano de obra:</strong>
                    <span id="totalManoObra">$0,00</span>
                </div>
            </section>

            <!-- COSTOS ADICIONALES -->
            <section class="panel-presupuesto">
                <h2>4. Gastos, imprevistos y ganancia</h2>

                <div class="form-grid">

                    <div class="form-group">
                        <label for="porc_gastos_obra">
                            Gastos de obra (%)
                        </label>

                        <input
                            type="number"
                            name="porc_gastos_obra"
                            id="porc_gastos_obra"
                            class="form-control porcentaje"
                            min="0"
                            max="100"
                            step="0.01"
                            value="8"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="porc_gastos_oficina">
                            Gastos de oficina (%)
                        </label>

                        <input
                            type="number"
                            name="porc_gastos_oficina"
                            id="porc_gastos_oficina"
                            class="form-control porcentaje"
                            min="0"
                            max="100"
                            step="0.01"
                            value="12"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="porc_imprevistos">
                            Imprevistos (%)
                        </label>

                        <input
                            type="number"
                            name="porc_imprevistos"
                            id="porc_imprevistos"
                            class="form-control porcentaje"
                            min="0"
                            max="100"
                            step="0.01"
                            value="5"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="porc_ganancia">
                            Ganancia (%)
                        </label>

                        <input
                            type="number"
                            name="porc_ganancia"
                            id="porc_ganancia"
                            class="form-control porcentaje"
                            min="0"
                            max="100"
                            step="0.01"
                            value="15"
                            required
                        >
                    </div>

                </div>
            </section>

            <!-- RESUMEN -->
            <section class="panel-presupuesto">
                <h2>5. Resumen del presupuesto</h2>

                <div class="resumen-presupuesto">
                    <p>
                        Materiales:
                        <strong id="resumenMateriales">$0,00</strong>
                    </p>

                    <p>
                        Mano de obra:
                        <strong id="resumenManoObra">$0,00</strong>
                    </p>

                    <p>
                        Costo directo:
                        <strong id="costoBase">$0,00</strong>
                    </p>

                    <p>
                        Gastos de obra:
                        <strong id="totalGastosObra">$0,00</strong>
                    </p>

                    <p>
                        Gastos de oficina:
                        <strong id="totalGastosOficina">$0,00</strong>
                    </p>

                    <p>
                        Imprevistos:
                        <strong id="totalImprevistos">$0,00</strong>
                    </p>

                    <p>
                        Ganancia:
                        <strong id="totalGanancia">$0,00</strong>
                    </p>

                    <hr>

                    <h3>
                        Total final:
                        <strong id="totalFinal">$0,00</strong>
                    </h3>
                </div>
            </section>

            <!-- CONDICIONES Y OBSERVACIONES -->
            <section class="panel-presupuesto">
                <h2>6. Condiciones y observaciones</h2>

                <div class="form-group">
                    <label for="validez_dias">
                        Validez del presupuesto (días)
                    </label>

                    <input
                        type="number"
                        name="validez_dias"
                        id="validez_dias"
                        class="form-control"
                        min="1"
                        value="15"
                    >
                </div>

                <div class="form-group">
                    <label for="forma_pago">Forma de pago</label>

                    <textarea
                        name="forma_pago"
                        id="forma_pago"
                        class="form-control"
                        rows="3"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="clausula_redeterminacion">
                        Cláusula de redeterminación
                    </label>

                    <textarea
                        name="clausula_redeterminacion"
                        id="clausula_redeterminacion"
                        class="form-control"
                        rows="3"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="detalle_general">Observaciones</label>

                    <textarea
                        name="detalle_general"
                        id="detalle_general"
                        class="form-control"
                        rows="4"
                    ></textarea>
                </div>
            </section>

            <div class="acciones-formulario">
                <button type="submit" class="btn-guardar">
                    Guardar presupuesto
                </button>

                <a href="index.php" class="btn btn-outline">
                    Cancelar
                </a>
            </div>

        </form>
    </div>
</main>

<script src="../../assets/js/presupuesto.js?v=5" defer></script>