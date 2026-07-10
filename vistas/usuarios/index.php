<?php

require_once "../../modelos/Usuario.php";

$usuario = new Usuario();

$usuarios = $usuario->obtenerTodos();

require_once "../../layouts/header.php";
require_once "../../layouts/sidebar.php";

?>
<main class="content">

    <div class="page-title">
        <h1>Usuarios</h1>
        <p>Administración de usuarios registrados.</p>
    </div>

    <div class="table-container">

        <div class="toolbar">

            <div class="toolbar-left">

                <input type="text" id="buscarUsuario" class="search-box" placeholder="Buscar usuario...">

                <select class="filter" id="filtroRol">
                    <option value="">Todos los roles</option>
                    <option>Gerente</option>
                    <option>Administrativo</option>
                    <option>Jefe de Obra</option>
                    <option>Depósito</option>
                    <option>Empleado</option>
                    <option>Cliente</option>
                </select>

                <select id="filtroEstado" class="filter">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>

            </div>

            <a href="agregar.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
                Nuevo Usuario
            </a>

        </div>
        <table class="table" id="tablaUsuarios">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u) { ?>
                    <tr>
                        <td><?= $u["nombre"] . " " . $u["apellido"]; ?></td>
                        <td><?= $u["correo"]; ?></td>
                        <td><?= $u["nombre_rol"]; ?></td>
                        <td>
                            <span class="<?= $u['estado'] == 1 ? 'badge badge-success' : 'badge badge-danger' ?>">
                                <?= $u['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td>
                            <a href="editar.php?id=<?php echo $u['id_usuario']; ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="../../controladores/UsuarioController.php?accion=baja&id=<?php echo $u['id_usuario']; ?>">
                                <?= $u['estado'] == 1 ? '<i class="fa-solid fa-times"></i>' : '<i class="fa-solid fa-check link-accionA"></i> ' ?>
                            </a>


                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>
<?php
$script = "usuarios";
require_once "../../layouts/footer.php";
?>