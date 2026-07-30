<?php

require_once "../../modelos/Cliente.php";

$usuarioModel = new Cliente();

$id = $_GET["id"];

$cliente = $usuarioModel->buscarPorId($id);

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";
?>

<main class="content">
    <div class="page-title">
        <h1>
            Editar cliente
        </h1>
        <p>
            Modifique los datos del cliente.
        </p>
    </div>

    <div class="form-card">
        <form
            class="form"
            action="../../controladores/ClienteController.php"
            method="POST"
            autocomplete="off">

            <input
                type="hidden"
                name="accion"
                value="editar">
            <input
                type="hidden"
                name="id_usuario"
                value="<?= $cliente["id_usuario"]; ?>">

            <div class="form-row">
                <div class="form-group">

                    <label>
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="input"
                        value="<?= $cliente["nombre"]; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>
                        Apellido
                    </label>
                    <input
                        type="text"
                        name="apellido"
                        class="input"
                        value="<?= $cliente["apellido"]; ?>"
                        required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>
                        Correo
                    </label>
                    <input
                        type="email"
                        name="correo"
                        class="input"
                        value="<?= $cliente["correo"]; ?>"
                        required>
                </div>
                <div class="form-group">
                    <label>
                        Rol
                    </label>
                    <p class="input">cliente</p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>
                        Documento
                    </label>
                    <input
                        type="text"
                        name="documento"
                        class="input"
                        value="<?= $cliente["documento"] ?? ""; ?>">
                </div>
                <div class="form-group">
                    <label>
                        Teléfono
                    </label>
                    <input
                        type="text"
                        name="telefono"
                        class="input"
                        value="<?= $cliente["telefono"] ?? ""; ?>">
                </div>
            </div>

            <div class="form-actions">
                <a
                    href="index.php"
                    class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i>
                    Guardar cambios
                </button>
            </div>

        </form>
    </div>
</main>
